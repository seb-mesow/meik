<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Enum\Category;
use App\Models\Rubric;
use App\Repository\RubricRepository;
use Illuminate\Database\Seeder;

class RubricSeeder extends Seeder
{
	public function __construct(
		private readonly RubricRepository $rubric_repository
	) {}

	/**
	 * Seed the application's database.
	 */
	public function run(): void
	{
		$all_rubrics = $this->rubric_repository->get_all();
		foreach ($all_rubrics as $rubric) {
			$this->rubric_repository->remove($rubric);
		}

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
