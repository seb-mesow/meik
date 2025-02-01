<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
	/**
	 * Display the login view.
	 */
	public function login_form(): Response
	{
		return Inertia::render('Auth/Login', [
			'canResetPassword' => Route::has('password.request'),
			'status' => session('status'),
		]);
	}

	/**
	 * Handle an incoming authentication request.
	 */
	public function login(LoginRequest $request): RedirectResponse
	{
		$request->authenticate();
		
		$request->session()->regenerate();
		
		// !!! Ändere auch in web.php für die Route 'root' !!!
		return redirect()->route('category.overview');
	}

	/**
	 * Destroy an authenticated session.
	 */
	public function logout(Request $request): RedirectResponse
	{
		Auth::logout(); // use default session guard which is 'couchdb' !

		$request->session()->invalidate();
		
		// CSRF token neusetzen
		$request->session()->regenerateToken();

		return redirect()->route('login.form');
	}
}
