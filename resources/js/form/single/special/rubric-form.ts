import { GroupSelectForm, IGroupSelectFormConstructorArgs, IGroupType } from "../generic/group-select-form";
import { ISingleValueForm2Parent } from "../generic/single-value-form2";

export type IRubric = Readonly<{
	id: string,
	name: string,
}>;
export type ICategory = Readonly<{
	id: string,
	name: string,
}>;
export type ICategoryWithRubrics = Readonly<{
	id: string,
	name: string,
	rubrics: IRubric[],
}>;

export interface IRubricFormConstructorArgs extends Pick<IGroupSelectFormConstructorArgs<IRubric, ICategory>, 'val'|'required'|'on_change'|'validate'> {
	selectable_categories_with_rubrics: ICategoryWithRubrics[];
}

export class RubricForm extends GroupSelectForm<IRubric, ICategory> {
	private readonly selectable_categories_with_rubrics: ICategoryWithRubrics[];
	
	public constructor(args: IRubricFormConstructorArgs, id: string|number, parent: ISingleValueForm2Parent<IRubric>) {
		const _args: IGroupSelectFormConstructorArgs<IRubric, ICategory> = args;
		_args.optionLabel = 'name';
		super(args, id, parent);
		this.selectable_categories_with_rubrics = args.selectable_categories_with_rubrics;
	}
	
	protected create_value_from_ui_value(ui_value: IRubric|string|undefined): IRubric|null|undefined {
		// console.log(`RubricForm::create_value_from_ui_value(): ui_value ==`);
		// console.log(ui_value);
		if (!ui_value) {
			return null;
		}
		if (typeof ui_value === 'string') {
			const suggestions = this.search_suggestions(ui_value, (rubric_name, query) => rubric_name === query);
			if (suggestions.length === 1 && suggestions[0].children.length === 1) {
				return suggestions[0].children[0];
			}
			return undefined; // multiple or no match
		}
		return ui_value;
	}
	
	protected create_ui_value_from_value(value: IRubric|null): string|undefined {
		return value ? value.name : undefined;
	}
	
	protected get_shown_suggestions(query: string): Promise<Readonly<IGroupType<IRubric, ICategory>>[]> {
		return new Promise((resolve) => resolve(this.search_suggestions(query, (rubric_name, query) => rubric_name.includes(query))));
	};
	
	private search_suggestions(query: string, filter_func: (rubric_name: string, query: string) => boolean): IGroupType<IRubric, ICategory>[] {
		query = query.trim().toLowerCase();
		const suggestions: IGroupType<IRubric, ICategory>[] = [];
		for (const category_with_rubrics of this.selectable_categories_with_rubrics) {
			const rubrics: IRubric[] = category_with_rubrics.rubrics.filter((rubric) => filter_func(rubric.name.toLowerCase(), query));
			if (rubrics.length > 0) {
				suggestions.push({
					parent: category_with_rubrics,
					children: rubrics,
				});
			}
		}
		return suggestions;
	}
}
