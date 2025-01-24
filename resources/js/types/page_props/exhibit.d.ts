import { IFreeTextsInitPageProps } from "./freetexts"

export interface IExhibitInitPageProps {
	id?: number,
	val: {
		inventory_number: {
			val: string,
			errs?: string[]
		},
		name: {
			val: string,
			errs?: string[]
		},
		manufacturer: {
			val: string,
			errs?: string[]
		},
		free_texts: {
			val: IFreeTextsInitPageProps[],
			errs?: string[]
		}
	},
	errs?: string[],
	title_image?: {
		id: string,
		description: string,
	},
}
