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
		connected_exhibits: {
			id: number,
			name: string
		}[]
	},
	errs?: string[],
	title_image?: {
		id: string,
		description: string,
		image_width: number,
		image_height: number,
	},
}

export interface IConnectedExhibitProps {
	id: number,
	errs?: string[],
	val: {
		name: {
			val: string,
			errs?: string[]
		},
	},
}
