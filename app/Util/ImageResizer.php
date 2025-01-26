<?php
declare(strict_types=1);

namespace App\Util;

use App\Exceptions\UnknownImageFormatException;
use RuntimeException;

/**
 * @phpstan-type ImageInfos array{
 *     data: string,
 *     width: int,
 *     height: int,
 * }
 */
class ImageResizer {
	
	// Definition: 20 % der Seitenlängen von FullHD
	// Größe Originalbild: max. 5,933 MiB (1920 x 1080 px)
	// Größe Thumbnails: max. 0,237 MiB (384 x 216 px)
	// --> Verringerung der Größe auf 3,995 % des Originalbildes
	
	private const int MAX_THUMBNAIL_LENGTH = 200;
	
	/**
	 * Output ist immer ein PNG-Bild
	 * 
	 * @param string $image_data
	 * @throws UnknownImageFormatException
	 * @throws RuntimeException
	 * @return
	 */
	public function create_thumbnail(string $image_data): string {
		// error_log("ImageResizer::create_thumbnail(): start");
		// error_log("ImageResizer::create_thumbnail(): size of image: ".(strlen($image_data)/1024)." kiB");
		$time_begin = microtime(true);
		$image = imagecreatefromstring($image_data);
		if (!$image) {
			throw new UnknownImageFormatException();
		}
		
		$o_w = imagesx($image);
		$o_h = imagesy($image);
				
		if ($o_w <= self::MAX_THUMBNAIL_LENGTH) {
			if ($o_h <= self::MAX_THUMBNAIL_LENGTH) {
				return $image_data;
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
		
		$thumbnail = imagescale($image, $t_w);
		if (!$thumbnail) {
			throw new RuntimeException('scaling image failed');
		}
		
		// ob_start();
		$stream = fopen('php://memory', 'r+');
		imagepng($thumbnail, $stream, 0);
		// Keine Kompression -> schnellers entpacken und darstellen
		rewind($stream);
		$thumbnail_data = stream_get_contents($stream);
		fclose($stream);
		if (strlen($thumbnail_data) > strlen($image_data)) {
			$thumbnail_data = $image_data;
		}
		$time_end = microtime(true);
		// error_log("ImageResizer::create_thumbnail(): size of thumbnail ".(strlen($thumbnail_data)/(1024))." kiB");
		// error_log("ImageResizer::create_thumbnail(): dimen of thumbnail ".(imagesx($thumbnail))."x".(imagesy($thumbnail))." (WxH)");
		error_log("ImageResizer::create_thumbnail(): took ". ($time_end - $time_begin)." s");
		// error_log("ImageResizer::create_thumbnail(): end");
		return $thumbnail_data;
	}
}
