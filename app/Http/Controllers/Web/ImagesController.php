<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Exhibit;
use App\Repository\ExhibitRepository;
use App\Repository\ImageOrderRepository;
use App\Repository\ImageRepository;
use Inertia\Inertia;
use Inertia\Response;

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
		private readonly ImageRepository $image_repository,
	) {}
	
	public function details(int $exhibit_id): Response {
		$exhibit = $this->exhibit_repository->get($exhibit_id);
		$images = $this->create_images_page_props($exhibit);
		return Inertia::render('Exhibit/Images', [
			'name' => $exhibit->get_name(),
			'init_props' => [
				'exhibit_id' => $exhibit->get_id(),
				'images' => $images,
			]
		]);
	}
	
	/**
	 * @return ImageInitPageProps[]
	 */
	private function create_images_page_props(Exhibit $exhibit): array {
		$images_order = $this->image_order_repository->get($exhibit->get_id());
		/** @var ImageInitPageProps[] */
		$page_props = [];
		foreach ($images_order->get_image_ids() as $image_id) {
			// potential performance bottleneck
			$image = $this->image_repository->get($image_id);
			$page_props[] = [
				'id' => $image->get_id(),
				'description' => $image->get_description(),
				'is_public' => $image->get_is_public(),
			];
		}
		return $page_props;
	}

}
