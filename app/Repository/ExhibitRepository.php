<?php

declare(strict_types=1);

namespace App\Repository;

use App\Models\Exhibit;
use PHPOnCouch\CouchClient;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Date;
use PHPOnCouch\Exceptions\CouchException;

final class ExhibitRepository
{
    public function __construct(
        private readonly CouchClient $client
    ) {}


    /**
     * @var string $id
     * @return Exhibit|null
     */
    public function get_all_exhibits(): mixed
    {
        return $this->client->getView('exhibit_filter', 'by_exhibit_prefix');
    }

    /**
     * @var string $id
     * @return Exhibit|null
     */
    public function get_exhibit(string $id): mixed
    {
        try {
            return $this->client->getDoc($id);
        } catch (CouchException $ex) {
            return null;
        }
    }

    /**
     * @return Exhibit
     */
    public function create(
        Exhibit $exhibit
    ): mixed {
        return $this->client->storeDoc($exhibit);
    }

    /**
     * @return Exhibit
     */
    public function update(
        Exhibit $exhibit
    ): void {
        try {
            $doc = $this->client->getDoc($exhibit->get__id());
            $this->client->storeDoc($doc);
        } catch(Exception $e) {
            echo "ERROR: ".$e->getMessage()." (".$e->getCode().")<br>\n";
        }


    }

    /**
     * @return Exhibit
     */
    public function delete(
        string $id
    ): void {
        try {
            $doc = $this->client->getDoc($id);
        } catch (Exception $e) {
            echo "ERROR: ".$e->getMessage()." (".$e->getCode().")<br>\n";
        }
        // permanently remove the document
        try {
            $this->client->deleteDoc($doc);
        } catch (Exception $e) {
            echo "ERROR: ".$e->getMessage()." (".$e->getCode().")<br>\n";
        }
    }
}
