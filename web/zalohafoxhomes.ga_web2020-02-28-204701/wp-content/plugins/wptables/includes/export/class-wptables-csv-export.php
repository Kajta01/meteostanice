<?php
/**
 * WordPress Tables plugin.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */
class WPTables_CsvExport {
	private $table_id;
	private $fields;
	private $data;

	public function __construct($table_id) {
		$this->table_id = $table_id;
		$this->fields = json_decode(get_post_meta($table_id, 'wpt_fields', true), true);
		$post = get_post($table_id);
		if ($post->post_mime_type == 'mysql/db-table') {
			$table_name = json_decode($post->post_content);
			$loader = new WPTables_MySqlLoader();
			$this->data = $loader->load_table($table_name, $fields);
		} elseif ($post->post_mime_type == 'mysql/mysql-query') {
			$query_str = $post->post_content;
			$loader = new WPTables_MySqlLoader();
			$this->data = $loader->load_query($query_str); 
		} else {
			$this->data = json_decode($post->post_content, true);
		}
	}

	public function export() {
		$cols = array();
		$titles = array();
		foreach ($this->fields as $field) {
			if ($field['visible']) {
				$cols[] = $field['name'];
				$titles[] = $field['title'];
			}
		}
		$output = array($titles);
		foreach ($this->data as $data_row) {
			$row = array();
			foreach ($cols as $col_name) {
				$row[] = $data_row[$col_name];
			}
			$output[] = $row;
		}
		return $this->to_csv_str($output);
	}

	private function to_csv_str( array $fields, $delimiter = ',', $enclosure = '"', $encloseAll = true, $nullToMysqlNull = false ) {
	    $delimiter_esc = preg_quote($delimiter, '/');
	    $enclosure_esc = preg_quote($enclosure, '/');

	    $outputString = "";
	    foreach($fields as $tempFields) {
	        $output = array();
	        foreach ( $tempFields as $field ) {
	            // ADDITIONS BEGIN HERE
	            if (gettype($field) == 'integer' || gettype($field) == 'double') {
	                $field = strval($field); // Change $field to string if it's a numeric type
	            }
	            // ADDITIONS END HERE
	            if ($field === null && $nullToMysqlNull) {
	                $output[] = 'NULL';
	                continue;
	            }
	            // Enclose fields containing $delimiter, $enclosure or whitespace
	            if ( $encloseAll || preg_match( "/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field ) ) {
	                $field = $enclosure . str_replace($enclosure, $enclosure . $enclosure, $field) . $enclosure;
	            }
	            $output[] = $field;
	        }
	        $outputString .= implode( $delimiter, $output )."\r\n";
	    }
	    return chr(0xEF).chr(0xBB).chr(0xBF).$outputString; 
	}
}
?>
