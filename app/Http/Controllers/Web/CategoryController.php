<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Enum\Category;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class CategoryController extends Controller
{
	public function overview(): InertiaResponse {
		$categories = array_map(static fn(Category $category): array => [
			'id' => $category->value,
			'name' => $category->get_pretty_name(),
		], Category::cases());
		
		return Inertia::render('Category/CategoryOverview', [
			'categories' => $categories
		]);
	}
}
