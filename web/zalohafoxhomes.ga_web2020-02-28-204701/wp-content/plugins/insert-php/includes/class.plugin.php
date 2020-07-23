<?php
	/**
	 * PHP snippets plugin base
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 19.02.2018, Webcraftic
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	if( !class_exists('WINP_Plugin') ) {

		class WINP_Plugin extends Wbcr_Factory404_Plugin {

			/**
			 * @var Wbcr_Factory404_Plugin
			 */
			private static $app;

			/**
			 * @param string $plugin_path
			 * @param array $data
			 * @throws Exception
			 */
			public function __construct($plugin_path, $data)
			{
				parent::__construct($plugin_path, $data);

				self::$app = $this;

				$this->setTextDomain();
				$this->setModules();

				$this->globalScripts();

				if( is_admin() ) {
					$this->adminScripts();
				}
			}

			/**
			 * @return WINP_Plugin
			 */
			public static function app()
			{
				return self::$app;
			}

			/**
			 * @return bool
			 */
			public function currentUserCan()
			{
				return current_user_can('manage_options');
			}

			protected function setTextDomain()
			{

				load_plugin_textdomain('insert-php', false, dirname(WINP_PLUGIN_BASE) . '/languages/');
			}
			
			protected function setModules()
			{

				$this->load(array(
					array('libs/factory/bootstrap', 'factory_bootstrap_404', 'admin'),
					array('libs/factory/forms', 'factory_forms_405', 'admin'),
					array('libs/factory/pages', 'factory_pages_405', 'admin'),
					array('libs/factory/types', 'factory_types_404'),
					array('libs/factory/taxonomies', 'factory_taxonomies_324'),
					array('libs/factory/metaboxes', 'factory_metaboxes_403', 'admin'),
					array('libs/factory/viewtables', 'factory_viewtables_403', 'admin'),
					array('libs/factory/shortcodes', 'factory_shortcodes_324', 'all'),
					array('libs/factory/notices', 'factory_notices_403', 'admin'),

				));
			}

			private function registerPages()
			{
				$this->registerPage('WINP_ExportPage', WINP_PLUGIN_DIR . '/admin/pages/export.php');
				$this->registerPage('WINP_SettingsPage', WINP_PLUGIN_DIR . '/admin/pages/settings.php');
			}

			private function registerTypes()
			{
				$this->registerType('WSC_TasksItemType', WINP_PLUGIN_DIR . '/admin/types/snippets-post-types.php');

				require_once(WINP_PLUGIN_DIR . '/admin/types/snippets-taxonomy.php');
				Wbcr_FactoryTaxonomies324::register('WINP_SnippetsTaxonomy', $this);
			}

			private function registerShortcodes()
			{
				require_once(WINP_PLUGIN_DIR . '/includes/shortcodes.php');
				Wbcr_FactoryShortcodes324::register('WINP_SnippetShortcode', $this);
			}

			private function adminScripts()
			{
				require_once(WINP_PLUGIN_DIR . '/admin/includes/class.snippets.viewtable.php');
				require_once(WINP_PLUGIN_DIR . '/admin/boot.php');

				$this->registerTypes();
				$this->registerPages();
			}
			
			private function globalScripts()
			{
				$this->registerShortcodes();

				add_action('plugins_loaded', array($this, 'executeActiveSnippets'), 1);
			}

			/**
			 *  Execute the snippets once the plugins are loaded
			 */
			public function executeActiveSnippets()
			{
				global $wpdb;

				$save_mode = false;

				if( isset($_REQUEST['wbcr-php-snippets-safe-mode']) && !isset($_COOKIE['wbcr-php-snippets-safe-mode']) && $this->currentUserCan() ) {
					$save_mode = true;
					setcookie("wbcr-php-snippets-safe-mode", 1, time() + 3600);
				}

				$snippets = $wpdb->get_results("SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} INNER JOIN {$wpdb->postmeta} ON ( {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id ) WHERE (
				( {$wpdb->postmeta}.meta_key = '" . $this->getPrefix() . "snippet_scope' AND {$wpdb->postmeta}.meta_value = 'evrywhere' )
) AND {$wpdb->posts}.post_type = '" . WINP_SNIPPETS_POST_TYPE . "' AND (({$wpdb->posts}.post_status = 'publish'))");

				if( empty($snippets) ) {
					return;
				}

				foreach((array)$snippets as $snippet) {
					$id = (int)$snippet->ID;

					$is_active = (int)WINP_Helper::getMetaOption($id, 'snippet_activate', 0);
					$code = WINP_Helper::getMetaOption($id, 'snippet_code');

					if( $is_active ) {
						if( isset($_POST['wbcr_inp_snippet_scope']) && isset($_POST['post_ID']) && (int)$_POST['post_ID'] === $id && $this->currentUserCan() ) {
							return;
						}

						if( ($save_mode || isset($_COOKIE['wbcr-php-snippets-safe-mode'])) && $this->currentUserCan() ) {
							return;
						}

						$this->executeSnippet($code, $id);
					}
				}
			}

			/**
			 * Execute a snippet
			 *
			 * Code must NOT be escaped, as
			 * it will be executed directly
			 *
			 * @param string $code The snippet code to execute
			 * @param int $id The snippet ID
			 * @param bool $catch_output Whether to attempt to suppress the output of execution using buffers
			 *
			 * @return mixed        The result of the code execution
			 */
			public function executeSnippet($code, $id = 0, $catch_output = true)
			{

				if( empty($code) ) {
					return false;
				}

				if( $catch_output ) {
					ob_start();
				}

				$result = eval($code);

				if( $catch_output ) {
					ob_end_clean();
				}

				return $result;
			}

			/**
			 * Retrieve the first error in a snippet's code
			 *
			 * @param int $snippet_id
			 *
			 * @return array|bool
			 */
			public function getSnippetError($snippet_id)
			{
				if( !intval($snippet_id) ) {
					return false;
				}

				$snippet_code = WINP_Helper::getMetaOption($snippet_id, 'snippet_code');

				$result = $this->executeSnippet($snippet_code, $snippet_id);

				if( false !== $result ) {
					return false;
				}

				$error = error_get_last();

				if( is_null($error) ) {
					return false;
				}

				return $error;
			}
		}
	}