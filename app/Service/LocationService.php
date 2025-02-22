<?php
declare(strict_types=1);

namespace App\Service;

use App\Models\Location;
use App\Repository\LocationRepository;

/**
 * @phpstan-type ILocationProps array{
 *     id: int,
 *     name: string,
 *     is_public: boolean,
 * }
 */
final class LocationService {
	
	public function __construct(
		private readonly LocationRepository $location_repository
	) {}
	
	/**
	 * @return array{
	 *     locations: ILocationProps[],
	 *     total_count: int,
	 * }
	 */
	public function query(?int $page_number = null, ?int $count_per_page = null): array {
		assert(($page_number === null) === ($count_per_page === null));
		
		$result = $this->location_repository->query($page_number, $count_per_page);
		
		$result['locations'] =  array_map(static fn(Location $location): array => self::determinate_props($location), $result['locations']);
		
		return $result;
	}
	
	/**
	 * @return ILocationProps
	 */
	private static function determinate_props(Location $location): array {
		return [
			'id' => $location->get_id(),
			'name' => $location->get_name(),
			'is_public' => $location->get_is_public(),
		];
	}
}
