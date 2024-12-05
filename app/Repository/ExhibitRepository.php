<?php
declare(strict_types=1);

namespace App\Repository;

use App\Models\Exhibit;
use App\Models\FreeText;
use PHPOnCouch\CouchClient;
use Exception;
use Illuminate\Support\Facades\Date;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPOnCouch\Exceptions\CouchException;
use PHPOnCouch\Exceptions\CouchNotFoundException;
use Illuminate\Support\Str;
use stdClass;

/**
 * @phpstan-type ExhibitDoc object{
 *     _id: string,
 *     _rev?: string,
 *     inventory_number: string,
 *     name: string,
 *     manufacturer: string,
 *     free_texts: FreeTextDoc[]
 * }
 * 
 * @phpstan-type FreeTextDoc object{
 *     heading: string,
 *     html: string,
 *     is_public: bool
 * }
 */
final class ExhibitRepository
{
	public const ID_PREFIX = "exhibit:";
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
		$res = $this->client->find([
			'_id' => ['$beginsWith' => self::ID_PREFIX],
		]);
		$_this = $this;
		return array_map(static function (stdClass $doc) use ($_this): Exhibit {
			return $_this->create_exhibit_from_doc($doc);
		}, $res->docs);
	}

	 /**
     * @var string $id
     * @return array<Location>
     */
    public function get_exhibits_paginated(int $page = 0, int $page_size = 10,): array
    {
        $locations = $this->client->limit($page_size)->skip($page * $page_size)->find([
            '_id' => ['$beginsWith' => self::ID_PREFIX],
        ])->docs;
        return $this->exhibitsFromArray($locations);
    }

	public function find(string $id): ?Exhibit {
		try {
			return $this->get($id);
		} catch (CouchNotFoundException $e) {
			return null;
		}
	}
	
	public function get(string $id): Exhibit {
		$couch_db_id = self::ID_PREFIX . $id;
		$exhibit_doc = $this->client->getDoc($couch_db_id);
		return $this->create_exhibit_from_doc($exhibit_doc);
	}

	/**
	 * ID darf noch nicht gesetzt sein
	 */
	public function insert(Exhibit $exhibit): Exhibit {
		assert(!$exhibit->get_id());
		assert(!$exhibit->get_rev());
		$doc = $this->create_doc_from_exhibit($exhibit); // setzt neue ID
		$response = $this->client->storeDoc($doc);
		$doc->_rev = $response->rev;
		return $this->create_exhibit_from_doc($doc);
	}
	
	public function update(Exhibit $exhibit): Exhibit {
		assert($exhibit->get_id());
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

	public function remove(Exhibit $exhibit): void {
		assert($exhibit->get_id());
		assert($exhibit->get_rev());
		$delete_doc = new stdClass();
		$delete_doc->_id = self::ID_PREFIX . $exhibit->get_id();
		$delete_doc->_rev = $exhibit->get_rev();
		try {
			$this->client->deleteDoc($delete_doc);
		} catch (Exception $e) {
			echo "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")<br>\n";
			throw $e;
		}
	}
	
	private function determinate_next_available_id(): string {
		// TODO letzte freie ID ermitteln
		return Str::uuid()->toString();
	}
	
	/**
	 * @return ExhibitDoc
	 */
	private function create_doc_from_exhibit(Exhibit $exhibit): stdClass {
		$exhibit_doc = new stdClass();
		
		$exhibit_doc->_id = self::ID_PREFIX . ($exhibit->get_id() ?? $this->determinate_next_available_id());
		if ($rev = $exhibit->get_rev()) {
			$exhibit_doc->_rev = $rev;
		}
		
		$exhibit_doc->inventory_number = $exhibit->get_inventory_number();
		$exhibit_doc->name = $exhibit->get_name();
		$exhibit_doc->manufacturer = $exhibit->get_manufacturer();
		$_this = $this;
		
		$free_text_docs = array_map(static function (FreeText $free_text) use ($_this): stdClass {
			return $_this->create_doc_from_free_text($free_text);
		}, $exhibit->get_free_texts());
		$exhibit_doc->free_texts = $free_text_docs;
		
		// $exhibit_doc->manufacturer = $exhibit->get_manufacturer();
		// $exhibit_doc->year_of_construction = $exhibit->get_year_of_construction();
		// $exhibit_doc->aquiry_date = $exhibit->get_aquiry_date(); 
		// $exhibit_doc->text_blocks = $exhibit->get_text_blocks();
		
		return $exhibit_doc;
	}
	
	/**
	 * @param ExhibitDoc $exhibit_doc
	 */
	private function create_exhibit_from_doc(stdClass $exhibit_doc): Exhibit {
		$id = substr($exhibit_doc->_id, strlen(self::ID_PREFIX));
		
		$_this = $this;
		$free_texts = array_map(static function (stdClass $free_text_doc) use ($_this): FreeText {
			return $_this->create_free_text_from_doc($free_text_doc);
		}, $exhibit_doc->free_texts);
		
		return new Exhibit(
			inventory_number: $exhibit_doc->inventory_number,
			name: $exhibit_doc->name,
			manufacturer: $exhibit_doc->manufacturer,
			free_texts: $free_texts,
			id: $id,
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

	private function create_doc_from_free_text(FreeText $free_text): stdClass {
		$free_text_doc =  new stdClass();
		$free_text_doc->heading = $free_text->get_heading();
		$free_text_doc->html = $free_text->get_html();
		$free_text_doc->is_public = $free_text->get_is_public();
		return $free_text_doc;
	}
	
	/**
	 * @param FreeTextDoc $free_text_doc
	 */
	private function create_free_text_from_doc(stdClass $free_text_doc): FreeText {
		return new FreeText(
			heading: $free_text_doc->heading,
			html: $free_text_doc->html,
			is_public: $free_text_doc->is_public,
		);
	}
}
