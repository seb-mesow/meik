<?php
declare(strict_types=1);

namespace App\Http\Controllers\AJAX;

use App\Http\Controllers\Controller;
use App\Models\Exhibit;
use App\Models\FreeText;
use App\Repository\ExhibitRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
	
	public function set_metadata(Request $request, string $id)
	{
		$inventory_number = $request->input('inventory_number');
		$name = $request->input('name');
		$manufacturer = $request->input('manufacturer');
		
		$exhibit = $this->exhibit_repository->get($id);
		$exhibit->set_inventory_number($inventory_number);
		$exhibit->set_name($name);
		$exhibit->set_manufacturer($manufacturer);
		$exhibit = $this->exhibit_repository->update($exhibit);
		sleep(5); // TODO entfernen
	}
	
	/**
	 * inserts a new free text at the specified index
	 */
	public function add_free_text(Request $request, string $exhibit_id, int $free_text_index) {
		$heading = $request->input('heading');
		$html = $request->input('html');
		$is_public = $request->input('is_public');
		
		$free_text = new FreeText(
			heading: $heading,
			html: $html,
			is_public: $is_public,
		);
		
		$exhibit = $this->exhibit_repository->get($exhibit_id);
		$exhibit->insert_free_text($free_text, $free_text_index);
		$this->exhibit_repository->update($exhibit);
	}
	
	/**
	 * updates/replaces the free text at the specified index
	 */
	public function update_free_text(Request $request,  string $exhibit_id, int $free_text_index) {
		$heading = $request->input('heading');
		$html = $request->input('html');
		$is_public = $request->input('is_public');
		
		$free_text = new FreeText(
			heading: $heading,
			html: $html,
			is_public: $is_public,
		);
		
		$exhibit = $this->exhibit_repository->get($exhibit_id);
		$exhibit->update_free_text($free_text, $free_text_index);
		$this->exhibit_repository->update($exhibit);
	}
	
	/**
	 * deletes the new free text at the specified index
	 */
	public function delete_free_text(Request $request,  string $exhibit_id, int $free_text_index) {
		$exhibit = $this->exhibit_repository->get($exhibit_id);
		$exhibit->remove_free_text($free_text_index);
		$this->exhibit_repository->update($exhibit);
	}
	
	public function update(Request $request)
	{
		$exhibit = $this->serializer->deserialize($request->getContent(), Exhibit::class, 'json');
		return $this->exhibit_repository->update($exhibit);
	}
}
