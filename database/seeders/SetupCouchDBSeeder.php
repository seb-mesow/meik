<?php
declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use PHPOnCouch\CouchAdmin;
use PHPOnCouch\CouchClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException;
use Exception;

class SetupCouchDBSeeder extends Seeder
{
    private CouchClient $client;
    private CouchAdmin $admin;
    private GuzzleClient $http_client;
    
    public function __construct() {
        $this->http_client = new GuzzleClient([
            'base_uri' => $this->get_url(),
            'timeout' => 5,
            'allow_redirects' => false,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
        $this->client = new CouchClient($this->get_url(), $this->get_database_name(), [
            'username' => $this->get_sys_admin_username(),
            'password' => $this->get_sys_admin_password(),
            // 'cookie_auth' => true,
        ]);
        $this->admin = new CouchAdmin($this->client);
    }
    
	private function get_url(): string {
		return env('COUCHDB_URL');
	}
	private function get_database_name(): string {
		return env('COUCHDB_DATABASE');
	}
	private function get_sys_admin_username(): string {
		return env('COUCHDB_SYS_ADMIN_USERNAME');
	}
	private function get_sys_admin_password(): string {
		return env('COUCHDB_SYS_ADMIN_PASSWORD');
	}
	private function get_admin_username(): string {
		return env('COUCHDB_ADMIN_USERNAME');
	}
	private function get_admin_password(): string {
		return env('COUCHDB_ADMIN_PASSWORD');
	}
	private function get_username(): string {
		return env('COUCHDB_USERNAME');
	}
	private function get_password(): string {
		return env('COUCHDB_PASSWORD');
	}
	
    public function run(): void {
        $this->create_database();
		$this->create_users();
	}
	
	private function create_users() {
		$all_users = $this->admin->getAllUsers(true);
        foreach($all_users as $user) {
            $user_name = $user->doc->name;
            $this->admin->deleteUser($user_name);
        }
        
        // $all_users = $this->admin->getAllUsers(true);
        
        $this->admin->createUser($this->get_admin_username(), $this->get_admin_password());
        $this->admin->createUser($this->get_username(), $this->get_password());
        
        $this->admin->addDatabaseAdminUser($this->get_admin_username());
        $this->admin->addDatabaseMemberUser($this->get_username());
	}
	
	private function create_database() {
	    $status_code = -1;
        $response = null;
        try {
            $response = $this->http_client->put('/' . $this->get_database_name(), [
                'auth' => [ $this->get_sys_admin_username(), $this->get_sys_admin_password() ],
				'http_errors' => false,
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
	}
}
