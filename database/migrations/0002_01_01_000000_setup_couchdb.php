<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use GuzzleHttp\Client as GuzzleClient;
use PHPOnCouch\CouchAdmin;
use PHPOnCouch\CouchClient;

return new class extends Migration
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
	
	/**
	 * Run the migrations.
	 */
	public function up(): void {
		$this->delete_all_users();
		$this->delete_database_if_exists();
		$this->create_database();
		$this->create_users();
	}
	
	private function delete_database_if_exists(): void {
		try {	
			$this->http_client->delete('/'. $this->get_database_name(), [
				'auth' => [ $this->get_sys_admin_username(), $this->get_sys_admin_password() ],
				'http_errors' => false
			]);
		} catch (Throwable $err) {
			throw $err;
		}
	}
	
	private function create_database(): void {
		try {
			$this->http_client->put('/' . $this->get_database_name(), [
				'auth' => [ $this->get_sys_admin_username(), $this->get_sys_admin_password() ],
				'http_errors' => false,
			]);
		} catch (Throwable $err) {
			throw $err;
		}
	}
	
	private function delete_all_users() {
		$all_users = $this->admin->getAllUsers(true);
		foreach($all_users as $user) {
			$user_name = $user->doc->name;
			$this->admin->deleteUser($user_name);
		}
	}
	
	private function create_users() {
		$this->admin->createUser($this->get_admin_username(), $this->get_admin_password());
		$this->admin->createUser($this->get_username(), $this->get_password());
		
		$this->admin->addDatabaseAdminUser($this->get_admin_username());
		$this->admin->addDatabaseMemberUser($this->get_username());
	}
	
	/**
	 * Reverse the migrations.
	 */
	public function down(): void {
		$this->delete_database_if_exists();
	}
};
