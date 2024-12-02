<?php
declare(strict_types=1);

namespace App\Repository;

use App\Models\Interfaces\MainModel;
use PHPOnCouch\CouchClient;
use stdClass;

/**
 * @phpstan-type MainModelMetaDoc object {
 *     next_id: int
 * }
 * 
 * @phpstan-type StubMainModelDoc object{
 *     _id: string,
 *     _rev: string,
 * }
 */
trait RepositoryTrait
{
	protected const string ID_PREFIX = self::MODEL_TYPE_ID . ':';
	protected const string META_DOC_ID = 'meta:' . self::MODEL_TYPE_ID;
	
	protected readonly CouchClient $client;
	private readonly stdClass $meta_doc;
	
	/**
	 * @param MainModel $main_model
	 * @param MainModelMetaDoc $main_model_meta_doc
	 * @return StubMainModelDoc
	 */
	protected function create_stub_doc_from_model(MainModel $main_model, ?stdClass $main_model_meta_doc = null): stdClass {
		$stub_main_model_doc = new stdClass();
		$stub_main_model_doc->_id = 
			self::ID_PREFIX . ($main_model->get_id() ?? $this->determinate_next_available_model_id());
		if ($rev = $main_model->get_rev()) {
			$stub_main_model_doc->_rev = $rev;
		}
		return $stub_main_model_doc;
	}
	
	/**
	 * @param StubMainModelDoc $main_model_doc
	 * @return string
	 */
	protected function determinate_model_id_from_doc(stdClass $main_model_doc): string {
		return substr($main_model_doc->_id, strlen(self::ID_PREFIX));
	}
	
	/**
	 * @return MainModelMetaDoc
	 */
	protected function get_meta_doc(): stdClass {
		return $this->client->getDoc(self::META_DOC_ID);
	}
	
	/**
	 * @param MainModelMetaDoc $main_model_meta_doc
	 * @return MainModelMetaDoc
	 */
	private function update_meta_doc(): void {
		$response = $this->client->storeDoc($this->meta_doc);
		$this->meta_doc->_rev = $response->rev;
	}
	
	/**
	 * @param MainModelMetaDoc $meta_doc
	 * @return int
	 */
	protected function determinate_next_available_model_id(): int {
		$next_id = $this->meta_doc->next_id++;
		$this->update_meta_doc();
		return $next_id;
	}
		
	protected function determinate_next_available_sub_model_id(string $namespace): int {
		$next_id = $this->meta_doc->{$namespace}->next_id++;
		$this->update_meta_doc();
		return $next_id;
	}
}
