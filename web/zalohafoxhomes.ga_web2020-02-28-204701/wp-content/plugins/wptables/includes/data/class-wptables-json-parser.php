<?php
/**
 * WordPress Tables plugin.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */
class WPTables_JsonParser extends WPTables_Parser {
	public function parse_text($text) {
		$text = stripslashes($text);
		$data = json_decode($text, true);
		if (!$data) {
			return false;
		}
		$is_first_header = false;
		$is_array_of_objects = $this->is_array_of_objects($data);
		$header = $this->parse_header($data[0], $is_array_of_objects, $is_first_header);
		if (!$is_array_of_objects) {
			if ($is_first_header) {
				array_shift($data);
			}
			foreach ($data as &$row) {
				$row = array_combine($header, $row);
			}
		}	
		return array(
			"fields"=> $this->create_fields($header, count($data) > 0 ? $data[0] : null),
			"data"	=> $this->process_data($data)
		);
	}

	private function is_array_of_objects($data) {
		if (count($data) < 1) {
			return false;
		}
		$arr = $data[0];
		if (count($data) < 1) {
			return false;
		}
		return array_keys($arr) !== range(0, count($arr) - 1);
	}

	public function parse_file($file) {
		$text = file_get_contents($file);
		if ($text) {
			return $this->parse_text($text);
		} else {
		    return false;
		} 
	}

	private function parse_header($obj, $is_array_of_objects, $is_first_header) {
		$output = array();
		if ($is_array_of_objects) {
			foreach ($obj as $key => $value) {
				$output[] = $key;
			}
		} else if ($is_first_header) {
			return $obj;
		} else {
			for ($i=0; $i < count($obj); $i++) { 
				$output[] = __('Field', 'wptables')." $i";
			}
		}
		return $output;
	}
}
?>
