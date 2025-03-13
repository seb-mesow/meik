import { Ref, shallowRef } from "vue";
import { ISingleValueForm2, ISingleValueForm2ConstructorArgs, ISingleValueForm2Parent, SingleValueForm2, UISingleValueForm2 } from "./single-value-form2";
import { AutoCompleteCompleteEvent } from "primevue/autocomplete";
import { IOptionFinder, NumberOptionFinder, StringOptionFinder } from "@/util/option-finder";

export interface UIMultiSelectForm<O> extends UISingleValueForm2<O[]|null> {
	readonly shown_suggestions: Readonly<Ref<Readonly<O[]>>>;
	on_complete(event: AutoCompleteCompleteEvent): Promise<void>;
	on_before_show(): Promise<void>;
	on_hide(): Promise<void>;
	on_tab_keydown(event: KeyboardEvent): Promise<void>;
	optionLabel: string|undefined;
}

export interface IMultipleSelectOption<I extends string|number> {
	id: I,
	name: string,
}

/**
 * @param I type of the Property `id` of each option
 * @param O internal value of options and primary return value of `get_value()`
 * @param R whether `get_value()` only returns `O` or `O|null`; default `false`
 */
export interface IMultiSelectForm<I extends string|number, O extends IMultipleSelectOption<I>, R extends boolean = false> extends ISingleValueForm2<O[], R> {};

export interface IMultiSelectFormConstructorArgs<I extends string|number, O extends IMultipleSelectOption<I>, R extends boolean = false> extends Omit<ISingleValueForm2ConstructorArgs<O[], R>, 'val'> {
	val_ids: I[]|undefined,
	selectable_options: O[]|undefined,
	search_in: 'name'|'id',
	optionLabel?: string,
}

type _FilterFunc = (option_str: string, criteria: string) => boolean;
type _QueryCounterpartGetter<I extends string|number, O extends IMultipleSelectOption<I>> = (option: O) => string;

/**
 * @param O internal value of options and primary return value of `get_value()`
 * @param R whether `get_value()` only returns `O` or `O|null`; default `false`
 */
export abstract class MultiSelectForm<I extends string|number, O extends IMultipleSelectOption<I>, R extends boolean = false> extends SingleValueForm2<O[], O[]|null, R> implements IMultiSelectForm<I, O, R>, UIMultiSelectForm<O> {
	public readonly shown_suggestions: Ref<Readonly<O[]>> = shallowRef([]);
	public readonly optionLabel: string|undefined;
	
	private is_overlay_shown: boolean = false;
	// protected selectable_options: O[];
	// private _search_counterpart: (option: O) => string;
	
	// protected abstract id_counterpart(option: O): string; // = (option) => option.id.toLowerCase();
	// protected abstract name_counterpart(option: O): string; // = (option) => option.name.toLowerCase();
	private readonly option_finder: IOptionFinder<I, O>;
	private static readonly partial_match_filter: _FilterFunc = (option_str, criteria) => option_str.includes(criteria);
	private static readonly full_match_filter: _FilterFunc = (option_str, criteria) => option_str === criteria;
	
	public constructor(args: IMultiSelectFormConstructorArgs<I, O, R>, id: string|number, parent: ISingleValueForm2Parent<O[]>, option_finder: IOptionFinder<I, O>) {
		console.log(`MultipleSelectForm::constructor(): args ==`);
		console.log(args);
		
		let initial_values: O[] = [];
		if (args.val_ids !== undefined) {
			if (args.selectable_options === undefined || args.selectable_options?.length < 0) {
				throw new Error(`Assertation failed: MultiSelectForm::constructor(): ${id.toString()}: val_id is provided, but there are no selectable_options .`);
			} else {
				console.log(`MultipleSelectForm::constructor(): option_finder.find_all_by_many`);
				// until the super() call we are only allowed to use static methods :-/
				initial_values = option_finder.find_all_by_many(args.val_ids, 'id', MultiSelectForm.full_match_filter);
			}
		}
		
		console.log(`MultipleSelectForm::constructor(): initial_values ==`);
		console.log(initial_values);
		
		const _args : ISingleValueForm2ConstructorArgs<O[], R> = {
			...args,
			...{ val: initial_values },
		};
		super(_args, id, parent);
		
		this.option_finder = option_finder;
		this.optionLabel = args.optionLabel;
		// this.selectable_options = args.selectable_options ?? [];
		// if (args.search_in === 'name') {
		// 	this._search_counterpart = this.name_counterpart;
		// } else {
		// 	this._search_counterpart = this.id_counterpart;
		// }
	}
	
