import { IImageIDsOrder } from "@/types/ajax/image";
import { IImageForm, IImageFormParent, ImageForm } from "./imageform";
import { IImageFormConstructorArgs as IRealImageFormConstructorArgs } from "./imageform";

export interface IImagesForm {
	readonly children: Readonly<IImageForm[]>;
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
	public children: IImageForm[];
	
	private next_ui_id: number = 0;
	
	public constructor(args: IImagesFormConstructorArgs) {
		this.exhibit_id = args.exhibit_id;
		this.children = args.images.map((_args: IImageFormConstructorArgs): ImageForm => new ImageForm({
			id: _args.id,
			description: _args.description,
			is_public: _args.is_public,
			parent: this,
			ui_id: this.next_ui_id++,
		}));
	}
	
	public get_index_for_persisting(form: IImageForm): number {
		let index = 0;
		for (const child_form of this.children) {
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
		this.children = this.children.filter((_form: IImageForm): boolean => _form.ui_id !== form.ui_id);
	}
	
	public delete_form_and_update_order(args: { form: IImageForm, new_ids_order: IImageIDsOrder }): void {
		// no ui_id necessary
		this.delete_form(args.form);
		this.update_order(args.new_ids_order);
	}
	
	public update_order(new_ids_order: IImageIDsOrder): void {
		const temp = this.children;
		const new_children: IImageForm[] = [];
		let t = 0;
		// copy unsaved forms at the front
		while (t < temp.length) {
			const form = temp[t];
			if (form.id) {
				break;
			}
			new_children.push(form);
			t++;
		}
		// remove unsaved forms at the front 
		temp.splice(0, t);
		// update with new indices from argument
		new_ids_order.forEach((id) => {
			// loop until find index from argument and append that form
			let t_begin = 0;
			while (t_begin < temp.length) {
				if (temp[t_begin].id === id) {
					break;
				}
				t_begin++;
			}
			if (t_begin >= temp.length) {
				throw new Error(`unknown Image ID ${id} for update_ids_order()`);
			}
			new_children.push(temp[t_begin]);
			// after the found form copy all subsequent unsaved forms until the next saved form
			let t_end = t_begin + 1;
			while (t_end < temp.length) {
				const form = temp[t_end];
				if (form.id) {
					break;
				}
				new_children.push(form);
				t_end++;
			}
			temp.splice(t_begin, t_end - t_begin);
		});
		
		if (temp.length > 0) {
			console.log("temp (end) ==");
			console.log(temp);
			throw new Error("Assertation failed: `temp` should be empty, but still contains elements.");
		}
		// trigger reactivity
		this.children = new_children;
	}
}
