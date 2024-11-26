<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Exhibit;
use PHPOnCouch\CouchClient;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Date;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use PHPOnCouch\Exceptions\CouchException;
use stdClass;

final class ExhibitRepository
{

    private const ID_PREFIX = "exhibit:";
    private Serializer $serializer;

    public function __construct(
        private readonly CouchClient $client
    ) {
        $this->serializer = SerializerBuilder::create()->build();
    }


    /**
     * @var string $id
     * @return array<Exhibit>
     */
    public function get_all_exhibits(): array
    {
        $exhibits = $this->client->find([
            '_id' => ['$beginsWith' => self::ID_PREFIX],
        ])->docs;
        return $this->exhibitsFromArray($exhibits);
    }

    /**
     * @var string $id
     * @return Exhibit|null
     */
    public function get_exhibit(string $id): mixed
    {
        try {
            return $this->exhibitFromObject($this->client->getDoc($id));
        } catch (CouchException $ex) {
            return null;
        }
    }

    /**
     * @return Exhibit
     */
    public function create(
        Exhibit $exhibit
    ): Exhibit {
        $doc = $this->client->storeDoc($this->objectFromExhibit($exhibit));
        return $this->exhibitFromObject($doc);
    }

    /**
     * @return Exhibit
     */
    public function update(
        Exhibit $exhibit
    ): Exhibit {
        try {
            $doc = $this->client->getDoc($exhibit->get__id());
            $exhibit->set_rev($doc->_rev);
            $doc = $this->client->storeDoc($this->objectFromExhibit($exhibit));
            return $this->exhibitFromObject($doc);
        } catch (Exception $e) {
            echo "ERROR: " . $e->getMessage() . " (" . $e->getCode() . ")<br>\n";
        }
    }

    /**
     * @var string $id
     * @return Exhibit
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
     * @return Exhibit
     */
    public function exhibitFromObject($object): Exhibit
    {
        return $this->serializer->deserialize(json_encode($object), Exhibit::class, "json");
    }

    /**
     * @var array<object> $array
     * @return array<Exhibit>
     */
    public function exhibitsFromArray($array): array
    {
        return $this->serializer->deserialize(json_encode($array), 'array<' . Exhibit::class . '>', "json");
    }

    /**
     * @var Exhibit $exhibit
     * @return stdClass
     */
    public function objectFromExhibit(Exhibit $exhibit): stdClass
    {
        $object = (new stdClass());
        $object->_id = $exhibit->get__id();
        $object->_rev = $exhibit->get__rev();
        $object->name = $exhibit->get_name();
        $object->manufacturer = $exhibit->get_manufacturer();
        $object->year_of_construction = $exhibit->get_year_of_construction();
        $object->aquiry_date = $exhibit->get_aquiry_date(); 
        $object->free_text_fielfs = $exhibit->get_free_text_fields(); 

        return $object;
    }
}
