<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Exhibit;
use App\Repository\ExhibitRepository;
use Illuminate\Database\Seeder;

class ExhibitSeeder extends Seeder
{
	public function __construct(
		private readonly ExhibitRepository $exhibit_repository
	) {}
	
	/**
	 * Seed the application's database.
	 */
	public function run(): void {
		$this->create_exhibit(new Exhibit(
			'1',
			'MR 610 (Modell 1986)',
			'VEB Röhrenwerk Mühlhausen im VEB Kombinat Mikroelektronik (DDR_RFT)'
		));
		$this->create_exhibit(new Exhibit(
			'2', 
			'Tiumphator CRN1',
			'Triumphator Leipzig (Mölkau) DDR'
		));
		$this->create_exhibit(new Exhibit(
			'3',
			'Nixdorf 8810 M55',
			'Nixdorf Computer AG Paderborn',
		));
		$this->create_exhibit(new Exhibit(
			'4',
			'Nixdorf BA42',
			'Diebold Nixdorf GmbH Paderborn',
		));
	}
	
	private function create_exhibit(Exhibit $exhibit): void {
		$existing_exhibit = $this->exhibit_repository->find($exhibit->get_id());
		if ($existing_exhibit) {
			$exhibit = new Exhibit(
				$exhibit->get_id(),
				$exhibit->get_name(),
				$existing_exhibit->get_rev()
			);
			$this->exhibit_repository->update($exhibit);
		} else {
			$this->exhibit_repository->insert($exhibit);
		}
	}
}
