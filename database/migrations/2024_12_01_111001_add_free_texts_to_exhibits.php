<?php
declare(strict_types=1);

use App\Repository\ExhibitRepository;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPOnCouch\CouchClient;

return new class extends Migration
{
	private readonly CouchClient $client;
	private readonly string $id_prefix;
	
	public function __construct() {
		$this->client = App::make(CouchClient::class);
		$this->id_prefix = ExhibitRepository::ID_PREFIX;
	}
	
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		$exhibit_docs = $this->client->find([
			'_id' => ['$beginsWith' => $this->id_prefix],
		]);
		foreach($exhibit_docs->docs as $exhibit_doc) {
			$exhibit_doc->free_texts??= [];
			$this->client->storeDoc($exhibit_doc);
		}
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		$exhibit_docs = $this->client->find([
			'_id' => ['$beginsWith' => $this->id_prefix],
		]);
		foreach($exhibit_docs as $exhibit_doc) {
			if (!is_array($exhibit_doc->free_texts) || count($exhibit_doc->free_texts) <= 0) {
				unset($exhibit_doc->free_texts);
				$this->client->storeDoc($exhibit_doc);
			}
		}
	}
};
