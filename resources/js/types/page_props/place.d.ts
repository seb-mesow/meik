export interface IPlaceInitPageProps {
	id: string,
	name: string,
}

export interface IPlacesInitPageProps {
	location_id: string,
	places: IPlaceInitPageProps[],
	total_count: number,
	count_per_page: number, // muss zweckmäßiger im Backend angegeben werden, damit nicht mehr Daten als nötig im Backend geladen werden
}
