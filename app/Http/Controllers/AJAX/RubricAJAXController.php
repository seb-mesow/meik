<?php

declare(strict_types=1);

namespace App\Http\Controllers\AJAX;

use App\Http\Controllers\Controller;
use App\Models\Enum\Category;
use App\Models\Rubric;
use App\Repository\RubricRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class RubricAJAXController extends Controller
{
	private Serializer $serializer;

	public function __construct(
		private readonly RubricRepository $rubric_repository
	) {
		$this->serializer = SerializerBuilder::create()->build();
	}

	public function get_paginated(Request $request): JsonResponse
	{
		$page = (int) $request->query('page');
		$page_size = (int) $request->query('page_size');
		$category = $request->query('category');

		['rubrics' => $rubrics, 'total_count' => $total_count] =
			$this->rubric_repository->get_rubrics_paginated($category, $page, $page_size);
		/** @var Rubric[] $rubrics */
		/** @var int $total_count */
		$rubrics_json = array_map(static fn(Rubric $rubric): array => [
			'id' => $rubric->get_id(),
			'name' => $rubric->get_name(),
		], $rubrics);
		return response()->json([
			'rubrics' => $rubrics_json,
			'total_count' => $total_count
		]);
	}

	public function create(Request $request): JsonResponse
	{
		$name = $request->input('name');
		$category = $request->input('category');
		
		$category = Category::from($category);
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
		$category = $request->input('category');
		
		$category = Category::from($category);
		$rubric = $this->rubric_repository->get($rubric_id);
		$rubric->set_name($name);
		$rubric->set_category($category);
		$this->rubric_repository->update($rubric);
	}

	public function delete(string $rubric_id): void {
		$this->rubric_repository->remove_by_id($rubric_id);
	}
}
