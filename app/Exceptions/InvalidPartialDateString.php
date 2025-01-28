<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class InvalidPartialDateString extends RuntimeException
{
	public function __construct(
		public readonly string $invalid_string,
		public readonly ?Throwable $previous = null,
	) {}
}
