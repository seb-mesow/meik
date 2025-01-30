<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Enum\Category;
use App\Models\Rubric;
use App\Repository\RubricRepository;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use JMS\Serializer\Serializer;

class RubricController extends Controller
{
	public function __construct(
		private readonly Serializer $serializer,
		private readonly RubricRepository $rubric_repository,
	) {}

	public function overview(string $category_id): InertiaResponse
	{
		$category = Category::from($category_id);
		
		['rubrics' => $rubrics, 'total_count' => $total_count] =
			$this->rubric_repository->get_rubrics_paginated($category_id, 0, 50);
		
		$rubrics_json = array_map(static fn(Rubric $rubric): array => [
			'id' => $rubric->get_id(),
			'name' => $rubric->get_name()
		], $rubrics);
		
		return Inertia::render('Rubric/RubricOverview', [
			'rubrics' => $rubrics_json,
			'category' => [
				'id' => $category->value,
				'name' => $category->get_pretty_name(),
			]
			// 'total_count' => $total_count
		]);
	}
}
