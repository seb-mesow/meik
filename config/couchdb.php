<?php
declare(strict_types=1);

return [
	'url' => env('COUCHDB_URL'),
	'database' => env('COUCHDB_DATABASE'),
	'username' => env('COUCHDB_USERNAME'),
	'password' => env('COUCHDB_PASSWORD'),
	'admin_username' => env('COUCHDB_ADMIN_USERNAME'),
	'admin_password' => env('COUCHDB_ADMIN_PASSWORD'),
];
