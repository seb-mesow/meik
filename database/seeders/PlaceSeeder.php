<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Place;
use App\Repository\LocationRepository;
use App\Repository\PlaceRepository;
use Illuminate\Database\Seeder;

class PlaceSeeder extends Seeder
{
	private const int COUNT = 100;
	
	public function __construct(
		private readonly PlaceRepository $place_repository,
		private readonly LocationRepository $location_repository,
	) {}
	
	/**
	 * Run the database seeds.
	 */
	public function run(): void {
		$raum_628_id = $this->find_location("Raum 628")->get_id();
		
		$this->create_place(new Place(
			name: "Vitrine hinten rechts",
			location_id: $raum_628_id,
		));
		$this->create_place(new Place(
			name: "Vitrine hinten links",
			location_id: $raum_628_id,
		));
		$this->create_place(new Place(
			name: "Vitrine vorne links",
			location_id: $raum_628_id,
		));
		$this->create_place(new Place(
			name: "Vitrine vorne rechts",
			location_id: $raum_628_id,
		));
		$this->create_place(new Place(
			name: "Schreibtisch mitte",
			location_id: $raum_628_id,
		));
		
		$keller_085_id = $this->find_location("Keller 085")->get_id();
		$this->create_place(new Place(
			name: "Regal 1 (linker Gang, Wand)",
			location_id: $keller_085_id,
		));
		$this->create_place(new Place(
			name: "Regal 2 (linker Gang, zur Mitte)",
			location_id: $keller_085_id,
		));
		$this->create_place(place: new Place(
			name: "Regal 3 (rechter Gang, zur Mitte)",
			location_id: $keller_085_id,
		));
		$this->create_place(new Place(
			name: "Regal 4 (rechter Gang, Wand)",
			location_id: $keller_085_id,
		));
		
		$standort_mit_vielen_plaetzen_id = $this->find_location("Standort mit vielen Plätzen")->get_id();
		$this->create_many_places($standort_mit_vielen_plaetzen_id);
	}
	
	private function create_place(Place $place): void {
		$this->place_repository->insert($place);
	}
	
	private function create_many_places(string $location_id): void {
		for ($i = 0; $i < self::COUNT; $i++) {
			$this->create_place(new Place(
				name: "Platz $i",
				location_id: $location_id,
			));
			if (($i % 10) === 0) {
				sleep(1);
			}
		}
	}
	
	private function find_location(string $name): Location {
		$location = $this->location_repository->find_by_name($name);
		assert($location);
		return $location;
	}
}
