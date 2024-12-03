<?php
declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Exhibit;
use App\Repository\ExhibitRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Inertia\Inertia;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use stdClass;

class ExhibitAPIController extends Controller
{
	private Serializer $serializer;

    public function __construct(
        private readonly ExhibitRepository $exhibit_repository
    ) {
        $this->serializer = SerializerBuilder::create()->build();
    }

    /**
	 * TODO als API-Route umformulieren
	 */ 
    public function get_all_exhibits(Request $request)
    {
        $page = (int)$request->input('page', 0);
        $pageSize = (int)$request->input('pageSize', 10);
        $exhibits = $this->exhibit_repository->get_exhibits_paginated($page, $pageSize);
        return $this->serializer->serialize($exhibits, 'json', new SerializationContext);
    }


}
