export interface ILocationInitPageProps {
	id: string;
	name: string;
	is_public: boolean;
}

export interface ILocationsInitPageProps {
	locations: ILocationInitPageProps[];
	total_count: number;
}
