<?php
/**
 * WordPress Tables plugin.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */
class WPTables_MySqlParser extends WPTables_Parser {
	public function parse_table($table_name) {
		global $wpdb;
		$header = $wpdb->get_col( "DESC " . $table_name, 0);
		return array(
			"fields"=> $this->create_fields($header),
			"data"	=> $table_name
		);
	}

	public function parse_query($query) {
		$query = stripslashes($query);
		$orig_query = $query;
		$query = WPTables_MySqlHelper::replace_query_vars($query);
		if (!WPTables_MySqlParser::is_query_valid($query)) {
			return new WP_Error("wpt_mysql_error_1",
				__("Query is not allowed. Please use regular SELECT syntax.", 'wptables'));
		}
		global $wpdb;
		$results = $wpdb->get_results( $query, ARRAY_A );
		if (!$results && $wpdb->last_error) {
			$error = $wpdb->last_error;
			return new WP_Error("wpt_mysql_error_2", $error);
		}
		$header = array();
		$row = array_shift($results);
		foreach ($row as $name => $value) {
			$header[] = $name;
		}
		return array(
			"fields"=> $this->create_fields($header),
			"data"	=> $orig_query
		);
	}

	public static function is_query_valid($query) {
		// Check if SELECT is in the query
		if (preg_match('/SELECT/', strtoupper($query)) != 0) {
			// Array with forbidden query parts
			$disAllow = array(
				'INSERT',
				'UPDATE',
				'DELETE',
				'RENAME',
				'DROP',
				'CREATE',
				'TRUNCATE',
				'ALTER',
				'COMMIT',
				'ROLLBACK',
				'MERGE',
				'CALL',
				'EXPLAIN',
				'LOCK',
				'GRANT',
				'REVOKE',
				'SAVEPOINT',
				'TRANSACTION',
				'SET',
			);
			// Convert array to pipe-seperated string
			$disAllow = implode('|', $disAllow);

			// Check if no other harmfull statements exist
			if (preg_match('/('.$disAllow.')/', strtoupper($query)) == 0) {
				return true;
			}
		}
		return false;
	}
}
?>
