<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Exhibit;
use PHPOnCouch\CouchClient;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Date;
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
     * @return Exhibit|null
     */
    public function get_all_exhibits(): mixed
    {
        $exhibits = $this->client->find([
            '_id' => ['$beginsWith' => self::ID_PREFIX],
        ])->docs;

        return $this->exhibitFromObject($exhibits);
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
     * @var Exhibit $exhibit
     * @return stdClass
     */
    public function objectFromExhibit(Exhibit $exhibit): object
    {
        return json_decode($this->serializer->serialize($exhibit, 'json'));
    }
}
