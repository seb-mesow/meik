<?php
declare(strict_types=1);

namespace App\Repository;

use App\Exceptions\AttachmentNotFoundException;
use App\Models\Image;
use App\Repository\Traits\StringIdRepositoryTrait;
use App\Util\StringIdGenerator;
use App\VO\ImageInfos;
use Illuminate\Auth\Access\AuthorizationException;
use PHPOnCouch\CouchClient;
use stdClass;
use PHPOnCouch\Exceptions\CouchNotFoundException;

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
 *     image?: object{
 *         width: int,
 *         height: int,
 *     },
 *     thumbnail?: object{
 *         width: int,
 *         height: int,
 *     },
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
	
	public const string MODEL_TYPE_ID = "image";
	private const string IMAGE_ATTACHMENT_NAME = 'image';
	private const string THUMBNAIL_ATTACHMENT_NAME = 'thumbnail';
	public const string DEFAULT_IMAGE_CONTENT_TYPE = 'application/octet-stream';
	
	public function __construct(
		CouchClient $client,
		StringIdGenerator $string_id_generator,
	) {
		$this->client = $client;
		$this->string_id_generator = $string_id_generator;
	}
	
	/**
	 * @param string $image_id
	 * @return Image
	 * @throws CouchNotFoundException
	 */
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
		if (property_exists($image_doc, 'image')) {
			$image_width = $image_doc->image->width;
			$image_height = $image_doc->image->height;
		} else {
			$image_width = null;
			$image_height = null;
		}
		if (property_exists($image_doc, 'thumbnail')) {
			$thumbnail_width = $image_doc->thumbnail->width;
			$thumbnail_height = $image_doc->thumbnail->height;
		} else {
			$thumbnail_width = null;
			$thumbnail_height = null;
		}
		return new Image(
			description: $image_doc->description,
			is_public: $image_doc->is_public,
			image_width: $image_width,
			image_height: $image_height,
			thumbnail_width: $thumbnail_width,
			thumbnail_height: $thumbnail_height,
			attachments: property_exists($image_doc, '_attachments') ? $image_doc->_attachments : null,
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
		
		if (($image_width = $image->get_nullable_image_width()) !== null) {
			$image_doc->image = new stdClass();
			$image_doc->image->width = $image_width;
			$image_doc->image->height = $image->get_image_height();
		}
		if (($thumbnail_width = $image->get_nullable_thumbnail_width()) !== null) {
			$image_doc->thumbnail = new stdClass();
			$image_doc->thumbnail->width = $thumbnail_width;
			$image_doc->thumbnail->height = $image->get_thumbnail_height();
		}
		if ($attachments = $image->get_attachments()) {
			$image_doc->_attachments = $attachments;
		}
		return $image_doc;
	}
	
	/**
	 * @throws CouchNotFoundException
	 * @throws AttachmentNotFoundException
	 * @return FileInfos
	 */
	public function get_internal_file(string $image_id): array {
		return $this->get_attachment($image_id, self::IMAGE_ATTACHMENT_NAME, false);
	}
	
	/**
	 * @throws CouchNotFoundException
	 * @throws AttachmentNotFoundException
	 * @return FileInfos
	 */
	public function get_internal_thumbnail(string $image_id): array {
		return $this->get_attachment($image_id, self::THUMBNAIL_ATTACHMENT_NAME, false);
	}
	
	/**
	 * @throws AuthorizationException
	 * @throws CouchNotFoundException
	 * @throws AttachmentNotFoundException
	 * @return FileInfos
	 */
	public function get_public_file(string $image_id): array {
		return $this->get_attachment($image_id, self::IMAGE_ATTACHMENT_NAME, true);
	}
	
	/**
	 * @throws AuthorizationException
	 * @throws CouchNotFoundException
	 * @throws AttachmentNotFoundException
	 * @return FileInfos
	 */
	public function get_public_thumbnail(string $image_id): array {
		return $this->get_attachment($image_id, self::THUMBNAIL_ATTACHMENT_NAME, true);
	}
	
	/**
	 * @throws AuthorizationException
	 * @throws CouchNotFoundException
	 * @throws AttachmentNotFoundException
	 * @return FileInfos
	 */
	private function get_attachment(string $image_id, string $attachment_name, bool $must_be_public): array {
		// TODO fork php-on-couch and request only once (The Content-Type is in the header of the response of couchdb,)
		$doc_id = $this->determinate_doc_id_from_model_id($image_id);
		/** @var ImageDoc */
		$image_doc = $this->client->getDoc($doc_id);
		if ($must_be_public && !$image_doc->is_public) {
			throw new AuthorizationException("Image $image_id is not public");
		}
		if (!property_exists($image_doc, '_attachments')
		|| !property_exists($image_doc->_attachments, $attachment_name)) {
			throw new AttachmentNotFoundException($image_id, $attachment_name);
		}
		$content_type = $image_doc->_attachments->$attachment_name->content_type;
		$image_stub_doc = $this->create_stub_doc_from_doc_id($doc_id);
		try {
			$file = $this->client->getAttachment($image_stub_doc, $attachment_name);
		} catch (CouchNotFoundException $e) {
			throw new AttachmentNotFoundException($image_id, $attachment_name);
		}
		return [ 'content_type' => $content_type, 'file' => $file ];
	}
	
	// TODO always retrieve image type from user's file
	
	/**
	 * Image-Doc muss bereits in DB vorhanden sein!
	 * 
	 * Achtung! Lieber ImageService::set_file_and_thumbnail() nutzen
	 */
	public function set_image(string $image_id, ImageInfos $image_infos): void {
		$doc_id = $this->determinate_doc_id_from_model_id($image_id);
		$image_doc = $this->client->getDoc($doc_id); // nötig. um aktuelle rev zu bekommen
		
		// storeAttachment wirft KEINE Exception, wenn es zu einem Update-Konflikt kommt
		$this->client->storeAsAttachment(
			doc: $image_doc,
			data: $image_infos->file_data,
			filename: self::IMAGE_ATTACHMENT_NAME,
			contentType: $image_infos->content_type,
		);
		$image_doc = $this->client->getDoc($doc_id); // nötig. um aktuelle _attachments zu bekommen
		
		$image_doc->image = new stdClass();
		$image_doc->image->width = $image_infos->width;
		$image_doc->image->height = $image_infos->height;
		
		$this->client->storeDoc($image_doc);
	}
	
	/**
	 * Image-Doc muss bereits in DB vorhanden sein!
	 */
	public function set_thumbnail(string $image_id, ImageInfos $thumbnail_infos): void {
		$doc_id = $this->determinate_doc_id_from_model_id($image_id);
		$image_doc = $this->client->getDoc($doc_id); // nötig. um aktuelle rev zu bekommen
		
		// storeAttachment wirft KEINE Exception, wenn es zu einem Update-Konflikt kommt
		$this->client->storeAsAttachment(
			doc: $image_doc,
			data: $thumbnail_infos->file_data,
			filename: self::THUMBNAIL_ATTACHMENT_NAME,
			contentType: $thumbnail_infos->content_type,
		);
		$image_doc = $this->client->getDoc($doc_id); // nötig. um aktuelle _attachments zu bekommen
		
		$image_doc->thumbnail = new stdClass();
		$image_doc->thumbnail->width = $thumbnail_infos->width;
		$image_doc->thumbnail->height = $thumbnail_infos->height;
		
		$this->client->storeDoc($image_doc);
	}
}
