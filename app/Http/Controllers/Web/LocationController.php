<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Repository\LocationRepository;
use Inertia\Inertia;

class LocationController extends Controller
{
	private const int DEFAULT_COUNT_PER_PAGE = 10;
	
	public function __construct(
		private readonly LocationRepository $location_repository
	) {}

	public function overview() {
		[ 'locations' => $locations, 'total_count' => $total_count ] = 
			$this->location_repository->get_paginated(0, self::DEFAULT_COUNT_PER_PAGE);
		
		$locations_json = array_map(static fn(Location $location): array => [
			'id' => $location->get_id(),
			'name' => $location->get_name(),
			'is_public' => $location->get_is_public(),
		] , $locations);
		
		return Inertia::render('Locations/Locations', [
			'init_props' => [
				'locations' => $locations_json,
				'total_count' => $total_count
			]
		]);
	}
}
