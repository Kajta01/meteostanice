<?php
	/**
	 * Plugin Name: PHP code snippets (Insert PHP)
	 * Plugin URI: https://wordpress.org/plugins/insert-php/
	 * Description: Run PHP code inserted into WordPress posts and pages. An easy, clean and easy way to add code snippets to your site. You do not need to edit the functions.php file of your theme again!
	 * Author: Will Bontrager Software, LLC <will@willmaster.com>, Webcraftic <wordpress.webraftic@gmail.com>
	 * Version: 2.0.6
	 * Text Domain: insert-php
	 * Domain Path: /languages/
	 * Author URI: http://www.willmaster.com/contact.php
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	if( defined('WINP_PLUGIN_ACTIVE') ) {
		return;
	}

	// Set the constant that the plugin is activated
	define('WINP_PLUGIN_ACTIVE', true);

	// Root directory of the plugin
	define('WINP_PLUGIN_DIR', dirname(__FILE__));
	
	// Absolute url of the root directory of the plugin
	define('WINP_PLUGIN_URL', plugins_url(null, __FILE__));

	// Relative url of the plugin
	define('WINP_PLUGIN_BASE', plugin_basename(__FILE__));
	
	// The type of posts used for snippets types
	define('WINP_SNIPPETS_POST_TYPE', 'wbcr-snippets');

	// The taxonomy used for snippets types
	define('WINP_SNIPPETS_TAXONOMY', 'wbcr-snippet-tags');

	

	global $wp_version;

	// Troubleshoot old versions of WordPress
	if( version_compare($wp_version, '4.2.0', '>') ) {

		require_once(WINP_PLUGIN_DIR . '/libs/factory/core/boot.php');

		require_once(WINP_PLUGIN_DIR . '/includes/class.helpers.php');
		require_once(WINP_PLUGIN_DIR . '/includes/class.plugin.php');

		new WINP_Plugin(__FILE__, array(
			'prefix' => 'wbcr_inp_',
			'plugin_name' => 'wbcr_insert_php',
			'plugin_title' => __('PHP code snippets (Insert PHP)', 'insert-php'),
			'plugin_version' => '2.0.6',
			'required_php_version' => '5.2',
			'required_wp_version' => '4.2',
			'plugin_build' => 'free',
			//'updates' => WINP_PLUGIN_DIR . '/updates/'
		));
	} else {
		// Notification about the old version of Wordpress
		add_action('admin_notices', function () {
			echo '<div class="notice notice-error"><p>' . __('You are using the old version of Wordpress, for the <b>PHP code snippets (Insert PHP)</b> plugin you need Wordpress 4.2 and higher versions!', 'insert-php') . '</p></div>';
		});
	}

	/*
	Note: This plugin requires WordPress version 3.3.1 or higher.

	Information about the Insert PHP plugin can be found here:
	http://www.willmaster.com/software/WPplugins/go/iphphome_iphplugin

	Instructions and examples can be found here:
	http://www.willmaster.com/software/WPplugins/go/iphpinstructions_iphplugin
	*/

	// todo: This is the code of the old version of the plugin, left unchanged for compatibility. Delete in the new major version of the plugin
	if( !function_exists('will_bontrager_insert_php') ) {

		function will_bontrager_insert_php($content)
		{
			$will_bontrager_content = $content;
			preg_match_all('!\[insert_php[^\]]*\](.*?)\[/insert_php[^\]]*\]!is', $will_bontrager_content, $will_bontrager_matches);
			$will_bontrager_nummatches = count($will_bontrager_matches[0]);
			for($will_bontrager_i = 0; $will_bontrager_i < $will_bontrager_nummatches; $will_bontrager_i++) {
				ob_start();
				eval($will_bontrager_matches[1][$will_bontrager_i]);
				$will_bontrager_replacement = ob_get_contents();
				ob_clean();
				ob_end_flush();
				$will_bontrager_content = preg_replace('/' . preg_quote($will_bontrager_matches[0][$will_bontrager_i], '/') . '/', $will_bontrager_replacement, $will_bontrager_content, 1);
			}

			return $will_bontrager_content;
		} # function will_bontrager_insert_php()

		add_filter('the_content', 'will_bontrager_insert_php', 9);
	} # if( ! function_exists('will_bontrager_insert_php') )
