<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FreeText;
use App\Repository\CategoryRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;
use JMS\Serializer\Serializer;

class CategoryController extends Controller
{
	public function __construct(
		private readonly Serializer $serializer,
	) {}

	public function overview() {
		$categorys = ['Hardware', 'Software', 'Buch', 'Sonstiges'];
		return Inertia::render('Category/CategoryOverview', [
			'categorys' => $categorys
		]);
	}
}
