<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wp_query;
$args = array_merge( $wp_query->query_vars, array( 'post_type' => 'wptables_table' ) );
$args['posts_per_page'] = -1;
query_posts( $args );

while ( have_posts() ) {
	the_post();
	$post_id = get_the_ID();
	delete_post_meta($post_id, 'wpt_fields');
	delete_post_meta($post_id, 'wpt_options');
	wp_delete_post($post_id, true);
}