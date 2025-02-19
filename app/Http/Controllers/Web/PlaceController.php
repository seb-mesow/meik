<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Repository\LocationRepository;
use App\Repository\PlaceRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlaceController extends Controller
{
	private const int DEFAULT_COUNT_PER_PAGE = 10;

	public function __construct(
		private readonly PlaceRepository $place_repository,
		private readonly LocationRepository $location_repository,
	) {}
	
	public function overview(string $location_id) {
		[ 'places' => $places, 'total_count' => $total_count ] = 
			$this->place_repository->query($location_id, 0, self::DEFAULT_COUNT_PER_PAGE);
		
		$places_json = array_map(static fn(Place $place): array => [
			'id' => $place->get_id(),
			'name' => $place->get_name(),
		] , $places);
		
		$location = $this->location_repository->get($location_id);
		
		return Inertia::render('Locations/Places', [
			'location_name' => $location->get_name(),
			'init_props' => [
				'location_id' => $location->get_id(),
				'places' => $places_json,
				'total_count' => $total_count,
			]
		]);
	}
}
