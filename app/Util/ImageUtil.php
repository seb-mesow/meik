<?php
declare(strict_types=1);

namespace App\Util;

use App\Exceptions\UnknownImageFormatException;
use App\VO\ImageInfos;
use RuntimeException;

final class ImageUtil {
	
	// Definition: 20 % der Seitenlängen von FullHD
	// Größe Originalbild: max. 5,933 MiB (1920 x 1080 px)
	// Größe Thumbnails: max. 0,237 MiB (384 x 216 px)
	// --> Verringerung der Größe auf 3,995 % des Originalbildes
	
	private const int MAX_THUMBNAIL_LENGTH = 200;
	
	/**
	 * @param string $image_data
	 * @return ImageInfos
	 * 
	 * @throws UnknownImageFormatException
	 */
	public function create_image_infos(string $image_data, string $content_type): ImageInfos {
		$gd_image = imagecreatefromstring($image_data);
		if (!$gd_image) {
			throw new UnknownImageFormatException();
		}
		return new ImageInfos(
			$image_data,
			imagesx($gd_image),
			imagesy($gd_image),
			$content_type,
			$gd_image
		);
	}
	
	/**
	 * @param ImageInfos $image_infos
	 * @return ImageInfos
	 * 
	 * @throws UnknownImageFormatException
	 * @throws RuntimeException
	 */
	public function create_thumbnail(ImageInfos $image_infos): ImageInfos {
		$time_begin = microtime(true);
		
		$o_w = $image_infos->width;
		$o_h = $image_infos->height;
				
		if ($o_w <= self::MAX_THUMBNAIL_LENGTH) {
			if ($o_h <= self::MAX_THUMBNAIL_LENGTH) {
				return $image_infos;
			} else {
				$f = self::MAX_THUMBNAIL_LENGTH/$o_h;
			}
		} else {
			if ($o_h <= self::MAX_THUMBNAIL_LENGTH) {
				$f = self::MAX_THUMBNAIL_LENGTH/$o_w;
			} else {
				if ($o_w >= $o_h) {
					$f = self::MAX_THUMBNAIL_LENGTH/$o_w;
				} else {
					$f = self::MAX_THUMBNAIL_LENGTH/$o_h;
				}
			}
		}
		
		$t_w = intval($o_w * $f);
		
		$gd_thumbnail = imagescale($image_infos->gd_image, $t_w);
		if (!$gd_thumbnail) {
			throw new RuntimeException('scaling image failed');
		}
		
		$stream = fopen('php://memory', 'r+');
		imagepng($gd_thumbnail, $stream, 0);
		// Keine Kompression -> schnellers entpacken und darstellen
		rewind($stream);
		$thumbnail_file_data = stream_get_contents($stream);
		fclose($stream);
		
		if (strlen($thumbnail_file_data) > strlen($image_infos->file_data)) {
			$thumbnail_infos = $image_infos;
		} else {
			$thumbnail_infos = new ImageInfos(
				$thumbnail_file_data,
				imagesx($gd_thumbnail),
				imagesy($gd_thumbnail),
				'image/png',
				$gd_thumbnail
			);
		}
		
		error_log("ImageResizer::create_thumbnail(): took ". (microtime(true) - $time_begin)." s");
		return $thumbnail_infos;
	}
}
