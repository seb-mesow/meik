<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Exhibit;
use PHPOnCouch\CouchClient;
use App\Models\User;
use Illuminate\Support\Facades\Date;
use PHPOnCouch\Exceptions\CouchException;

final class ExhibitRepository
{
    public const string ID_PREFIX = "exhibit:";

    public function __construct(
        private readonly CouchClient $client
    ) {}

    /**
     * @return Exhibit
     */
    public function create(
        string $designation,
        string $inventory_number,
        string $manufacturer,
        string $year_of_construction,
        string $location,
        Date $aquiry_date
    ): Exhibit {
        $this->client->storeDoc((object) [
            '_id' => self::ID_PREFIX . $inventory_number,
            'designation' => $designation,
            'manufacturer' => $manufacturer,
            'year_of_construnction' => $year_of_construction,
            'location' => $location,
            'aquiry_date' => $aquiry_date
        ]);
        return new Exhibit($inventory_number, $designation, $manufacturer, $year_of_construction, $location, $aquiry_date);
    }

    /**
     * @var string $id
     * @return Exhibit|null
     */
    public function get_exhibit(string $id): ?Exhibit {
        try {
            return $this->client->getDoc($id);
        } catch(CouchException $ex) {
            return null;
        }
    }

        /**
     * @var string $id
     * @return Exhibit|null
     */
    public function get_all_exhibits(): ?Exhibit {
        try {
            return $this->client->getAllDocs();
        } catch(CouchException $ex) {
            return null;
        }
    }
}
