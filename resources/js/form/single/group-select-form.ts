import { shallowRef, ShallowRef } from "vue";
import { ISingleValueForm2ConstructorArgs, ISingleValueForm2Parent, SingleValueForm2, UISingleValueForm2 } from "./single-value-form2";
import { AutoCompleteCompleteEvent } from "primevue/autocomplete";

export interface UIGroupSelectForm<C, P = C> extends UISingleValueForm2<string|undefined> {
	readonly shown_suggestions: Readonly<ShallowRef<Readonly<IGroupType<C, P>[]>>>;
	on_complete(event: AutoCompleteCompleteEvent): Promise<void>;
	on_before_show(): Promise<void>;
	on_hide(): Promise<void>;
	on_tab_keydown(event: KeyboardEvent): Promise<void>;
	get_option_label(option: C): string;
}

export interface IGroupType<C = string, P = C> {
	children: C[];
	parent: P;
}

export interface IGroupSelectFormConstructorArgs<C = string, P = C> extends ISingleValueForm2ConstructorArgs<C> {
	get_shown_suggestions?: (query: string) => Promise<Readonly<IGroupType<C, P>>[]>;
	get_option_label?: (option: C) => string;
}

/**
 * Either the constructor argument get_shown_suggestions must be provided
 * or the function get_shown_suggestions() must be overridden in a subclass.
 *
 * same for get_option_label
 */
export class GroupSelectForm<C = string, P = C> extends SingleValueForm2<C, string|undefined> implements UIGroupSelectForm<C, P> {
	public readonly shown_suggestions: ShallowRef<Readonly<IGroupType<C, P>[]>>;
	private readonly _get_shown_suggestions: (query: string) => Promise<Readonly<IGroupType<C, P>>[]>;
	private readonly _get_option_label: (option: C) => string;
	private is_overlay_shown: boolean = false;
	
	public constructor(args: IGroupSelectFormConstructorArgs<C, P>, id: string|number, parent: ISingleValueForm2Parent<C>) {
		super(args, id, parent);
		this._get_shown_suggestions = args.get_shown_suggestions ?? (() => Promise.reject('no get_shown_suggestions()'));
		this._get_option_label = args.get_option_label ?? (() => 'no get_option_label()');
		this.shown_suggestions = shallowRef([]);
	}
	
	public async on_complete(event: AutoCompleteCompleteEvent): Promise<void> {
		this.shown_suggestions.value = await this.get_shown_suggestions(event.query);
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
		if (this.is_overlay_shown
			&& this.shown_suggestions.value.length === 1
			&& this.shown_suggestions.value[0].children.length === 1
		) {
			event.preventDefault();
			const first: C = this.shown_suggestions.value[0].children[0];
			return this.set_value_in_editing(first);
		}
	}
	
	protected get_shown_suggestions(query: string): Promise<Readonly<IGroupType<C, P>>[]> {
		return this._get_shown_suggestions(query);
	}
	
	public get_option_label(query: C): string {
		return this._get_option_label(query);
	}
}
