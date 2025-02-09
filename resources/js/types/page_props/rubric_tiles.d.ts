export interface IRubricTileProps {
	id: string,
	name: string,
};

export interface IRubricTilesMainProps {
	rubric_tiles: IRubricTileProps[],
	count_per_page: number,
};
