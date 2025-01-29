<?php
declare(strict_types=1);

namespace Database\Seeders\Traits;

use PHPOnCouch\CouchClient;
use RuntimeException;
use stdClass;

trait SeederTrait
{
	private readonly CouchClient $client;
	
	private function remove_all_documents_by_model_type_id(string $model_type_id): void {
		$docs = $this->client
			->limit(PHP_INT_MAX)
			->find([
				'_id' => [
					'$beginsWith' => $model_type_id . ':'
				],
			])
			->docs;
		$_this = $this;
		array_walk($docs, static function (stdClass $doc) use ($_this): void {
			$_this->client->deleteDoc($doc);
		});
	}
}
