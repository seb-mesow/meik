<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Exhibit;
use App\Models\Image;
use App\Repository\ExhibitRepository;
use App\Repository\ImageOrderRepository;
use App\Repository\ImageRepository;
use App\Repository\RubricRepository;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

/**
 * @phpstan-type ImageInitPageProps array{
 *     id: string,
 *     description: string,
 *     is_public: bool,
 * }
 */
class ImagesController extends Controller
{
	public function __construct(
		private readonly ExhibitRepository $exhibit_repository,
		private readonly ImageOrderRepository $image_order_repository,
		private readonly RubricRepository $rubric_repository,
		private readonly ImageRepository $image_repository,
	) {}
	
	public function details(int $exhibit_id): InertiaResponse {
		$exhibit = $this->exhibit_repository->get($exhibit_id);
		$images = $this->create_images_page_props($exhibit);
		
		$rubric = $this->rubric_repository->get($exhibit->get_rubric_id());
		$category = $rubric->get_category();
		
		return Inertia::render('Exhibit/Images', [
			'name' => $exhibit->get_name(),
			'init_props' => [
				'exhibit_id' => $exhibit->get_id(),
				'images' => $images,
			],
			'category' => [
				'id' => $category->value,
				'name' => $category->get_pretty_name()
			],
			'rubric' => [
				'id' => $rubric->get_id(),
				'name' => $rubric->get_name()
			]
		]);
	}
	
	/**
	 * @return ImageInitPageProps[]
	 */
	private function create_images_page_props(Exhibit $exhibit): array {
		$images = $this->image_repository->get_images($exhibit->get_id());
		return array_map(static fn(Image $image): array => [
			'id' => $image->get_id(),
			'description' => $image->get_description(),
			'is_public' => $image->get_is_public(),
		], $images);
	}

}
