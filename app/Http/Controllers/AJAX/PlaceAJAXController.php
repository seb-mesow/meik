<?php
declare(strict_types=1);

namespace App\Http\Controllers\AJAX;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Repository\PlaceRepository;
use App\Service\PlaceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlaceAJAXController extends Controller
{
	public function __construct(
		private readonly PlaceRepository $place_repository,
		private readonly PlaceService $place_service,
	) {}
	
	public function query(Request $request): JsonResponse {
		$location_id = $request->query('location_id');
		$page_number = $request->query('page_number');
		$count_per_page = $request->query('count_per_page');
		
		$location_id = is_string($location_id) ? trim($location_id) : null;
		$page_number = is_string($page_number) ? (int) $page_number : null;
		$count_per_page = is_string($count_per_page) ? (int) $count_per_page : null;
		
		assert(($page_number === null) == ($count_per_page === null));
		
		$result = $this->place_service->query($location_id, $page_number, $count_per_page);
		
		return response()->json($result);
	}
	
	public function create(Request $request, string $location_id): JsonResponse {
		$name = (string) $request->input('name');
		// TODO assert, that $location_id exists
		$place = new Place(
			name: $name,
			location_id: $location_id
		);
		$this->place_repository->insert($place);
		return response()->json($place->get_id());
	}
	
	public function update(Request $request, string $place_id): void {
		$name = (string) $request->input('name');
		
		$place = $this->place_repository->get($place_id);
		$place->set_name($name);
		$this->place_repository->update($place);
	}
	
	public function delete(string $place_id): void {
		$this->place_repository->remove_by_id($place_id);
	}
}
