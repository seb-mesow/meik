<?php
declare(strict_types=1);

namespace App\Service;

use App\Models\User;
use App\Repository\CouchDBUserProvider;

/**
 * @phpstan-type IUserProps array{
 *     id: string,
 *     username: string,
 *     forename: string,
 *     surname: string,
 *     role_id: string,
 * }
 */
final class UserService {
	
	public function __construct(
		private readonly CouchDBUserProvider $user_repository
	) {}
	
	/**
	 * @return array{
	 *     users: IUserProps[],
	 *     total_count: int,
	 * }
	 */
	public function query(?int $page_number = null, ?int $count_per_page = null): array {
		assert(($page_number === null) === ($count_per_page === null));
		
		$result = $this->user_repository->query($page_number, $count_per_page);
		
		$result['users'] =  array_map(static fn(User $user): array => self::determinate_props($user), $result['users']);
		
		return $result;
	}
	
	/**
	 * @return IUserProps
	 */
	private static function determinate_props(User $user): array {
		return [
			'id' => $user->get_id(),
			'username' => $user->get_username(),
			'forename' => $user->get_forename(),
			'surname' => $user->get_surname(),
			'role_id' => $user->get_role()->get_id(),
		];
	}
}
