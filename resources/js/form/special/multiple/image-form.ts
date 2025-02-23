import axios, { AxiosError, AxiosRequestConfig, AxiosResponse } from "axios";
import { ISingleValueForm2, ISingleValueForm2ConstructorArgs, ISingleValueForm2Parent, SingleValueForm2, UISingleValueForm2 } from "@/form/generic/single/single-value-form2";
import {
	IImageIDsOrder,
	ICreateImageRequestData,
	ICreateImage200ResponseData,
	ICreateImage422ResponseData,
	IDeleteImageRequestData,
	IDeleteImage200ResponseData,
	IDeleteImage422ResponseData,
	IReplaceImageRequestData,
	IReplaceImage422ResponseData,
	IReplaceImage200ResponseData,
	IUpdateImageMetaDataRequestData,
	IUpdateImageMetaData422ResponseData
} from "@/types/ajax/image";
import { route } from "ziggy-js";
import { ref, Ref } from "vue";
import { IMultipleValueForm, MultipleValueForm } from "@/form/generic/multiple/multiple-value-form";
import { StringForm } from "@/form/generic/single/string-form";

export interface UIImageForm {
	readonly id?: string;
	readonly ui_id: number;
	readonly description: Readonly<UISingleValueForm2<string>>;
	readonly is_public: Readonly<UISingleValueForm2<boolean>>;
	readonly file_url: Readonly<Ref<string>>;
	on_dragover(e: DragEvent): void;
	on_drop(e: DragEvent): void;
	click_save(): void;
	click_delete(): void;
	exists_in_db(): boolean;
	readonly is_save_button_loading: Readonly<Ref<boolean>>;
	readonly has_changes: Readonly<Ref<boolean>>; // muss reactive sein, damit als Property für Button verwendbar
	readonly is_delete_button_loading: Readonly<Ref<boolean>>;
}

export interface IImageForm {
}

// um eine zirkuläre Abhängigkeit zu vermeiden:
// locationform.ts (lower module) soll nichts von locationsform.ts (higher module) importieren
export interface IImageFormParent {
	readonly exhibit_id: number;
	get_index_for_persisting(form: IImageForm): number;
	delete_form(form: IImageForm): void;
	delete_form_and_update_order(args: { form: IImageForm, new_ids_order: IImageIDsOrder }): void;
	update_order(new_ids_order: IImageIDsOrder ): void;
}

export interface IImageFormConstructorArgs {
	data?: {
		id: string,
		description: string,
		is_public: boolean,
	},
	parent: IImageFormParent,
	ui_id: number,
}

export class ImageForm implements UIImageForm, IImageForm {
	public id?: string;
	public readonly ui_id: number;
	public readonly description: ISingleValueForm2<string, false> & UISingleValueForm2<string>;
	public readonly is_public: ISingleValueForm2<boolean, true> & UISingleValueForm2<boolean>;
	public readonly is_save_button_loading: Ref<boolean> = ref(false);
	public readonly has_changes: Ref<boolean>;
	public readonly is_delete_button_loading: Ref<boolean> = ref(false);
	public readonly file_url: Ref<string>;
	
	private readonly parent: IImageFormParent;
	
	private readonly fields: IMultipleValueForm & ISingleValueForm2Parent<any> = new MultipleValueForm();
	
	private file?: File;
	private new_file: boolean = false;
	
	public constructor(args: IImageFormConstructorArgs) {
		this.id = args.data?.id;
		
		this.description = new StringForm<true>({
			val: args.data?.description,
			required: true,
			on_change: () => {
				console.log(`recieved change from description`);
				this.has_changes.value = true;
			},
		}, 'description', this.fields);
		this.is_public = new SingleValueForm2<boolean, boolean, true>({
			val: args.data?.is_public,
			required: true,
			on_change: () => {
				console.log(`recieved change from is_public`);
				this.has_changes.value = true; 
			},
		}, 'is_public', this.fields);
		this.parent = args.parent;
		this.ui_id = args.ui_id;
		console.log(`construct: this.ui_id == ${this.ui_id}`);
		this.file_url = ref(this.determinate_external_file_url());
		this.has_changes = ref(false);
		
		// this.dragover_event_listener = (e: DragEvent) => { this.on_dragover(e); };
		// this.drop_event_listener = (e: DragEvent) => { this.on_drop(e); };
	}
	
	public exists_in_db(): boolean {
		return (typeof this.id === 'string') && (this.id.length > 0);
	}
	
	/**
	 * wird alle paar Hundert Millisekunden ausgelöst
	 * @param e 
	 * @returns 
	 */
	public on_dragover(e: DragEvent): void {
		e.preventDefault(); // mark as a drop target
		if (e.dataTransfer) {
			for (const i in e.dataTransfer.files) {
				// const file = e.dataTransfer.files[i];
				// console.log(`file dragged over: name == ${file.name}, type == ${file.type}`);
				// if (file.type?.startsWith('image/')) {
				// Während des Dragover-Events scheint die Datei noch nicht gelesen zu werden.
				// Daher ist erstmal jeder Dateityp hier reinkopierbar.
				e.dataTransfer.dropEffect = 'copy';
				return;
				// }
			}
			e.dataTransfer.dropEffect = 'none';
		}
	}
	
