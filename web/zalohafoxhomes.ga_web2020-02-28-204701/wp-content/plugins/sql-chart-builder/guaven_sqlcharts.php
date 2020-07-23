<?php
/**
 * Plugin Name:       Guaven SQL Charts
 * Plugin URI:        http://guaven.com/updatepusher
 * Description:       Turn Your SQL Queries to Beautiful Dynamic Charts- Pie, Line, Area, Donut, Bar Charts with date/input filters.
 * Version:           2.1.1
 * Author:            Guaven Labs
 * Author URI:        http://guaven.com/
 * Text Domain:       guaven_sqlcharts
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


require_once(dirname(__FILE__)."/functions.php");
require_once(dirname(__FILE__)."/googlecharts-deprecated.php");
