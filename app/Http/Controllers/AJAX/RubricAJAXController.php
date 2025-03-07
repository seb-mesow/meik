<?php

declare(strict_types=1);

namespace App\Http\Controllers\AJAX;

use App\Http\Controllers\Controller;
use App\Models\Enum\Category;
use App\Models\Rubric;
use App\Repository\RubricRepository;
use App\Service\RubricService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RubricAJAXController extends Controller
{
	public function __construct(
		private readonly RubricService $rubric_service,
		private readonly RubricRepository $rubric_repository,
	) {}

	public function query(Request $request): JsonResponse
	{
		$category_id = $request->query('category_id');
		$page_number = $request->query('page_number');
		$count_per_page = $request->query('count_per_page');
		
		$category_id = is_string($category_id) ? trim($category_id) : null;
		$category_id = $category_id === '' ? null : $category_id;
		$page_number = is_string($page_number) ? trim($page_number) : null;
		$page_number = $page_number === '' ? null : $page_number;
		$page_number = is_numeric($page_number) ? (int) $page_number : null;
		$count_per_page = is_string($count_per_page) ? trim($count_per_page) : null;
		$count_per_page = $count_per_page === '' ? null : $count_per_page;
		$count_per_page = is_numeric($count_per_page) ? (int) $count_per_page : null;
		
		assert(($page_number === null) == ($count_per_page === null));
		
		$result = $this->rubric_service->query($category_id, $page_number, $count_per_page);
		
		return response()->json($result);
	}

	public function create(Request $request): JsonResponse
	{
		$name = $request->input('name');
		$category_id = $request->input('category_id');
		
		$category = Category::from($category_id);
		$rubric = new Rubric(
			name: $name,
			category: $category
		);
		$this->rubric_repository->insert($rubric);
		
		return response()->json($rubric->get_id());
	}

	public function update(Request $request, string $rubric_id): void
	{
		$name = $request->input('name');
		$category_id = $request->input('category_id');
		
		$category = Category::from($category_id);
		$rubric = $this->rubric_repository->get($rubric_id);
		$rubric->set_name($name);
		$rubric->set_category($category);
		$this->rubric_repository->update($rubric);
	}

	public function delete(string $rubric_id): void {
		$this->rubric_repository->remove_by_id($rubric_id);
	}
	
	// Dafür gibt es  $this->rubric_service->query
	public function search_rubrics(Request $request)
	{
		$query = $request->query('query');
		$queryParts = explode(' ', $query);
		$selectors = [];
		foreach ($queryParts as $queryPart) {
			if(!$queryPart) {
				continue;
			}

			$selector = [
				'$or' => [
					[
						'name' => [
							'$regex' => '(?i)' . $queryPart // Regex für name
						]
					],
				]
			];

			$selectorParts[] = $selector;
		}

		$selectorParts[] = [
			'_id' => [
				'$beginsWith' => 'rubric'
			]
		];
		$selectors = [
			'$and' => $selectorParts
		];
		$rubrics = $this->rubric_repository->get_by_selectors($selectors);
		$rubrics_json = array_map(static fn(Rubric $rubric): array => [
			'id' => $rubric->get_id(),
			'name' => $rubric->get_name(),
			'category' => $rubric->get_category()
		], $rubrics);
		return $rubrics_json;
	}
}
