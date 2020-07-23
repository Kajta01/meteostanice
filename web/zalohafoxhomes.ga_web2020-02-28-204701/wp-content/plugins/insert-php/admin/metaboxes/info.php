<?php
	
	class WINP_InfoMetaBox extends Wbcr_FactoryMetaboxes403_Metabox {

		/**
		 * A visible title of the metabox.
		 *
		 * Inherited from the class FactoryMetabox.
		 * @link http://codex.wordpress.org/Function_Reference/add_meta_box
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $title;

		/**
		 * The part of the page where the edit screen
		 * section should be shown ('normal', 'advanced', or 'side').
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $context = 'side';


		/**
		 * The priority within the context where the boxes should show ('high', 'core', 'default' or 'low').
		 *
		 * @link http://codex.wordpress.org/Function_Reference/add_meta_box
		 * Inherited from the class FactoryMetabox.
		 *
		 * @since 1.0.0
		 * @var string
		 */
		public $priority = 'core';

		public $css_class = 'factory-bootstrap-404 factory-fontawesome-000';

		protected $errors = array();
		protected $source_channel;
		protected $facebook_group_id;
		protected $paginate_url;

		public function __construct($plugin)
		{
			parent::__construct($plugin);

			$this->title = __('Meet with Clearfy plugin!', 'insert-php');
		}


		/**
		 * Configures a metabox.
		 *
		 * @since 1.0.0
		 * @param Wbcr_Factory404_ScriptList $scripts A set of scripts to include.
		 * @param Wbcr_Factory404_StyleList $styles A set of style to include.
		 * @return void
		 */
		public function configure($scripts, $styles)
		{
		}

		public function html()
		{
			?>
			<div class="wbcr-inp-metabox-banner">
				<strong class="wbcr-inp-big-text"><?php _e('Do you use snippets to disable unused WordPress features?', 'insert-php'); ?></strong>

				<p><?php _e('We can offer you a simpler and more reliable solution - our popular plugin for WordPress optimization
					Clearfy.', 'insert-php'); ?></p>

				<p><?php _e('Just click toggles to turn on or off unused WordPress functions.', 'insert-php'); ?></p>
				<ul>
					<li class="wbcr-inp-big-text">- <?php _e('No snippets', 'insert-php'); ?></li>
					<li class="wbcr-inp-big-text">- <?php _e('Do not waste time', 'insert-php'); ?></li>
					<li class="wbcr-inp-big-text">- <?php _e('Do not worry about security', 'insert-php'); ?></li>
					<li class="wbcr-inp-big-text">- <?php _e('It\'s free', 'insert-php'); ?></li>
				</ul>
				<a href="https://clearfy.pro/?utm_source=wordpress.org&utm_campaign=wbcr_php_snippets" class="wbcr-inp-button" target="_blank">
					<?php _e('Download for free', 'insert-php'); ?>
				</a>
			</div>
		<?php
		}
	}