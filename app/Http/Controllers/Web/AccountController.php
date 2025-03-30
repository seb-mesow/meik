<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Enum\UserRole;
use App\Service\UserService;
use Inertia\Inertia;
use Request;
use Inertia\Response as InertiaResponse;

final class AccountController extends Controller
{
	public function __construct(
		private readonly UserService $user_service,
	) {}
	
	public function details(Request $request): InertiaResponse
	{
		return Inertia::render('Account/Account', [
		]);
	}
	
	public function change_password(Request $request): InertiaResponse
	{
		return Inertia::render('Account/ChangePassword', [
		]);
	}
}
