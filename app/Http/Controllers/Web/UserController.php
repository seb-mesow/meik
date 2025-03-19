<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repository\CouchDBUserProvider;
use Inertia\Inertia;
use Request;
use Inertia\Response;

final class UserController extends Controller
{
	public function __construct(
		private readonly CouchDBUserProvider $repository,
	) {}
	
	public function overview(Request $request): Response
	{
		$users = $this->repository->get_all();
		
		$user_arr = array_map(static function(User $user): array {
			return [
				'username' => $user->get_username(),
				'forename' => $user->get_forename(),
				'surname' => $user->get_surname(),
				'is_admin' => $user->get_role(),
			];
		}, $users);
		
		return Inertia::render('Users/UserOverview', [
            'users' => $user_arr,
        ]);
	}
}
