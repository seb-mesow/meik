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
    
    private const string SYS_ADMIN_USER = 'H-j6d)5<\*3m1!p_Rq}8';
    private const string SYS_ADMIN_PASSWORD = '1e~0XAy(L.|6-fUj+Kw2V>f,';
    private const string DB_ADMIN_USER = 'nf65gh-98j4qW1K43-J2L89j0h1n2bV-H875N-mnJGj-hGHV2af3sdg-iuaz3hq2nbB-fcf';
    private const string DB_ADMIN_PASSWORD = 'n(4jzh_8g*769#N/n=87hbdf"-_,7%4"5v79)';
    private const string DB_USER = 'n2if-hwui9vbw4-kvbi6v98gyyx7-0ygn7veq-wvey9sogv5we-24r3jycyc';
    private const string DB_PASSWORD = 'Oy,3YmM!62+DB4q#cE7b_9rp';
    
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
        // $status_code = -1;
        try {
            $response = $this->http_client->put('/' . self::DATABASE, [
                'auth' => [ self::SYS_ADMIN_USER, self::SYS_ADMIN_PASSWORD ],
            ]);
            // $status_code = $response->getStatusCode();
        } catch (ClientException $e) {
            if (!str_contains($e->getMessage(), 'The database could not be created, the file already exists.')) {
                throw $e;
            }
        }
        // if ($status_code !== 201 && $status_code !== 412) {
        //     throw new Exception('Error creating database. Http response code ' . $status_code);
        // }
        
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
