import { IUserRole } from "@/form/special/multiple/user-form";

export interface ISelectableValuesProps {
	role: IUserRole[],
};

export interface IUserInitPageProps {
	id: string;
	username: string;
	forename: string;
	surname: string;
	role_id: string;
};

export interface IUsersInitPageProps {
	users: IUserInitPageProps[];
	total_count: number;
	count_per_page: number, // muss zweckmäßiger im Backend angegeben werden, damit nicht mehr Daten als nötig im Backend geladen werden
	selectable_values: ISelectableValuesProps,
};
