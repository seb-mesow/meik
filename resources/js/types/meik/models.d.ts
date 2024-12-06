import { IValueForm as IValueForm, IForm, FormSpec, IFormSpec, CheckFormSpec } from "@/util/form";

export interface IExhibitForTile {
	id: string;
	name: string;
};

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

export interface IFreeTextForm {
	id: string,
	index: number,
	persisted: boolean,
	errs: [],
	val: {
		heading: {
			id: 'heading',
			val: string,
			errs: []
		},
		html: {
			id: 'html',
			val: string,
			errs: []
		},
		is_public: {
			id: 'is_public',
			val: boolean,
			errs: []
		},
	},
};
export interface IFreeTextsForms {
	id: 'free_texts',
	errs: [],
	val: { [key: string]: IFreeTextForm },
}
