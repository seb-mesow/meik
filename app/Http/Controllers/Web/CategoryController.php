<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Enum\Category;
use App\Service\RubricService;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class CategoryController extends Controller
{
	private const int RUBRIC_INITAL_COUNT_PER_PAGE = 20;
	
	public function __construct(
		private readonly RubricService $rubric_service,
	) {}
	
	public function overview(): InertiaResponse {
		$categories = array_map(static fn(Category $category): array => [
			'id' => $category->value,
			'name' => $category->get_name(),
		], Category::cases());
		
		return Inertia::render('Category/CategoryOverview', [
			'categories' => $categories
		]);
	}
	
	public function details(string $category_id): InertiaResponse {
		$category = Category::from($category_id);
		
		['rubrics' => $rubric_props ] = $this->rubric_service->query($category_id, 0, self::RUBRIC_INITAL_COUNT_PER_PAGE);
		
		$selectable_categories = $this->determinate_selectable_categories();
		
		return Inertia::render('Category/Category', [
			'selectable_categories' => $selectable_categories,
			'category' => [
				'id' => $category->get_id(),
				'name' => $category->get_name(),
			],
			'rubric_tiles_main_props' => [
				'rubric_tiles' => $rubric_props,
				'count_per_page' => self::RUBRIC_INITAL_COUNT_PER_PAGE,
			],
		]);
	}
	
	/**
	 * @return array{ id: string, name: string }[]
	 */
	private function determinate_selectable_categories(): array {
		$all_categories = Category::cases();
		return array_map(static fn (Category $category): array => [
			'id' => $category->get_id(),
			'name' => $category->get_name(),
		], $all_categories);
	}
}
