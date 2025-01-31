import axios, { AxiosRequestConfig, AxiosResponse } from "axios";
import {
	ICreateRubric200ResponseData,
	ICreateRubricRequestData,
	IUpdateRubricRequestData
} from "@/types/ajax/rubric";
import { ISingleValueForm2, ISingleValueForm2ConstructorArgs, SingleValueForm2 } from "./singlevalueform2";
import { route } from "ziggy-js";
import { IRubricTileProps } from "@/types/page_props/rubric_overview";

export interface IRubricForm {
	readonly name: ISingleValueForm2<string>;
	save(): void
};

export interface IRubricFormConstructorArgs {
	id?: string,
	name?: ISingleValueForm2ConstructorArgs<string>,
	category: string,
	dialog_ref: any,
	on_created?: (tile: IRubricTileProps) => void
	on_updated?: (tile: IRubricTileProps) => void
};

export class RubricForm implements IRubricForm {
	private id?: string;
	public name: SingleValueForm2<string>;
	private category: string;
	private dialog_ref: any;
	private on_created: (tile: IRubricTileProps) => void; 
	private on_updated: (tile: IRubricTileProps) => void; 

	public constructor(args: IRubricFormConstructorArgs) {
		this.id = args.id;
		
		const name_args: ISingleValueForm2ConstructorArgs<string> = {
			val: args.name?.val ?? '',
			errs: args.name?.errs
		};
		this.name = new SingleValueForm2(name_args, 'name');
		this.category = args.category ?? '';
		
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
		console.log(`ajax_update(): this.category == ${this.category}`);
		
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
				const new_rubric_id = response.data;
				this.on_created({ id: new_rubric_id, name: this.name.val });
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
		const request_config: AxiosRequestConfig<IUpdateRubricRequestData> = {
			method: "put",
			url: route('ajax.rubric.update', { rubric_id: this.id }),
			data: {
				name: this.name.val,
				category: this.category
			},
		};
		return axios.request(request_config).then(() => {
			if (this.id === undefined) {
				throw new Error("undefined id");
			}
			this.on_updated({ id: this.id, name: this.name.val });
			this.dialog_ref.close({
				data: {
					id: this.id,
					name: this.name.val,
				}
			});
		});
	}
}
