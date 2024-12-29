import { 
	IFreeTextIndicesOrder,
	IUpdateFreeTextRequestData,
	ICreateFreeTextRequestData,
	ICreateFreeText200ResponseData,
	IDeleteFreeTextRequestData,
	IDeleteFreeText200ResponseData,
	IUpdateFreeText422ResponseData,
	ICreateFreeText422ResponseData,
} from "@/types/ajax/freetext";
import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import { ISingleValueForm, ISingleValueFormConstructorArgs, SingleValueForm } from "./singlevalueform";

export interface IFreeTextForm {
	readonly id?: number;
	heading: ISingleValueForm<string>;
	html: ISingleValueForm<string>;
	is_public: ISingleValueForm<boolean>;
	readonly ui_id: number;
	click_delete(): void;
	click_save(): void;
	readonly is_save_button_loading: boolean;
	readonly is_delete_button_loading: boolean;
}

// um eine zirkulräre Abhängigkeit zu vermeiden:
// freetextform.ts (lower module) soll nichts von freetextsforms.ts (higher module) importieren
export interface IFreeTextFormParent {
	update_indices(args: { new_indices_order: IFreeTextIndicesOrder }): void;
	get_index_for_persisting(args: { form: IFreeTextForm }): number;
	delete_form(args: { ui_id: number }): void;
}

export type IFreeTextFormConstructorArgs = Readonly<{
	id?: number;
	errs?: string[];
	heading?: ISingleValueFormConstructorArgs<string>;
	html?: ISingleValueFormConstructorArgs<string>;
	is_public?: ISingleValueFormConstructorArgs<boolean>;
	ui_id: number;
	exhibit_id: number;
	parent: IFreeTextFormParent;
}>;

export class FreeTextForm implements IFreeTextForm {
	private errs: string[];
	public id?: number;
	public heading: SingleValueForm<string>;
	public html: SingleValueForm<string>;
	public is_public: SingleValueForm<boolean>;
	
	private readonly exhibit_id: number;
	private parent: IFreeTextFormParent;
	
	public ui_id: number;
	public is_save_button_loading: boolean;
	public is_delete_button_loading: boolean;
	
	public constructor(args: IFreeTextFormConstructorArgs) {
		this.id = args.id;
		
		const heading_args: ISingleValueFormConstructorArgs<string> = {
			val: args.heading?.val ?? '',
			errs: args.heading?.errs,
		};
		const html_args: ISingleValueFormConstructorArgs<string> = {
			val: args.html?.val ?? '',
			errs: args.html?.errs,
		};
		const is_public_args: ISingleValueFormConstructorArgs<boolean> = {
			val: args.is_public?.val ?? false,
			errs: args.is_public?.errs,
		};
		this.errs = args.errs ?? [];
		this.heading = new SingleValueForm(heading_args, 'heading');
		this.html = new SingleValueForm(html_args, 'html');
		this.is_public = new SingleValueForm(is_public_args, 'is_public');
		
		this.ui_id = args.ui_id;
		this.exhibit_id = args.exhibit_id;
		
		this.is_save_button_loading = false;
		this.is_delete_button_loading = false;
		
		this.parent = args.parent;
	}
	
	private is_persisted(): boolean {
		return this.id !== undefined;
	}
	
	private async ajax_update() {
		console.log(`PUT exhibit.free_text.update ${this.exhibit_id} ${this.id}`);
		const request_config: AxiosRequestConfig<IUpdateFreeTextRequestData> = {
			method: "put",
			url: route('exhibit.free_text.update', [this.exhibit_id, this.id]),
			data: {
				val: {
					heading: {
						val: this.heading.val
					},
					html: {
						val: this.html.val
					},
					is_public: {
						val: this.is_public.val
					}
				}
			}
		};
		return axios.request(request_config).then(
			() => {},
			(err) => {
				console.log(err);
				const response: AxiosResponse<IUpdateFreeText422ResponseData> = err.response;
				this.errs = response.data.errs;
				this.heading.errs = response.data.val.heading.errs;
				this.is_public.errs = response.data.val.is_public.errs;
				this.html.errs = response.data.val.html.errs;
			}
		);
	}
	
	private async ajax_create() {
		console.log(`POST exhibit.free_text.create ${this.exhibit_id}`);
		const request_config: AxiosRequestConfig<ICreateFreeTextRequestData> = {
			method: "post",
			url: route('exhibit.free_text.create', [this.exhibit_id]),
			data: {
				index: this.parent.get_index_for_persisting({form: this}),
				val: {
					heading: {
						val: this.heading.val
					},
					html: {
						val: this.html.val
					},
					is_public: {
						val: this.is_public.val
					}
				}
			}
		};
		return axios.request(request_config).then(
			(response: AxiosResponse<ICreateFreeText200ResponseData>) => {
				this.id = response.data.id;
				this.parent.update_indices({ new_indices_order: response.data.indices_order });
			},
			(err) => {
				console.log(err);
				const response: AxiosResponse<ICreateFreeText422ResponseData> = err.response;
				this.errs = response.data.errs;
				this.heading.errs = response.data.val.heading.errs;
				this.is_public.errs = response.data.val.is_public.errs;
				this.html.errs = response.data.val.html.errs;
			}
		);
	}
	
	private async ajax_delete() {
		console.log(`DELETE exhibit.free_text.delete ${this.exhibit_id} ${this.id}`);
		const request_config: AxiosRequestConfig<IDeleteFreeTextRequestData> = {
			method: "delete",
			url: route('exhibit.free_text.delete', [this.exhibit_id, this.id])
		};
		return axios.request(request_config).then(
			(response: AxiosResponse<IDeleteFreeText200ResponseData>) => {
				this.parent.delete_form({ ui_id: this.ui_id }); // muss vor update_indices() passieren
				this.parent.update_indices({ new_indices_order: response.data });
			}
		);
	}
	
	public async click_save() {
		console.log("click_save");
		this.is_save_button_loading = true;
		if (this.is_persisted()) {
			await this.ajax_update();
		} else {
			await this.ajax_create();
		}
		console.log("finally begin");
		this.is_save_button_loading = false;
		console.log("finally end");
	}
	
	public async click_delete() {
		if (this.is_persisted()) {
			this.is_delete_button_loading = true;
			await this.ajax_delete();
			this.is_delete_button_loading = false;
		} else {
			this.parent.delete_form({ ui_id: this.ui_id });
		}
	}
}
