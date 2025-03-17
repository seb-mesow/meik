<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Enum\Category;
use App\Models\Rubric;
use App\Repository\RubricRepository;
use Database\Seeders\Traits\SeederTrait;
use Illuminate\Database\Seeder;
use PHPOnCouch\CouchClient;

class RubricSeeder extends Seeder
{
	use SeederTrait;
	
	public function __construct(
		CouchClient $client,
		private readonly RubricRepository $rubric_repository
	) {
		$this->client = $client;
	}

	/**
	 * Seed the application's database.
	 */
	public function run(): void
	{
		$this->remove_all_documents_by_model_type_id(RubricRepository::MODEL_TYPE_ID);
		
		$this->create_rubric(new Rubric(
			name: 'PC',
			category: Category::HARDWARE
		));

		$this->create_rubric(new Rubric(
			name: 'Taschenrechner',
			category: Category::HARDWARE
		));

		$this->create_rubric(new Rubric(
			name: 'Spiele',
			category: Category::SOFTWARE
		));

		$this->create_rubric(new Rubric(
			name: 'Office',
			category: Category::SOFTWARE
		));

		$this->create_rubric(new Rubric(
			name: 'Bedienungsanleitungen',
			category: Category::BOOK
		));

		$this->create_rubric(new Rubric(
			name: 'Fachbücher',
			category: Category::BOOK
		));
		
		// Einkommentieren wenn benötigt
		for ($i = 1; $i < 100; $i++) {
			$this->create_rubric(new Rubric(
				name: "Rubrik $i",
				category: Category::OTHER
			));
		}
		
		$this->create_rubric(new Rubric(
			name: 'Sonstiges',
			category: Category::OTHER,
			id: 'sonstiges'
		));
	}

	private function create_rubric(Rubric $rubric): void
	{
		$this->rubric_repository->insert($rubric);
	}
}
