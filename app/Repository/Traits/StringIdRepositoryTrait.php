<?php
declare(strict_types=1);

namespace App\Repository\Traits;

use App\Models\Interfaces\StringIdentifiable;
use App\Models\Interfaces\Revisionable;
use App\Util\StringIdGenerator;
use PHPOnCouch\CouchClient;
use stdClass;

/**
 * Die _id wird bei neuen Docs sofort gesetzt.
 * Die _rev fehlt bei neuen Models erstmal.
 * @phpstan-type StubMainModelDoc object{
 *     _id: string,
 *     _rev?: string,
 * }
 */
trait StringIdRepositoryTrait
{
	private readonly CouchClient $client;
	private readonly stdClass $meta_doc;
	private readonly StringIdGenerator $string_id_generator;
	
	/**
	 * setzt als Nebeneffekt bei neuen Models die ID
	 * 
	 * @param StringIdentifiable&Revisionable $main_model
	 * @return StubMainModelDoc
	 */
	private function create_stub_doc_from_model(StringIdentifiable&Revisionable $model): stdClass {
		$stub_main_model_doc = new stdClass();
		if (is_null($model->get_nullable_id())) {
			$model->set_id($this->string_id_generator->generate_model_id());
		}
		$stub_main_model_doc->_id = $this->determinate_doc_id_from_model($model);
		if ($rev = $model->get_nullable_rev()) {
			$stub_main_model_doc->_rev = $rev;
		}
		return $stub_main_model_doc;
	}
	
	private function generate_new_model_id(): string {
		return (self::MODEL_TYPE_ID);
	}
	
	/**
	 * für `delete()`-Funktion
	 */
	private function determinate_doc_id_from_model(StringIdentifiable $model): string {
		return $this->determinate_doc_id_from_model_id($model->get_id());
	}
	
	/**
	 * für `get()`-Funktion
	 */
	private function determinate_doc_id_from_model_id(string $model_id): string {
		return self::MODEL_TYPE_ID . $model_id;
	}
	
	/**
	 * @param StubMainModelDoc $main_model_doc
	 * @return string
	 */
	private function determinate_model_id_from_doc(stdClass $main_model_doc): string {
		return substr($main_model_doc->_id, strlen(self::MODEL_TYPE_ID));
	}
}
