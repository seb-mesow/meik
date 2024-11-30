<?php
declare(strict_types=1);

namespace App\Http\Controllers\AJAX;

use App\Http\Controllers\Controller;
use App\Models\Exhibit;
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
	
	public function update(Request $request)
	{
		$exhibit = $this->serializer->deserialize($request->getContent(), Exhibit::class, 'json');
		
		return $this->exhibit_repository->update($exhibit);
	}
}
