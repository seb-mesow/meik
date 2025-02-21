<?php
declare(strict_types=1);

namespace App\Service;

use App\Models\Rubric;
use App\Repository\RubricRepository;

/**
 * @phpstan-type IRubricProps array{
 *     id: int,
 *     name: string,
 *     category_id: string,
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
	public function query(?string $category_id = null, ?int $page_number = null, ?int $count_per_page = null): array {
		$count_per_page ??= self::DEFAULT_COUNT_PER_PAGE;
		
		$result = $this->rubric_repository->query($category_id, $page_number, $count_per_page);
		
		$result['rubrics'] =  array_map(static fn(Rubric $rubric): array => self::determinate_props($rubric), $result['rubrics']);
		
		return $result;
	}
	
	/**
	 * @return IRubricProps
	 */
	private static function determinate_props(Rubric $rubric): array {
		return [
			'id' => $rubric->get_id(),
			'name' => $rubric->get_name(),
			'category_id' => $rubric->get_category()->get_id(),
		];
	}
}
