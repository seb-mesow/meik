export interface IExhibitOverviewExhibitTileInitPageProps {
	id: number,
	name: string,
	inventory_number: string,
	year_of_manufacture: number,
	location_name: string,
	place_name: string,
}

export interface IExhibitOverviewInitPageProps {
	exhibits: IExhibitOverviewExhibitTileInitPageProps[],
}
