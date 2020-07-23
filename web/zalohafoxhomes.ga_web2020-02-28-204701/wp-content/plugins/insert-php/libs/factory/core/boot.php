<?php
	/**
	 * Factory Plugin
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

	if( defined('FACTORY_404_LOADED') ) {
		return;
	}
	define('FACTORY_404_LOADED', true);

	define('FACTORY_404_VERSION', '000');

	define('FACTORY_404_DIR', dirname(__FILE__));
	define('FACTORY_404_URL', plugins_url(null, __FILE__));

	#comp merge
	require_once(FACTORY_404_DIR . '/includes/functions.php');
	require_once(FACTORY_404_DIR . '/includes/request.class.php');
	require_once(FACTORY_404_DIR . '/includes/base.class.php');

	require_once(FACTORY_404_DIR . '/includes/assets-managment/assets-list.class.php');
	require_once(FACTORY_404_DIR . '/includes/assets-managment/script-list.class.php');
	require_once(FACTORY_404_DIR . '/includes/assets-managment/style-list.class.php');

	require_once(FACTORY_404_DIR . '/includes/plugin.class.php');

	require_once(FACTORY_404_DIR . '/includes/activation/activator.class.php');
	require_once(FACTORY_404_DIR . '/includes/activation/update.class.php');
	#endcomp
