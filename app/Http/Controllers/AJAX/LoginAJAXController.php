<?php
declare(strict_types=1);

namespace App\Http\Controllers\AJAX;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class LoginAJAXController extends Controller
{
	/**
	 * Handle an incoming authentication request.
	 */
	public function login(LoginRequest $request): JsonResponse
	{
		try {
			$request->authenticate();
			
			// erneuert u.a. den CSRF-Token
			$request->session()->regenerate();
		} catch (Throwable $e) {
			return response(status: 422)->json();
		}
		
		// !!! Ändere auch in web.php für die Route 'root' !!!
		// return redirect()->route('category.overview');
		return response()->json();
	}

	/**
	 * Destroy an authenticated session.
	 */
	public function logout(Request $request): RedirectResponse
	{
		// use default session guard, which is configured only in config/auth.php !
		Auth::logout();

		$request->session()->invalidate();
		
		// CSRF token neusetzen
		$request->session()->regenerateToken();

		return redirect()->route('login.form');
	}
}
