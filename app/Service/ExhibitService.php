<?php
declare(strict_types=1);

namespace App\Service;

use App\Models\Exhibit;
use App\Repository\ExhibitRepository;
use App\Repository\LocationRepository;
use App\Repository\PlaceRepository;

/**
 * @phpstan-type IExhibitTileProps array{
 *     id: int,
 *     name: string,
 *     inventory_number: string,
 *     manufacture_date: string,
 *     manufacturer: string,
 *     location_name: string,
 *     place_name: string,
 *     title_image?: array{
 *         id: string,
 *         description: string,
 *         thumbnail_width: int,
 *         thumbnail_height: int
 *     }
 * }
 */
final class ExhibitService {
	
	public function __construct(
		private readonly ExhibitRepository $exhibit_repository,
		private readonly PlaceRepository $place_repository,
		private readonly LocationRepository $location_repository,
		private readonly ImageService $image_service,
	) {}
	
	/**
	 * @param Exhibit[] $exhibits
	 * @return IExhibitTileProps[]
	 */
	public function determinate_tiles_props(array $selectors = [], ?int $page_number = null, ?int $count_per_page = null): array {
		assert(($page_number === null) === ($count_per_page === null));
		
		$exhibits = $this->exhibit_repository->query(
			additonal_selectors: $selectors,
			page_number: $page_number,
			count_per_page: $count_per_page,
		);
		$_this = $this;
		return array_map(static function(Exhibit $exhibit) use ($_this): array {
			return $_this->determinate_tile_props($exhibit);
		}, $exhibits);
	}
	
	/**
	 * @return IExhibitTileProps
	 */
	private function determinate_tile_props(Exhibit $exhibit): array
	{
		$place = $this->place_repository->get($exhibit->get_place_id());
		$location = $this->location_repository->get($place->get_location_id());
		
		$tile_props = [
			'id' => $exhibit->get_id(),
			'name' => $exhibit->get_name(),
			'inventory_number' => $exhibit->get_inventory_number(),
			'manufacture_date' => $exhibit->get_manufacture_date(),
			'manufacturer' => $exhibit->get_manufacturer(),
			'location_name' => $location->get_name(),
			'place_name' => $place->get_name(),
		];
		if ($title_image = $this->image_service->get_internal_title_image($exhibit)) {
			$tile_props ['title_image'] = [
				'id' => $title_image->get_id(),
				'description' => $title_image->get_description(),
				'thumbnail_width' => $title_image->get_thumbnail_width(),
				'thumbnail_height' => $title_image->get_thumbnail_height(),
			];	
		}
		return $tile_props;
	}
}
