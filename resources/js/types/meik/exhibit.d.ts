import { IFreeTextForm, IFreeTextInitPageProps } from "../ajax/freetext"

export interface IExhibitForm {
	id: number,
	val: {
		inventory_number: {
			id: 'inventory_number',
			val: string,
			errs: string[]
		},
		name: {
			id: 'name',
			val: string,
			errs: string[]
		},
		manufacturer: {
			id: 'manufacturer',
			val: string,
			errs: string[]
		},
		free_texts: {
			id: 'free_texts',
			val: IFreeTextForm[],
			errs: string[]
		}
	}
}
