<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Repository\LocationRepository;
use App\Service\PlaceService;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PlaceController extends Controller
{
	private const int INITIAL_COUNT_PER_PAGE = 10;
	
	public function __construct(
		private readonly PlaceService $place_service,
		private readonly LocationRepository $location_repository,
	) {}
	
	public function overview(string $location_id): InertiaResponse {
		$result = $this->place_service->query($location_id, 0, self::INITIAL_COUNT_PER_PAGE);
		
		$location = $this->location_repository->get($location_id);
		
		return Inertia::render('Locations/Places', [
			'location_name' => $location->get_name(),
			'init_props' => [
				'location_id' => $location->get_id(),
				'places' => $result['places'],
				'total_count' => $result['total_count'],
				'count_per_page' => self::INITIAL_COUNT_PER_PAGE,
			],
		]);
	}
}
