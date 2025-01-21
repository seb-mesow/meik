<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Exhibit;
use App\Models\FreeText;
use App\Repository\Traits\IntIdRepositoryTrait;
use Dotenv\Util\Regex;
use PHPOnCouch\CouchClient;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPOnCouch\Exceptions\CouchNotFoundException;
use stdClass;

/**
 * Die _id wird bei neuen Docs sofort gesetzt.
 * @phpstan-type ExhibitDoc object{
 *     _id: string,
 *     _rev?: string,
 *     inventory_number: string,
 *     name: string,
 *     manufacturer: string,
 *     year_of_manufacture: int,
 *     place_id: int,
 *     free_texts: FreeTextDoc[]
 * }
 * 
 * Die _id wird bei neuen Docs sofort gesetzt.
 * @phpstan-type FreeTextDoc object{
 *     _id: int,
 *     heading: string,
 *     html: string,
 *     is_public: bool
 * }
 * 
 * @phpstan-type MetaDoc object{
 *     next_id: int,
 *     free_text: object {
 *         next_id: int
 *     }
 * }
 */
final class ExhibitRepository
{
	use IntIdRepositoryTrait;

	private const MODEL_TYPE_ID = "exhibit";

	private Serializer $serializer;

	public function __construct(
		CouchClient $client
	) {
		$this->client = $client;
		$this->meta_doc = $this->get_meta_doc();

		$this->serializer = SerializerBuilder::create()->build();
	}

	/**
	 * @var string $id
	 * @return array<Exhibit>
	 */
	public function get_all(): array
	{
		$res = $this->client->find([
			'_id' => ['$beginsWith' => self::MODEL_TYPE_ID . ':'],
		]);
		$_this = $this;
		return array_map(static function (stdClass $doc) use ($_this): Exhibit {
			return $_this->create_exhibit_from_doc($doc);
		}, $res->docs);
	}

	/**
	 * @var string $id
	 * @return \App\Models\Exhibit[]
	 */
	public function get_exhibits_paginated(array|null $additonalSelectors = null, int $page = 0, int $page_size = 10,): array
	{

		$selectors = [
			'_id' => [
				'$beginsWith' => self::MODEL_TYPE_ID
			]
		];

		if ($additonalSelectors) {
			$selectors = array_merge($selectors, $additonalSelectors);
		}

		$exhibits = $this->client
			->limit($page_size)
			->skip($page * $page_size)
			->find([
				'$and' => [
					$selectors
				]
			])
			->docs;
		$_this = $this;
		return array_map(static function (stdClass $doc) use ($_this): Exhibit {
			return $_this->create_exhibit_from_doc($doc);
		}, $exhibits);
	}

	public function find(int $id): ?Exhibit
	{
		try {
			return $this->get($id);
		} catch (CouchNotFoundException $e) {
			return null;
		}
	}

	public function get(int $id): Exhibit
	{
		$doc_id = $this->determinate_doc_id_from_model_id($id);
		$exhibit_doc = $this->client->getDoc($doc_id);
		return $this->create_exhibit_from_doc($exhibit_doc);
	}

	public function insert(Exhibit $exhibit): void
	{
		assert(!$exhibit->get_nullable_id());
		assert(!$exhibit->get_nullable_rev());
		$doc = $this->create_doc_from_exhibit($exhibit); // setzt neue ID
		$response = $this->client->storeDoc($doc);
		$exhibit->set_rev($response->rev);
	}

	public function update(Exhibit $exhibit): void
	{
		assert($exhibit->get_id());
		assert($exhibit->get_rev());
		$doc = $this->create_doc_from_exhibit($exhibit);
		$response = $this->client->storeDoc($doc);
		$exhibit->set_rev($response->rev);
	}

	public function remove(Exhibit $exhibit): void
	{
		assert($exhibit->get_id());
		assert($exhibit->get_rev());
		$delete_doc = $this->create_stub_doc_from_model($exhibit);
		$this->client->deleteDoc($delete_doc);
	}

	public function get_by_selectors(array $selectors): array
	{
		$docs = $this->client->find(
			$selectors
		)->docs;

		$_this = $this;
		return array_map(static function (stdClass $doc) use ($_this): Exhibit {
			return $_this->create_exhibit_from_doc($doc);
		}, $docs);
	}

	/**
	 * @return ExhibitDoc
	 */
	private function create_doc_from_exhibit(Exhibit $exhibit): stdClass
	{
		/** @var ExhibitDoc */
		$exhibit_doc = $this->create_stub_doc_from_model($exhibit);

		$exhibit_doc->inventory_number = $exhibit->get_inventory_number();
		$exhibit_doc->name = $exhibit->get_name();
		$exhibit_doc->manufacturer = $exhibit->get_manufacturer();
		$exhibit_doc->year_of_manufacture = $exhibit->get_year_of_manufacture();
		$exhibit_doc->place_id = $exhibit->get_place_id();
		$exhibit_doc->rubric_id = $exhibit->get_rubric_id();

		$_this = $this;
		$free_text_docs = array_map(static function (FreeText $free_text) use ($_this): stdClass {
			return $_this->create_doc_from_free_text($free_text);
		}, $exhibit->get_free_texts());
		$exhibit_doc->free_texts = $free_text_docs;

		return $exhibit_doc;
	}

	/**
	 * @param ExhibitDoc $exhibit_doc
	 */
	private function create_exhibit_from_doc(stdClass $exhibit_doc): Exhibit
	{
		$_this = $this;
		$free_texts = array_map(static function (stdClass $free_text_doc) use ($_this): FreeText {
			return $_this->create_free_text_from_doc($free_text_doc);
		}, $exhibit_doc->free_texts);

		return new Exhibit(
			inventory_number: $exhibit_doc->inventory_number,
			name: $exhibit_doc->name,
			manufacturer: $exhibit_doc->manufacturer,
			year_of_manufacture: $exhibit_doc->year_of_manufacture,
			place_id: $exhibit_doc->place_id,
			free_texts: $free_texts,
			rubric_id: $exhibit_doc?->rubric_id ?? '',
			id: $this->determinate_model_id_from_doc($exhibit_doc),
			rev: $exhibit_doc->_rev,
		);
	}

	/**
	 * @return FreeTextDoc
	 */
	private function create_doc_from_free_text(FreeText $free_text): stdClass
	{
		/** @var FreeTextDoc */
		$free_text_doc = $this->create_stub_sub_doc_from_sub_model($free_text, 'free_text');
		$free_text_doc->heading = $free_text->get_heading();
		$free_text_doc->html = $free_text->get_html();
		$free_text_doc->is_public = $free_text->get_is_public();
		return $free_text_doc;
	}

	/**
	 * @param FreeTextDoc $free_text_doc
	 */
	private function create_free_text_from_doc(stdClass $free_text_doc): FreeText
	{
		return new FreeText(
			id: (int) $free_text_doc->_id,
			heading: $free_text_doc->heading,
			html: $free_text_doc->html,
			is_public: $free_text_doc->is_public,
		);
	}
}
