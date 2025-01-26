export interface IExhibitOverviewExhibitTileInitPageProps {
	id: number,
	name: string,
	inventory_number: string,
	year_of_manufacture: number,
	manufacturer: string,
	location_name: string,
	place_name: string,
	title_image?: {
		id: string,
		description: string,
		thumbnail_width: number,
		thumbnail_height: number,
	}
}

export interface IExhibitOverviewInitPageProps {
	exhibits: IExhibitOverviewExhibitTileInitPageProps[],
}
