import { IImageIDsOrder } from "@/types/ajax/image";
import { UIImageForm, IImageFormParent, ImageForm, IImageForm } from "../multiple/image-form";
import { shallowRef, ShallowRef } from "vue";

export interface IImagesForm {
	readonly children: ShallowRef<Readonly<UIImageForm[]>>;
	on_mounted(): void;
	on_tile_container_dragover(event: DragEvent): void;
}

export interface IImagesFormConstructorArgs {
	data: {
		exhibit_id: number;
		images: {
			id: string,
			description: string,
			is_public: boolean,
		}[];
	},
}

export class ImagesForm implements IImagesForm, IImageFormParent {
	public readonly exhibit_id: number;
	public children: ShallowRef<(IImageForm & UIImageForm)[]>;
	
	private next_ui_id: number = 0;
	
	private tile_container: HTMLElement = null as any;
	private currently_dragged_tile: IImageForm = null as any;
	
	public constructor(args: IImagesFormConstructorArgs) {
		this.exhibit_id = args.data.exhibit_id;
		this.children = shallowRef(args.data.images.map((_args): ImageForm => new ImageForm({
			data: {
				id: _args.id,
				description: _args.description,
				is_public: _args.is_public,
			},
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
		
		const children_in_db_in_new_order = new_ids_order.map((id: string): (IImageForm & UIImageForm) => {
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
	
	public on_mounted(): void {
		this.tile_container = document.getElementById('image-tile-container') as HTMLElement;
	}
	
	public set_currently_dragged_tile(image: IImageForm): void {
		this.currently_dragged_tile = image;
	}
	
	public on_tile_container_dragover(event: DragEvent): void {
		event.preventDefault();
		const elem_below = this.determinate_closest_draggable_element_below(event);
		if (elem_below) {
			this.tile_container.insertBefore(this.currently_dragged_tile.tile, elem_below);
		} else {
			this.tile_container.appendChild(this.currently_dragged_tile.tile);
		}
	}
	
	private determinate_closest_draggable_element_below(drag_over_event: MouseEvent): Element|null {
		const elems: NodeListOf<Element> = this.tile_container.querySelectorAll('.image-tile:not(.image-tile-dragging)');
		let cur_smallest_distance_below_mouse: number = Number.POSITIVE_INFINITY;
		let closest_element_below: Element|null = null;
		const mouse_y = drag_over_event.clientY;
		let log_str = '';
		elems.forEach((elem, index) => {
			const box = elem.getBoundingClientRect();
			const distance_below_mouse = (box.top + box.bottom)/2 - mouse_y;
			if (distance_below_mouse > 0 && distance_below_mouse < cur_smallest_distance_below_mouse) {
				cur_smallest_distance_below_mouse = distance_below_mouse;
				closest_element_below = elem;
			}
			log_str += ' ' + Math.round(distance_below_mouse);
		});
		console.log(log_str);
		return closest_element_below;
	}
}
