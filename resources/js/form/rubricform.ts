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
	try_save(new_form: RubricForm): void;
	cancel_editing(): void;
};

export interface IRubricFormConstructorArgs {
	id?: string,
	name?: ISingleValueForm2ConstructorArgs<string>,
	category?: string,
	toast_service: ToastServiceMethods,
	confirm_service: ConfirmationServiceMethods,
};

export class RubricForm implements IRubricForm {
	public id?: string;
	public name: SingleValueForm2<string>;
	public category: string;

	private readonly toast_service: ToastServiceMethods;
	private readonly confirm_service: ConfirmationServiceMethods;
	
	public constructor(args: IRubricFormConstructorArgs) {
		this.toast_service = args.toast_service;
		this.confirm_service = args.confirm_service;
		
		const name_args: ISingleValueForm2ConstructorArgs<string> = {
			val: args.name?.val ?? '',
			errs: args.name?.errs
		};
		this.id = args.id;
		this.name = new SingleValueForm2(name_args, 'name');
		this.category = args.category ?? ''
	}
	
	private is_persisted(): boolean {
		return this.id !== undefined;
	}
	
	public async try_save(new_form: Pick<RubricForm, 'name'>): Promise<void> {
		return new Promise(async (resolve: () => void, reject: () => void) => {
			console.log('RubricForm::on_row_edit_save()');
			console.log(`this.name.val === ${this.name.val}`);
			console.log(`this.name.val_in_editing === ${this.name.val_in_editing}`);
			// this ist direkt das Objekt in der rows-Property oder ein Proxy darauf.
			
			// Die bearbeitete Zeile wird automatisch aus editing_rows entfernt;
			
			if (!this.name.val_in_editing) {
				this.toast_service.add({ severity: 'error', summary: 'Name notwendig', detail: 'Das Feld "Name" darf nicht leer sein', life: 3000 });
				
				// Es muss das IDENTISCHE Zeilen-Objekt in editing_rows erhalten blieben
				// (newData ist ein Kopie und damit zwar gleich aber nicht identisch.)
				reject();
				return;
			}
			
			// Werte der Zeile im Objekt rows setzen:
			this.name.commit();

			await this.save();
			console.log("Ende");
			resolve();
		});
	}
	
	public cancel_editing(): void {
		console.log('RubricForm::cancel_editing()');
		console.log(`this.id === ${this.id}`);
		console.log(`this.name.val === ${this.name.val}`);
		
		this.name.rollback();
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
				this.id = response.data
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
			data:this.name.val,
		};
		return axios.request(request_config);
	}
}
