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

export interface ISelectForm<O> extends ISingleValueForm2<O> {};

export interface ISelectFormConstructorArgs<O = string> extends ISingleValueForm2ConstructorArgs<O> {
	optionLabel?: string,
	get_shown_suggestions?: (query: string) => Promise<Readonly<O[]>>;
}

/**
 * Either the constructor argument get_shown_suggestions must be provided
 * or the function get_shown_suggestions() must be overridden in a subclass.
 *
 * same for get_option_label
 */
export class SelectForm<O = string> extends SingleValueForm2<O, O|string|undefined> implements ISelectForm<O>, UISelectForm<O> {
	public readonly shown_suggestions: Ref<Readonly<O[]>>;
	public readonly optionLabel: string|undefined;
	
	private readonly _get_shown_suggestions: (query: string) => Promise<Readonly<O[]>>;
	private is_overlay_shown: boolean = false;
	
	public constructor(args: ISelectFormConstructorArgs<O>, id: string|number, parent: ISingleValueForm2Parent<O>) {
		super(args, id, parent);
		this._get_shown_suggestions = args.get_shown_suggestions ?? (() => Promise.reject('no get_shown_suggestions()'));
		this.shown_suggestions = shallowRef([]);
		this.optionLabel = args.optionLabel;
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
	
	protected get_shown_suggestions(query: string): Promise<Readonly<O[]>> {
		return this._get_shown_suggestions(query);
	}
	
	// public get_option_label(option: O): string|undefined {
	// 	return this.create_ui_value_from_value(option);
	// }
}
