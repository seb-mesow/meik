<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Exhibit;
use App\Models\FreeText;
use App\Models\Image;
use App\Models\ImageOrder;
use App\Repository\ExhibitRepository;
use App\Repository\ImageOrderRepository;
use App\Repository\ImageRepository;
use Illuminate\Database\Seeder;
use RuntimeException;

class ImageSeeder extends Seeder
{
	private int $dummy_image_counter = 0;
	private const int COUNT_OF_NUMBER_IMAGES = 20;
	
	public function __construct(
		private readonly ImageOrderRepository $image_order_repository,
		private readonly ImageRepository $image_repository,
		private readonly ExhibitSeeder $exhibit_seeder,
	) {}
	
	/**
	 * Seed the application's database.
	 */
	public function run(): void {
		$exhibits = $this->exhibit_seeder->get_exhibits();
		
		$image_order = $this->create_image_order($exhibits[0]);
		$this->insert_next_dummy_image($image_order);
		$this->insert_next_dummy_image($image_order);
		$this->insert_next_dummy_image($image_order);
		$this->insert_next_dummy_image($image_order);
		$this->insert_next_dummy_image($image_order);
		$this->insert_image_order($image_order);
		
		$image_order = $this->create_image_order($exhibits[1]);
		$this->insert_next_dummy_image($image_order);
		$this->insert_next_dummy_image($image_order);
		$this->insert_next_dummy_image($image_order);
		$this->insert_image_order($image_order);
		
		$image_order = $this->create_image_order($exhibits[2]);
		$this->insert_next_dummy_image($image_order);
		$this->insert_next_dummy_image($image_order);
		$this->insert_image_order($image_order);
		
		// fourth exhibit without images
	}
	
	private function create_image_order(Exhibit $exhibit): ImageOrder {
		return new ImageOrder(id: $exhibit->get_id());
	}
	
	private function insert_image_order(ImageOrder $image_order): void {
		$this->image_order_repository->insert($image_order);
	}
	
	private function insert_next_dummy_image(ImageOrder $image_order): void {
		if ($this->dummy_image_counter >= self::COUNT_OF_NUMBER_IMAGES) {
			throw new RuntimeException('max count of number images reached');
		}
		$number = ++$this->dummy_image_counter;
		$is_public = ($number % 2) === 1;
		$rel_filepath = 'numbers/' . (string) $number . '.png';
		$description = 'Bild mit der Nummer ' . (string) $number . '.';
		$this->insert_image($image_order, $rel_filepath, 'image/png', $description, $is_public);
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
		$this->image_repository->set_file($image->get_id(), $image_data, $mime_type);
		$index = count($image_order->get_image_ids());
		$image_order->insert_image_id($image->get_id(), $index);
	}
	
	private function determinate_image_filepath(string $rel_filepath): string {
		return __DIR__ . '/images/' . $rel_filepath;
	}
}
