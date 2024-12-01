<?php
declare(strict_types=1);

namespace App\Util;

use stdClass;

class FormTransformer {
	/**
	 * creates a form for attributes without errors
	 * 
	 * Not yet set attributes and sub-attributes must have the value null.
	 * 
	 * @param array<string|int, mixed> $attrs
	 * @return array
	 */
	public function create_form(array $attrs): array {
		$vals = [];
		foreach ($attrs as $prop => $val) {
			$vals[$prop] = $this->create_value_form($prop, $attrs[$prop]);
		}
		return [
			'vals' => $vals,
			'errs' => [],
		];
	}
	
	/**
	 * creates a sub-form for one attribute without errors
	 * 
	 * Not yet set attributes and sub-attributes must have the value null.
	 * 
	 * @param string|int $attr
	 * @param mixed $val
	 * @return array
	 */
	private function create_value_form(string|int $attr, mixed $val): array {
		if (is_array($val)) {
			$mapped_val = $this->create_array_form($val);
		} else if ($val instanceof stdClass) {
			$mapped_val = $this->create_stdclass_form($val);
		} else {
			$mapped_val = $this->create_simple_form($val);
		}
		return [
			'id' => $attr,
			'val' => $mapped_val,
			'errs' => []
		];
	}
	
	private function create_stdclass_form(stdClass $std_class): array {
		$obj = [];
		foreach ($std_class as $key => $value) {
			$obj[$key] = $this->create_value_form($key, $value);
		}
		return $obj;
	}
	
	private function create_array_form(array $array): array {
		$form_values = [];
		foreach ($array as $index => $elem) {
			$form_values[$index] = $this->create_value_form($index, $elem);
		}
		return $form_values;
	}
	
	private function create_simple_form(mixed $val): mixed {
		return $val;
	}
}
