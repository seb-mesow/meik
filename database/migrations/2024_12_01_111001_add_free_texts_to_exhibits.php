<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use PHPOnCouch\CouchClient;

return new class extends Migration
{
	private readonly CouchClient $client;
	private readonly string $id_prefix;
	
	private const string META_DOC_ID = 'meta:exhibit';
	
	public function __construct() {
		$this->client = App::make(CouchClient::class);
		$this->id_prefix = "exhibit:";
	}
	
	public function up(): void {
		$meta_doc = $this->client->getDoc(self::META_DOC_ID);
		$free_text_meta_doc = new stdClass;
		$free_text_meta_doc->next_id = 0;
		$meta_doc->free_text = $free_text_meta_doc;
		$this->client->storeDoc($meta_doc);
		
		$exhibit_docs = $this->client->find([
			'_id' => ['$beginsWith' => $this->id_prefix],
		]);
		foreach($exhibit_docs->docs as $exhibit_doc) {
			$exhibit_doc->free_texts??= [];
			$this->client->storeDoc($exhibit_doc);
		}
	}

	public function down(): void {
		$exhibit_docs = $this->client->find([
			'_id' => ['$beginsWith' => $this->id_prefix],
		]);
		foreach($exhibit_docs as $exhibit_doc) {
			if (!is_array($exhibit_doc->free_texts) || count($exhibit_doc->free_texts) <= 0) {
				unset($exhibit_doc->free_texts);
				$this->client->storeDoc($exhibit_doc);
			}
		}
		
		$meta_doc = $this->client->getDoc(self::META_DOC_ID);
		unset($meta_doc->free_text);
		$this->client->storeDoc($meta_doc);
	}
};
