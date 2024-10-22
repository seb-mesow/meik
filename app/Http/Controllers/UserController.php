<?php
declare(strict_types=1);

namespace App\Http\Controllers;
use App\Models\User;
use App\Repository\CouchDBUserProvider;
use Inertia\Inertia;
use Request;
use Inertia\Response;

final class UserController extends Controller
{
	public function __construct(
		private readonly CouchDBUserProvider $user_provider,
	) {}
	
	public function all_users(Request $request): Response
	{
		$users = $this->user_provider->get_all();
		
		$user_arr = array_map(static function(User $user): array {
			return [
				'name' => $user->name,
				'is_admin' => $user->is_admin,
			];
		}, $users);
		
		return Inertia::render('Users/UserOverview', [
            'users' => $user_arr,
        ]);
	}
}
