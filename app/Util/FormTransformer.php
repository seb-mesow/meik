<?php
declare(strict_types=1);

namespace App\Util;

use stdClass;

class FormTransformer {
	
	/**
	 * creates a form without errors
	 * 
	 * Not yet set attributes and sub-attributes must have the value null.
	 * 
	 * @param string|int $id
	 * @param mixed $val
	 * @return array
	 */
	public function create_form(string|int $id, mixed $val, bool $persisted): array {
		if (is_array($val)) {
			$mapped_val = $this->create_array_form($val, $persisted);
		} elseif ($val instanceof stdClass) {
			$mapped_val = $this->create_stdclass_form($val, $persisted);
		} else {
			$mapped_val = $val;
		}
		return [
			'id' => $id,
			'val' => $mapped_val,
			'errs' => [],
			'persisted' => $persisted,
		];
	}
	
	private function create_stdclass_form(stdClass $std_class, bool $persisted): array {
		$obj = [];
		foreach ($std_class as $key => $value) {
			$obj[$key] = $this->create_form($key, $value, $persisted);
		}
		return $obj;
	}
	
	private function create_array_form(array $array, bool $persisted): array {
		$form_values = [];
		foreach ($array as $index => $elem) {
			$form_values[$index] = $this->create_form($index, $elem, $persisted);
		}
		return $form_values;
	}
}
