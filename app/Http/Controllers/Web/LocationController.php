<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Service\LocationService;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class LocationController extends Controller
{
	private const int INITIAL_COUNT_PER_PAGE = 10;
	
	public function __construct(
		private readonly LocationService $location_service,
	) {}

	public function overview(): InertiaResponse {
		$result = $this->location_service->query(0, self::INITIAL_COUNT_PER_PAGE);
		
		return Inertia::render('Locations/Locations', [
			'init_props' => [
				'locations' => $result['locations'],
				'total_count' => $result['total_count'],
				'count_per_page' => self::INITIAL_COUNT_PER_PAGE,
			],
		]);
	}
}
