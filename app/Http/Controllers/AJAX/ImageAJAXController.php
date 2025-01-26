<?php
declare(strict_types=1);

namespace App\Http\Controllers\AJAX;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Repository\ImageOrderRepository;
use App\Repository\ImageRepository;
use App\Service\ImageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ImageAJAXController extends Controller
{
	public function __construct(
		private readonly ImageRepository $image_repository,
		private readonly ImageOrderRepository $image_order_repository,
		private readonly ImageService $image_service,
	) {}
	
	public function create(Request $request, int $exhibit_id): JsonResponse {
		$index = (int) $request->input('index');
		$description = $request->input('description');
		$is_public = $request->input('is_public') === "true";
		
		$image = new Image(
			description: $description, 
			is_public: $is_public,
		);

		$this->image_repository->insert($image);
		$image_id = $image->get_id();
		$image_order = $this->image_order_repository->get($exhibit_id);
		
		$file = $request->file('image');
		$this->image_service->set_file_and_thumbnail($image_id, $file->getContent(), $file->getClientMimeType());
		
		$image_order->insert_image_id($image_id, $index);
		$this->image_order_repository->update($image_order);
		$ids_order = $image_order->get_image_ids();
		
		return response()->json([
			'id' => $image_id,
			'ids_order' => $ids_order,
		]);
	}
	
	public function update(Request $request, string $image_id): Response {
		$description = $request->input('description');
		$is_public = $request->input('is_public') === "true";
		
		$image = $this->image_repository->get($image_id);
		$image->set_description($description);
		$image->set_is_public($is_public);
		$this->image_repository->update($image);
		
		if ($request->hasFile('image')) {
			$file = $request->file('image');
			$this->image_service->set_file_and_thumbnail($image_id, $file->getContent(), $file->getClientMimeType());
		}
		
		return response()->make();
	}
	
	public function delete(int $exhibit_id, string $image_id): JsonResponse {
		$this->image_repository->remove_by_id($image_id);
		$image_order = $this->image_order_repository->get($exhibit_id);
		$image_order->remove_image_id($image_id);
		$this->image_order_repository->update($image_order);
		$ids_order = $image_order->get_image_ids();
		return response()->json($ids_order);
	}
	
	public function move(Request $request, int $exhibit_id, string $image_id): JsonResponse {
		$new_index = $request->input();
		$image_order = $this->image_order_repository->get($exhibit_id);
		$image_order->move_image_id($image_id, $new_index);
		$this->image_order_repository->update($image_order);
		$ids_order = $image_order->get_image_ids();
		return response()->json($ids_order);
	}
	
	public function get_image(string $image_id): Response {
		[ 'content_type' => $content_type, 'file' => $file ] = $this->image_repository->get_internal_file($image_id);
		return response($file)
			->header('Content-Type', $content_type);
	}
	
	public function get_thumbnail(string $image_id): Response {
		[ 'content_type' => $content_type, 'file' => $file ] = $this->image_repository->get_internal_thumbnail($image_id);
		return response($file)
			->header('Content-Type', $content_type);
	}
}
