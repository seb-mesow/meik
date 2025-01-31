<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\App;
use PHPOnCouch\CouchClient;
use PHPOnCouch\Exceptions\CouchNotFoundException;

return new class extends Migration
{
	private const string DESIGN_DOC_ID = '_design/user';
	private const string VIEW = 'by-username';
	
	private readonly CouchClient $client;
	private readonly string $map_function;
	
	public function __construct() {
		$this->client = App::make(CouchClient::class.'.admin');
		
		$id_prefix = 'username:';
		$this->map_function = <<<END
		function (doc) {
			if (doc._id.startsWith('$id_prefix')) {
				emit(doc.username, null);
			}
		}
		END;
	}
	
	public function up(): void {
		try {
			$design_doc = $this->client->getDoc(self::DESIGN_DOC_ID);
		} catch (CouchNotFoundException $e) {
			$design_doc = new stdClass();
			$design_doc->_id = self::DESIGN_DOC_ID;
		}
		$design_doc->language = 'javascript';
		$design_doc->views->{self::VIEW} = [
			'map' => $this->map_function
		];
		$this->client->storeDoc($design_doc);
	}

	public function down(): void {
		try {
			$design_doc = $this->client->getDoc(self::DESIGN_DOC_ID);
		} catch (CouchNotFoundException $e) {
			return;
		}
		$views = $design_doc->views;
		unset($view[self::VIEW]);
		$design_doc->views = $views;
		$this->client->storeDoc($design_doc);
	}
};
