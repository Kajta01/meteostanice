<?php
/**
 * WordPress Tables plugin.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */
class WPTables_ManualData extends WPTables_Parser {
	public function create_data($cols, $rows) {
		$fields = array();
		for ($i = 0; $i < $cols; $i++) {
			$fields[$i] = $this->create_field("f{$i}", 'text', "Field ".($i+1));
		}
		$data = array();
		for ($i = 0; $i < $rows; $i++) {
			$row = array();
			for ($c = 0; $c < $cols; $c++) {
				$row["f{$c}"] = '';
			}
			$data[] = $row;
		}	
		return array(
			"fields"=> $fields,
			"data"	=> $data
		);
	}
}
?>
