<?php
declare(strict_types=1);

namespace App\Http\Controllers\AJAX;

use App\Repository\CouchDBUserProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserAJAXController extends Controller
{
	public function __construct(
		private readonly CouchDBUserProvider $repository
	) {}
	
	public function set_admin(Request $request, string $username)
	{
		sleep(5); // TODO rausnehmen
		$user = $this->repository->find_by_username($username);
		if (!$user) {
			return response(null, 404);
		}
		$is_admin = $request->input('is_admin');
		$user = $user->with_is_admin($is_admin);
		$this->repository->update($user);
		return response(null, 204);
	}
}
