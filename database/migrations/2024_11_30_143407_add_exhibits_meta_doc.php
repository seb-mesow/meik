<?php
declare(strict_types=1);

use App\Repository\ExhibitRepository;
use Illuminate\Database\Migrations\Migration;
use PHPOnCouch\CouchClient;

return new class extends Migration
{
	private readonly CouchClient $client;
	private readonly string $id_prefix;
	
	private const string META_DOC_ID = 'meta:exhibit';
	
	public function __construct() {
		$this->client = App::make(CouchClient::class);
	}
	
	/**
	 * Run the migrations.
	 */
	public function up(): void
	{
		$meta_doc = new stdClass();
		$meta_doc->_id = self::META_DOC_ID;
		$meta_doc->next_id = 0;
		$this->client->storeDoc($meta_doc);
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		$meta_doc = new stdClass();
		$meta_doc->_id = self::META_DOC_ID;
		$this->client->deleteDoc($meta_doc);
	}
};
