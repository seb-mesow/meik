import { GroupSelectForm, IGroupSelectFormConstructorArgs, IGroupType } from "./group-select-form";
import { ISingleValueForm2Parent } from "./single-value-form2";

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

export interface IRubricFormConstructorArgs extends Omit<IGroupSelectFormConstructorArgs<IRubric, ICategory>, 'get_shown_suggestions'|'get_option_label'> {
	selectable_categories_with_rubrics: ICategoryWithRubrics[];
}

export class RubricForm extends GroupSelectForm<IRubric, ICategory> {
	private readonly selectable_categories_with_rubrics: ICategoryWithRubrics[];
	
	public constructor(args: IRubricFormConstructorArgs, id: string|number, parent: ISingleValueForm2Parent<IRubric>) {
		super(args, id, parent);
		this.selectable_categories_with_rubrics = args.selectable_categories_with_rubrics;
	}
	
	protected create_value_from_ui_value(ui_value: string|undefined): IRubric|null|undefined {
		if (!ui_value) {
			return null;
		}
		const suggestions = this.search_suggestions(ui_value, (rubric_name, query) => rubric_name === query);
		if (suggestions.length === 1 && suggestions[0].children.length === 1) {
			return suggestions[0].children[0];
		}
		return undefined; // multiple or no match
	}
	
	protected create_ui_value_from_value(value: IRubric|null): string|undefined {
		console.log("RubricForm::create_ui_value_from_value(): value ==");
		console.log(value);
		return value ? value.name : undefined;
	}
	
	public get_option_label(option: IRubric): string {
		return option.name;
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
