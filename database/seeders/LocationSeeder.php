<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Location;
use App\Repository\LocationRepository;
use Database\Seeders\Traits\SeederTrait;
use Illuminate\Database\Seeder;
use PHPOnCouch\CouchClient;

class LocationSeeder extends Seeder
{
	use SeederTrait;
	
	private const int COUNT = 100;
	
	public function __construct(
		CouchClient $client,
		private readonly LocationRepository $location_repository,
	) {
		$this->client = $client;
	}
	
	/**
	 * Run the database seeds.
	 */
	public function run(): void {
		$this->remove_all_documents_by_model_type_id(LocationRepository::MODEL_TYPE_ID);
		
		$this->create_location(new Location(
			name: "Keller 085",
			is_public: true,
		));
		$this->create_location(new Location(
			name: "Raum 628",
			is_public: false
		));
		$this->create_location(new Location(
			name: "Standort mit vielen Plätzen",
			is_public: false
		));
		
		$is_public = false;
		for ($i = 0; $i < self::COUNT; $i++) {
			$this->create_location(new Location(
				name: "Standort $i",
				is_public: $is_public,
			));
			$is_public = !$is_public;
			if (($i % 10) === 0) {
				usleep(100);
			}
		}
	}
	
	private function create_location(Location $location): void {
		$this->location_repository->insert($location);
	}
}
