<?php
declare(strict_types=1);

namespace App\Http\Controllers\AJAX;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Repository\LocationRepository;
use App\Service\LocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationAJAXController extends Controller
{
	public function __construct(
		private readonly LocationRepository $location_repository,
		private readonly LocationService $location_service,
	) {}

	public function query(Request $request): JsonResponse {
		$page_number = $request->query('page_number');
		$count_per_page = $request->query('count_per_page');
		
		$page_number = is_string($page_number) ? (int) $page_number : null;
		$count_per_page = is_string($count_per_page) ? (int) $count_per_page : null;
		
		assert(($page_number === null) == ($count_per_page === null));
		
		$result = $this->location_service->query($page_number, $count_per_page);
		
		return response()->json($result);
	}

	public function create(Request $request): JsonResponse {
		$name = (string) $request->input('name');
		$is_public = (bool) $request->input('is_public');
		
		$location = new Location(
			name: $name,
			is_public: $is_public,
		);
		$this->location_repository->insert($location);
		return response()->json($location->get_id());
	}

	public function update(Request $request, string $location_id): void {
		$name = (string) $request->input('name');
		$is_public = (bool) $request->input('is_public');
		
		$location = $this->location_repository->get($location_id);
		$location->set_name($name);
		$location->set_is_public($is_public);
		$this->location_repository->update($location);
	}

	public function delete(string $location_id): void {
		$this->location_repository->remove_by_id($location_id);
	}
}
