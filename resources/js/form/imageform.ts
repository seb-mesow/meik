import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import { ISingleValueForm2, ISingleValueForm2ConstructorArgs, SingleValueForm2 } from "./singlevalueform2";
import { ICreateImage200ResponseData, ICreateImage422ResponseData, ICreateImageRequestData, IDeleteImage200ResponseData, IDeleteImage422ResponseData, IDeleteImageRequestData, IImageIDsOrder, ISetImageFile200ResponseData, ISetImageFileRequestData, IUpdateImageMetaData200ResponseData, IUpdateImageMetaData422ResponseData, IUpdateImageMetaDataRequestData } from "@/types/ajax/image";
import { route } from "ziggy-js";
import { ref, Ref } from "vue";

export interface IImageForm {
	readonly id?: string;
	readonly ui_id: number;
	readonly description: ISingleValueForm2<string>;
	readonly is_public: ISingleValueForm2<boolean>;
	readonly file_url: string;
	on_mounted(): void;
	click_save(): void;
	click_delete(): void;
	readonly is_save_button_loading: Readonly<Ref<boolean>>;
	readonly has_changes: Readonly<Ref<boolean>>; // muss reactive sein, damit als Property für Button verwendbar
	readonly is_delete_button_loading: Readonly<Ref<boolean>>;
}

// um eine zirkuläre Abhängigkeit zu vermeiden:
// locationform.ts (lower module) soll nichts von locationsform.ts (higher module) importieren
export interface IImageFormParent {
	readonly exhibit_id: number;
	get_index_for_persisting(form: IImageForm): number;
	delete_form(args: { form: IImageForm, ids_order: IImageIDsOrder }): void;
	update_ids_order(ids_order: IImageIDsOrder ): void;
}

export interface IImageFormConstructorArgs {
	id?: string,
	description?: ISingleValueForm2ConstructorArgs<string>,
	is_public?: ISingleValueForm2ConstructorArgs<boolean>,
	parent: IImageFormParent,
	ui_id: number,
}

export class ImageForm implements IImageForm {
	public id?: string;
	public readonly ui_id: number;
	public description: SingleValueForm2<string>;
	public is_public: SingleValueForm2<boolean>;
	public is_save_button_loading: Ref<boolean> = ref(false);
	public has_changes: Ref<boolean>;
	public is_delete_button_loading: Ref<boolean> = ref(false);
	public file_url: string;
	
	private errs: string[] = [];
	
	private readonly parent: IImageFormParent;
	private _image_zone?: HTMLElement;
	private _drop_zone?: HTMLElement;
	private file?: File;
	private new_file: boolean = false;
	
	public constructor(args: IImageFormConstructorArgs) {
		this.id = args.id;
		this.description = new SingleValueForm2({
			val: args.description?.val ?? '',
			errs: args.description?.errs,
			on_change: () => {
				console.log(`recieved change from description`);
				this.has_changes.value = true;
			},
		}, 'description');
		this.is_public = new SingleValueForm2({
			val: args.is_public?.val ?? false,
			errs: args.is_public?.errs,
			on_change: () => {
				console.log(`recieved change from is_public`);
				this.has_changes.value = true; 
			},
		}, 'is_public');
		this.parent = args.parent;
		this.ui_id = args.ui_id;
		this.file_url = this.determinate_external_file_url();
		console.log(`construct`);
		this.has_changes = ref(false);
		console.log(`construct: this.has_changes ==`);
		console.log(this.has_changes);
	}
	
	private has_id(): boolean {
		return typeof this.id === 'string';
	}
	
	private is_new(): boolean {
		return typeof this.id !== 'string';
	}
	
	private image_zone(): HTMLElement {
		if (this._image_zone) {
			return this._image_zone;
		}
		throw new Error('Image zone still undefinied');
	}
	private drop_zone(): HTMLElement {
		if (this._drop_zone) {
			return this._drop_zone;
		}
		throw new Error('Drop zone still undefinied');
	}
	
	public on_mounted(): void {
		const image_zone = document.getElementById(`image-zone-${this.ui_id}`);
		const drop_zone = document.getElementById(`drop-zone-${this.ui_id}`);
		if (!image_zone) {
			throw new Error(`Image zone ${this.ui_id} not found`);
		}
		if (!drop_zone) {
			throw new Error(`Drop zone ${this.ui_id} not found`);
		}
		this._image_zone = image_zone;
		this._drop_zone = drop_zone;
		this.update_zone_visibility();
		this._image_zone.addEventListener('dragover', (e: DragEvent) => { this.on_dragover(e); });
		this._image_zone.addEventListener('drop', (e: DragEvent) => { this.on_drop(e); });
		this._drop_zone.addEventListener('dragover', (e: DragEvent) => { this.on_dragover(e); });
		this._drop_zone.addEventListener('drop', (e: DragEvent) => { this.on_drop(e); });
	}
	
