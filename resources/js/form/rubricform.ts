import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import {
	ICreateRubric200ResponseData,
	ICreateRubricRequestData,
	IDeleteRubric200ResponseData,
	IUpdateRubricRequestData
} from "@/types/ajax/rubric";
import { ToastServiceMethods } from "primevue/toastservice";
import { ConfirmationServiceMethods } from "primevue/confirmationservice";
import { ISingleValueForm2ConstructorArgs, SingleValueForm2 } from "./singlevalueform2";
import { route } from "ziggy-js";

export interface IRubricForm {
	save(): void
};

export interface IRubricFormConstructorArgs {
	id?: string,
	name?: ISingleValueForm2ConstructorArgs<string>,
	category?: string,
	toast_service: ToastServiceMethods,
	confirm_service: ConfirmationServiceMethods,
	dialog_ref: any
};

export class RubricForm implements IRubricForm {
	public id?: string;
	public name: SingleValueForm2<string>;
	public category: string;
	public dialog_ref: any;

	private readonly toast_service: ToastServiceMethods;
	private readonly confirm_service: ConfirmationServiceMethods;

	public constructor(args: IRubricFormConstructorArgs) {
		this.toast_service = args.toast_service;
		this.confirm_service = args.confirm_service;
		this.dialog_ref = args.dialog_ref

		const name_args: ISingleValueForm2ConstructorArgs<string> = {
			val: args.name?.val ?? '',
			errs: args.name?.errs
		};
		this.id = args.id;
		this.name = new SingleValueForm2(name_args, 'name');
		this.category = args.category ?? ''

		console.log(args);
	}

	private is_persisted(): boolean {
		return this.id !== undefined;
	}

	public async save(): Promise<void> {
		if (this.is_persisted()) {
			return this.ajax_update();
		} else {
			return this.ajax_create();
		}
	}

	private ajax_create(): Promise<void> {
		const request_config: AxiosRequestConfig<ICreateRubricRequestData> = {
			method: "post",
			url: route('ajax.rubric.create'),
			data: {
				name: this.name.val,
				category: this.category
			},
		};
		return axios.request(request_config).then(
			(response: AxiosResponse<ICreateRubric200ResponseData>) => {
				this.dialog_ref.close({
					data: JSON.parse(response.data)
				})
			}
		);
	}

	private ajax_update(): Promise<void> {
		if (!this.id) {
			throw new Error("undefined id");
		}
		const request_config: AxiosRequestConfig<IUpdateRubricRequestData> = {
			method: "put",
			url: route('ajax.rubric.update', { rubric_id: this.id }),
			data: {
				id: this.id,
				name: this.name.val,
				category: this.category
			},
		};
		return axios.request(request_config).then((response) => {
			this.dialog_ref.close({
				data: JSON.parse(response.data)
			})
		});
	}
}
