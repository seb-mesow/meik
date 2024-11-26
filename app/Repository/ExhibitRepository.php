<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Exhibit;
use PHPOnCouch\CouchClient;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Date;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPOnCouch\Exceptions\CouchException;
use PHPOnCouch\Exceptions\CouchNotFoundException;
use stdClass;

/**
 * @phpstan-type ExhibitDoc object{
 *     _id: string,
 *     _rev?: string,
 *     name: string,
 * }
 */
final class ExhibitRepository
{
    private const ID_PREFIX = "exhibit:";
    private Serializer $serializer;

    public function __construct(
        private readonly CouchClient $client
    ) {
        $this->serializer = SerializerBuilder::create()->build();
    }


    /**
     * @var string $id
     * @return array<Exhibit>
     */
    public function get_all(): array
    {
        $exhibits = $this->client->find([
            '_id' => ['$beginsWith' => self::ID_PREFIX],
        ])->docs;
        return $this->exhibitsFromArray($exhibits);
    }

	public function find(string $id): ?Exhibit {
		try {
			return $this->get($id);
		} catch (CouchNotFoundException $e) {
			return null;
		}
	}
	
	private function get(string $id): Exhibit {
		$couch_db_id = self::ID_PREFIX . $id;
		$exhibit_doc = $this->client->getDoc($couch_db_id);
		return $this->create_exhibit_from_doc($exhibit_doc);
	}

	public function insert(Exhibit $exhibit): Exhibit {
		assert(!$exhibit->get_rev());
		$doc = $this->create_doc_from_exhibit($exhibit);
		$response = $this->client->storeDoc($doc);
		$doc->_rev = $response->rev;
		return $this->create_exhibit_from_doc($doc);
	}
	
	public function update(Exhibit $exhibit): Exhibit {
		assert($exhibit->get_rev());
		try {
			$doc = $this->create_doc_from_exhibit($exhibit);
			$response = $this->client->storeDoc($doc);
			$doc->_rev = $response->rev;
			return $this->create_exhibit_from_doc($doc);
		} catch (Exception $e) {
			echo "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")<br>\n";
			throw $e;
		}
	}

    /**
     * @var string $id
     * @return Exhibit
     */
    public function delete(string $id): void {
        try {
            $doc = $this->client->getDoc($id);
        } catch (Exception $e) {
            echo "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")<br>\n";
        }
        // permanently remove the document
        try {
            $this->client->deleteDoc($doc);
        } catch (Exception $e) {
            echo "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")<br>\n";
        }
    }

	/**
	 * @param ExhibitDoc $exhibit_doc
	 */
	private function create_exhibit_from_doc(stdClass $exhibit_doc): Exhibit {
		$id = substr($exhibit_doc->_id, strlen(self::ID_PREFIX));
		return new Exhibit(
			id: $id,
			name: $exhibit_doc->name,
			rev: $exhibit_doc->_rev,
		);
	}

    /**
     * @var array<object> $array
     * @return array<Exhibit>
     */
    public function exhibitsFromArray($array): array
    {
        return $this->serializer->deserialize(json_encode($array), 'array<' . Exhibit::class . '>', "json");
    }

	private function create_doc_from_exhibit(Exhibit $exhibit): stdClass {
		$exhibit_doc = new stdClass();
		$exhibit_doc->_id = self::ID_PREFIX . $exhibit->get_id();
		if ($rev = $exhibit->get_rev()) {
			$exhibit_doc->_rev = $rev;
		}
		$exhibit_doc->name = $exhibit->get_name();
		// $exhibit_doc->manufacturer = $exhibit->get_manufacturer();
		// $exhibit_doc->year_of_construction = $exhibit->get_year_of_construction();
		// $exhibit_doc->aquiry_date = $exhibit->get_aquiry_date(); 
		// $exhibit_doc->text_blocks = $exhibit->get_text_blocks(); 
		return $exhibit_doc;
	}
}
