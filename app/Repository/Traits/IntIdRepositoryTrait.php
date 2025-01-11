<?php
declare(strict_types=1);

namespace App\Repository\Traits;

use App\Models\Interfaces\IntIdentifiable;
use App\Models\Interfaces\Revisionable;
use PHPOnCouch\CouchClient;
use stdClass;

/**
 * @phpstan-type MainModelMetaDoc object {
 *     next_id: int
 * }
 * 
 * Die _id wird bei neuen Docs sofort gesetzt.
 * Die _rev fehlt bei neuen Models erstmal.
 * @phpstan-type StubMainModelDoc object{
 *     _id: string,
 *     _rev?: string,
 * }
 * 
 * Die _id wird bei neuen Docs sofort gesetzt.
 * @phpstan-type StubSubModelDoc object{
 *     _id: string,
 * }
 */
trait IntIdRepositoryTrait
{
	protected const string ID_PREFIX = self::MODEL_TYPE_ID . ':';
	protected const string META_DOC_ID = 'meta:' . self::MODEL_TYPE_ID;
	
	protected readonly CouchClient $client;
	private readonly stdClass $meta_doc;
	
	/**
	 * setzt als Nebeneffekt bei neuen Models die ID
	 * 
	 * @param IntIdentifiable&Revisionable $main_model
	 * @return StubMainModelDoc
	 */
	protected function create_stub_doc_from_model(IntIdentifiable&Revisionable $model): stdClass {
		$stub_main_model_doc = new stdClass();
		if ($model->get_nullable_id() === null) {
			$model->set_id($this->determinate_next_available_model_id());
		}
		$stub_main_model_doc->_id = $this->determinate_doc_id_from_model($model);
		if ($rev = $model->get_nullable_rev()) {
			$stub_main_model_doc->_rev = $rev;
		}
		return $stub_main_model_doc;
	}
	
	/**
	 * setzt als Nebeneffekt bei neuen Models die ID
	 * 
	 * @param IntIdentifiable $sub_model
	 * @return StubSubModelDoc
	 */
	private function create_stub_sub_doc_from_sub_model(IntIdentifiable $sub_model, string $namespace): stdClass {
		$stub_sub_doc = new stdClass();
		if ($sub_model->get_nullable_id() === null) {
			$sub_model->set_id($this->determinate_next_available_sub_model_id($namespace));
		}
		$stub_sub_doc->_id = $sub_model->get_id();
		return $stub_sub_doc;
	}
	
	/**
	 * für `delete()`-Funktion
	 */
	private function determinate_doc_id_from_model(IntIdentifiable $model): string {
		return $this->determinate_doc_id_from_model_id($model->get_id());
	}
	
	/**
	 * für `get()`-Funktion
	 */
	private function determinate_doc_id_from_model_id(int $model_id): string {
		return self::ID_PREFIX . ((string) $model_id);
	}
	
	/**
	 * @param StubMainModelDoc $main_model_doc
	 * @return int
	 */
	protected function determinate_model_id_from_doc(stdClass $main_model_doc): int {
		return (int) substr($main_model_doc->_id, strlen(self::ID_PREFIX));
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
