<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Location;
use PHPOnCouch\CouchClient;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Date;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPOnCouch\Exceptions\CouchException;
use stdClass;

final class LocationRepository
{

    private const ID_PREFIX = "location:";
    private Serializer $serializer;

    public function __construct(
        private readonly CouchClient $client
    ) {
        $this->serializer = SerializerBuilder::create()->build();
    }


    /**
     * @var string $id
     * @return array<Location>
     */
    public function get_locations_count(): int
    {
        $locations = $this->client->find([
            '_id' => ['$beginsWith' => self::ID_PREFIX],
        ]);
        return count($locations->docs);
    }

    /**
     * @var string $id
     * @return array<Location>
     */
    public function get_locations_paginated(int $page = 0, int $page_size = 10,): array
    {
        $locations = $this->client->limit($page_size)->skip($page * $page_size)->find([
            '_id' => ['$beginsWith' => self::ID_PREFIX],
        ])->docs;
        return $this->locationsFromArray($locations);
    }

    /**
     * @var string $id
     * @return Location|null
     */
    public function get_location(string $id): mixed
    {
        try {
            return $this->locationFromObject($this->client->getDoc($id));
        } catch (CouchException $ex) {
            return null;
        }
    }

    /**
     * @return Location
     */
    public function create(
        Location $location
    ): Location {
        $doc = $this->client->storeDoc($this->objectFromLocation($location));
        $doc = $this->client->getDoc($location->get__id());
        return $this->locationFromObject($doc);
    }

    /**
     * @return Location
     */
    public function update(
        Location $location
    ): Location {
        try {
            $doc = $this->client->getDoc($location->get__id());
            $location->set__rev($doc->_rev);
            $doc = $this->client->storeDoc($this->objectFromLocation($location));
            $doc = $this->client->getDoc($location->get__id());
            return $this->locationFromObject($doc);
        } catch (Exception $e) {
            echo "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")<br>\n";
        }
    }

    /**
     * @var string $id
     * @return Location
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
     * @return Location
     */
    public function locationFromObject($object): Location
    {
        return $this->serializer->deserialize(json_encode($object), Location::class, "json");
    }

    /**
     * @var array<object> $array
     * @return array<Location>
     */
    public function locationsFromArray($array): array
    {
        return $this->serializer->deserialize(json_encode($array), 'array<' . Location::class . '>', "json");
    }

    /**
     * @var Location $location
     * @return stdClass
     */
    public function objectFromLocation(Location $location): stdClass
    {
        $object = (new stdClass());
        $object->_id = $location->get__id();
        if ($location->get__rev()) {
            $object->_rev = $location->get__rev();
        }
        $object->name = $location->get_name();
        $object->is_public = $location->get_is_public();
        return $object;
    }
}
