<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Exhibit;
use App\Models\Image;
use App\Models\ImageOrder;
use App\Repository\ExhibitRepository;
use App\Repository\ImageOrderRepository;
use App\Repository\ImageRepository;
use App\Service\ImageService;
use Database\Seeders\Traits\SeederTrait;
use Illuminate\Database\Seeder;
use PHPOnCouch\CouchClient;
use RuntimeException;

class ImageSeeder extends Seeder
{
	use SeederTrait;
	
	private int $number_image_counter = 0;
	private const int COUNT_OF_NUMBER_IMAGES = 20;
	
	private readonly array $all_exhibit_ids;
	private readonly int $all_exhibit_ids_length;
	private int $all_exhibit_ids_index = 0;
	private readonly array $stock_files;
	private readonly int $stock_files_length;
	private int $stock_files_index = 0;
	
	public function __construct(
		CouchClient $client,
		private readonly ExhibitRepository $exhibit_repository,
		private readonly ImageOrderRepository $image_order_repository,
		private readonly ImageRepository $image_repository,
		private readonly ImageService $image_service,
	) {
		$this->client = $client;
		
		$this->all_exhibit_ids = array_map(static fn(Exhibit $exhibit): int => $exhibit->get_id(), $this->exhibit_repository->get_all());
		$this->all_exhibit_ids_length = count($this->all_exhibit_ids);
		
		$stock_files = array_diff(scandir(__DIR__ . '/images/stock'), ['..', '.', 'LICENCES.md']);
		shuffle($stock_files);
		$this->stock_files = $stock_files;
		$this->stock_files_length = count($this->stock_files);
	}
	
	/**
	 * Seed the application's database.
	 */
	public function run(): void {
		$this->remove_all_documents_by_model_type_id(ImageOrderRepository::MODEL_TYPE_ID);
		$this->remove_all_documents_by_model_type_id(ImageRepository::MODEL_TYPE_ID);
		
		$image_order = $this->create_image_order();
		# $this->insert_next_number_image($image_order);
		# $this->insert_next_number_image($image_order);
		# $this->insert_next_number_image($image_order);
		# $this->insert_next_number_image($image_order);
		# $this->insert_next_number_image($image_order);
		$this->insert_image_order($image_order);
		
		$image_order = $this->create_image_order();
		# $this->insert_next_number_image($image_order);
		# $this->insert_next_number_image($image_order);
		# $this->insert_next_number_image($image_order);
		$this->insert_image_order($image_order);
		
		$image_order = $this->create_image_order();
		# $this->insert_next_number_image($image_order);
		# $this->insert_next_number_image($image_order);
		# $this->insert_image_order($image_order);
		
		// 4th exhibit without images
		
		for ($i = 0; $i < 100; $i++) {
			$image_order = $this->create_image_order();
			$count = fake()->numberBetween(0, 3);
			for ($j = 0; $j < $count; $j++) {
				$this->insert_next_stock_image($image_order);
			}
			$this->insert_image_order($image_order);
		}
	}
	
	private function create_image_order(): ImageOrder {
		if ($this->all_exhibit_ids_index >= $this->all_exhibit_ids_length) {
			$this->all_exhibit_ids_index = 0;
		}
		return new ImageOrder(id: $this->all_exhibit_ids[$this->all_exhibit_ids_index++]);
	}
	
	private function insert_image_order(ImageOrder $image_order): void {
		$this->image_order_repository->insert($image_order);
	}
	
	private function insert_next_number_image(ImageOrder $image_order): void {
		if ($this->number_image_counter >= self::COUNT_OF_NUMBER_IMAGES) {
			throw new RuntimeException('max count of number images reached');
		}
		$number = ++$this->number_image_counter;
		$is_public = ($number % 2) === 1;
		$rel_filepath = 'numbers/' . (string) $number . '.png';
		$description = 'Bild mit der Nummer ' . (string) $number . '.';
		$this->insert_image($image_order, $rel_filepath, 'image/png', $description, $is_public);
	}
	
	private function insert_next_stock_image(ImageOrder $image_order): void {
		if ($this->stock_files_index >=$this->stock_files_length) {
			$this->stock_files_index = 0;
		}
		$index = $this->stock_files_index++;
		$is_public = fake()->boolean(66);
		$rel_filepath = 'stock/' . $this->stock_files[$index];
		$description = fake()->words(fake()->numberBetween(1, 10), true);
		$this->insert_image($image_order, $rel_filepath, 'image/jpeg', $description, $is_public);
	}
	
	private function insert_image(
		ImageOrder $image_order,
		string $rel_filepath,
		string $mime_type = ImageRepository::DEFAULT_IMAGE_CONTENT_TYPE,
		string $description = '',
		bool $is_public = false
	): void {
		$image = new Image(description: $description, is_public: $is_public);
		$this->image_repository->insert($image);
		usleep(100);
		$image_data = file_get_contents($this->determinate_image_filepath($rel_filepath));
		$this->image_service->set_file_and_thumbnail($image->get_id(), $image_data, $mime_type);
		$index = count($image_order->get_image_ids());
		$image_order->insert_image_id($image->get_id(), $index);
	}
	
	private function determinate_image_filepath(string $rel_filepath): string {
		return __DIR__ . '/images/' . $rel_filepath;
	}
}
