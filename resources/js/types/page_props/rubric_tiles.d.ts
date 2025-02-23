import { ICategory } from "@/form/special/multiple/rubric-form";

export interface IRubricTileProps {
	id: string,
	name: string,
	category_id: string,
};

export interface IRubricTilesMainProps {
	rubric_tiles: IRubricTileProps[],
	count_per_page: number,
};
