<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Location;
use App\Repository\LocationRepository;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
	public function __construct(
		private readonly LocationRepository $location_repository
	) {}
	
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		$this->create_location((new Location())
			->set__id("location:Keller 0851733426576496")
			->set_name("Keller 085")
			->set_is_public(true)
		);
		$this->create_location((new Location())
			->set__id("location:Raum 6281733426554903")
			->set_name("Raum 628")
			->set_is_public(false)
		);
	}
	
	private function create_location(Location $loc) {
		$this->location_repository->create($loc);
	}
}
