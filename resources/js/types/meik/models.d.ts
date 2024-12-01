import { IValueForm as IValueForm, IForm } from "@/util/form";

export interface IExhibitForTile {
	id: string;
	name: string;
};

export interface IFreeText {
	heading: string,
	html: string,
	is_public: boolean,
};
