<?php
declare(strict_types=1);

namespace App\Repository;

use App\Models\Enum\Category;
use App\Models\Rubric;
use App\Repository\Traits\StringIdRepositoryTrait;
use App\Util\StringIdGenerator;
use PHPOnCouch\CouchClient;
use JMS\Serializer\Serializer;
use PHPOnCouch\Exceptions\CouchNotFoundException;
use stdClass;

/**
 * @phpstan-type RubricDoc object{
 *    _id: string,
 *    _rev?: string,
 *    category_id: string,
 *    name: string,
 *}
 */
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
	 * @return Rubric[]
	 */
	public function get_all(): array
	{
		$res = $this->client->find([
			'_id' => [
				'$beginsWith' => self::ID_PREFIX
			],
		]);
		return $this->create_rubrics_from_docs($res->docs);
	}
	
	/**
	 * @return array{
	 *     rubrics: Rubric[],
	 *     total_count: int
	 * }
	 */
	public function query(?string $category_id, ?int $page_number, ?int $count_per_page): array
	{
		$client = $this->client
			->key($category_id)
			->reduce(false);
		
		if ($page_number !== null) {
			assert($count_per_page !== null);
			$client = $this->client
				->limit($count_per_page)
				->skip($page_number * $count_per_page);
		}
		
		// Hinweis: Es ist möglich mehrere Queries auf einmal auszuführen
		// /{db}/_design/{ddoc}/_view/{view}/queries
		$response = $client
			->include_docs(true)
			->getView(self::MODEL_TYPE_ID, 'by-category-id');
		$rubrics = $this->create_rubrics_from_view_response($response);
		
		if ($page_number !== null) {
			$response = $client
				->getView(self::MODEL_TYPE_ID, 'by-category-id');
			$total_count = $response->rows[0]?->value ?? 0;
		}
		
		$ret = [ 'rubrics' => $rubrics ];
		if ($total_count) {
			$ret['total_count'] = $total_count;
		}
		return $ret;
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
	
	public function remove_by_id(string $rubric_id): void {
		$doc_id = $this->determinate_doc_id_from_model_id($rubric_id);
		$doc = $this->client->getDoc($doc_id); // retrieves _rev
		$this->client->deleteDoc($doc);
	}

	public function get_by_selectors(array $selectors): array
	{
		$docs = $this->client->find(
			$selectors
		)->docs;

		return $this->create_rubrics_from_docs($docs);
	}

	/**
	 * @return RubricDoc
	 */
	private function create_doc_from_rubric(Rubric $rubric): stdClass
	{
		/** @var RubricDoc */
		$rubric_doc = $this->create_stub_doc_from_model($rubric);

		$rubric_doc->name = $rubric->get_name();
		$rubric_doc->category_id = $rubric->get_category()->value;

		return $rubric_doc;
	}

	/**
	 * @param RubricDoc $doc
	 */
	private function create_rubric_from_doc(stdClass $doc): Rubric
	{
		return new Rubric(
			name: $doc->name,
			category: Category::from($doc->category_id),
			id: $this->determinate_model_id_from_doc($doc),
			rev: $doc->_rev,
		);
	}
	
	/**
	 * @param RubricDoc[] $docs
	 * @return Rubric[]
	 */
	private function create_rubrics_from_docs(array $docs): array {
		$_this = $this;
		return array_map(static function (stdClass $doc) use ($_this): Rubric {
			return $_this->create_rubric_from_doc($doc);
		}, $docs);
	}
	
	/**
	 * @return Rubric[]
	 */
	private function create_rubrics_from_view_response(stdClass $response): array {
		$_this = $this;
		return array_map(static function (stdClass $row) use ($_this): Rubric {
			return $_this->create_rubric_from_doc($row->doc);
		}, $response->rows);
	}
}
