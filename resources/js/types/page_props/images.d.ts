import { IImageIDsOrder } from "../ajax/image";

export interface IImageInitPageProps {
	id: string;
	description: string;
	is_public: boolean;
	image: {
		height: number,
		width: number,
	};
	thumbnail: {
		height: number,
		width: number,
	};
}

export interface IImagesInitPageProps {
	exhibit_id: int;
	images: IImageInitPageProps[];
}
