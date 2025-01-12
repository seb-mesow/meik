<?php
declare(strict_types=1);

use App\Repository\LocationRepository;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\App;
use PHPOnCouch\CouchClient;
use PHPOnCouch\Exceptions\CouchNotFoundException;

return new class extends Migration
{
	private const string DESIGN_DOC_ID = '_design/location';
	private const string VIEW = 'all';
	
	private readonly CouchClient $client;
	private readonly string $map_function;
	
	public function __construct() {
		$this->client = App::make(CouchClient::class.'.admin');
		
		$model_type_id = LocationRepository::MODEL_TYPE_ID;
		$this->map_function = <<<END
		function(doc) {
			if (doc._id.startsWith('$model_type_id')) {
				emit(doc.name, null);
				// no value specified
				// retrieve by seperate lookup or include_docs parameter
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
		unset($views[self::VIEW]);
		if (count($views) < 1) {
			$this->client->deleteDoc($design_doc);
		} else {	
			$design_doc->views = $views;
			$this->client->storeDoc($design_doc);
		}
	}
};
