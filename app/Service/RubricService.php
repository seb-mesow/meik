<?php
declare(strict_types=1);

namespace App\Service;

use App\Models\Rubric;
use App\Repository\RubricRepository;

/**
 * @phpstan-type IRubricProps array{
 *     id: int,
 *     name: string,
 * }
 */
final class RubricService {
	public const int DEFAULT_COUNT_PER_PAGE = 20;
	
	public function __construct(
		private readonly RubricRepository $rubric_repository
	) {}
	
	/**
	 * @return array{
	 *     rubrics: IRubricProps[],
	 *     total_count: int,
	 * }
	 */
	public function determinate_props_of_multiple_paginated(string $category_id, int $page_number, int $count_per_page = self::DEFAULT_COUNT_PER_PAGE): array {
		['rubrics' => $rubrics, 'total_count' => $total_count] =
			$this->rubric_repository->get_rubrics_paginated($category_id, $page_number, $count_per_page);
		
		$rubric_props =  array_map(static fn(Rubric $rubric): array => self::determinate_props($rubric), $rubrics);
		
		return [
			'rubrics' => $rubric_props,
			'total_count' => $total_count
		];
	}
	
	/**
	 * @return IRubricProps
	 */
	private static function determinate_props(Rubric $rubric): array {
		return [
			'id' => $rubric->get_id(),
			'name' => $rubric->get_name(),
		];
	}
}
