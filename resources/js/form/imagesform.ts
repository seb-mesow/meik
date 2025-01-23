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
	public readonly children: IImageForm[];
	
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
		for (const form of this.children) {
			if (form === form) {
				return index;
			}
			if (form.id) {
				index++;
			}
		}
		throw new Error("Assertation failed: child form not found");
	}
	
	public delete_form(args: { form: IImageForm; ids_order: IImageIDsOrder; }): void {
		throw new Error("Method not implemented.");
	}
	
	public update_ids_order(ids_order: IImageIDsOrder): void {
		throw new Error("Method not implemented.");
	}
}
