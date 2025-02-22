export interface ILocationInitPageProps {
	id: string;
	name: string;
	is_public: boolean;
}

export interface ILocationsInitPageProps {
	locations: ILocationInitPageProps[];
	total_count: number;
	count_per_page: number, // muss zweckmäßiger im Backend angegeben werden, damit nicht mehr Daten als nötig im Backend geladen werden
}
