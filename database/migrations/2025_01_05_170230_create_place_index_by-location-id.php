<?php
declare(strict_types=1);

use App\Repository\PlaceRepository;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\App;
use PHPOnCouch\CouchClient;
use PHPOnCouch\Exceptions\CouchNotFoundException;

return new class extends Migration
{
	private const string DESIGN_DOC_ID = '_design/place';
	private const string VIEW = 'by-location-id';
	
	private readonly CouchClient $client;
	private readonly string $map_function;
	private readonly string $reduce_function;
	
	public function __construct() {
		$this->client = App::make(CouchClient::class.'.admin');
		
		$id_prefix = PlaceRepository::MODEL_TYPE_ID;
		$this->map_function = <<<END
		function(doc) {
			if (doc._id.startsWith('$id_prefix')) {
				emit(doc.location_id, null);
				// no value specified
				// retrieve by seperate lookup or include_docs parameter
			}
		}
		END;
		$this->reduce_function = '_count';
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
			'reduce' => $this->reduce_function,
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
