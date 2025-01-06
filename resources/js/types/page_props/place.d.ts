export interface IPlaceInitPageProps {
	id: string;
	name: string;
}

export interface IPlacesInitPageProps {
	location_id: string;
	places: IPlaceInitPageProps[];
	total_count: number;
}
