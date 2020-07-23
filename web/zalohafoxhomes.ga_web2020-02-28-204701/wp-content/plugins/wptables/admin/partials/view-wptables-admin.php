<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */
?>
<?php 
if (isset($_GET['action']) && $_GET['action'] == 'edit') {
	$view = new WPTables_EditTableView($_GET['table']);
	$view->render();
} else {
	$view = new WPTables_ListView();
	$view->render();
}