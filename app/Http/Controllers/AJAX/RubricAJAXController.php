<?php
declare(strict_types=1);

namespace App\Http\Controllers\AJAX;

use App\Http\Controllers\Controller;
use App\Models\Rubric;
use App\Repository\RubricRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RubricAJAXController extends Controller
{
	public function __construct(
		private readonly RubricRepository $rubric_repository
	) {}

	public function get_paginated(Request $request): JsonResponse {
		$page_number = (int) $request->query('page_number');
		$count_per_page = (int) $request->query('count_per_page');
		
		[ 'rubrics' => $rubrics, 'total_count' => $total_count ] =
			$this->rubric_repository->get_rubrics_paginated([], $page_number, $count_per_page);
		/** @var Rubric[] $rubrics */
		/** @var int $total_count */
		$rubrics_json = array_map(static fn(Rubric $rubric): array => [
			'id' => $rubric->get_id(),
			'name' => $rubric->get_name(),
		] , $rubrics);
		return response()->json([
			'rubrics' => $rubrics_json,
			'total_count' => $total_count
		]);
	}

	public function create(Request $request): JsonResponse {
		$name = (string) $request->input('val.name.val');
		
		$rubric = new Rubric(
			name: $name,
			category: 'sonstiges'
		);
		$this->rubric_repository->insert($rubric);
		return response()->json($rubric->get_id());
	}

	public function update(Request $request, string $rubric_id): void {
		$name = (string) $request->input('val.name.val');
		
		$rubric = $this->rubric_repository->get($rubric_id);
		$rubric->set_name($name);
		$this->rubric_repository->update($rubric);
	}

	// public function delete(string $rubric_id): void {
	// 	$this->rubric_repository->remove_by_id($rubric_id);
	// }
}
