import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import * as RubricAJAX from "@/types/ajax/rubric";
import { ISingleValueForm2, ISingleValueForm2Parent, UISingleValueForm2 } from "../../generic/single/single-value-form2";
import { route } from "ziggy-js";
import { ISelectForm, SelectForm, UISelectForm } from "../../generic/single/select-form";
import { IMultipleValueForm, MultipleValueForm } from "../../generic/multiple/multiple-value-form";
import { StringForm } from "../../generic/single/string-form";
import { IRubricTileProps } from "@/types/page_props/rubric_tiles";
import { DynamicDialogCloseOptions } from "primevue/dynamicdialogoptions";
import { ref, Ref } from "vue";

export type ICategory = Readonly<{
	id: string,
	name: string,
}>;

export interface IRubricForm {
	readonly category: Readonly<UISelectForm<ICategory>>;
	readonly name: Readonly<UISingleValueForm2<string>>;
	click_save(): void;
	readonly is_save_button_enabled: Readonly<Ref<boolean>>;
	readonly is_save_button_loading: Readonly<Ref<boolean>>;
};

export interface IDialogRef {
	data: Omit<IRubricFormConstructorArgs, 'dialog_ref'|'selectable_categories'>,
	close(tile: IRubricTileProps): void;
}

export interface ICloseOptions extends DynamicDialogCloseOptions {
	data: IRubricTileProps;
}

export interface IRubricFormConstructorArgs {
	data?: {
		id: string,
		category_id: string,
		name: string,
	},
	preset?: {
		category_id: string,
	}
	selectable_categories: ICategory[],
	dialog_ref: IDialogRef,
	on_rubric_created?: (tile: IRubricTileProps) => void,
	on_rubric_updated?: (tile: IRubricTileProps) => void,
};

export class RubricForm implements IRubricForm {
	private id?: string;
	
	public category: ISelectForm<ICategory, true> & UISelectForm<ICategory>;
	public name: ISingleValueForm2<string, true> & UISingleValueForm2<string>;
	public readonly is_save_button_enabled: Ref<boolean, boolean>;
	public readonly is_save_button_loading: Ref<boolean, boolean>;
	
	private dialog_ref: IDialogRef;
	private _on_rubric_created: (tile: IRubricTileProps) => void; 
	private _on_rubric_updated: (tile: IRubricTileProps) => void; 
	
	private readonly fields: IMultipleValueForm & ISingleValueForm2Parent<any>;
	
	public constructor(args: IRubricFormConstructorArgs) {
		this.id = args.data?.id;
		
		this.is_save_button_enabled = ref(false);
		this.is_save_button_loading = ref(false);
		
		this.fields = new MultipleValueForm({
			on_child_change: async (form) => {
				this.is_save_button_enabled.value = await form.is_valid();
			},
		});
		
		const category_id = args.data?.category_id ?? args.preset?.category_id;
		
		this.category = new SelectForm<ICategory, true>({
			val_id: category_id,
			required: true,
			search_in: 'name',
			optionLabel: 'name',
			selectable_options: args.selectable_categories,
			validate: async (value_in_editing) => {
				if (value_in_editing === undefined) {
					return ['Bitte eine auswählbare Kategorie angeben'];
				}
				return [];
			},
		}, 'category_id', this.fields);
		
		this.name = new StringForm<true>({
			val: args.data?.name,
			required: true,
		}, 'name', this.fields);
		
		this.dialog_ref = args.dialog_ref;
		this._on_rubric_created = args.on_rubric_created ?? (() => {});
		this._on_rubric_updated = args.on_rubric_updated ?? (() => {});
	}

	private exists_in_db(): boolean {
		return this.id !== undefined;
	}

	public async click_save(): Promise<void> {
		this.fields.commit();
		
		this.is_save_button_loading.value = true;
		try {
			if (this.exists_in_db()) {
				return this.ajax_update();
			} else {
				return this.ajax_create();
			}
		} catch {
			this.is_save_button_loading.value = false;
		}
	}

	private async ajax_create(): Promise<void> {
		console.log(`ajax_update(): this.category_id == ${this.category.get_value().id}`);
		
		const request_config: AxiosRequestConfig<RubricAJAX.Create.IRequestData> = {
			method: "post",
			url: route('ajax.rubric.create'),
			data: {
				name: this.name.get_value(),
				category_id: this.category.get_value().id,
			},
		};
		
		return axios.request(request_config).then(
			(response: AxiosResponse<RubricAJAX.Create.I200ResponseData>) => {
				const new_rubric_id : string = response.data;
				this._on_rubric_created({
					id: new_rubric_id,
					name: this.name.get_value(),
					category_id: this.category.get_value().id,
				});
				this.dialog_ref.close({
					id: new_rubric_id,
					name: this.name.get_value(),
					category_id: this.category.get_value().id,
				});
			}
		);
	}

	private async ajax_update(): Promise<void> {
		if (this.id === undefined) {
			throw new Error("undefined id");
		}
		const request_config: AxiosRequestConfig<RubricAJAX.Update.IRequestData> = {
			method: "put",
			url: route('ajax.rubric.update', { rubric_id: this.id }),
			data: {
				name: this.name.get_value(),
				category_id: this.category.get_value().id,
			},
		};
		return axios.request(request_config).then(() => {
			if (this.id === undefined) {
				throw new Error("undefined id");
			}
			this._on_rubric_updated({
				id: this.id,
				name: this.name.get_value(),
				category_id: this.category.get_value().id,
			});
			this.dialog_ref.close({
				id: this.id,
				name: this.name.get_value(),
				category_id: this.category.get_value().id,
			});
		});
	}
}
