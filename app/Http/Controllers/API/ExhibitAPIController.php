<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Exhibit;
use App\Repository\ExhibitRepository;
use Illuminate\Http\JsonResponse;
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

    public function get_exhibits_paginated(Request $request): JsonResponse
    {
        $page = (int)$request->input('page', 0);
        $pageSize = (int)$request->input('pageSize', 10);
        $exhibits = $this->exhibit_repository->get_exhibits_paginated(null, $page, $pageSize);
        return new JsonResponse($this->serializer->serialize($exhibits, 'json', (new SerializationContext)));
    }

    public function get_exhibit_by_id(int $id): JsonResponse
    {
        $exhibit = $this->exhibit_repository->get($id);
        return new JsonResponse($this->serializer->serialize($exhibit, 'json', (new SerializationContext)));
    }

    public function search_exhibits(string $query): JsonResponse
    {
        $queryParts = explode(' ', $query);
        $selectors = [];

        foreach ($queryParts as $queryPart) {
            $selector = [
                '$or' => [
                    [
                        'manufacturer' => [
                            '$regex' => '(?i)' . $queryPart // Regex für manufacturer
                        ]
                    ],
                    [
                        'name' => [
                            '$regex' => '(?i)' . $queryPart // Regex für name
                        ]
                    ],
                    [
                        'inventory_number' => [
                            '$eq' => $queryPart // Exakte Übereinstimmung für inventory_number
                        ]
                    ]
                ]
            ];
            $selectorParts[] = $selector;
        }

        $selectors = [
            '$and' => $selectorParts
        ];

        $exhibits = $this->exhibit_repository->get_by_selectors($selectors);

        return new JsonResponse($this->serializer->serialize($exhibits, 'json', (new SerializationContext)));
    }

    public function find_exhibits_by_filter(Request $request): JsonResponse
    {
        $fields = $request->all('field');
        $operator = $request->input('operator', 'and');

        switch ($operator) {
            case 'and':
                $operator = [
                    '$and'
                ];
            case 'or':
                $operator = [
                    '$or'
                ];
        }

        foreach ($fields['field'] as $field) {
            $field_pair = explode(':', $field);

            $selector = [
                $field_pair[0] => [
                    '$eq' => $field_pair[1]
                ]
            ];
            $selectorParts[] = $selector;
        }

        $selectors = [
            '$and' => $selectorParts
        ];
        $exhibits = $this->exhibit_repository->get_by_selectors($selectors);

        return new JsonResponse($this->serializer->serialize($exhibits, 'json', (new SerializationContext)));
    }
}
