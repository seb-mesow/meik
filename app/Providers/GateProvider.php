<?php
declare(strict_types=1);

namespace App\Providers;

use App\Models\Enum\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class GateProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 */
	public function boot(): void
	{
		Gate::define('create-exhibit', static fn (User $user): bool => $user->is_at_least(cmp_role: UserRole::EDITOR));
		Gate::define('update-exhibit', static fn (User $user): bool => $user->is_at_least(cmp_role: UserRole::EDITOR));
		Gate::define('delete-exhibit', static fn (User $user): bool => $user->is_at_least(cmp_role: UserRole::ADMIN));
		
		Gate::define('create-place', static fn (User $user): bool => $user->is_at_least(cmp_role: UserRole::ADMIN));
		Gate::define('update-place', static fn (User $user): bool => $user->is_at_least(cmp_role: UserRole::ADMIN));
		Gate::define('delete-place', static fn (User $user): bool => $user->is_at_least(cmp_role: UserRole::ADMIN));
		
		Gate::define('create-location', static fn (User $user): bool => $user->is_at_least(cmp_role: UserRole::ADMIN));
		Gate::define('update-location', static fn (User $user): bool => $user->is_at_least(cmp_role: UserRole::ADMIN));
		Gate::define('delete-location', static fn (User $user): bool => $user->is_at_least(cmp_role: UserRole::ADMIN));
		
		Gate::define('create-rubric', static fn (User $user): bool => $user->is_at_least(cmp_role: UserRole::ADMIN));
		Gate::define('update-rubric', static fn (User $user): bool => $user->is_at_least(cmp_role: UserRole::ADMIN));
		Gate::define('delete-rubric', static fn (User $user): bool => $user->is_at_least(cmp_role: UserRole::ADMIN));
		
		Gate::define('create-user', static fn (User $user): bool => $user->is_at_least(cmp_role: UserRole::ADMIN));
		Gate::define('update-user', static fn (User $user): bool => $user->is_at_least(cmp_role: UserRole::ADMIN)); // nur andere User
		Gate::define('delete-user', static fn (User $user): bool => $user->is_at_least(cmp_role: UserRole::ADMIN));
	}
}
