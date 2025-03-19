import { ref, Ref } from "vue";
import { ISingleValueForm2, ISingleValueForm2ConstructorArgs, ISingleValueForm2Parent, SingleValueForm2, UISingleValueForm2 } from "./single-value-form2";
import { FULL_MATCH, IOptionFinder, StringOptionFinder } from "@/util/option-finder";

export interface UISimpleSelectForm<O> extends UISingleValueForm2<O|undefined> {
	readonly options: Readonly<Ref<Readonly<Readonly<O>[]>>>;
	// on_complete(event: AutoCompleteCompleteEvent): Promise<void>;
	// on_before_show(): Promise<void>;
	// on_hide(): Promise<void>;
	// on_tab_keydown(event: KeyboardEvent): Promise<void>;
	// optionLabel: string|undefined;
}

export interface ISelectOption<I extends string|number> {
	id: I,
	name: string,
}

/**
 * @param O internal value of options and primary return value of `get_value()`
 * @param R whether `get_value()` only returns `O` or `O|null`; default `false`
 */
export interface ISimpleSelectForm<I extends string|number, O extends ISelectOption<I>, R extends boolean = false> extends ISingleValueForm2<O, R> {};

export interface ISimpleSelectFormConstructorArgs<I extends string|number, O extends ISelectOption<I>, R extends boolean = false> extends Omit<ISingleValueForm2ConstructorArgs<O, R>, 'val'> {
	val_id: I|undefined,
	selectable_options: O[],
	// optionLabel?: string,
}

/**
 * @param O internal value of options and primary return value of `get_value()`
 * @param R whether `get_value()` only returns `O` or `O|null`; default `false`
 */
export abstract class SimpleSelectForm<I extends string|number, O extends ISelectOption<I>, R extends boolean = false> extends SingleValueForm2<O, O|undefined, R> implements ISimpleSelectForm<I, O, R>, UISimpleSelectForm<O> {
	public readonly options: Ref<O[]>
	
	public constructor(args: ISimpleSelectFormConstructorArgs<I, O, R>, id: string|number, parent: ISingleValueForm2Parent<O>, option_finder: IOptionFinder<I, O>) {
		let initial_value: O|undefined = undefined;
		if (args.val_id !== undefined) {
			if (args.selectable_options?.length < 0) {
				throw new Error(`Assertation failed: SimpleSelectForm::constructor(): ${id.toString()}: val_id is provided, but there are no selectable_options.`);
			} else {
				console.log(`SimpleSelectForm::constructor(): option_finder.find_all_by_many`);
				// until the super() call we are only allowed to use static methods :-/
				initial_value = option_finder.find_first(args.val_id, 'id', FULL_MATCH);
			}
		}
		
		const _args : ISingleValueForm2ConstructorArgs<O, R> = {
			...args,
			...{ val: initial_value },
		};
		super(_args, id, parent);
		
		// @ts-expect-error
		this.options = ref(args.selectable_options);
	}
}

export class StringSimpleSelectForm<O extends ISelectOption<string>, R extends boolean = false> extends SimpleSelectForm<string, O, R> {
	public constructor(args: ISimpleSelectFormConstructorArgs<string, O, R>, id: string|number, parent: ISingleValueForm2Parent<O>) {
		super(args, id, parent, new StringOptionFinder(args.selectable_options));
	}
}
