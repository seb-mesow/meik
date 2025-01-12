<?php
declare(strict_types=1);

namespace App\Http\Controllers\AJAX;

use App\Http\Controllers\Controller;
use App\Repository\ImageRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ImageAJAXController extends Controller
{
	public function __construct(
		private readonly ImageRepository $image_repository
	) {}
	
	public function create(): JsonResponse {
		return response()->json();
	}
	
	public function delete(): JsonResponse {
		return response()->json();
	}
	
	public function move(): JsonResponse {
		return response()->json();
	}
	
	public function get_meta_data(): JsonResponse {
		return response()->json();
	}
	
	public function update_meta_data(): JsonResponse {
		return response()->json();
	}
	
	public function get_file(string $image_id): Response {
		[ 'content_type' => $content_type, 'file' => $file ] = $this->image_repository->get_file($image_id);
		return response($file)
			->header('Content-Type', $content_type);
	}
	
	public function set_file(string $image_id): JsonResponse {
		return response()->json();
	}
	
	public function get_thumbnail_file(string $image_id): Response {
		[ 'content_type' => $content_type, 'file' => $file ] = $this->image_repository->get_thumbnail($image_id);
		return response($file)
			->header('Content-Type', $content_type);
	}
}
