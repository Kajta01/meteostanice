<?php
	/**
	 * Factory Metaboxes
	 *
	 * @author Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright (c) 2018, Webcraftic Ltd
	 *
	 * @package factory-metaboxes
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

	if( defined('FACTORY_METABOXES_403_LOADED') ) {
		return;
	}
	define('FACTORY_METABOXES_403_LOADED', true);

	define('FACTORY_METABOXES_403_DIR', dirname(__FILE__));
	define('FACTORY_METABOXES_403_URL', plugins_url(null, __FILE__));

	#comp merge
	require(FACTORY_METABOXES_403_DIR . '/metaboxes.php');
	require(FACTORY_METABOXES_403_DIR . '/metabox.class.php');
	require(FACTORY_METABOXES_403_DIR . '/includes/form-metabox.class.php');
	require(FACTORY_METABOXES_403_DIR . '/includes/publish-metabox.class.php');
	#endcomp