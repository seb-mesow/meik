<?php
declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class AttachmentNotFoundException extends RuntimeException
{
	public function __construct(
		public readonly string|int $model_id,
		public readonly string $attachment_name,
		public readonly ?Throwable $previous = null,
	) {}
}
