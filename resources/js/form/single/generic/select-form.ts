import { Ref, shallowRef } from "vue";
import { ISingleValueForm2, ISingleValueForm2ConstructorArgs, ISingleValueForm2Parent, SingleValueForm2, UISingleValueForm2 } from "./single-value-form2";
import { AutoCompleteCompleteEvent } from "primevue/autocomplete";

export interface UISelectForm<O> extends UISingleValueForm2<O|string|undefined> {
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
export interface ISelectForm<O extends ISelectOption, R extends boolean = false> extends ISingleValueForm2<O, R> {};

export interface ISelectFormConstructorArgs<O extends ISelectOption, R extends boolean = false> extends ISingleValueForm2ConstructorArgs<O, R> {
	search_in: 'name'|'id',
	optionLabel?: string,
	selectable_options?: O[],
}

/**
 * @param O internal value of options and primary return value of `get_value()`
 * @param R whether `get_value()` only returns `O` or `O|null`; default `false`
 */
export class SelectForm<O extends ISelectOption, R extends boolean = false> extends SingleValueForm2<O, O|string|undefined, R> implements ISelectForm<O, R>, UISelectForm<O> {
	public readonly shown_suggestions: Ref<Readonly<O[]>> = shallowRef([]);
	public readonly optionLabel: string|undefined;
	
	private is_overlay_shown: boolean = false;
	protected selectable_options: O[];
	private get_query_counterpart: (option: O) => string;
	
	public constructor(args: ISelectFormConstructorArgs<O, R>, id: string|number, parent: ISingleValueForm2Parent<O>) {
		super(args, id, parent);
		this.optionLabel = args.optionLabel;
		this.selectable_options = args.selectable_options ?? [];
		if (args.search_in === 'name') {
			this.get_query_counterpart = (option) => option.name.toLowerCase();
		} else {
			this.get_query_counterpart = (option) => option.id.toLowerCase(); 
		}
	}
	
	protected create_value_from_ui_value(ui_value: O|string|undefined): O|null|undefined {
		if (!ui_value) {
			return null;
		}
		if (typeof ui_value === 'string') {
			const suggestions = this.search_suggestions(ui_value, (option_str, query) => option_str === query);
			if (suggestions.length === 1) {
				return suggestions[0];
			}
			return undefined; // multiple or no match
		}
		return ui_value;
	}
	
	protected create_ui_value_from_value(value: O|null|undefined): O|undefined {
		return value ?? undefined;
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
	
	private get_shown_suggestions(query: string): Promise<Readonly<O[]>> {
		return new Promise((resolve) => resolve(this.search_suggestions(query, (option_str, query) => option_str.includes(query))));
	}
	
	private search_suggestions(query: string, filter_func: (option_str: string, query: string) => boolean): O[] {
		query = query.trim().toLowerCase();
		return this.selectable_options.filter((option) => filter_func(this.get_query_counterpart(option), query));
	}
}
