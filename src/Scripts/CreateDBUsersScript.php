<?php
declare(strict_types=1);

namespace Meik\Scripts;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

require_once(dirname(__DIR__, 2) . '/vendor/autoload.php');

use PHPOnCouch\CouchClient;
use PHPOnCouch\CouchAdmin;
use PHPOnCouch\Exceptions;
use GuzzleHttp\Client as GuzzleClient;

final class CreateDBUsersScript {
    private const string SCHEME = 'http://';
    private const string DOMAIN_NAME = 'couchdb';
    private const int PORT = 5984;
    
    private const string SYS_ADMIN_USER = 'nJgHb8j3yN4BgdG46N8dnddzhy7523Hd3gAsfY10jw7Vk9wkQyp3Pdb2el6vfopy1gfF789J5GvcdS68Tf6VGcvG6ghDF4d3Dx6F';
    private const string SYS_ADMIN_PASSWORD = '7cFcFdVsHV2Gs65o7CgKNdH15pLH7tH2zBxDQxS4syPfVj6Ytp4qCfDv4CgEhZhb65TFvcjoBHvg654DFcxgh654Cd49yAQPMnK5';
    private const string DB_ADMIN_USER = 'f65gh98j4qW1K43J2l89j0h1n2byH875NmnJGjhGhV2af3sdgiUaz3hq2nbfcfz5Mh8TysW25PmnGqad90dcgTwpkhYfrQgv67Pq';
    private const string DB_ADMIN_PASSWORD = '4jzh8g769Nn87hbd745v79jnHvby627hv7ghVFH78HkMB4vfCFh0LcXRFgtPQweVCX47hVXbxcyl87vgbBVfS46Sj8vVcdEy1DUy';
    private const string DB_USER = 'ifyKhwui9vbIw3kvQbi6v98gyyEx70Zgn7veqDw4vey9sLv5weY2C4r3jcyEcZT654yqMm4knbc8Vt56vDq0678Kc45Ygf494Bsv';
    private const string DB_PASSWORD = 'Oy3YmM62DB4cE7b9rphx4hbgv6g65vgCcf68H546hvBvjuZzTz87hyGvoppnBHG646vVjC5gVuhfgO0OpOjqcCr57GVBy842Bsda';
    
    private const string DATABASE = 'meik';
    
    private CouchClient $client;
    private CouchAdmin $admin;
    private GuzzleClient $http_client;
    
    public function __construct() {
        $this->http_client = new GuzzleClient([
            'base_uri' => self::SCHEME . self::DOMAIN_NAME . ':' . self::PORT,
            'timeout' => 5,
            'allow_redirects' => false,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
        $this->client = new CouchClient(self::SCHEME . self::DOMAIN_NAME . ':' . self::PORT, self::DATABASE, [
            'username' => self::SYS_ADMIN_USER,
            'password' => self::SYS_ADMIN_PASSWORD,
            // 'cookie_auth' => true,
        ]);
        $this->admin = new CouchAdmin($this->client);
    }
    
    public function execute(): void {
        $status_code = -1;
        $response = null;
        try {
            $response = $this->http_client->put('/' . self::DATABASE, [
                'auth' => [ self::SYS_ADMIN_USER, self::SYS_ADMIN_PASSWORD ],
            ]);
        } catch (ClientException $e) {
            if (!str_contains($e->getMessage(), 'The database could not be created, the file already exists.')) {
                throw $e;
            }
        } finally {
            if ($response) {
                $status_code = $response->getStatusCode();
            }
        }
        if ($status_code !== 201 && $status_code !== 412) {
            throw new Exception('Error creating database. Http response code ' . $status_code);
        }
        
        $all_users = $this->admin->getAllUsers(true);
        foreach($all_users as $user) {
            $user_name = $user->doc->name;
            $this->admin->deleteUser($user_name);
        }
        
        $all_users = $this->admin->getAllUsers(true);
        
        $this->admin->createUser(self::DB_ADMIN_USER, self::DB_ADMIN_PASSWORD);
        $this->admin->createUser(self::DB_USER, self::DB_PASSWORD);
        
        $this->admin->addDatabaseAdminUser(self::DB_ADMIN_USER);
        $this->admin->addDatabaseMemberUser(self::DB_USER);
    }
}

try {
    (new CreateDBUsersScript)->execute();
} catch (Throwable $e) {
    exit(1);
}
