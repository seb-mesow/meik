<?php
declare(strict_types=1);

namespace App\Http\Controllers\AJAX;

use App\Http\Controllers\Controller;
use App\Models\Exhibit;
use App\Models\FreeText;
use App\Repository\ExhibitRepository;
use Illuminate\Http\Request;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class ExhibitAJAXController extends Controller
{
	private Serializer $serializer;

	public function __construct(
		private readonly ExhibitRepository $exhibit_repository
	) {
		$this->serializer = SerializerBuilder::create()->build();
	}
	
	public function set_metadata(Request $request, int $exhibit_id)
	{
		$inventory_number = $request->input('inventory_number');
		$name = $request->input('name');
		$manufacturer = $request->input('manufacturer');
		
		$exhibit = $this->exhibit_repository->get($exhibit_id);
		$exhibit->set_inventory_number($inventory_number);
		$exhibit->set_name($name);
		$exhibit->set_manufacturer($manufacturer);
		$exhibit = $this->exhibit_repository->update($exhibit);
		sleep(5); // TODO entfernen
	}
	
	public function create_free_text(Request $request, int $exhibit_id) {
		$index = $request->input('index');
		$heading = $request->input('val.heading.val');
		$html = $request->input('val.html.val');
		$is_public = $request->input('val.is_public.val');
		
		$free_text = new FreeText(
			heading: $heading,
			html: $html,
			is_public: $is_public,
		);
		
		$exhibit = $this->exhibit_repository->get($exhibit_id);
		$exhibit->insert_free_text($free_text, $index);
		$exhibit = $this->exhibit_repository->update($exhibit);
		$free_text_id = $exhibit->get_free_texts()[$index]->get_id();
		$indices_order = $exhibit->determinate_indices_order();
		
		return response()->json([
			'id' => $free_text_id,
			'indices_order' => $indices_order
		]);
	}
	
	public function update_free_text(Request $request, int $exhibit_id, int $free_text_id) {
		$heading = $request->input('val.heading.val');
		$html = $request->input('val.html.val');
		$is_public = $request->input('val.is_public.val');
		
		$free_text = new FreeText(
			id: $free_text_id,
			heading: $heading,
			html: $html,
			is_public: $is_public,
		);
		
		$exhibit = $this->exhibit_repository->get($exhibit_id);
		$exhibit->update_free_text($free_text);
		$this->exhibit_repository->update($exhibit);
	}
	
	public function delete_free_text(Request $request, int $exhibit_id, int $free_text_id) {
		$exhibit = $this->exhibit_repository->get($exhibit_id);
		$exhibit->remove_free_text($free_text_id);
		$this->exhibit_repository->update($exhibit);
		$indices_order = $exhibit->determinate_indices_order();
		return response()->json($indices_order);
	}
	
	public function move_free_text(Request $request, int $exhibit_id, int $free_text_id) {
		$new_index = $request->input();
		$exhibit = $this->exhibit_repository->get($exhibit_id);
		$exhibit->move_free_text($free_text_id, $new_index);
		$this->exhibit_repository->update($exhibit);
		$indices_order = $exhibit->determinate_indices_order();
		return response()->json($indices_order);
	}
	
	public function update(Request $request) {
		$exhibit = $this->serializer->deserialize($request->getContent(), Exhibit::class, 'json');
		return $this->exhibit_repository->update($exhibit);
	}
}
