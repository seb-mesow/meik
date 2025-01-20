import { IImageIDsOrder } from "../ajax/image";

export interface IImageInitPageProps {
	id: string;
	description: string;
	is_public: boolean;
}

export interface IImagesInitPageProps {
	exhibit_id: int;
	images: IImageInitPageProps[];
}
