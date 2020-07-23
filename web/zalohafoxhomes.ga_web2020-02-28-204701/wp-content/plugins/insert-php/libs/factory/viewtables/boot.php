<?php
	/**
	 * Factory viewtable
	 *
	 * @author Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright (c) 2018, Webcraftic Ltd
	 *
	 * @package factory-viewtables
	 * @since 1.0.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	// module provides function only for the admin area
	if( !is_admin() ) {
		return;
	}

	if( defined('FACTORY_VIEWTABLES_403_LOADED') ) {
		return;
	}
	define('FACTORY_VIEWTABLES_403_LOADED', true);

	define('FACTORY_VIEWTABLES_403_DIR', dirname(__FILE__));
	define('FACTORY_VIEWTABLES_403_URL', plugins_url(null, __FILE__));

	#comp merge
	require(FACTORY_VIEWTABLES_403_DIR . '/viewtable.class.php');
	require(FACTORY_VIEWTABLES_403_DIR . '/includes/viewtable-columns.class.php');
	#endcomp