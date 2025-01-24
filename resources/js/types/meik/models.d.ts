import { IValueForm as IValueForm, IForm, FormSpec, IFormSpec, CheckFormSpec } from "@/util/form";

interface ICheckFormSpec {
	id: boolean
};
type CheckFormSpec<T extends {
	id: boolean
}> = T;

type MyType2 = {} & never;

type Check<T extends string> = T;
type Test = Check<number>

// Due to TypeScript's principle of structual typing there seems no possibility
// to check for the correct TYPED-shape of a dervied/implemeted type. :(

export interface IExhibitForm {
	id?: int,
	errs: string[],
	val: {
		inventory_number: {
			id: 'inventory_number',
			val: string,
			errs: string[];
		},
		name: {
			id: 'name',
			val: string,
			errs: string[];
		},
		manufacturer: {
			id: 'manufacturer',
			val: string,
			errs: string[];
		},
		free_texts: IFreeTextsForms,
	},
	title_image?: {
		id: string,
		description: string,
	},
}

