<?php

	// if uninstall.php is not called by WordPress, die
	if( !defined('WP_UNINSTALL_PLUGIN') ) {
		die;
	}

	global $wpdb;

	$snippets = get_posts(array(
		'post_type' => 'wbcr-snippets',
		'post_status' => 'any',
		'numberposts' => -1
	));

	if( !empty($snippets) ) {
		foreach((array)$snippets as $snippet) {
			wp_delete_post($snippet->ID, true);
		}
	}

	$taxonomy = 'wbcr-snippet-tags';
	$query = 'SELECT t.name, t.term_id
            FROM ' . $wpdb->terms . ' AS t
            INNER JOIN ' . $wpdb->term_taxonomy . ' AS tt
            ON t.term_id = tt.term_id
            WHERE tt.taxonomy = "' . $taxonomy . '"';

	$terms = $wpdb->get_results($query);

	foreach($terms as $term) {
		wp_delete_term($term->term_id, $taxonomy);
	}

	// remove plugin options
	$wpdb->query("DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE 'wbcr_inp_%';");