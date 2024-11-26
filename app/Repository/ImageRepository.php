<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Image;
use PHPOnCouch\CouchClient;
use Exception;
use Illuminate\Support\Facades\Date;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPOnCouch\Exceptions\CouchException;
use stdClass;

/**
 * @phpstan-type ImageDoc object{
 *     _id: string,
 *     _rev?: string,
 *     data: string,
 * }
 */
final class ImageRepository
{
    private const ID_PREFIX = "image:";
	
    private Serializer $serializer;

    public function __construct(
        private readonly CouchClient $client
    ) {
        $this->serializer = SerializerBuilder::create()->build();
    }

	public function get_by_hash(string $hash): Image {
		
	}
	
	/**
	 * @param ImageDoc $image_doc
	 */
	public function create_image_from_doc(stdClass $image_doc): Image
	{
		return new Image($image_doc->data, $image_doc->_rev);
	}
	
	/**
	 * @return ImageDoc
	 */
	public function create_doc_from_image(Image $image): stdClass
	{
		/** @var ImageDoc */
		$image_doc = new stdClass();
		$image_doc->_id = self::ID_PREFIX . $image->get_hash();
		if ($rev = $image->get_rev()) {
			$image_doc->_rev = $rev;
		}
		$image_doc->data = $image->get_data();
		return $image_doc;
	}

}
