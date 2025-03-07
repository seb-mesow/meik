import { Ref, shallowRef, Static } from "vue";
import { ISingleValueForm2, ISingleValueForm2ConstructorArgs, ISingleValueForm2Parent, SingleValueForm2, UISingleValueForm2 } from "./single-value-form2";
import { AutoCompleteCompleteEvent } from "primevue/autocomplete";

export interface UIMultiSelectForm<O> extends UISingleValueForm2<O[]|null> {
	readonly shown_suggestions: Readonly<Ref<Readonly<O[]>>>;
	on_complete(event: AutoCompleteCompleteEvent): Promise<void>;
	on_before_show(): Promise<void>;
	on_hide(): Promise<void>;
	on_tab_keydown(event: KeyboardEvent): Promise<void>;
	optionLabel: string|undefined;
}

export interface ISelectOption {
	id: string,
	name: string,
}

/**
 * @param O internal value of options and primary return value of `get_value()`
 * @param R whether `get_value()` only returns `O` or `O|null`; default `false`
 */
export interface IMultiSelectForm<O extends ISelectOption, R extends boolean = false> extends ISingleValueForm2<O[], R> {};

export interface IMultiSelectFormConstructorArgs<O extends ISelectOption, R extends boolean = false> extends Omit<ISingleValueForm2ConstructorArgs<O, R>, 'val'> {
	val_id: string|undefined,
	selectable_options: O[]|undefined,
	search_in: 'name'|'id',
	optionLabel?: string,
}

type _FilterFunc = (option_str: string, criteria: string) => boolean;
type _QueryCounterpartGetter<O extends ISelectOption> = (option: O) => string;

/**
 * @param O internal value of options and primary return value of `get_value()`
 * @param R whether `get_value()` only returns `O` or `O|null`; default `false`
 */
export class MultiSelectForm<O extends ISelectOption, R extends boolean = false> extends SingleValueForm2<O[], O[]|null, R> implements IMultiSelectForm<O, R>, UIMultiSelectForm<O> {
	public readonly shown_suggestions: Ref<Readonly<O[]>> = shallowRef([]);
	public readonly optionLabel: string|undefined;
		
	private is_overlay_shown: boolean = false;
	protected selectable_options: O[];
	private _search_counterpart: (option: O) => string;
	
	private static readonly id_counterpart: _QueryCounterpartGetter<ISelectOption> = (option) => option.id.toLowerCase();
	private static readonly name_counterpart: _QueryCounterpartGetter<ISelectOption> = (option) => option.name.toLowerCase();
	private static readonly partial_match_filter: _FilterFunc = (option_str, criteria) => option_str.includes(criteria);
	private static readonly full_match_filter: _FilterFunc = (option_str, criteria) => option_str === criteria;
	
	public constructor(args: IMultiSelectFormConstructorArgs<O, R>, ids: string[]|number[], parent: ISingleValueForm2Parent<O>) {
		let initial_value: O|undefined = undefined;
		if (args.val_id !== undefined) {
			if (args.selectable_options === undefined || args.selectable_options?.length < 0) {
				throw new Error(`Assertation failed: MultiSelectForm::constrcutor(): ${ids.toString()}: val_id is provided, but there are no selectable_options .`);
			} else {
				// until the super() call we are only allowed to use static methods :-/
				initial_value = MultiSelectForm._find_one_suggestion(MultiSelectForm.full_match_filter, MultiSelectForm.id_counterpart, args.val_id, args.selectable_options);
			}
		}
		
		const _args : ISingleValueForm2ConstructorArgs<O, R> = {
			...args,
			...{ val: initial_value },
		};
		super(_args, id, parent);
		
		this.optionLabel = args.optionLabel;
		this.selectable_options = args.selectable_options ?? [];
		if (args.search_in === 'name') {
			this._search_counterpart = MultiSelectForm.name_counterpart;
		} else {
			this._search_counterpart = MultiSelectForm.id_counterpart;
		}
	}
	
	protected create_value_from_ui_value(ui_value: O[]|null): O[]|null {
		if (!ui_value) {
			return null;
		}

		return ui_value;
	}
	
	protected create_ui_value_from_value(value: O[]|null): O[]|undefined {
		return value ?? undefined;
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
			return this.set_value_in_editing(first);
		}
	}
	
	// Das für Ajax call verwenden
	private get_shown_suggestions(criteria: string): Promise<Readonly<O[]>> {
		return new Promise((resolve) => resolve(this.search_many_suggestions(MultiSelectForm.partial_match_filter, this._search_counterpart, criteria)));
	}
	
	private search_many_suggestions(filter: _FilterFunc, counterpart: _QueryCounterpartGetter<O>, criteria: string): O[] {
		return MultiSelectForm._search_many_suggestions(filter, counterpart, criteria, this.selectable_options);
	}
	
	private static _search_many_suggestions<_O extends ISelectOption>(filter: _FilterFunc, counterpart: _QueryCounterpartGetter<_O>, criteria: string, selectable_options: _O[]): _O[] {
		criteria = criteria.trim().toLowerCase();
		return selectable_options.filter((option) => filter(counterpart(option), criteria));
	}
	
	private find_one_suggestion(filter: _FilterFunc, counterpart: _QueryCounterpartGetter<O>, criteria: string): O|undefined {
		return MultiSelectForm._find_one_suggestion(filter, counterpart, criteria, this.selectable_options);
	}
	
	private static _find_one_suggestion<_O extends ISelectOption>(filter: _FilterFunc, counterpart: _QueryCounterpartGetter<_O>, criteria: string, selectable_options: _O[]): _O|undefined {
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
