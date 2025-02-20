<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Enum\Category;
use App\Models\Rubric;
use App\Repository\ExhibitRepository;
use App\Repository\RubricRepository;
use App\Service\ExhibitService;
use App\Service\RubricService;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class RubricController extends Controller
{
	public function __construct(
		private readonly RubricRepository $rubric_repository,
		private readonly ExhibitService $exhibit_service,
	) {}

	public function details(string $rubric_id): InertiaResponse {
		$rubric = $this->rubric_repository->get($rubric_id);
		$category = $rubric->get_category();
		
		$selectors = [
			'rubric_id' =>  [
				'$eq' => $rubric_id
			]
		];
		
		$exhibit_tile_props = $this->exhibit_service->determinate_tiles_props(page_number: 0, selectors: $selectors);
		
		return Inertia::render('Rubric/Rubric', [
			'category' => [
				'id' => $category->value,
				'name' => $category->get_name(),
			],
			'rubric' => [
				'id' => $rubric->get_id(),
				'name' => $rubric->get_name(),
			],
			'exhibit_tiles_main_props' => [
				'exhibit_tiles' => $exhibit_tile_props,
				'count_per_page' => ExhibitService::DEFAULT_COUNT_PER_PAGE,
			]
		]);
	}
}
