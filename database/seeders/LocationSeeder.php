<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Location;
use App\Repository\LocationRepository;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
	private const int COUNT = 100;
	
	public function __construct(
		private readonly LocationRepository $location_repository
	) {}
	
	/**
	 * Run the database seeds.
	 */
	public function run(): void {
		$this->create_location(new Location(
			name: "Keller 085",
			is_public: true,
		));
		$this->create_location(new Location(
			name: "Raum 628",
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
				sleep(1);
			}
		}
	}
	
	private function create_location(Location $location): void {
		$this->location_repository->insert($location);
	}
}
