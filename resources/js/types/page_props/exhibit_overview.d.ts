export interface IExhibitOverviewExhibitTileInitPageProps {
	id: number,
	name: string,
	inventory_number: string,
	manufacture_date: string,
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
