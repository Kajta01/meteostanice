<?php
	/**
	 * This class is implemented page: import, export in the admin panel.
	 *
	 * @author Alex Kovalev <alex.kovalevv@gmail.com>
	 * @copyright (c) 2018, OnePress Ltd
	 *s
	 * @package core
	 * @since 1.0.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	/**
	 * Common Settings
	 */
	class WINP_ExportPage extends Wbcr_FactoryPages405_AdminPage {

		/**
		 * @param Wbcr_Factory404_Plugin $plugin
		 */
		public function __construct(Wbcr_Factory404_Plugin $plugin)
		{
			$this->menu_post_type = WINP_SNIPPETS_POST_TYPE;

			$this->id = "export";
			$this->menu_title = __('Import/Export', 'insert-php');

			parent::__construct($plugin);

			$this->plugin = $plugin;
		}

		/**
		 * Prints the contents of the page.
		 */
		public function indexAction()
		{
			?>
			<div class="wrap ">
				<div class="factory-bootstrap-404 factory-fontawesome-000">
					<h3><?php _e('Import/Export', 'insert-php') ?></h3>

					<p><?php _e('To export or migrate your snippets, you can use native WordPress functional or any other import/export plugin for custom post types.', 'insert-php'); ?></p>

					<p><?php _e('Let\'s have a look how you can export your snippets:', 'insert-php'); ?></p>

					<h3><?php _e('Export with WordPress', 'insert-php'); ?></h3>
					<ul>
						<li>1. <?php _e('Go to Tools -> Export', 'insert-php'); ?></li>
						<li>2. <?php _e('Select "PHP snippets" custom post', 'insert-php'); ?></li>
						<li>3. <?php _e('Press "Download Export File" button', 'insert-php'); ?></li>
					</ul>
					<img src="<?= WINP_PLUGIN_URL ?>/admin/assets/img/79018c6892.png" width="800" alt=""/>

					<h3><?php _e('Import using other plugin', 'insert-php'); ?></h3>
					<ul>
						<li>1. <?php _e('Go to Tools -> Import', 'insert-php'); ?></li>
						<li>2. <?php _e('Install WordPress import plugin', 'insert-php'); ?></li>
						<li>3. <?php _e('Run Importer after installation', 'insert-php'); ?></li>
					</ul>
					<img src="<?= WINP_PLUGIN_URL ?>/admin/assets/img/b2347551e4.png" width="800" alt=""/>
					<ul>
						<li>1. <?php _e('Make sure you are on the import page', 'insert-php'); ?></li>
						<li>2. <?php _e('Select the export file that you downloaded at the export stage', 'insert-php'); ?></li>
						<li>3. <?php _e('Press "Upload file and import" button', 'insert-php'); ?></li>
					</ul>
					<img src="<?= WINP_PLUGIN_URL ?>/admin/assets/img/43d2351a21.png" width="800" alt=""/>
				</div>
			</div>
		<?php
		}
	}
