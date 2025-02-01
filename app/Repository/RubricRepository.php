<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Rubric;
use App\Repository\Traits\StringIdRepositoryTrait;
use App\Util\StringIdGenerator;
use PHPOnCouch\CouchClient;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPOnCouch\Exceptions\CouchNotFoundException;
use stdClass;

final class RubricRepository
{
	use StringIdRepositoryTrait;

	public const string MODEL_TYPE_ID = 'rubric';

	private Serializer $serializer;

	public function __construct(
		CouchClient $client,
		StringIdGenerator $string_id_generator,
	) {
		$this->client = $client;
		$this->string_id_generator = $string_id_generator;
	}

	/**
	 * @var string $id
	 * @return array<Rubric>
	 */
	public function get_all(): array
	{
		$res = $this->client->find([
			'_id' => ['$beginsWith' => self::MODEL_TYPE_ID],
		]);
		$_this = $this;
		return array_map(static function (stdClass $doc) use ($_this): Rubric {
			return $_this->create_rubric_from_doc($doc);
		}, $res->docs);
	}

	/**
	 * @var string $id
	 */
	public function get_rubrics_paginated(string $category, int $page = 0, int $page_size = 10): array
	{
		$response = $this->client
			->key($category)
			->reduce(false)
			->limit($page_size)
			->skip($page * $page_size)
			->include_docs(true)
			->getView(self::MODEL_TYPE_ID, 'by-category-name');
		$_this = $this;
		$rubrics = array_map(static function (stdClass $row) use ($_this): Rubric {
			return $_this->create_rubric_from_doc($row->doc);
		}, $response->rows);

		$response = $this->client
			->key($category)
			->limit($page_size)
			->skip($page * $page_size)
			->getView(self::MODEL_TYPE_ID, 'by-category-name');
		$total_count = $response->rows[0]?->value ?? 0;

		return [
			'rubrics' => $rubrics,
			'total_count' => $total_count,
		];
	}

	public function find(string $id): ?Rubric
	{
		try {
			return $this->get($id);
		} catch (CouchNotFoundException $e) {
			return null;
		}
	}

	public function get(string $id): Rubric
	{
		$doc_id = $this->determinate_doc_id_from_model_id($id);
		$rubric_doc = $this->client->getDoc($doc_id);
		return $this->create_rubric_from_doc($rubric_doc);
	}

	public function insert(Rubric $rubric): void
	{
		assert(!$rubric->get_nullable_id());
		assert(!$rubric->get_nullable_rev());
		$doc = $this->create_doc_from_rubric($rubric); // setzt neue I
		$response = $this->client->storeDoc($doc);
		$rubric->set_rev($response->rev);
	}

	public function update(Rubric $rubric): void
	{
		assert($rubric->get_id());
		assert($rubric->get_rev());
		$doc = $this->create_doc_from_rubric($rubric);
		$response = $this->client->storeDoc($doc);
		$rubric->set_rev($response->rev);
	}

	public function remove(Rubric $rubric): void
	{
		assert($rubric->get_id());
		assert($rubric->get_rev());
		$delete_doc = $this->create_stub_doc_from_model($rubric);
		$this->client->deleteDoc($delete_doc);
	}

	public function get_by_selectors(array $selectors): array
	{
		$docs = $this->client->find(
			$selectors
		)->docs;

		$_this = $this;
		return array_map(static function (stdClass $doc) use ($_this): Rubric {
			return $_this->create_rubric_from_doc($doc);
		}, $docs);
	}

	public function determinate_rubric_props(Rubric $rubric): array
	{
		return [
			'id' => $rubric->get_id(),
			'name' => $rubric->get_name(),
			'category' => $rubric->get_category(),
		];
	}

	/**
	 * @return RubricDoc
	 */
	private function create_doc_from_rubric(Rubric $rubric): stdClass
	{
		/** @var RubricDoc */
		$rubric_doc = $this->create_stub_doc_from_model($rubric);

		$rubric_doc->name = $rubric->get_name();
		$rubric_doc->category = $rubric->get_category();

		return $rubric_doc;
	}

	/**
	 * @param RubricDoc $rubric_doc
	 */
	public function create_rubric_from_doc(stdClass $rubric_doc): Rubric
	{
		return new Rubric(
			name: $rubric_doc->name,
			category: $rubric_doc->category,
			id: $this->determinate_model_id_from_doc($rubric_doc),
			rev: $rubric_doc->_rev,
		);
	}
}
