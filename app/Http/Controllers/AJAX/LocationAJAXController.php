<?php
declare(strict_types=1);

namespace App\Http\Controllers\AJAX;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Repository\LocationRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class LocationAJAXController extends Controller
{
	private Serializer $serializer;

	public function __construct(
		private readonly LocationRepository $location_repository
	) {
		$this->serializer = SerializerBuilder::create()->build();
	}

	public function get_paginated(Request $request): JsonResponse {
		$page_number = (int) $request->query('page_number');
		$count_per_page = (int) $request->query('count_per_page');
		
		[ 'locations' => $locations, 'total_count' => $total_count ] =
			$this->location_repository->get_paginated($page_number, $count_per_page);
		/** @var Location[] $locations */
		/** @var int $total_count */
		$locations_json = array_map(static fn(Location $location): array => [
			'id' => $location->get_id(),
			'name' => $location->get_name(),
			'is_public' => $location->get_is_public(),
		] , $locations);
		return response()->json([
			'locations' => $locations_json,
			'total_count' => $total_count
		]);
	}

	public function create(Request $request): JsonResponse {
		$name = (string) $request->input('val.name.val');
		$is_public = (bool) $request->input('val.is_public.val');
		
		$location = new Location(
			name: $name,
			is_public: $is_public,
		);
		$this->location_repository->insert($location);
		return response()->json($location->get_id());
	}

	public function update(Request $request, string $location_id): void {
		$name = (string) $request->input('val.name.val');
		$is_public = (bool) $request->input('val.is_public.val');
		
		$location = $this->location_repository->get($location_id);
		$location->set_name($name);
		$location->set_is_public($is_public);
		$this->location_repository->update($location);
	}

	public function delete(string $location_id): void {
		$this->location_repository->remove_by_id($location_id);
	}
}
