import {
	FreeTextForm,
	IFreeTextForm,
	IFreeTextFormConstructorArgs as IRealFreeTextFormConstructorArgs,
	IFreeTextFormParent
} from "./freetextform";
import {
	IFreeTextIndicesOrder
} from "@/types/ajax/freetext";

export interface IFreeTextsForm {
	readonly children: Readonly<IFreeTextForm[]>;
	readonly errs: Readonly<string[]>;
	append_form(): void;
}

export type IFreeTextFormConstructorArgs = Pick<IRealFreeTextFormConstructorArgs, 
	'id' | 'heading' | 'html' | 'is_public'
>;

export type IFreeTextsFormConstructorArgs = Readonly<{
	exhibit_id: number;
	val: IFreeTextFormConstructorArgs[];
	errs?: string[];
}>;

export class FreeTextsForm implements IFreeTextsForm, IFreeTextFormParent {
	private readonly exhibit_id: number;
	private next_ui_id: number;
	
	children: FreeTextForm[];
	errs: Readonly<string[]>;
	
	public constructor(args: IFreeTextsFormConstructorArgs) {
		this.exhibit_id = args.exhibit_id;
		this.next_ui_id = 0;
		
		this.errs = args.errs ?? [];
		this.children = args.val.map((_args: IFreeTextFormConstructorArgs, index: number): FreeTextForm => new FreeTextForm({
			id: _args.id,
			heading: _args.heading,
			is_public: _args.is_public,
			html: _args.html,
			ui_id: this.next_ui_id++,
			exhibit_id: this.exhibit_id,
			parent: this,
		}));
		
		this.children = this.children
	}
	
	// Nummern von Freitexten:
	// index: int ... Reihenfolge in der UI und den API-Antworten
	// id: string ... eindeutige ID in der Datenbank; NUR gespeicherte Freitexte haben eine id
	// ui_id: int ... eindeutige ID im Frontend; AUCH ungespeicherte Freitexte haben eine ui_id
	public append_form() {
		//const temp = this.children;
		// temp.push(new FreeTextForm({
		this.children.push(new FreeTextForm({
			ui_id: this.next_ui_id++,
			exhibit_id: this.exhibit_id,
			parent: this,
		}));
		// this.children = temp; // trigger reactivity
	}
	
	public delete_form(args: { ui_id: number }) {
		console.log(`delete form with ui_id = ${args.ui_id}`);
		this.children = this.children.filter((form: FreeTextForm): boolean => form.ui_id !== args.ui_id);
	}

	public get_index_for_persisting(args: { form: IFreeTextForm; }): number {
		let index = 0;
		for (const child_form of this.children) {
			if (child_form === args.form) {
				return index;
			}
			if (child_form.id) {
				index++;
			}
		}
		throw new Error("Assertation failed: child form not found");
	}
	
	public update_indices(args: { new_indices_order: IFreeTextIndicesOrder}): void {
		const temp = this.children;
		console.log("new_indicesorder (begin) == ");
		console.log(args.new_indices_order);
		console.log("temp (begin) == ");
		console.log(temp);
		const new_children: FreeTextForm[] = [];
		let t = 0;
		// copy unsaved forms at the front
		while (t < temp.length) {
			const form = temp[t];
			if (form.id !== undefined) {
				break;
			}
			new_children.push(form);
			t++;
		}
		temp.splice(0, t);
		// update with new indices from argument
		args.new_indices_order.forEach((id) => {
			// loop until find index from argument and append that form
			let t_begin = 0;
			while (t_begin < temp.length) {
				if (temp[t_begin].id === id) {
					break;
				}
				t_begin++;
			}
			if (t_begin >= temp.length) {
				throw new Error(`unknown FreeText ID ${id} for indices update`);
			}
			new_children.push(temp[t_begin]);
			// after the found form copy all subsequent unsaved forms until the next saved form
			let t_end = t_begin + 1;
			while (t_end < temp.length) {
				const form = temp[t_end];
				if (form.id) {
					break;
				}
				new_children.push(form);
				t_end++;
			}
			temp.splice(t_begin, t_end - t_begin);
		});
		
		if (temp.length > 0) {
			console.log("temp (end) ==");
			console.log(temp);
			throw new Error("Assertation failed: `temp` should be empty, but still contains elements.");
		}
		// trigger reactivity
		this.children = new_children;
	}
}
