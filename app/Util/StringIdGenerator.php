<?php
declare(strict_types=1);

namespace App\Util;

class StringIdGenerator {
	private const string DATETIME_FORMAT = 'YmdHis';
	private const string RANDOM_CHARACTERS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	private const int RANDOM_CHARACTERS_MAX_INDEX = 61; 
	private const int RANDOM_LENGTH = 4;
	
	public function generate_model_id(): string {
		$id = gmdate(self::DATETIME_FORMAT);
		for ($i = 0; $i < self::RANDOM_LENGTH; $i++) {
			$id .= self::RANDOM_CHARACTERS[mt_rand(0, self::RANDOM_CHARACTERS_MAX_INDEX)];
		}
		return $id;
	}
}
