<?php
declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Enum\UserRole;
use App\Service\UserService;
use Inertia\Inertia;
use Request;
use Inertia\Response as InertiaResponse;

final class UserController extends Controller
{
	private const int INITIAL_COUNT_PER_PAGE = 10;
	
	public function __construct(
		private readonly UserService $user_service,
	) {}
	
	public function overview(Request $request): InertiaResponse
	{
		$result = $this->user_service->query(0, self::INITIAL_COUNT_PER_PAGE);
		
		return Inertia::render('Users/Users', [
			'users' => $result['users'],
			'total_count' => $result['total_count'],
			'count_per_page' => self::INITIAL_COUNT_PER_PAGE,
			'selectable_values' => $this->determinate_selectable_values(),
		]);
	}
	
	private function determinate_selectable_values(): array {
		$_this = $this;
		$all_roles = UserRole::cases();
		$all_roles = array_map(static function (UserRole $role) use ($_this): array {
			return [
				'id' => $role->get_id(),
				'name' => $role->get_name(),
			];
		}, $all_roles);
		
		return [
			'role' => $all_roles,
		];
	}
	
	public function new(Request $request): InertiaResponse
	{
		return Inertia::render('Users/Register', [
			'selectable_values' => $this->determinate_selectable_values(),
		]);
	}
}
