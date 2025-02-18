import { shallowRef, ShallowRef } from "vue";
import { ISingleValueForm2ConstructorArgs, SingleValueForm2, UISingleValueForm2 } from "./single-value-form2";
import { AutoCompleteCompleteEvent } from "primevue/autocomplete";

export interface UIGroupSelectForm<C, P = C> extends UISingleValueForm2<string> {
	readonly shown_suggestions: Readonly<ShallowRef<Readonly<IGroupType<C, P>[]>>>;
	on_complete(event: AutoCompleteCompleteEvent): Promise<void>;
	on_tab_keydown(event: KeyboardEvent): Promise<void>;
	on_clear(): Promise<void>;
}

export interface IGroupType<C = string, P = C> {
	children: C[];
	parent: P;
}

export interface IGroupSelectFormConstructorArgs<C = string, P = C> extends ISingleValueForm2ConstructorArgs<C> {
	get_shown_suggestions: (query: string) => Promise<Readonly<IGroupType<C, P>>[]>;
}

export class GroupSelectForm<C = string, P = C> extends SingleValueForm2<C, string> implements UIGroupSelectForm<C, P> {
	public readonly shown_suggestions: ShallowRef<Readonly<IGroupType<C, P>[]>>;
	private readonly get_shown_suggestions: (query: string) => Promise<Readonly<IGroupType<C, P>>[]>;
	
	public constructor(args: IGroupSelectFormConstructorArgs<C, P>, id: string|number) {
		super(args, id);
		this.get_shown_suggestions = args.get_shown_suggestions;
		this.shown_suggestions = shallowRef([]);
	}
	
	public async on_complete(event: AutoCompleteCompleteEvent): Promise<void> {
		this.shown_suggestions.value = await this.get_shown_suggestions(event.query);
	}
	
	public async on_clear(): Promise<void> {
		// damit man über ein leeres (wenn auch invalides) SelectFeld tabben kann
		this.shown_suggestions.value = [];
	}
	
	public async on_tab_keydown(event: KeyboardEvent): Promise<void> {
		if (!this.is_valid() && this.shown_suggestions.value.length > 0) {
			event.preventDefault();
			const first_group: IGroupType<C,P> = this.shown_suggestions.value[0];
			if (first_group.children.length > 0) {
				const first: C = first_group.children[0];
				return this.set_value_in_editing(first);
			}
		}
	}
}