	/**
	 * wird alle paar Hundert Millisekunden ausgelöst
	 * @param e 
	 * @returns 
	 */
	private on_dragover(e: DragEvent): void {
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
	
	private on_drop(e: DragEvent): void {
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
					URL.revokeObjectURL(this.file_url);
					this.file_url = URL.createObjectURL(this.file);
					this.update_zone_visibility();
					return;
				}
			}
		}
	}
	
	private update_zone_visibility(): void {
		if (this.file_url) {
			this.image_zone().style.display = 'block';
			this.image_zone().hidden = false;
			this.drop_zone().style.display = 'none';
			this.drop_zone().hidden = true;
		} else {
			this.image_zone().style.display = 'none';
			this.drop_zone().hidden = true;
			this.drop_zone().style.display = 'flex';
			this.drop_zone().hidden = false;
		}
	}
	
	private determinate_external_file_url(): string {
		if (this.id) {
			return route('ajax.image.get_file', { image_id: this.id });
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
			if (this.is_new()) {
				await this.ajax_create_metadata();
				await this.ajax_set_file_if_new_file();
			} else {
				await Promise.all([
					this.ajax_update_metadata(),
					this.ajax_set_file_if_new_file(),
				]);
			}
		} finally {
			this.is_save_button_loading.value = false;
		}
	}
	
	public async click_delete(): Promise<void> {
		
	}
	
	private async ajax_create_metadata(): Promise<void> {
		const request_config: AxiosRequestConfig<ICreateImageRequestData> = {
			method: "post",
			url: route('ajax.exhibit.image.create', [this.parent.exhibit_id]),
			data: {
				index: this.parent.get_index_for_persisting(this),
				description: this.description.val,
				is_public: this.is_public.val,
			}
		};
		console.log('ajax_create_metadata');
		return axios.request(request_config).then(
			(response: AxiosResponse<ICreateImage200ResponseData>) => {
				this.id = response.data.id;
				this.parent.update_ids_order(response.data.ids_order);
			},
			(err) => {
				console.log(err);
				const response: AxiosResponse<ICreateImage422ResponseData> = err.response;
				this.errs = response.data.errs;
				this.description.errs = response.data.description;
				this.is_public.errs = response.data.is_public;
			}
		);
	}
	
	private async ajax_update_metadata(): Promise<void> {
		if (!this.id) {
			throw new Error("undefined id");
		}
		const request_config: AxiosRequestConfig<IUpdateImageMetaDataRequestData> = {
			method: "put",
			url: route('ajax.image.update_meta_data', { image_id: this.id }),
			data: {
				description: this.description.val,
				is_public: this.is_public.val,
			}
		};
		console.log('ajax_update_metadata');
		return axios.request(request_config).then(
			() => {},
			(response: AxiosResponse<IUpdateImageMetaData422ResponseData>) => {
				this.description.errs = response.data.description;
				this.is_public.errs = response.data.is_public;
			}
		);
	}
	
	private async ajax_delete(): Promise<void> {
		if (!this.id) {
			throw new Error("undefined id");
		}
		const request_config: AxiosRequestConfig<IDeleteImageRequestData> = {
			method: "delete",
			url: route('ajax.exhibit.image.delete', { exhibit_id: this.parent.exhibit_id, image_id: this.id })
		};
		console.log('ajax_delete');
		return axios.request(request_config).then(
			(response: AxiosResponse<IDeleteImage200ResponseData>) => {
				this.parent.delete_form({ form: this, ids_order: response.data });
			},
			(response: AxiosResponse<IDeleteImage422ResponseData>) => {
				this.errs = response.data;
			}
		);
	}
	
	private async ajax_set_file_if_new_file(): Promise<void> {
		if (!this.id) {
			throw new Error("undefined id");
		}
		if (!this.new_file || !this.file) {
			return;
		}
		const request_config: AxiosRequestConfig<ISetImageFileRequestData> = {
			method: "put",
			url: route('ajax.image.set_file', { image_id: this.id }),
			headers: {
				"Content-Type": this.file.type
			},
			data: this.file,
		};
		console.log('ajax_set_file');
		return axios.request(request_config).then(
			(response: AxiosResponse) => {
				this.new_file = false;
			},
			(response: AxiosResponse<IDeleteImage422ResponseData>) => {
				this.errs = response.data;
			}
		);
	}
}
