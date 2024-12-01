<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Exhibit;
use App\Models\FreeText;
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
		$all_exhibits = $this->exhibit_repository->get_all();
		foreach ($all_exhibits as $exhibit) {
			$this->exhibit_repository->remove($exhibit);
		}
		
		$this->create_exhibit(new Exhibit(
			inventory_number: '1',
			name: 'MR 610 (Modell 1986)',
			manufacturer: 'VEB Röhrenwerk Mühlhausen im VEB Kombinat Mikroelektronik (DDR_RFT)',
			free_texts: [
				new FreeText(
					heading: "öffentlicher Freitext 0",
					html: "<p>Das kann jeder lesen.</p>",
					is_public: true
				),
				new FreeText(
					heading: "interner Freitext 1",
					html: "<p>Das können nur Mitarbeiter lesen.</p>",
					is_public: false
				),
			]
		));
		$this->create_exhibit(new Exhibit(
			inventory_number: '2', 
			name: 'Tiumphator CRN1',
			manufacturer: 'Triumphator Leipzig (Mölkau) DDR',
		));
		$this->create_exhibit(new Exhibit(
			inventory_number: '3',
			name: 'Nixdorf 8810 M55',
			manufacturer: 'Nixdorf Computer AG Paderborn',
		));
		$this->create_exhibit(new Exhibit(
			inventory_number: '4',
			name: 'Nixdorf BA42',
			manufacturer: 'Diebold Nixdorf GmbH Paderborn',
		));
	}
	
	private function create_exhibit(Exhibit $exhibit): void {
		$this->exhibit_repository->insert($exhibit);
	}
}
