<?php 
/**
 * WordPress Tables plugin.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */
abstract class WPTables_Parser {

	protected function create_fields($header, $data = null) {
		$fields = array();
		foreach ($header as $field) {
			$fields[] = $this->create_field($field, $this->get_type($field, $data));
		}
		return $fields;
	}

	protected function get_type($field, $data) {
		if ($data && isset($data[$field])) {
			$value = $data[$field];
			if (is_numeric($value)) {
				return "number";
			}
		}
		return "text";
	}

	protected function create_field($name, $type, $title = null) {
		$output = array();
		$output['name'] = $this->replace_dots($name);
		$output['title'] = $title ? $title : $name;
		$output['type'] = $type;
		$output['visible'] = true;
		return $output;
	}

	protected function process_data($data) {
		if (count($data) > 0) {
			foreach ($data as $row_idx => $row) {
				foreach ($row as $key => $value) {
					$new_key = $this->replace_dots($key);
					if ($new_key != $key) {
						$row[$new_key] = $value;
						unset($row[$key]);
					}
				}
				$data[$row_idx] = $row;
			}
		}
		return $data;
	}

	protected function replace_dots($str) {
		return str_replace(".", "-", $str);
	}
}

?>