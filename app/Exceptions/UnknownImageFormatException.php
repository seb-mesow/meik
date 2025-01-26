<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class UnknownImageFormatException extends RuntimeException
{
	public function __construct(
		public readonly ?Throwable $previous = null
	) {}
}
