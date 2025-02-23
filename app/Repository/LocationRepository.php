<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Location;
use App\Repository\Traits\StringIdRepositoryTrait;
use App\Util\StringIdGenerator;
use PHPOnCouch\CouchClient;
use PHPOnCouch\Exceptions\CouchNotFoundException;
use stdClass;

/**
 * Die _id wird bei neuen Docs sofort gesetzt.
 * @phpstan-type LocationDoc object{
 *     _id: string,
 *     _rev?: string,
 *     name: string,
 *     is_public: bool
 * }
 */
final class LocationRepository
{
	use StringIdRepositoryTrait;
	
	public const string MODEL_TYPE_ID = 'location';

	public function __construct(
		CouchClient $client,
		StringIdGenerator $string_id_generator,
	) {
		$this->client = $client;
		$this->string_id_generator = $string_id_generator;
	}
	
	/**
	 * @deprecated use `query()` instead
	 * @var string $id
	 * @return Location[]
	 */
	public function get_all(): array
	{
		$res = $this->client
			->limit(PHP_INT_MAX)
			->find([
			'_id' => [
				'$beginsWith' => self::ID_PREFIX
			],
		]);
		return $this->create_locations_from_docs($res->docs);
	}
	
	/**
	 * @return array{
	 *     locations: Location[],
	 *     total_count: int
	 * }
	 */
	public function query(?int $page_number = null, ?int $count_per_page = null): array {
		$client = $this->client;
		if ($page_number !== null && $count_per_page !== null) {
			$client = $client
				->limit($count_per_page)
				->skip($page_number * $count_per_page);
		}
		
		$response = $client
			->reduce(false)
			->include_docs(true)
			->getView(self::MODEL_TYPE_ID, 'all');
		
		$locations = $this->create_locations_from_view_response($response);
		
		return [
			'locations' => $locations,
			'total_count' => $response->total_rows, // nur eine Request nötig, da zur Zeit keine Suchkriterien angebar
		];
	}
	
	public function find_by_name(string $name): ?Location {
		$response = $this->client
			->key($name)
			->include_docs(true)
			->getView(self::MODEL_TYPE_ID, 'all');
		if ($cnt = count($response->rows) > 0) {
			assert($cnt === 1);
			return $this->create_location_from_doc($response->rows[0]->doc);
		}
		return null;
	}
	
	public function find(string $id): ?Location {
		try {
			return $this->get($id);
		} catch (CouchNotFoundException $e) {
			return null;
		}
	}
	
	public function get(string $id): Location {
		// Das Caching bei Places und Locations erspart ca. 750 ms .
		$_this = $this;
		return $this->cached(__FUNCTION__, $id, static function(string $_id) use ($_this): Location {
			$doc_id = $_this->determinate_doc_id_from_model_id($_id);
			$location_doc = $_this->client->getDoc($doc_id);
			return $_this->create_location_from_doc($location_doc);
		}, $id);
	}
	
	public function insert(Location $location): void {
		assert(!$location->get_nullable_id());
		assert(!$location->get_nullable_rev());	
		$doc = $this->create_doc_from_location($location);
		$response = $this->client->storeDoc($doc);
		$location->set_rev($response->rev);
	}
	
	public function update(Location $location): void {
		assert($location->get_id());
		assert($location->get_rev());
		$doc = $this->create_doc_from_location($location);
		$response = $this->client->storeDoc($doc);
		$location->set_rev($response->rev);
	}
	
	public function remove(Location $location): void {
		assert($location->get_id());
		assert($location->get_rev());
		$delete_doc = $this->create_stub_doc_from_model($location);
		$this->client->deleteDoc($delete_doc);
	}
	
	public function remove_by_id(string $location_id): void {
		$doc_id = $this->determinate_doc_id_from_model_id($location_id);
		$doc = $this->client->getDoc($doc_id); // retrieves _rev
		$this->client->deleteDoc($doc);
	}

	/**
	 * @param LocationDoc $location_doc
	 */
	private function create_location_from_doc(stdClass $location_doc): Location {
		return new Location(
			name: $location_doc->name,
			is_public: $location_doc->is_public,
			id: $this->determinate_model_id_from_doc($location_doc),
			rev: $location_doc->_rev,
		);
	}

	/**
	 * @param LocationDoc[] $location_docs
	 * @return Location[]
	 */
	private function create_locations_from_docs(array $location_docs): array {
		$_this = $this;
		return array_map(static function(stdClass $location_doc) use ($_this): Location {
			return $_this->create_location_from_doc($location_doc);
		}, $location_docs);
	}

	/**
	 * @return LocationDoc
	 */
	private function create_doc_from_location(Location $location): stdClass {
		/** @var LocationDoc */
		$location_doc = $this->create_stub_doc_from_model($location);
		$location_doc->name = $location->get_name();
		$location_doc->is_public = $location->get_is_public();
		return $location_doc;
	}
	
	/**
	 * @return Location[]
	 */
	private function create_locations_from_view_response(stdClass $response): array {
		$_this = $this;
		return array_map(static function(stdClass $row) use ($_this): Location {
			return $_this->create_location_from_doc($row->doc);
		}, $response->rows);
	}
}
