import { shallowRef, ShallowRef } from "vue";
import { ISingleValueForm2, ISingleValueForm2ConstructorArgs, SingleValueForm2 } from "./singlevalueform2";
import { AutoCompleteCompleteEvent } from "primevue/autocomplete";

export interface IGroupSelectForm<
	ChildType extends any = string,
	ParentType extends any = ChildType
> extends ISingleValueForm2<
	ChildType
> {
	readonly shown_suggestions: Readonly<ShallowRef<IGroupType<ChildType, ParentType>[]>>;
	on_complete(event: AutoCompleteCompleteEvent): Promise<void>;
}

export interface IGroupType<
	ChildType = string,
	ParentType = ChildType,
> {
	children: ChildType[];
	parent: ParentType;
}

export interface IGroupSelectFormConstructorArgs<
	ChildType = string,
	ParentType = ChildType
>
extends ISingleValueForm2ConstructorArgs<
	ChildType
> {
	get_shown_suggestions: (query: string) => Promise<Readonly<IGroupType<ChildType, ParentType>>[]>;
}

export class GroupSelectForm<
	ChildType = string,
	ParentType = ChildType,
> extends SingleValueForm2<
	ChildType
> implements IGroupSelectForm<
	ChildType,
	ParentType
> {
	public readonly shown_suggestions: ShallowRef<(Readonly<IGroupType<ChildType, ParentType>>)[]>;
	private readonly get_shown_suggestions: (query: string) => Promise<Readonly<IGroupType<ChildType, ParentType>>[]>;
	
	public constructor(args: IGroupSelectFormConstructorArgs<ChildType, ParentType>, id: string|number) {
		super(args, id);
		this.get_shown_suggestions = args.get_shown_suggestions;
		this.shown_suggestions = shallowRef([]);
	}
	
	public async on_complete(event: AutoCompleteCompleteEvent): Promise<void> {
		const groups = await this.get_shown_suggestions(event.query);
		this.shown_suggestions.value = groups.map((group: IGroupType<ChildType, ParentType>): IGroupType<ChildType, ParentType> => 
			group
		);
	}
}
