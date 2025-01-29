<?php
declare(strict_types=1);

namespace App\Util;

use App\Exceptions\InvalidPartialDateString;

final class PartialDateValidator {
	/**
	 * @throws InvalidPartialDateString
	 */
	public function validate_string(string $partial_date_string): void {
		if (preg_match('/^\d\d\d\d-\d\d-\d\d$/', $partial_date_string) === 1) {
			return;
		}
		if (preg_match('/^\d\d\d\d-\d\d$/', $partial_date_string) === 1) {
			return;
		}
		if (preg_match('/^\d\d\d\d$/', $partial_date_string) === 1) {
			return;
		}
		throw new InvalidPartialDateString($partial_date_string);
	}
}
