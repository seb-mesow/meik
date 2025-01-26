import { IImageIDsOrder } from "@/types/ajax/image";
import { IImageForm, IImageFormParent, ImageForm } from "./imageform";
import { IImageFormConstructorArgs as IRealImageFormConstructorArgs } from "./imageform";
import { reactive, Reactive, ShallowReactive, shallowReactive, shallowRef, ShallowRef, unref } from "vue";

export interface IImagesForm {
	readonly children: ShallowRef<IImageForm[]>;
}

export type IImageFormConstructorArgs = Pick<IRealImageFormConstructorArgs, 
	'id' | 'description' | 'is_public'
>;

export interface IImagesFormConstructorArgs {
	exhibit_id: number;
	images: IImageFormConstructorArgs[];
}

export class ImagesForm implements IImagesForm, IImageFormParent {
	public readonly exhibit_id: number;
	public children: ShallowRef<IImageForm[]>;
	
	private next_ui_id: number = 0;
	
	public constructor(args: IImagesFormConstructorArgs) {
		this.exhibit_id = args.exhibit_id;
		this.children = shallowRef(args.images.map((_args: IImageFormConstructorArgs): ImageForm => new ImageForm({
			id: _args.id,
			description: _args.description,
			is_public: _args.is_public,
			parent: this,
			ui_id: this.next_ui_id++,
		})));
	}
	
	public get_index_for_persisting(form: IImageForm): number {
		let index = 0;
		for (const child_form of this.children.value) {
			if (child_form.ui_id === form.ui_id) {
				return index;
			}
			if (child_form.exists_in_db()) {
				index++;
			}
		}
		throw new Error("Assertation failed: child form not found");
	}
	
	public delete_form(form: IImageForm): void {
		const prev_cnt = this.children.value.length;
		this.children.value = this.children.value.filter((_form: IImageForm): boolean => _form.ui_id !== form.ui_id);
		if (this.children.value.length !== prev_cnt - 1) {
			new Error("Assertation failed: count of forms remains equal, despite deleting form");
		}
		console.log(`form ${form.ui_id} deleted`)
	}
	
	public delete_form_and_update_order(args: { form: IImageForm, new_ids_order: IImageIDsOrder }): void {
		// no ui_id necessary
		const old_order: string = this.children.value.reduce((acc, cur, index, array) => {
			return acc + ", " + cur.ui_id;
		}, '');
		console.log(`delete: old_order == ${old_order}`);
		
		this.delete_form(args.form);
		this.update_order(args.new_ids_order);
		console.log(`form ${args.form.ui_id} removed`)
	}
	
	public update_order(new_ids_order: IImageIDsOrder): void {
		const old_order: string = this.children.value.reduce((acc, cur, index, array) => {
			return acc + ", " + cur.ui_id;
		}, '');
		console.log(`old_order == ${old_order}`);
		
		const children_in_db_in_new_order = new_ids_order.map((id: string): IImageForm => {
			for (const form of this.children.value) {
				if (form.id === id) {
					return form;
				}
			}
			throw new Error(`unknown Image ID ${id} for update_order()`);
		});
		let children_in_db_in_new_order_index = 0;
		
		for (const index in this.children.value) {
			if (this.children.value[index].exists_in_db()) {
				this.children.value[index] = children_in_db_in_new_order[children_in_db_in_new_order_index++];
			}
		}
		if (children_in_db_in_new_order_index !== children_in_db_in_new_order.length) {
			throw new Error("Assertation failed: children_in_db_in_new_order_index should equal children_in_db_in_new_order.length");
		}
		
		const new_order: string = this.children.value.reduce((acc, cur) => {
			return acc + ", " + cur.ui_id;
		}, '');
		console.log(`new_order == ${new_order}`);
	}
}
