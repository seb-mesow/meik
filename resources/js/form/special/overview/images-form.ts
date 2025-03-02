import { IImageIDsOrder } from "@/types/ajax/image";
import { UIImageForm, IImageFormParent, ImageForm, IImageForm } from "../multiple/image-form";
import { shallowRef, ShallowRef } from "vue";
import { update_order_from_partial_order } from "@/util/update-order";
import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import * as ImageAJAX from '@/types/ajax/image';
import { route } from "ziggy-js";

export interface UIImagesForm {
	readonly children_in_editing: ShallowRef<Readonly<UIImageForm[]>>;
	on_mounted(): void;
	on_tile_container_dragover(event: DragEvent): void;
	click_add(): void;
	click_image_order_save(): void;
	click_image_order_rollback(): void;
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

export class ImagesForm implements UIImagesForm, IImageFormParent {
	public readonly exhibit_id: number;
	public children_in_editing: ShallowRef<(IImageForm & UIImageForm)[]>;
	
	private next_ui_id: number = 0;
	
	private tile_container: HTMLElement = null as any;
	private currently_dragged_tile: IImageForm = null as any;
	private currently_not_dragged_tiles : IImageForm[] = [];
	private current_tile_order: IImageForm[] = [];
	private children: (IImageForm & UIImageForm)[];
	
	public constructor(args: IImagesFormConstructorArgs) {
		this.exhibit_id = args.data.exhibit_id;
		this.children = args.data.images.map((_args): ImageForm => new ImageForm({
			data: {
				id: _args.id,
				description: _args.description,
				is_public: _args.is_public,
			},
			parent: this,
			ui_id: this.next_ui_id++,
		}));
		this.children_in_editing = shallowRef([...this.children]); // copy of array useful
	}

	public click_add(): void {
		this.children_in_editing.value = [ ...this.children_in_editing.value, new ImageForm({
			parent: this,
			ui_id: this.next_ui_id++,
		})];
	}
	
	public get_index_for_persisting(form: IImageForm): number {
		let index = 0;
		for (const child_form of this.children_in_editing.value) {
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
		const prev_cnt = this.children_in_editing.value.length;
		this.children_in_editing.value = this.children_in_editing.value.filter((_form: IImageForm): boolean => _form.ui_id !== form.ui_id);
		if (this.children_in_editing.value.length !== prev_cnt - 1) {
			new Error("Assertation failed: count of forms remains equal, despite deleting form");
		}
		this.children = this.children.filter((_form: IImageForm): boolean => _form.ui_id !== form.ui_id);
		if (this.children.length !== prev_cnt - 1) {
			new Error("Assertation failed: count of forms remains equal, despite deleting form");
		}
		console.log(`form ${form.ui_id} deleted`)
	}
	
	public delete_form_and_update_order(args: { form: IImageForm, new_ids_order: IImageIDsOrder }): void {
		// no ui_id necessary
		const old_order: string = this.children_in_editing.value.reduce((acc, cur, index, array) => {
			return acc + ", " + cur.ui_id;
		}, '');
		console.log(`delete: old_order == ${old_order}`);
		
		this.delete_form(args.form);
		this.update_order_by_partial_order(args.new_ids_order);
		console.log(`form ${args.form.ui_id} removed`)
	}
	
	public update_order_by_partial_order(new_ids_order: IImageIDsOrder): void {
		console.log(`new_ids_order == ${new_ids_order.join(', ')}`);
		this.log_order('old_order', this.children_in_editing.value);
		this.children_in_editing.value = update_order_from_partial_order(this.children_in_editing.value, new_ids_order);
		this.log_order('new__order', this.children_in_editing.value);
	}
	
	private log_order(var_name: string, image_order: { ui_id: number, id?: string}[]): void {
		const new_order: string = image_order.reduce((acc, cur) => {
			return acc + '\n' + cur.ui_id + ': ' + (cur.id === undefined ? 'undefined' : cur.id);
		}, '');
		console.log(`${var_name} ==${new_order}`);
	}
	
	public on_mounted(): void {
		this.tile_container = document.getElementById('image-tile-container') as HTMLElement;
	}
	
	public set_currently_dragged_tile(image: IImageForm): void {
		this.currently_dragged_tile = image;
		this.currently_not_dragged_tiles = this.children_in_editing.value.filter((child) => child !== image);
		this.current_tile_order = [ ...this.children_in_editing.value]; // so eine Kopie sein, daher spread
	}
	
	public on_tile_container_dragover(event: DragEvent): void {
		event.preventDefault();
		const old_pos = this.current_tile_order.findIndex((tile) => tile === this.currently_dragged_tile);
		if (old_pos >= 0) {
			this.current_tile_order.splice(old_pos, 1);
		}
		const tile_below = this.determinate_closest_draggable_tile_below(event);
		if (tile_below) {
			this.tile_container.insertBefore(this.currently_dragged_tile.tile, tile_below.tile);
			const pos_after = this.current_tile_order.findIndex((tile) => tile === tile_below);
			if (pos_after >= 0) {
				this.current_tile_order.splice(pos_after, 0, this.currently_dragged_tile);
			}
		} else {
			this.tile_container.appendChild(this.currently_dragged_tile.tile);
			this.current_tile_order.push(this.currently_dragged_tile);
		}
	}
	
	public on_tile_drag_end(tile: IImageForm, event: DragEvent): void {
		let str = 'drop: aktuelle Reihenfolge:';
		this.current_tile_order.forEach(tile => {
			str += ' ' + tile.ui_id;
		});
		this.children_in_editing.value = this.current_tile_order as (IImageForm & UIImageForm)[];
		console.log(str);
	}
	
	private determinate_closest_draggable_tile_below(drag_over_event: MouseEvent): IImageForm|null {
		let cur_smallest_distance_below_mouse: number = Number.POSITIVE_INFINITY;
		let closest_tile_below: IImageForm|null = null;
		const mouse_y = drag_over_event.clientY;
		let log_str = '';
		this.currently_not_dragged_tiles.forEach((tile, index) => {
			const box = tile.tile.getBoundingClientRect();
			const distance_below_mouse = (box.top + box.bottom)/2 - mouse_y;
			if (distance_below_mouse > 0 && distance_below_mouse < cur_smallest_distance_below_mouse) {
				cur_smallest_distance_below_mouse = distance_below_mouse;
				closest_tile_below = tile;
			}
			log_str += ' ' + Math.round(distance_below_mouse);
		});
		console.log(log_str);
		return closest_tile_below;
	}
	
	public click_image_order_save(): void {
		this.ajax_move();
	}
	
	public click_image_order_rollback(): void {
		throw new Error("Method not implemented.");
	}
	
	private async ajax_move(): Promise<void> {
		const cur_ids_order: string[] = [];
		for (const image of this.children_in_editing.value) {
			if (image.id !== undefined) {
				cur_ids_order.push(image.id);
			}
		}
		
		const request_config: AxiosRequestConfig<ImageAJAX.Move.IRequestData> = {
			method: 'patch',
			url: route('ajax.exhibit.image.move', this.exhibit_id),
			data: cur_ids_order,
		};
		
		return axios.request(request_config).then((response: AxiosResponse) => {
			this.children = this.children_in_editing.value;
		});
	}
}
