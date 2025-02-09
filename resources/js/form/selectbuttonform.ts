import { shallowRef, ShallowRef } from "vue";
import { ISingleValueForm2, ISingleValueForm2ConstructorArgs, SingleValueForm2 } from "./singlevalueform2";
import { AutoCompleteCompleteEvent } from "primevue/autocomplete";

export interface ISelectButtonForm<
	ValType extends any = string
> extends ISingleValueForm2<
	ValType
> {
	readonly shown_suggestions: Readonly<ShallowRef<Readonly<ValType>[]>>;
	on_complete(event: AutoCompleteCompleteEvent): Promise<void>;
}

export interface ISelectButtonFormConstructorArgs<
	ValType extends any = string
>
extends ISingleValueForm2ConstructorArgs<
	ValType
> {
	get_shown_suggestions: (query: string) => Promise<Readonly<ValType>[]>;
}

export class ISelectButtonForm<
	ValType extends any = string,
	HtmlIdType extends string|number = string
> extends SingleValueForm2<
	ValType
> implements ISelectForm<
	ValType
> {
	public readonly shown_suggestions: ShallowRef<Readonly<ValType>[]>;
	private readonly get_shown_suggestions: (query: string) => Promise<Readonly<ValType>[]>;
	
	public constructor(args: ISelectFormConstructorArgs<ValType>, id: HtmlIdType) {
		super(args, id);
		this.get_shown_suggestions = args.get_shown_suggestions;
		this.shown_suggestions = shallowRef([]);
	}
	
	public async on_complete(event: AutoCompleteCompleteEvent): Promise<void> {
		this.shown_suggestions.value = await this.get_shown_suggestions(event.query);
	}
}
