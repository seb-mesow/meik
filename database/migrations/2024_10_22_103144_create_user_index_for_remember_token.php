<?php
declare(strict_types=1);

use App\Repository\CouchDBUserProvider;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use PHPOnCouch\CouchAdmin;
use PHPOnCouch\CouchClient;
use PHPOnCouch\Exceptions\CouchNotFoundException;

return new class extends Migration
{
	private const string DESIGN_DOC_ID = '_design/user';
	// private const string ID_PREFIX = CouchDBUserProvider::ID_PREFIX;
	
	private readonly CouchClient $client;
	private readonly string $map_function;
	
	public function __construct() {
		$this->client = App::make(CouchClient::class.'.admin');
		
		$id_prefix = CouchDBUserProvider::ID_PREFIX;
		$this->map_function = <<<END
		function (doc) {
			if (doc._id.startsWith('$id_prefix')) {
				emit(doc.remember_token, null);
			}
		}
		END;
	}
	
    /**
     * Run the migrations.
     */
    public function up(): void
    {
		try {
			$design_doc = $this->client->getDoc(self::DESIGN_DOC_ID);
		} catch (CouchNotFoundException $e) {
			$design_doc = new stdClass();
			$design_doc->_id = self::DESIGN_DOC_ID;
		}
		$design_doc->language = 'javascript';
		$design_doc->views = [
			'by-remember_token' => [
				'map' => $this->map_function
			]
		];
		$this->client->storeDoc($design_doc);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('user_index_for_remember_token');
    }
};
