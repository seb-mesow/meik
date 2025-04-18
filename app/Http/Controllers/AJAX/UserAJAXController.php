<?php
declare(strict_types=1);

namespace App\Http\Controllers\AJAX;

use App\Http\Controllers\Controller;
use App\Models\Enum\UserRole;
use App\Models\User;
use App\Repository\CouchDBUserProvider;
use App\Service\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserAJAXController extends Controller
{
	public function __construct(
		private readonly CouchDBUserProvider $user_repository,
		private readonly UserService $user_service,
	) {}

	public function query(Request $request): JsonResponse {
		$page_number = $request->query('page_number');
		$count_per_page = $request->query('count_per_page');
		
		$page_number = is_string($page_number) ? trim($page_number) : null;
		$page_number = $page_number === '' ? null : $page_number;
		$page_number = is_numeric($page_number) ? (int) $page_number : null;
		$count_per_page = is_string($count_per_page) ? trim($count_per_page) : null;
		$count_per_page = $count_per_page === '' ? null : $count_per_page;
		$count_per_page = is_numeric($count_per_page) ? (int) $count_per_page : null;
		
		assert(($page_number === null) == ($count_per_page === null));
		
		$result = $this->user_service->query($page_number, $count_per_page);
		
		return response()->json($result);
	}

	public function create(Request $request): JsonResponse {
		$username = $request->input('username');
		$forename = $request->input('forename');
		$surname = $request->input('surname');
		$password = $request->input('password');
		$role_id = $request->input('role_id');
		
		$username = trim($username);
		$forename = trim($forename);
		$surname = trim($surname);
				
		$user = new User(
			username: $username,
			forename: $forename,
			surname: $surname,
			password: $password,
			role: UserRole::from($role_id),
		);
		$this->user_repository->insert($user);
		return response()->json($user->get_id());
	}

	public function update(Request $request, string $user_id): void {
		$username = $request->input('username');
		$forename = $request->input('forename');
		$surname = $request->input('surname');
		$role_id = $request->input('role_id');
		
		$username = trim($username);
		$forename = trim($forename);
		$surname = trim($surname);
		
		$user = $this->user_repository->get($user_id);
		$user->set_username($username);
		$user->set_forename($forename);
		$user->set_surname($surname);
		$user->set_role(UserRole::from($role_id));
		$this->user_repository->update($user);
	}

	public function delete(string $location_id): void {
		$this->user_repository->remove_by_id($location_id);
	}
}
