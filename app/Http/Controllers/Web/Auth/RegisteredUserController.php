<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repository\CouchDBUserProvider;
use App\Repository\UserRepository;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
	public function __construct(
		private readonly CouchDBUserProvider $user_provider
	) {}
	
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // $request->validate([
        //     // 'name' => 'required|string|max:255',
        //     // 'username' => 'required|string|lowercase|max:255|unique:'.User::class,
        //     'username' => 'required|string|lowercase|max:255:'.User::class,
        //     'password' => ['required', 'confirmed', Rules\Password::defaults()],
        // ]);
		
		$user = new User(
			$request->username,
			$request->username,
			$request->password,
			$request->forename,
			$request->surname,
			false
		);
		
        $this->user_provider->insert($user);
       	event(new Registered($user));
		
        Auth::login($user);

        return redirect(route('user.overview', absolute: false));
    }
}