	public on_drop(e: DragEvent): void {
		if (e.dataTransfer) {
			if (e.dataTransfer.files.length) {
				// Auch für falsche Dateien soll verhindert werden,
				// dass der Browser versucht die Datei zu öffen oder öffnen zu lassen.
				e.preventDefault();
			}
			for (const i in e.dataTransfer.files) {
				const file = e.dataTransfer.files[i];
				console.log(`file dropped: type == ${file.type}`);
				if (file.type?.startsWith('image/')) {
					this.file = file;
					this.new_file = true;
					this.has_changes.value = true;
					URL.revokeObjectURL(this.file_url.value);
					this.file_url.value = URL.createObjectURL(this.file);
					return;
				}
			}
		}
	}
	
	private determinate_external_file_url(): string {
		if (this.id) {
			return route('ajax.image.get_image', { image_id: this.id });
		}
		return '';
	}
	
	public async click_save(): Promise<void> {
		// Werte der Zeile im Objekt rows setzen:
		this.description.commit();
		this.is_public.commit();
		this.has_changes.value = false;
		
		this.is_save_button_loading.value = true;
		try {
			if (this.exists_in_db()) {
				if (this.new_file) {
					await this.ajax_replace();
				} else {
					await this.ajax_update_metadata();
				}
			} else {
				await this.ajax_create();
			}
		} finally {
			this.is_save_button_loading.value = false;
		}
	}
	
	public async click_delete(): Promise<void> {
		console.log("Klick delete");
		if (this.exists_in_db()) {
			this.is_delete_button_loading.value = true;
			try {
				await this.ajax_delete();
			} finally {
				this.is_delete_button_loading.value = false;
			}
		} else {
			console.log("delete from");
			this.parent.delete_form(this);
		}
	}
	
	private async ajax_create(): Promise<void> {
		if (!this.file || !this.new_file) {
			throw new Error("no file to upload");
		}
		const request_data: ICreateImageRequestData = {
			index: this.parent.get_index_for_persisting(this),
			description: this.description.get_value() ?? '',
			is_public: this.is_public.get_value(),
			image: this.file,
		}
		const request_config: AxiosRequestConfig<ICreateImageRequestData> = {
			method: "post",
			url: route('ajax.exhibit.image.create', { exhibit_id: this.parent.exhibit_id }),
			headers: {
				'Content-Type': 'multipart/form-data'
			},
			data: request_data,
		};
		console.log('ajax_create');
		return axios.request(request_config).then(
			(response: AxiosResponse<ICreateImage200ResponseData>) => {
				this.id = response.data.id;
				this.parent.update_order(response.data.ids_order);
				console.log('ajax_create success');
			},
			(err) => {
				const response: AxiosResponse<ICreateImage422ResponseData> = err.response;
				console.log('ajax_create fail');
			}
		);
	}
	
	private async ajax_replace(): Promise<void> {
		if (!this.id) {
			throw new Error("undefined ID");
		}
		if (!this.new_file) {
			throw new Error("tried to replace image, but file is not new");
		}
		if (!this.file) {
			throw new Error("no file to upload");
		}
		const request_config: AxiosRequestConfig<IReplaceImageRequestData> = {
			method: "post",
			url: route('ajax.exhibit.image.replace', { exhibit_id: this.parent.exhibit_id, image_id: this.id }),
			headers: {
				'Content-Type': 'multipart/form-data',
			},
			data: {
				description: this.description.get_value() ?? '',
				is_public: this.is_public.get_value(),
				image: this.file,
			},
		};
		console.log('ajax_replace() begin');
		return axios.request(request_config).then(
			(response: AxiosResponse<IReplaceImage200ResponseData>) => {
				this.id = response.data;
				console.log('ajax_replace() success');
			},
			(err: AxiosError<IReplaceImage422ResponseData>) => {
				const response: AxiosResponse<IReplaceImage422ResponseData>|undefined = err.response;
				// if (response) {
				// 	this.errs = response.data.errs;
				// 	this.description.errs = response.data.description;
				// 	this.is_public.errs = response.data.is_public;
				// }
				console.log('ajax_replace() fail');
			}
		);
	}
	
	private async ajax_delete(): Promise<void> {
		if (!this.id) {
			throw new Error("undefined ID");
		}
		const request_config: AxiosRequestConfig<IDeleteImageRequestData> = {
			method: "delete",
			url: route('ajax.exhibit.image.delete', { exhibit_id: this.parent.exhibit_id, image_id: this.id })
		};
		console.log('ajax_delete() begin');
		return axios.request(request_config).then(
			(response: AxiosResponse<IDeleteImage200ResponseData>) => {
				this.parent.delete_form_and_update_order({ form: this, new_ids_order: response.data });
				console.log('ajax_delete() success');
			},
			(response: AxiosResponse<IDeleteImage422ResponseData>) => {
				// this.errs = response.data;
				console.log('ajax_delete() fail');
			}
		);
	}
	
	private async ajax_update_metadata(): Promise<void> {
		if (!this.id) {
			throw new Error("undefined ID");
		}
		const request_config: AxiosRequestConfig<IUpdateImageMetaDataRequestData> = {
			method: "patch",
			url: route('ajax.image.update_meta_data', { image_id: this.id }),
			data: {
				description: this.description.get_value() ?? '',
				is_public: this.is_public.get_value(),
			}
		};
		console.log('ajax_update_metadata()');
		return axios.request(request_config).then(
			(response: AxiosResponse) => {
				console.log('ajax_update_meta_data() success');
			},
			(response: AxiosResponse<IUpdateImageMetaData422ResponseData>) => {
				// this.errs = response.data.errs;
				// this.description.errs = response.data.description;
				// this.is_public.errs = response.data.is_public;
				console.log('ajax_update_meta_data() fail');
			}
		);
	}
}
