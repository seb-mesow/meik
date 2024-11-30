<?php
declare(strict_types=1);

namespace App\Http\Controllers\AJAX;

use App\Http\Controllers\Controller;
use App\Models\Exhibit;
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

	public function update(Request $request)
	{
		$exhibit = $this->serializer->deserialize($request->getContent(), Exhibit::class, 'json');

		return $this->exhibit_repository->update($exhibit);
	}
}
