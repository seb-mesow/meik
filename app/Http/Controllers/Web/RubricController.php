<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Rubric;
use App\Repository\RubricRepository;
use Inertia\Inertia;
use JMS\Serializer\Serializer;

class RubricController extends Controller
{
	public function __construct(
		private readonly Serializer $serializer,
		private readonly RubricRepository $rubric_repository,
	) {}

	public function overview(string $category) {

		$selectors = [
			'category' => [
				'$eq' => $category
			]
			];

		$rubrics = $this->rubric_repository->get_rubrics_paginated($selectors);
		$array = array_map(static function(Rubric $rubric): array {
			return [
				'id' => $rubric->get_id(),
				'name' => $rubric->get_name(),
			];
		}, $rubrics);
		return Inertia::render('Rubric/RubricOverview', [
			'rubrics' => $array,
			'category_name' => $category
		]);
	}
}
