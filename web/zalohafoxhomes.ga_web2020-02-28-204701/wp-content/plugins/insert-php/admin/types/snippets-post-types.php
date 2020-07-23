<?php

	/**
	 * Php Snippets Type
	 * Declaration for custom post type of Php code snippets
	 * @link http://codex.wordpress.org/Post_Types
	 */
	class WSC_TasksItemType extends Wbcr_FactoryTypes404_Type {

		/**
		 * Custom post name.
		 * @var string
		 */
		//public $name = 'wbcr-scrapes';

		/**
		 * Template that defines a set of type options.
		 * Allowed values: public, private, internal.
		 * @var string
		 */
		public $template = 'private';

		/**
		 * Capabilities for roles that have access to manage the type.
		 * @link http://codex.wordpress.org/Roles_and_Capabilities
		 * @var array
		 */
		public $capabilities = array('administrator');

		/**
		 * @param Wbcr_Factory404_Plugin $plugin
		 */
		function __construct(Wbcr_Factory404_Plugin $plugin)
		{
			$this->name = WINP_SNIPPETS_POST_TYPE;
			$this->plural_title = __('PHP snippets', 'insert-php');
			$this->singular_title = __('PHP snippets', 'insert-php');

			parent::__construct($plugin);
		}

		/**
		 * Type configurator.
		 */
		public function configure()
		{

			$plural_name = $this->plural_title;
			$singular_name = $this->singular_title;

			$labels = array(
				'singular_name' => $this->singular_title,
				'name' => $this->plural_title,
				'all_items' => sprintf(__('Snippets', 'insert-php'), $plural_name),
				'add_new' => sprintf(__('+ Add snippet', 'insert-php'), $singular_name),
				'add_new_item' => sprintf(__('Add new', 'insert-php'), $singular_name),
				'edit' => sprintf(__('Edit', 'insert-php')),
				'edit_item' => sprintf(__('Edit snippet', 'insert-php'), $singular_name),
				'new_item' => sprintf(__('New snippet', 'insert-php'), $singular_name),
				'view' => sprintf(__('View', 'factory')),
				'view_item' => sprintf(__('View snippet', 'insert-php'), $singular_name),
				'search_items' => sprintf(__('Search snippets', 'insert-php'), $plural_name),
				'not_found' => sprintf(__('Snippet is not found', 'insert-php'), $plural_name),
				'not_found_in_trash' => sprintf(__('Snippt is not found in trash', 'insert-php'), $plural_name),
				'parent' => sprintf(__('Parent snippet', 'insert-php'), $plural_name)
			);

			$this->options['labels'] = apply_filters('wbcr_inp_items_lables', $labels);

			/**
			 * Menu
			 */

			$this->menu->icon = WINP_PLUGIN_URL . '/admin/assets/img/menu-icon.png';

			/**
			 * View table
			 */

			$this->view_table = 'WINP_SnippetsViewTable';

			/**
			 * Scripts & styles
			 */

			$this->scripts->request(array('jquery', 'jquery-effects-highlight', 'jquery-effects-slide'));

			$this->scripts->request(array(
				'control.checkbox',
				'control.dropdown',
				'control.list',
			), 'bootstrap');

			$this->styles->request(array(
				'bootstrap.core',
				'bootstrap.form-group',
				'bootstrap.form-metabox',
				'bootstrap.wp-editor',
				'bootstrap.separator',
				'control.checkbox',
				'control.dropdown',
				'control.list',
			), 'bootstrap');
		}
	}