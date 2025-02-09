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

	public function get_paginated(Request $request): JsonResponse
	{
		$category_id = (string) $request->query('category_id');
		$page_number = (int) $request->query('page_number');
		
		[ 'rubrics' => $rubrics ] = $this->rubric_service->determinate_props_of_multiple_paginated($category_id, $page_number);
		
		return response()->json($rubrics);
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
}
