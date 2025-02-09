import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import * as RubricAJAX from "@/types/ajax/rubric";
import { ISingleValueForm2, ISingleValueForm2ConstructorArgs, SingleValueForm2 } from "./singlevalueform2";
import { route } from "ziggy-js";
import { IRubricProps } from "@/types/page_props/rubric_overview";

export interface IRubricForm {
	readonly name: ISingleValueForm2<string>;
	save(): void
};

export interface IRubricFormConstructorArgs {
	id?: string,
	name?: ISingleValueForm2ConstructorArgs<string>,
	category_id: string,
	dialog_ref: any,
	on_created?: (tile: IRubricProps) => void
	on_updated?: (tile: IRubricProps) => void
};

export class RubricForm implements IRubricForm {
	private id?: string;
	public name: SingleValueForm2<string>;
	private category_id: string;
	private dialog_ref: any;
	private on_created: (tile: IRubricProps) => void; 
	private on_updated: (tile: IRubricProps) => void; 

	public constructor(args: IRubricFormConstructorArgs) {
		this.id = args.id;
		
		const name_args: ISingleValueForm2ConstructorArgs<string> = {
			val: args.name?.val ?? '',
			errs: args.name?.errs
		};
		this.name = new SingleValueForm2(name_args, 'name');
		this.category_id = args.category_id ?? '';
		
		this.dialog_ref = args.dialog_ref;
		this.on_created = args.on_created ?? (() => {});
		this.on_updated = args.on_updated ?? (() => {});
	}

	private exists_in_db(): boolean {
		return this.id !== undefined;
	}

	public async save(): Promise<void> {
		if (this.exists_in_db()) {
			return this.ajax_update();
		} else {
			return this.ajax_create();
		}
	} 

	private async ajax_create(): Promise<void> {
		console.log(`ajax_update(): this.category_id == ${this.category_id}`);
		
		const request_config: AxiosRequestConfig<RubricAJAX.Create.IRequestData> = {
			method: "post",
			url: route('ajax.rubric.create'),
			data: {
				name: this.name.get_value(),
				category_id: this.category_id,
			},
		};
		return axios.request(request_config).then(
			(response: AxiosResponse<RubricAJAX.Create.I200ResponseData>) => {
				const new_rubric_id = response.data;
				this.on_created({ id: new_rubric_id, name: this.name.get_value() });
				this.dialog_ref.close({
					data: {
						id: new_rubric_id,
						name: this.name.val,
					}
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
				category_id: this.category_id
			},
		};
		return axios.request(request_config).then(() => {
			if (this.id === undefined) {
				throw new Error("undefined id");
			}
			this.on_updated({ id: this.id, name: this.name.get_value() });
			this.dialog_ref.close({
				data: {
					id: this.id,
					name: this.name.val,
				}
			});
		});
	}
}
