<?php
declare(strict_types=1);

namespace App\Util;

use Illuminate\Support\Carbon;

final class DateTimeUtil {
	private const string ISO_8601_DATE_FORMAT = 'Y-m-d';
	private const string ISO_8601_DATETIME_FORMAT = 'Y-m-d\\TH:i:sp';
	
	public function format_as_iso_date(Carbon $date): string {
		return $date->format(self::ISO_8601_DATE_FORMAT);
	}
	
	public function format_as_iso_date_time(Carbon $date_time): string {
		return $date_time->format(self::ISO_8601_DATETIME_FORMAT);
	}
	
	public function parse_iso_date(string $date): Carbon {
		return Carbon::createFromFormat(self::ISO_8601_DATE_FORMAT, $date);
	}
	
	public function parse_iso_date_time(string $date_time): Carbon {
		return Carbon::createFromFormat(self::ISO_8601_DATETIME_FORMAT, $date_time);
	}
}
