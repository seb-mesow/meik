<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
				'permissions' => $this->determinate_permissions(),
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
	
	private function determinate_permissions(): array
	{
		return [
			'exhibit' => [
				'create' => Gate::allows('create-exhibit'),
				'update' => Gate::allows('update-exhibit'),
				'delete' => Gate::allows('delete-exhibit'),
			],
			'place' => [
				'create' => Gate::allows('create-place'),
				'update' => Gate::allows('update-place'),
				'delete' => Gate::allows('delete-place'),
			],
			'location' => [
				'create' => Gate::allows('create-location'),
				'update' => Gate::allows('update-location'),
				'delete' => Gate::allows('delete-location'),
			],
			'rubric' => [
				'create' => Gate::allows('create-rubric'),
				'update' => Gate::allows('update-rubric'),
				'delete' => Gate::allows('delete-rubric'),
			],
			'user' => [
				'create' => Gate::allows('create-user'),
				'update' => Gate::allows('update-user'), // nur andere User
				'delete' => Gate::allows('delete-user'),
			]
		];
	}
}
