import { Ref, shallowRef, ShallowRef } from "vue";
import { ISingleValueForm2ConstructorArgs, SingleValueForm2, UISingleValueForm2 } from "./singlevalueform2";
import { AutoCompleteCompleteEvent } from "primevue/autocomplete";

export interface UISelectForm<O> extends UISingleValueForm2<string> {
	readonly shown_suggestions: Readonly<Ref<Readonly<O[]>>>;
	on_complete(event: AutoCompleteCompleteEvent): Promise<void>;
	on_tab_keydown(event: KeyboardEvent): Promise<void>;
}

export interface ISelectFormConstructorArgs<O = string> extends ISingleValueForm2ConstructorArgs<O> {
	get_shown_suggestions: (query: string) => Promise<Readonly<O[]>>;
}

export class SelectForm<O = string> extends SingleValueForm2<O, string> implements UISelectForm<O> {
	public readonly shown_suggestions: Ref<Readonly<O[]>>;
	private readonly get_shown_suggestions: (query: string) => Promise<Readonly<O[]>>;
	
	public constructor(args: ISelectFormConstructorArgs<O>, id: string|number) {
		super(args, id);
		this.get_shown_suggestions = args.get_shown_suggestions;
		this.shown_suggestions = shallowRef([]);
	}
	
	public async on_complete(event: AutoCompleteCompleteEvent): Promise<void> {
		this.shown_suggestions.value = await this.get_shown_suggestions(event.query);
	}
	
	public async on_tab_keydown(event: KeyboardEvent): Promise<void> {
		console.log(`on_tab_keydown(): shown_suggestions == `);
		console.log(this.shown_suggestions.value);
		if (!this.is_valid() && this.shown_suggestions.value.length > 0) {
			event.preventDefault();
			const first: O = this.shown_suggestions.value[0];
			return this.set_value_in_editing(first);
		}
	}
}
