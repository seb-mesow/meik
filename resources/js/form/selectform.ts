import { shallowRef, ShallowRef } from "vue";
import { ISingleValueForm2ConstructorArgs, SingleValueForm2, UISingleValueForm2 } from "./singlevalueform2";
import { AutoCompleteCompleteEvent } from "primevue/autocomplete";

export interface UISelectForm<U> extends UISingleValueForm2<U> {
	readonly shown_suggestions: Readonly<ShallowRef<Readonly<U>[]>>;
	on_complete(event: AutoCompleteCompleteEvent): Promise<void>;
}

export interface ISelectFormConstructorArgs<T = string> extends ISingleValueForm2ConstructorArgs<T> {
	get_shown_suggestions: (query: string) => Promise<Readonly<T>[]>;
}

export class SelectForm<T = string> extends SingleValueForm2<T, T> implements UISelectForm<T> {
	public readonly shown_suggestions: ShallowRef<Readonly<T>[]>;
	private readonly get_shown_suggestions: (query: string) => Promise<Readonly<T>[]>;
	
	public constructor(args: ISelectFormConstructorArgs<T>, id: string|number) {
		super(args, id);
		this.get_shown_suggestions = args.get_shown_suggestions;
		this.shown_suggestions = shallowRef([]);
	}
	
	public async on_complete(event: AutoCompleteCompleteEvent): Promise<void> {
		this.shown_suggestions.value = await this.get_shown_suggestions(event.query);
	}
}
