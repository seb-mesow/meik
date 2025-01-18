<?php
declare(strict_types=1);

namespace App\Repository;

use App\Models\ImageOrder;
use App\Repository\Traits\IntIdRepositoryTrait;
use App\Util\StringIdGenerator;
use PHPOnCouch\CouchClient;
use stdClass;

/**
 * @phpstan-type ImageOrderDoc object{
 *     _id: string,
 *     _rev?: string,
 *     image_ids: string[],
 * }
 */
final class ImageOrderRepository
{
	use IntIdRepositoryTrait;
	
	public const MODEL_TYPE_ID = "imageorder";
	
	public function __construct(
		CouchClient $client,
		StringIdGenerator $string_id_generator,
	) {
		$this->client = $client;
		$this->string_id_generator = $string_id_generator;
	}

	public function get(int $exhibit_id): ImageOrder {
		$doc_id = $this->determinate_doc_id_from_model_id($exhibit_id);
		$image_order_doc = $this->client->getDoc($doc_id);
		return $this->create_image_order_from_doc($image_order_doc);
	}
	
	public function insert(ImageOrder $image_order): void {
		assert($image_order->get_nullable_id()); // must have exhibit id;
		assert(!$image_order->get_nullable_rev());
		$doc = $this->create_doc_from_image_order($image_order);
		$response = $this->client->storeDoc($doc);
		$image_order->set_rev($response->rev);
	}
	
	public function update(ImageOrder $image_order): void {
		assert($image_order->get_id());
		assert($image_order->get_rev());
		$doc = $this->create_doc_from_image_order($image_order);
		$response = $this->client->storeDoc($doc);
		$image_order->set_rev($response->rev);
	}
	
	public function remove_by_id(int $exhibit_id): void {
		$doc_id = $this->determinate_doc_id_from_model_id($exhibit_id);
		$doc = $this->client->getDoc($doc_id); // retrieves _rev
		$this->client->deleteDoc($doc);
	}
	
	/**
	 * @param ImageOrderDoc $image_doc
	 */
	public function create_image_order_from_doc(stdClass $image_order_doc): ImageOrder {
		return new Imageorder(
			image_ids: $image_order_doc->image_ids,
			id: $this->determinate_model_id_from_doc($image_order_doc),
			rev: $image_order_doc->_rev,
		);
	}
	
	/**
	 * @return ImageOrderDoc
	 */
	public function create_doc_from_image_order(ImageOrder $image_order): stdClass {
		/** @var ImageOrderDoc */
		$image_order_doc = $this->create_stub_doc_from_model($image_order);
		$image_order_doc->image_ids = $image_order->get_image_ids();
		return $image_order_doc;
	}

}
