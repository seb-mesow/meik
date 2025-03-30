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
use Illuminate\Support\Facades\Auth;

class AccountAJAXController extends Controller
{
	public function __construct(
		private readonly CouchDBUserProvider $user_repository,
		private readonly UserService $user_service,
	) {}

	public function change_password(Request $request): JsonResponse {
		$old_password = $request->input('old_password');
		$new_password = $request->input('new_password');
		
		$old_password = (is_string($old_password) && $old_password !== '') ? $old_password : null;
		$new_password = (is_string($new_password) && $new_password !== '') ? $new_password : null;
		
		if (!is_string($old_password) || !is_string($new_password)) {
			return response()->json(status: 422, data: [
				'empty request parameters'
			]);
		}
		
		/** @var User $user */
		$user = Auth::user();
		
		if (!$user->is_password($old_password)) {
			return response()->json(status: 422, data: [
				'old_password incorrect'
			]);
		}
		
		$user->set_password($new_password);
		$this->user_repository->update($user);
		
		return response()->json();
	}
}
