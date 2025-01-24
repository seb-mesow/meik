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
	}
}

export interface IExhibitOverviewInitPageProps {
	exhibits: IExhibitOverviewExhibitTileInitPageProps[],
}
