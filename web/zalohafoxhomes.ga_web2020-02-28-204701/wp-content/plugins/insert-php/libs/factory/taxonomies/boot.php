<?php
	/**
	 * Factory Taxonomies
	 *
	 * @author Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright (c) 2018, Webcraftic Ltd
	 *
	 * @package core
	 * @since 1.0.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	if( defined('FACTORY_T_000_LOADED') ) {
		return;
	}
	define('FACTORY_TAXONOMIES_324_LOADED', true);

	define('FACTORY_TAXONOMIES_324_DIR', dirname(__FILE__));
	define('FACTORY_TAXONOMIES_324_URL', plugins_url(null, __FILE__));

	load_plugin_textdomain('factory_taxonomies_324', false, dirname(plugin_basename(__FILE__)) . '/langs');

	// sets version of admin interface
	if( is_admin() ) {
		if( !defined('FACTORY_FLAT_ADMIN') ) {
			define('FACTORY_FLAT_ADMIN', true);
		}
	}

	#comp merge	
	require(FACTORY_TAXONOMIES_324_DIR . '/taxonomy.class.php');
	require(FACTORY_TAXONOMIES_324_DIR . '/taxonomy.php');
	#endcomp