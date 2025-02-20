import { ICategory } from "@/form/rubricform";

export interface IRubricTileProps {
	id: string,
	name: string,
	category_id: string,
};

export interface IRubricTilesMainProps {
	rubric_tiles: IRubricTileProps[],
	count_per_page: number,
};
