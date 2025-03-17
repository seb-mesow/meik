<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
	/**
	 * The root template that is loaded on the first page visit.
	 *
	 * @var string
	 */
	protected $rootView = 'app';

	/**
	 * Determine the current asset version.
	 */
	public function version(Request $request): ?string
	{
		return parent::version($request);
	}

	/**
	 * Define the props that are shared by default.
	 *
	 * @return array<string, mixed>
	 */
	public function share(Request $request): array
	{
		return [
			...parent::share($request),
			'auth' => [
				'user' => $request->user(),
			],
			// Solange alle requests mit Axios oder Interia versendet werden, ist das Folgende nicht nötig,
			// da der CSRF-Token über den Cookie XSRF-TOKEN bereitgestellt wird, welchen Axios selbstständig liest.
			// 'csrf_token' => csrf_token(),
			'ziggy' => fn () => [
				...(new Ziggy)->toArray(),
				'location' => $request->url(),
			],
		];
	}
}
