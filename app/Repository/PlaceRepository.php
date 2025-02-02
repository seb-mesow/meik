<?php
declare(strict_types=1);

namespace App\Repository;

use App\Models\Place;
use App\Repository\Traits\StringIdRepositoryTrait;
use App\Util\StringIdGenerator;
use PHPOnCouch\CouchClient;
use PHPOnCouch\Exceptions\CouchNotFoundException;
use stdClass;

/**
 * Die _id wird bei neuen Docs sofort gesetzt.
 * @phpstan-type PlaceDoc object{
 *     _id: string,
 *     _rev?: string,
 *     name: string,
 *     location_id: string,
 * }
 */
final class PlaceRepository
{
	use StringIdRepositoryTrait;
	
	public const string MODEL_TYPE_ID = 'place';

	public function __construct(
		CouchClient $client,
		StringIdGenerator $string_id_generator,
	) {
		$this->client = $client;
		$this->string_id_generator = $string_id_generator;
	}
	
	/**
	 * @return Place[]
	 */
	public function get_all(): array
	{
		$res = $this->client->find([
			'_id' => ['$beginsWith' => self::MODEL_TYPE_ID . ':'],
		]);
		return $this->create_places_from_docs($res->docs);
	}
	
	/**
	 * @return array{
	 *     places: Place[],
	 *     total_count: int
	 * }
	 */
	public function get_paginated(string $location_id, int $page_number, int $count_per_page): array {
		// Hinweis: Es ist möglich mehrere Queries auf einmal auszuführen
		// /{db}/_design/{ddoc}/_view/{view}/queries
		$response = $this->client
			->key($location_id)
			->reduce(false)
			->limit($count_per_page)
			->skip($page_number * $count_per_page)
			->include_docs(true)
			->getView(self::MODEL_TYPE_ID, 'by-location-id');
		
		$_this = $this;
		$places = array_map(static function(stdClass $row) use ($_this): Place {
			return $_this->create_place_from_doc($row->doc);
		}, $response->rows);
		
		$response = $this->client
			->key($location_id)
			->limit($count_per_page)
			->skip($page_number * $count_per_page)
			->getView(self::MODEL_TYPE_ID, 'by-location-id');
		$total_count = $response->rows[0]?->value ?? 0;
		
		return [
			'places' => $places,
			'total_count' => $total_count,
		];
	}
	
	public function find(string $id): ?Place {
		try {
			return $this->get($id);
		} catch (CouchNotFoundException $e) {
			return null;
		}
	}
	
	public function get(string $id): Place {
		// Das Caching bei Places und Locations erspart ca. 750 ms .
		$_this = $this;
		return $this->cached(__FUNCTION__, $id, static function(string $_id) use ($_this): Place {
			$doc_id = $_this->determinate_doc_id_from_model_id($_id);
			$place_doc = $_this->client->getDoc($doc_id);
			return $_this->create_place_from_doc($place_doc);
		}, $id);
	}
	
	public function insert(Place $place): void {
		assert(!$place->get_nullable_id());
		assert(!$place->get_nullable_rev());	
		$doc = $this->create_doc_from_place($place);
		$response = $this->client->storeDoc($doc);
		$place->set_rev($response->rev);
	}
	
	public function update(Place $place): void {
		assert($place->get_id());
		assert($place->get_rev());
		$doc = $this->create_doc_from_place($place);
		$response = $this->client->storeDoc($doc);
		$place->set_rev($response->rev);
	}
	
	public function remove(Place $place): void {
		assert($place->get_id());
		assert($place->get_rev());
		$delete_doc = $this->create_stub_doc_from_model($place);
		$this->client->deleteDoc($delete_doc);
	}
	
	public function remove_by_id(string $place_id): void {
		$doc_id = $this->determinate_doc_id_from_model_id($place_id);
		$doc = $this->client->getDoc($doc_id); // retrieves _rev
		$this->client->deleteDoc($doc);
	}
	
	/**
	 * @param PlaceDoc $place_doc
	 */
	private function create_place_from_doc(stdClass $place_doc): Place {
		return new Place(
			name: $place_doc->name,
			location_id: $place_doc->location_id,
			id: $this->determinate_model_id_from_doc($place_doc),
			rev: $place_doc->_rev,
		);
	}

	/**
	 * @param PlaceDoc[] $place_docs
	 * @return Place[]
	 */
	private function create_places_from_docs(array $place_docs): array {
		$_this = $this;
		return array_map(static function(stdClass $place_doc) use ($_this): Place {
			return $_this->create_place_from_doc($place_doc);
		}, $place_docs);
	}

	/**
	 * @return PlaceDoc
	 */
	private function create_doc_from_place(Place $place): stdClass {
		/** @var PlaceDoc */
		$place_doc = $this->create_stub_doc_from_model($place);
		$place_doc->name = $place->get_name();
		$place_doc->location_id = $place->get_location_id();
		return $place_doc;
	}
}
