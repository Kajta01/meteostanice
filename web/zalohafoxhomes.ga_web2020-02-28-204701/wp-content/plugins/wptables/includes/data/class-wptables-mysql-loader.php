<?php
/**
 * WordPress Tables plugin.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */
class WPTables_MySqlLoader {
	public function load_table($table_name, $fields) {
		global $wpdb;
		$cols = array();
		foreach ($fields as $field) {
			if ($field['visible']) {
				$cols[] = $field['name'];
			}
		}
		$query = $this->select_query($table_name, $cols);
		$results = $wpdb->get_results( $query, ARRAY_A );
		return $results;
	}

	protected function select_query($table_name, $fields) {
		$cols = isset($fields) && !empty($fields) ? join(',', $fields) : '*';
		return "SELECT {$cols} FROM {$table_name};";
	}

	public function load_query($query) {
		global $wpdb;
		$query = WPTables_MySqlHelper::replace_query_vars($query);
		$results = $wpdb->get_results( $query, ARRAY_A );
		return $results;
	}
}
?>