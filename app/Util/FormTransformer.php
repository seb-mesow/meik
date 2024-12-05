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
	public function create_form(string|int $id, mixed $val): array {
		if (is_array($val)) {
			$mapped_val = $this->create_array_form($val);
		} else {
			$mapped_val = $val;
		}
		$form = [
			'id' => $id,
			'val' => $mapped_val,
			'errs' => [],
		];
		if (is_array($val)) {
			if (array_key_exists('__persisted', $val)) {
				$form['persisted'] = $val['__persisted'];
			}
			if (array_key_exists('__index', $val)) {
				$form['index'] = $val['__index'];
			}
			if (array_key_exists('__key', $val)) {
				$form['key'] = $val['__key'];
			}
		}
		return $form;
	}
	
	private function create_stdclass_form(stdClass $std_class): array {
		$obj = [];
		foreach ($std_class as $key => $value) {
			$obj[$key] = $this->create_form($key, $value);
		}
		return $obj;
	}
	
	private function create_array_form(array $array): array {
		$form_values = [];
		foreach ($array as $key => $elem) {
			$form_values[$key] = $this->create_form($key, $elem);
		}
		return $form_values;
	}
}
