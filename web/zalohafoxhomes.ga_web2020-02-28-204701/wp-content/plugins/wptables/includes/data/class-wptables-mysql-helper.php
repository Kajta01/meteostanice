<?php 
/**
 * WordPress Tables plugin.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */
class WPTables_MySqlHelper
{
	public static function replace_query_vars($query) {
		$query = str_replace('$user_id', get_current_user_id(), $query);
		$query = apply_filters( 'wptables_mysql_query', $query );
		return $query;
	}
}
?>