	protected create_value_from_ui_value(ui_value: O[]|null): O[]|null {
		if (!ui_value) {
			return null;
		}

		return ui_value;
	}
	
	protected create_ui_value_from_value(value: O[]|null): O[]|null {
		return value;
	}
	
	public on_blur(event: Event): void {
		if (!this.is_overlay_shown) {
			super.on_blur(event);
		}
	}
	
	public async on_complete(event: AutoCompleteCompleteEvent): Promise<void> {
		// useless spread operator required, because of
		// https://github.com/primefaces/primevue/issues/5601
		this.shown_suggestions.value = [...(await this.get_shown_suggestions(event.query))];
	}
	
	public async on_before_show(): Promise<void> {
		// damit man über ein leeres (wenn auch invalides) SelectFeld tabben kann
		this.is_overlay_shown = true;
	}
	
	public async on_hide(): Promise<void> {
		// damit man über ein leeres (wenn auch invalides) SelectFeld tabben kann
		this.is_overlay_shown = false;
	}
	
	public async on_tab_keydown(event: KeyboardEvent): Promise<void> {
		if (this.is_overlay_shown && this.shown_suggestions.value.length === 1) {
			event.preventDefault();
			const first: O = this.shown_suggestions.value[0];
			let value_in_editing = this.get_value_in_editing();
			if (value_in_editing === null || value_in_editing === undefined) {
				value_in_editing = [ first ];
			} else {
				value_in_editing.push(first);
			}
			return this.set_value_in_editing(value_in_editing);
		}
	}
	
	// Das für Ajax call verwenden
	private get_shown_suggestions(criteria: string): Promise<Readonly<O[]>> {
		return new Promise((resolve) => resolve(this.option_finder.find_all(criteria, 'name', MultiSelectForm.partial_match_filter)));
	}
	
	private _find_one_suggestion<_O extends IMultipleSelectOption<I>>(filter: _FilterFunc, counterpart: _QueryCounterpartGetter<I, _O>, criteria: string, selectable_options: _O[]): _O|undefined {
		criteria = criteria.trim().toLowerCase();
		return selectable_options.find((option) => filter(counterpart(option), criteria));
	}
		
	private determinate_selectable_value_from_id<T extends { id: string }, R extends boolean = false>(id: string|undefined, selectable_values: T[], required?: R): R extends true ? T|undefined : T|null|undefined {
		if (id === undefined) {
			return undefined;
		}
		let value: T|null|undefined = selectable_values.find((selectable_value: T): boolean => selectable_value.id === id);
		if (required && (value === null || value === undefined)) {
			throw new Error(`ExhibitForm::determinate_selectable_value_from_id(): provided id ${id === undefined ? 'undefined' : "'"+id+"'"}, but no value could be found`);
		}
		if (value === undefined) {
			value = null;
		}
		// @ts-expect-error
		return value;
	};
}

class NumberMultipleSelectOptionHelper<O extends IMultipleSelectOption<number>> {
	protected id_counterpart(option: O): string {
		return option.id.toString();
	};
	
	protected name_counterpart(option: O): string {
		return option.name;
	}; 
}

class StringMultipleSelectOptionHelper<O extends IMultipleSelectOption<string>> {
	protected id_counterpart(option: O): string {
		return option.id.toString();
	};
	
	protected name_counterpart(option: O): string {
		return option.name;
	}; 
}

export class StringMultipleSelectForm<O extends IMultipleSelectOption<string>, R extends boolean = false> extends MultiSelectForm<string, O, R> {
	public constructor(args: IMultiSelectFormConstructorArgs<string, O, R>, id: string|number, parent: ISingleValueForm2Parent<O[]>) {
		super(args, id, parent, new StringOptionFinder(args.selectable_options));
	}
}

export class NumberMultipleSelectForm<O extends IMultipleSelectOption<number>, R extends boolean = false> extends MultiSelectForm<number, O, R> {
	public constructor(args: IMultiSelectFormConstructorArgs<number, O, R>, id: string|number, parent: ISingleValueForm2Parent<O[]>) {
		super(args, id, parent, new NumberOptionFinder(args.selectable_options));
	}
}


