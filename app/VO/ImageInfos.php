<?php
declare(strict_types=1);

namespace App\VO;

use GdImage;

final class ImageInfos {
	public function __construct(
		public readonly string $file_data,
		public readonly int $width,
		public readonly int $height,
		public readonly string $content_type,
		public readonly GdImage $gd_image,
	) {}
}
