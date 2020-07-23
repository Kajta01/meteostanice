<?php
	/**
	 * Admin boot
	 * @author Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright Alex Kovalev 05.06.2018
	 * @version 1.0
	 */

	/**
	 * Admin warning notices
	 */
	function wbcr_inp_admin_conflict_notices_error($notices, $plugin_name)
	{
		if( $plugin_name != WINP_Plugin::app()->getPluginName() ) {
			return $notices;
		}

		if( isset($_COOKIE['wbcr-php-snippets-safe-mode']) ) {
			$disable_safe_mode_url = add_query_arg(array('wbcr-php-snippets-disable-safe-mode' => 1));

			$safe_mode_notice = WINP_Plugin::app()
					->getPluginTitle() . ': ' . __('Running in safe mode. This mode your snippets will not be started.', 'insert-php');
			$safe_mode_notice .= ' <a href="' . $disable_safe_mode_url . '" class="button button-default">Disable Safe Mode</a>';

			$notices[] = array(
				'id' => 'inp_safe_mode',
				'type' => 'success',
				'dismissible' => false,
				'dismiss_expires' => 0,
				'text' => '<p>' . $safe_mode_notice . '</p>'
			);
		}

		$create_notice_url = admin_url('edit.php?post_type=' . WINP_SNIPPETS_POST_TYPE);

		$upgrade_plugin_notice = '<b>' . WINP_Plugin::app()
				->getPluginTitle() . '</b>: ' . __('Attention! This new 2.0 plugin version, we added the ability to insert php code using snippets. This is a more convenient and secure way than using shortcodes [insert_php] code execute [/ insert_php]. However, for compatibility reasons, we left support for [insert_php] shortcodes, but we will depreciate them in the next versions of the plugin.', 'insert-php');

		$upgrade_plugin_notice .= '<br><br>' . __('We strongly recommend you to transfer your php code to snippets and call them in your posts/pages and widgets using [wbcr_php_snippet id = "000"] shortcodes.', 'insert-php');

		//$upgrade_plugin_notice .= '<br><br><span style="color:red">' . __('If you have updated from version 1.3 of the plugin (Insert php). Please deactivate and activate the plugin to update the settings.', 'insert-php') . '</span>';

		$upgrade_plugin_notice .= '<br><br><a href="' . $create_notice_url . '" class="button button-default">' . __('Create new php snippet', 'insert-php') . '</a> ';

		$upgrade_plugin_notice .= '<a href="https://downloads.wordpress.org/plugin/insert-php.1.3.zip" class="button button-default">' . __('Download old version', 'insert-php') . '</a><br><br>';
		$upgrade_plugin_notice .= sprintf(__('If you have issues with the plugin new version or any suggestions, please contact us on <a href="%s" target="_blank">our forum</a>.', 'insert-php'), 'https://wordpress.org/support/plugin/insert-php');
		$upgrade_plugin_notice .= '<br>' . sprintf(__('We tried to make the plugin better and more convenient. If you like the new version, feel free to <a href="%s" target="_blank">leave your feedback</a>. It will give us motivation for further improvements.', 'insert-php'), 'https://wordpress.org/support/plugin/insert-php/reviews/#new-post');

		$notices[] = array(
			'id' => 'inp_upgrade_plugin',
			'type' => 'warning',
			'dismissible' => true,
			'dismiss_expires' => 0,
			'text' => '<p>' . $upgrade_plugin_notice . '</p>'
		);

		/**
		 * Show error notification after saving snippet. We can also show this message when the snippet is activated.
		 * We must warn the user that we can not perform the spippet due to an error.
		 */
		if( isset($_GET['wbcr_inp_save_snippet_result']) && $_GET['wbcr_inp_save_snippet_result'] == 'code-error' ) {

			$post_id = isset($_GET['post'])
				? intval($_GET['post'])
				: null;

			if( $post_id && $error = WINP_Plugin::app()->getSnippetError($post_id) ) {

				$error_message = sprintf('<p>%s</p><p><strong>%s</strong></p>', sprintf(__('The snippet has been deactivated due to an error on line %d:', 'insert-php'), $error['line']), $error['message']);

				$notices[] = array(
					'id' => 'inp_result_error',
					'where' => array('post', 'post-new', 'edit'),
					'type' => 'error',
					'dismissible' => false,
					'dismiss_expires' => 0,
					'text' => $error_message
				);
			}
		}

		return $notices;
	}

	add_filter('wbcr_factory_admin_notices', 'wbcr_inp_admin_conflict_notices_error', 10, 2);

	function wbcr_inp_admin_init()
	{
		$plugin = WINP_Plugin::app();

		// Disable safe mode

		if( isset($_COOKIE['wbcr-php-snippets-safe-mode']) && isset($_REQUEST['wbcr-php-snippets-disable-safe-mode']) && $plugin->currentUserCan() ) {
			unset($_COOKIE['wbcr-php-snippets-safe-mode']);
			setcookie('wbcr-php-snippets-safe-mode', null, -1);
			wp_safe_redirect(remove_query_arg(array('wbcr-php-snippets-disable-safe-mode')));
			exit;
		}

		// Register metaboxes

		require_once(WINP_PLUGIN_DIR . '/admin/metaboxes/base-options.php');
		Wbcr_FactoryMetaboxes403::registerFor(new WINP_BaseOptionsMetaBox($plugin), WINP_SNIPPETS_POST_TYPE, $plugin);

		require_once(WINP_PLUGIN_DIR . '/admin/metaboxes/info.php');
		Wbcr_FactoryMetaboxes403::registerFor(new WINP_InfoMetaBox($plugin), WINP_SNIPPETS_POST_TYPE, $plugin);

		// Upgrade up to new version
		if( !$plugin->getOption('upgrade_up_to_201', false) ) {
			$role = get_role('administrator');
			if( !$role ) {
				return;
			}

			$role->add_cap('edit_' . WINP_SNIPPETS_POST_TYPE);
			$role->add_cap('read_' . WINP_SNIPPETS_POST_TYPE);
			$role->add_cap('delete_' . WINP_SNIPPETS_POST_TYPE);
			$role->add_cap('edit_' . WINP_SNIPPETS_POST_TYPE . 's');
			$role->add_cap('edit_others_' . WINP_SNIPPETS_POST_TYPE . 's');
			$role->add_cap('publish_' . WINP_SNIPPETS_POST_TYPE . 's');
			$role->add_cap('read_private_' . WINP_SNIPPETS_POST_TYPE . 's');

			$plugin->updateOption('upgrade_up_to_201', 1);
		}
	}

	add_action('admin_init', 'wbcr_inp_admin_init');

	// ---
	// Editor
	//

	/**
	 * Asset scripts for the tinymce editor
	 *
	 * @param string $hook
	 */
	function wbcr_inp_enqueue_tinymce_assets($hook)
	{
		$pages = array(
			'post.php',
			'post-new.php',
			'widgets.php'
		);

		if( !in_array($hook, $pages) || !current_user_can('manage_options') ) {
			return;
		}

		wp_enqueue_script('wbcr-inp-tinymce-button-widget', WINP_PLUGIN_URL . '/admin/assets/js/tinymce4.4.js', array('jquery'), WINP_Plugin::app()
			->getPluginVersion(), true);
	}

	add_action('admin_enqueue_scripts', 'wbcr_inp_enqueue_tinymce_assets');

	/**
	 * Adds js variable required for shortcodes.
	 *
	 * @see before_wp_tiny_mce
	 * @since 1.1.0
	 */
	function wbcr_inp_tinymce_data($hook)
	{
		if( !current_user_can('manage_options') ) {
			return;
		}

		// styles for the plugin shorcodes
		$shortcode_icon = WINP_PLUGIN_URL . '/admin/assets/img/shortcode-icon.png';
		$shortcode_title = __('PHP snippets', 'insert-php');

		$snippets = get_posts(array(
			'post_type' => WINP_SNIPPETS_POST_TYPE,
			'meta_key' => WINP_Plugin::app()->getPrefix() . 'snippet_scope',
			'meta_value' => 'shortcode',
			'post_status' => 'publish',
			'numberposts' => -1
		));

		$result = array();
		foreach((array)$snippets as $snippet) {
			$result[] = array(
				'id' => $snippet->ID,
				'title' => empty($snippet->post_title)
					? '(no titled, ID=' . $snippet->ID . ')'
					: $snippet->post_title
			);
		}

		$shortcode_snippets_json = json_encode($result);

		?>
		<!-- <?= WINP_Plugin::app()->getPluginTitle() ?> for tinymce -->
		<style>
			i.wbcr-inp-shortcode-icon {
				background: url("<?php echo $shortcode_icon ?>") center no-repeat;
			}
		</style>
		<script>
			var wbcr_inp_tinymce_snippets_button_title = '<?php echo $shortcode_title ?>';
			var wbcr_inp_post_tinymce_nonce = '<?php echo wp_create_nonce('wbcr_inp_tinymce_post_nonce') ?>';
			var wbcr_inp_shortcode_snippets = <?= $shortcode_snippets_json ?>;
		</script>
		<!-- /end <?= WINP_Plugin::app()->getPluginTitle() ?> for tinymce -->
	<?php
	}

	add_action('admin_print_scripts-post.php', 'wbcr_inp_tinymce_data');
	add_action('admin_print_scripts-post-new.php', 'wbcr_inp_tinymce_data');
	add_action('admin_print_scripts-widgets.php', 'wbcr_inp_tinymce_data');
