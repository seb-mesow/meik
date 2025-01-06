<?php
declare(strict_types=1);

namespace App\Http\Controllers\AJAX;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Repository\PlaceRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlaceAJAXController extends Controller
{
	public function __construct(
		private readonly PlaceRepository $place_repository
	) {}
	
	public function get_paginated(Request $request, string $location_id): JsonResponse {
		$page_number = (int) $request->query('page_number');
		$count_per_page = (int) $request->query('count_per_page');
		
		[ 'places' => $places, 'total_count' => $total_count ] = 
			$this->place_repository->get_paginated($location_id, $page_number, $count_per_page);
		
		$places_json = array_map(static fn(Place $place): array => [
			'id' => $place->get_id(),
			'name' => $place->get_name(),
		] , $places);
		
		return response()->json([
			'places' => $places_json,
			'total_count' => $total_count
		]);
	}
	
	public function create(Request $request, string $location_id): JsonResponse {
		$name = (string) $request->input();
		// TODO assert, that $location_id exists
		$place = new Place(
			name: $name,
			location_id: $location_id
		);
		$this->place_repository->insert($place);
		return response()->json($place->get_id());
	}
	
	public function update(Request $request, string $place_id): void {
		$name = (string) $request->input();
		
		$place = $this->place_repository->get($place_id);
		$place->set_name($name);
		$this->place_repository->update($place);
	}
	
	public function delete(string $place_id): void {
		$this->place_repository->remove_by_id($place_id);
	}
}
