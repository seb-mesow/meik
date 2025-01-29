<?php
declare(strict_types=1);

namespace App\Service;

use App\Models\Exhibit;
use App\Models\Image;
use App\Repository\ImageRepository;
use App\Util\ImageUtil;

final class ImageService {
	public function __construct(
		private readonly ImageRepository $image_repository,
		private readonly ImageUtil $image_util,
	) {}
	
	/**
	 * @param Exhibit $exhibit
	 * @return Image[]
	 */
	public function get_all_public_images(Exhibit $exhibit): array {
		$images = $this->image_repository->get_images($exhibit->get_id());
		return array_filter($images, static fn(Image $image): bool => $image->get_is_public());
	}
	
	public function get_public_title_image(Exhibit $exhibit): ?Image {
		$images = $this->image_repository->get_images($exhibit->get_id());
		$title_image = null;
		// öffentliches Titelbild suchen
		foreach ($images as $image) {
			if ($image->get_is_public()) {
				$title_image = $image;
				break;
			}
		}
		return $title_image;
	}
	
	public function get_internal_title_image(Exhibit $exhibit): ?Image {
		$images = $this->image_repository->get_images($exhibit->get_id());
		$title_image = null;
		// zuerst öffentliches Titelbild suchen
		foreach ($images as $image) {
			if ($image->get_is_public()) {
				$title_image = $image;
				break;
			}
		}
		// als Ersatz alternativ nach dem ersten Bild suchen
		if (!$title_image) {
			foreach ($images as $image) {
				$title_image = $image;
				break;
			}
		}
		return $title_image;
	}
	
	public function set_file_and_thumbnail(string $image_id, string $image_data, string $content_type) {
		$image_infos = $this->image_util->create_image_infos($image_data, $content_type);
		$this->image_repository->set_image($image_id, $image_infos);
		
		$thumbnail_infos = $this->image_util->create_thumbnail($image_infos);
		$this->image_repository->set_thumbnail($image_id, $thumbnail_infos);
	}
}
