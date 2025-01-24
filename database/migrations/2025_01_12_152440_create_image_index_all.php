<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\App;
use PHPOnCouch\CouchClient;
use PHPOnCouch\Exceptions\CouchNotFoundException;

return new class extends Migration
{
	private const string DESIGN_DOC_ID = '_design/imageorder';
	private const string VIEW = 'all';
	
	private readonly CouchClient $client;
	private readonly string $map_function;
	
	public function __construct() {
		$this->client = App::make(CouchClient::class.'.admin');
		
		$model_type_id = 'image';
		$model_type_id_length = strlen($model_type_id);
		$this->map_function = <<<END
		function(doc) {
			if (doc._id.startsWith('$model_type_id_length')) {
				image_id = parseInt(doc._id.substring($model_type_id_length), 10);
				emit(image_id, null);
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
		$design_doc->views ??= new stdClass();
		$design_doc->views->{self::VIEW} = [
			'map' => $this->map_function,
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
		unset($views[self::VIEW]);
		if (count($views) < 1) {
			$this->client->deleteDoc($design_doc);
		} else {	
			$design_doc->views = $views;
			$this->client->storeDoc($design_doc);
		}
	}
};
