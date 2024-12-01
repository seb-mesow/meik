<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Place;
use PHPOnCouch\CouchClient;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Date;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPOnCouch\Exceptions\CouchException;
use stdClass;

final class PlaceRepository
{

    private const ID_PREFIX = "place:";
    private Serializer $serializer;

    public function __construct(
        private readonly CouchClient $client
    ) {
        $this->serializer = SerializerBuilder::create()->build();
    }


    /**
     * @var string $id
     * @return array<Place>
     */
    public function get_places_count(): int
    {
        $places = $this->client->find([
            '_id' => ['$beginsWith' => self::ID_PREFIX],
        ]);
        return count($places->docs);
    }

    /**
     * @var string $id
     * @return array<Place>
     */
    public function get_places_paginated(string $locationid, int $page = 0, int $page_size = 10,): array
    {
        $places = $this->client->limit($page_size)->skip($page * $page_size)->find([
            '_id' => ['$beginsWith' => self::ID_PREFIX],
            'location' => $locationid
        ])->docs;
        return $this->placesFromArray($places);
    }

    /**
     * @var string $id
     * @return Place|null
     */
    public function get_place(string $id): mixed
    {
        try {
            return $this->placeFromObject($this->client->getDoc($id));
        } catch (CouchException $ex) {
            return null;
        }
    }

    /**
     * @return Place
     */
    public function create(
        Place $place
    ): Place {
        $doc = $this->client->storeDoc($this->objectFromPlace($place));
        $doc = $this->client->getDoc($place->get__id());
        return $this->placeFromObject($doc);
    }

    /**
     * @return Place
     */
    public function update(
        Place $place
    ): Place {
        try {
            $doc = $this->client->getDoc($place->get__id());
            $place->set__rev($doc->_rev);
            $doc = $this->client->storeDoc($this->objectFromPlace($place));
            $doc = $this->client->getDoc($place->get__id());
            return $this->placeFromObject($doc);
        } catch (Exception $e) {
            echo "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")<br>\n";
        }
    }

    /**
     * @var string $id
     * @return Place
     */
    public function delete(
        string $id
    ): void {
        try {
            $doc = $this->client->getDoc($id);
        } catch (Exception $e) {
            echo "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")<br>\n";
        }
        // permanently remove the document
        try {
            $this->client->deleteDoc($doc);
        } catch (Exception $e) {
            echo "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")<br>\n";
        }
    }

    /**
     * @var stdClass $object
     * @return Place
     */
    public function placeFromObject($object): Place
    {
        return $this->serializer->deserialize(json_encode($object), Place::class, "json");
    }

    /**
     * @var array<object> $array
     * @return array<Place>
     */
    public function placesFromArray($array): array
    {
        return $this->serializer->deserialize(json_encode($array), 'array<' . Place::class . '>', "json");
    }

    /**
     * @var Place $place
     * @return stdClass
     */
    public function objectFromPlace(Place $place): stdClass
    {
        $object = (new stdClass());
        $object->_id = $place->get__id();
        if ($place->get__rev()) {
            $object->_rev = $place->get__rev();
        }
        $object->name = $place->get_name();
        $object->is_public = $place->get_is_public();
        $object->location = $place->getLocation();
        return $object;
    }
}
