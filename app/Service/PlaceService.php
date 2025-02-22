<?php
declare(strict_types=1);

namespace App\Service;

use App\Models\Place;
use App\Repository\PlaceRepository;

/**
 * @phpstan-type IPlaceProps array{
 *     id: int,
 *     name: string,
 * }
 */
final class PlaceService {
	
	public function __construct(
		private readonly PlaceRepository $place_repository
	) {}
	
	/**
	 * @return array{
	 *     places: IPlaceProps[],
	 *     total_count: int,
	 * }
	 */
	public function query(?string $location_id = null, ?int $page_number = null, ?int $count_per_page = null): array {
		assert(($page_number === null) === ($count_per_page === null));
		
		$result = $this->place_repository->query($location_id, $page_number, $count_per_page);
		
		$result['places'] =  array_map(static fn(Place $place): array => self::determinate_props($place), $result['places']);
		
		return $result;
	}
	
	/**
	 * @return IPlaceProps
	 */
	private static function determinate_props(Place $place): array {
		return [
			'id' => $place->get_id(),
			'name' => $place->get_name(),
		];
	}
}
