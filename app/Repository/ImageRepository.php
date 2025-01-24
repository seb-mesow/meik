<?php
declare(strict_types=1);

namespace App\Repository;

use App\Models\Image;
use App\Repository\Traits\StringIdRepositoryTrait;
use App\Util\StringIdGenerator;
use PHPOnCouch\CouchClient;
use stdClass;

/**
 * @phpstan-type AttachmentDoc object{
 *     content_type: string,
 *     digest: string,
 *     length: int,
 *     revpos: int,
 *     stub: true,
 * }
 * @phpstan-type ImageDoc object{
 *     _id: string,
 *     _rev?: string,
 *     description: string,
 *     is_public: string,
 *     _attachments?: object{
 *         image: AttachmentDoc,
 *         thumbnail: AttachmentDoc,
 *     }
 * }
 * @phpstan-type FileInfos array{
 *     content_type: string,
 *     file: string,
 * }
 */
final class ImageRepository
{
	use StringIdRepositoryTrait;
	
	private const string MODEL_TYPE_ID = "image";
	private const string ORIGINAL_IMAGE_ATTACHMENT_NAME = 'image';
	private const string THUMBNAIL_ATTACHMENT_NAME = 'thumbnail';
	public const string DEFAULT_IMAGE_CONTENT_TYPE = 'application/octet-stream';
	
	public function __construct(
		CouchClient $client,
		StringIdGenerator $string_id_generator,
	) {
		$this->client = $client;
		$this->string_id_generator = $string_id_generator;
	}

	public function get(string $image_id): Image {
		$doc_id = $this->determinate_doc_id_from_model_id($image_id);
		$image_doc = $this->client->getDoc($doc_id);
		return $this->create_image_from_doc($image_doc);
	}
	
	public function insert(Image $image): void {
		assert(!$image->get_nullable_id());
		assert(!$image->get_nullable_rev());	
		$doc = $this->create_doc_from_image($image);
		$response = $this->client->storeDoc($doc);
		$image->set_rev($response->rev);
	}
	
	public function update(Image $image): void {
		assert($image->get_id());
		assert($image->get_rev());
		$doc = $this->create_doc_from_image($image);
		$response = $this->client->storeDoc($doc);
		$image->set_rev($response->rev);
	}
	
	public function remove_by_id(string $image_id): void {
		$doc_id = $this->determinate_doc_id_from_model_id($image_id);
		$doc = $this->client->getDoc($doc_id); // retrieves _rev
		$this->client->deleteDoc($doc);
	}
	
	/**
	 * @param int $exhibit_id
	 * @return Image[]
	 */
	public function get_images(int $exhibit_id): array {
		$response = $this->client
			->startkey([$exhibit_id]) // @phpstan-ignore-line
			->endkey([$exhibit_id+1]) // @phpstan-ignore-line
			->inclusive_end(false)
			->include_docs(true) // @phpstan-ignore-line
			->getView(ImageOrderRepository::MODEL_TYPE_ID, 'by-exhibit-id-to-image-docs');
		$images = [];
		foreach ($response->rows as $image_doc) {
			$images[] = $this->create_image_from_doc($image_doc->doc);
		}
		return $images;
	}
	
	/**
	 * @param ImageDoc $image_doc
	 */
	public function create_image_from_doc(stdClass $image_doc): Image {
		return new Image(
			description: $image_doc->description,
			is_public: $image_doc->is_public,
			id: $this->determinate_model_id_from_doc($image_doc), 
			rev: $image_doc->_rev,
		);
	}
	
	/**
	 * @return ImageDoc
	 */
	public function create_doc_from_image(Image $image): stdClass {
		/** @var ImageDoc */
		$image_doc = $this->create_stub_doc_from_model($image);
		$image_doc->description = $image->get_description();
		$image_doc->is_public = $image->get_is_public();
		return $image_doc;
	}
	
	/**
	 * @return FileInfos
	 */
	public function get_file(string $image_id): array {
		// TODO fork php-on-couch and request only once (The Content-Type is in the header of the response of couchdb,)
		$doc_id = $this->determinate_doc_id_from_model_id($image_id);
		/** @var ImageDoc */
		$image_doc = $this->client->getDoc($doc_id);
		$content_type = $image_doc->_attachments->image->content_type;
		$image_stub_doc = $this->create_stub_doc_from_doc_id($doc_id);
		$file = $this->client->getAttachment($image_stub_doc, self::ORIGINAL_IMAGE_ATTACHMENT_NAME);
		return [ 'content_type' => $content_type, 'file' => $file ];
	}
	
	/**
	 * @return FileInfos
	 */
	public function get_thumbnail(string $image_id): array {
		// TODO fork php-on-couch and request only once (The Content-Type is in the header of the response of couchdb,)
		$doc_id = $this->determinate_doc_id_from_model_id($image_id);
		/** @var ImageDoc */
		$image_doc = $this->client->getDoc($doc_id);
		$content_type = $image_doc->_attachments->thumbnail->content_type;
		$image_stub_doc = $this->create_stub_doc_from_doc_id($doc_id);
		$file = $this->client->getAttachment($image_stub_doc, self::THUMBNAIL_ATTACHMENT_NAME);
		return [ 'content_type' => $content_type, 'file' => $file ];
	}
	

	
	// TODO always retrieve image type from user's file
	
	/**
	 * Image-Doc muss bereits in DB vorhanden sein!
	 */
	public function insert_file(string $image_id, string $image_data, string $content_type = self::DEFAULT_IMAGE_CONTENT_TYPE): void {
		$doc_id = $this->determinate_doc_id_from_model_id($image_id);
		$image_doc = $this->client->getDoc($doc_id);
		$this->client->storeAsAttachment(
			doc: $image_doc,
			data: $image_data,
			filename: self::ORIGINAL_IMAGE_ATTACHMENT_NAME,
			contentType: $content_type,
		);
	}
	
	/**
	 * Image-Doc muss bereits in DB vorhanden sein!
	 */
	public function update_file(string $image_id, string $image_data, string $content_type = self::DEFAULT_IMAGE_CONTENT_TYPE): void {
		$doc_id = $this->determinate_doc_id_from_model_id($image_id);
		$image_doc = $this->client->getDoc($doc_id);
		$this->client->storeAsAttachment(
			doc: $image_doc,
			data: $image_data,
			filename: self::ORIGINAL_IMAGE_ATTACHMENT_NAME,
			contentType: $content_type,
		);
	}
	
	/**
	 * Image-Doc muss bereits in DB vorhanden sein!
	 */
	public function insert_thumbnail(string $image_id, string $thumbnail_data, string $content_type = self::DEFAULT_IMAGE_CONTENT_TYPE): void {
		$doc_id = $this->determinate_doc_id_from_model_id($image_id);
		$image_doc = $this->client->getDoc($doc_id);
		$this->client->storeAsAttachment(
			doc: $image_doc,
			data: $thumbnail_data,
			filename: self::THUMBNAIL_ATTACHMENT_NAME,
			contentType: $content_type,
		);
	}
	
	/**
	 * Image-Doc muss bereits in DB vorhanden sein!
	 */
	public function update_thumbnail(string $image_id, string $thumbnail_data, string $content_type = self::DEFAULT_IMAGE_CONTENT_TYPE): void {
		$doc_id = $this->determinate_doc_id_from_model_id($image_id);
		$image_doc = $this->client->getDoc($doc_id);
		$this->client->storeAsAttachment(
			doc: $image_doc,
			data: $thumbnail_data,
			filename: self::THUMBNAIL_ATTACHMENT_NAME,
			contentType: $content_type,
		);
	}
}
