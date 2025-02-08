import { IFreeTextsPageProps } from "./freetexts"

export interface IExhibitProps {
	id: number,
	
	inventory_number: string,
	name: string,
	short_description: string,
	// no rubric, because already provided
	location: string,
	place: string,
	manufacturer: string,
	
	title_image?: {
		id: string,
		description: string,
		image_width: number,
		image_height: number,
	},
	
	free_texts: IFreeTextsPageProps[],
}
