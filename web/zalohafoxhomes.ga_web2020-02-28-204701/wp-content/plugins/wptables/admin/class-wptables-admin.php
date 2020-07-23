<?php
/**
 * WordPress Tables plugin.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */
define("WPT_POST_TYPE_TABLE", "wptables_table");

class WPTables_Admin {
	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	public function enqueue_styles() {
		wp_enqueue_style( 'wptables-public', WPT_BASE_URL . 'build/css/wptables.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'wptables-admin', WPT_BASE_URL . 'build/css/wptables-admin.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'handsontable', WPT_BASE_URL . 'admin/js/handsontable/handsontable.full.min.css', array(), $this->version, 'all' );
	}

	public function enqueue_scripts() {
		wp_enqueue_script( 'wptables-public', WPT_BASE_URL . 'build/js/wptables.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'wptables-admin', WPT_BASE_URL . 'build/js/wptables-admin.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( "jquery-ui-core", array('jquery'));
		wp_enqueue_script( "jquery-ui-sortable", array('jquery','jquery-ui-core'));
		wp_enqueue_script( "jquery-ui-widget", array('jquery','jquery-ui-core'));
		wp_enqueue_script( 'handsontable', WPT_BASE_URL.'admin/js/handsontable/handsontable.full.min.js', array(), $this->version, true);
		
		wp_localize_script( 'wptables-admin', 'wpt_consts', array( 
 			'url_add_new_table' => WPTables::url(array('page' => 'wptables-add-new')),
 			'confirm_delete_table'	=> __('Do you really want to delete this table?', 'wptables'),
 			'confirm_delete_table_field' => __('Do you really want to delete the field?', 'wptables')
 		));
	}

	public function plugin_update_message() {
		$response = file_get_contents( WPT_README_URL );
		if (!$response) {
			return;
		}
		$response = substr($response, strpos($response, "== Changelog =="));
		$lines = explode(PHP_EOL, $response);

		echo '<div style="color: #f00;">' . __( 'Take a minute to update, here\'s why:', 'wptables' ) . '</div>';
		echo "<ul>";
		foreach ($lines as $line) {
			if (trim($line) == false) {
				continue;
			}
			if (strpos($line, '== Changelog ==') !== false) {
				continue;
			}
			if (strpos($line, '= VERSION '.$this->version.' =') !== false) {
				break;
			}
			if (strpos($line, '= VERSION') !== false) {
				continue;
			}
			$line = trim(str_replace("*", "", $line));
			echo "<li>• {$line}</li>";
		}
		echo "</ul>";
	}

	public function add_menu() {
		$page = add_menu_page(
			__('Tables', 'wptables'), 
			__('WPTables', 'wptables'), 
			'edit_others_pages', 
			'wptables',
			array($this, 'render_admin_view'),
			'dashicons-grid-view',
			26
		);
		add_action('load-'.$page, array($this, 'main_add_help'));
		$page = add_submenu_page(
			'wptables',
			__('Add New Table', 'wptables'),
			__('Add New', 'wptables'),
			'edit_others_pages',
			'wptables-add-new',
			array($this, 'render_add_new_table_view' )
		);
		add_action('load-'.$page, array($this, 'main_add_help'));
		$page = add_submenu_page(
			'wptables',
			__('About WordPress Tables', 'wptables'),
			__('About', 'wptables'),
			'edit_others_pages',
			'wptables-about',
			array($this, 'render_about' )
		);
		add_action('load-'.$page, array($this, 'main_add_help'));
	}

	public function render_admin_view() {
		include("partials/view-wptables-admin.php");
	}

	public function render_add_new_table_view() {
		$view = new WPTables_AddNewTableView();
		$view->render();
	}

	public function render_about() {
		include("partials/view-wptables-about.php");
	}

	public function main_add_help() {
		$screen = get_current_screen();
	    $screen->add_help_tab(array(
	        'id'	=> 'wptables-help',
	        'title'	=> __('WordPress Tables'),
	        'content'	=> 
	        '<p>' . sprintf(__( 'Please visit <a href="%s" target="_blank">WordPress Tables page</a> to find more information about the plugin.', 'wptables'), WPTables::URL_PLUGIN_PAGE). '</p>'
	        .'<p>' . sprintf(__( 'You are welcome to leave your feedbacks and report issues on the <a href="%s" target="_blank">Support Forum</a>.', 'wptables'), WPTables::URL_SUPPORT) . '</p>'
	        .'<p>' . __( 'Also, any ideas and possible improvements are highly appreciated!', 'wptables' ) . '</p>'
	        .'<p>' . sprintf(__( 'Follow us on <a href="%1$s" target="_blank">Facebook</a> and <a href="%2$s" target="_blank">Twitter</a> to get the latest news and tutorials.', 'wptables'), WPTables::URL_FACEBOOK, WPTables::URL_TWITTER ). '</p>'
	    ));
	    $screen->set_help_sidebar( 
	    	'<p><strong>' . __( 'For more information:', 'wptables' ) . '</strong></p>'
	    	."<p><a href='".WPTables::URL_PLUGIN_PAGE."' target='_blank'>Documentation</a></p>"
	    	."<p><a href='".WPTables::URL_SUPPORT."' target='_blank'>Support Forum</a></p>"
	    	."<p><a href='".WPTables::URL_FACEBOOK."' target='_blank'>Facebook</a></p>"
	    	."<p><a href='".WPTables::URL_TWITTER."' target='_blank'>Twitter</a></p>"
	    );
	}

	public function action_add_new_table() {
		check_admin_referer('wpt-add-new-table');
		if (isset($_POST['title']) && !empty($_POST['title'])) {
			$title = $_POST['title'];
		}
		$encode_post_content = true;
		$format = $_POST['format'];
		if ($format == 'manual') {
			$type = "data";
			$cols = $_POST['input-cols'];
			$rows = 5;//$_POST['input-rows'];
			$parser = new WPTables_ManualData();
			$data = $parser->create_data($cols, $rows);
		}
		if ($format == 'csv') {
			$type = $_POST['input-type-csv'];
			$parser = new WPTables_CsvParser();
			if ($type == 'file') {
				$data = $parser->parse_file($_FILES['csv-data-file']['tmp_name']);
				if (!isset($title)) {
					$title = $_FILES['csv-data-file']['name'];
				}
			} elseif ($type == 'url') {
				$data = $parser->parse_file($_POST['csv-data-url']);
			} elseif ($type == 'text') {
				$data = $parser->parse_text($_POST['csv-data-text']);
			}
		}
		if ($format == 'json') {
			$type = $_POST['input-type-json'];
			$parser = new WPTables_JsonParser();
			if ($type == 'file') {
				$data = $parser->parse_file($_FILES['json-data-file']['tmp_name']);
			} elseif ($type == 'url') {
				$data = $parser->parse_file($_POST['json-data-url']);
			} elseif ($type == 'text') {
				$data = $parser->parse_text($_POST['json-data-text']);
			}
		}
		if ($format == 'mysql') {
			$type = $_POST['input-type-mysql'];
			$parser = new WPTables_MySqlParser();
			if ($type == 'db-table') {
				$data = $parser->parse_table($_POST['db-name'].".".$_POST['db-table']);
			} elseif ($type == 'mysql-query') {
				$encode_post_content = false;
				$data = $parser->parse_query($_POST['data-query']);
			}
		}
		if (isset($data) && $data !== false && !is_wp_error($data)) {
			if (!isset($title)) {
				$title = __("New Table", 'wptables');
			}
			$post_id = wp_insert_post(array(
				'post_title' => $title,
				'post_type'	=> WPT_POST_TYPE_TABLE,
				'post_content' => $encode_post_content ? WPTables::json_encode($data['data']) : $data['data'],
				'post_mime_type' => "{$format}/{$type}"
			));
			if (is_wp_error($post_id)){
			   $error_msg = $post_id->get_error_message();
			} else {
				update_post_meta($post_id, 'wpt_fields', WPTables::json_encode($data['fields']));
				update_post_meta($post_id, 'wpt_options', WPTables::json_encode($this->default_options()));
				WPTables::redirect(array('page' => 'wptables', 'action' => 'edit', 'table' => $post_id));
			}
		} else {
			if (is_wp_error($data)) {
				$error_msg = $data->get_error_message();
			} else {
				$error_msg = __('Error: Please specify valid data to import.', 'wptables');
			}
		}

		if (isset($error_msg) && !empty($error_msg)) {
			WPTables::redirect(array(
				'page' => 'wptables-add-new',
				'title' => urlencode($title),
				'error_msg'	=> urlencode($error_msg)
			));
		}
	}

	private function default_options() {
		return array(
			'sorting' 	=> true,
			'selecting' => true,
			'heading'	=> true,
			'paging'	=> true,
			'pageSize'	=> 20
		);
	}

	public function action_update_table() {
		$post_id = $_POST['table'];
		check_admin_referer( 'wpt-update-table-'.$post_id);

		$post = get_post($post_id);
		$redirect = array('page' => 'wptables', 'action' => 'edit', 'table' => $post_id);

		// update post
		$new_post = array();
		$new_post['ID'] = $post_id;
		$new_post['post_title'] = isset($_POST['title']) && !empty($_POST['title']) ? $_POST['title'] : __('New Table', 'wptables');
		if (isset($_POST['data']) && !empty($_POST['data'])) {
			$new_post['post_content'] = $_POST['data'];
		}
		if ($post->post_mime_type == 'mysql/mysql-query') {
			$parser = new WPTables_MySqlParser();
			$data = $parser->parse_query($_POST['data-query']);
			if (is_wp_error($data)) {
				$redirect['error_msg'] = urlencode($data->get_error_message());
				WPTables::redirect($redirect);
				return;
			}
			$new_post['post_content'] = $_POST['data-query'];
		}
		$result = wp_update_post($new_post);
		if (is_wp_error($result)) {
			$error_msg = $result->get_error_message();
		} else {
			// update fields
			$fields = array();
			foreach ( $_POST['fields'] as $name => $item ) {
				$field            = array(
					'name'  => $name,
					'title' => $item['title'],
					'type'  => $item['type']
				);
				$field['visible'] = isset( $item['visible'] ) && $item['visible'] == 'on';
				if ( isset( $item['width'] ) && ! empty( $item['width'] ) ) {
					$field['width'] = $item['width'];
				}
				if ( isset( $item['align'] ) && ! empty( $item['align'] ) ) {
					$field['align'] = $item['align'];
				}
				if ( isset( $item['css'] ) && ! empty( $item['css'] ) ) {
					$field['css'] = $item['css'];
				}
				if ( isset( $item['format'] ) ) {
					$field['format'] = $item['format'];
				}
				if ( isset( $item['currency_symbol'] ) ) {
					$field['currency_symbol'] = $item['currency_symbol'];
				}
				$fields[] = $field;
			}
			if ($post->post_mime_type == 'mysql/mysql-query' && $post->post_content != $new_post['post_content']) {
				$fields = $this->merge_fields($fields, $data['fields']);
			}
			update_post_meta( $post_id, 'wpt_fields', WPTables::json_encode( $fields ) );

			// update options
			$options = array();
			$config  = $_POST['config'];
			if ( isset( $config['width'] ) && ! empty( $config['width'] ) ) {
				$options['width'] = $config['width'];
			}
			if ( isset( $config['width-u'] ) && ! empty( $config['width-u'] ) ) {
				$options['width-u'] = $config['width-u'];
			}
			if ( isset( $config['height'] ) && ! empty( $config['height'] ) ) {
				$options['height'] = $config['height'];
			}
			if ( isset( $config['height-u'] ) && ! empty( $config['height-u'] ) ) {
				$options['height-u'] = $config['height-u'];
			}
			$options['sorting']   = isset( $config['sorting'] ) && $config['sorting'] == 'on';
			$options['selecting'] = isset( $config['selecting'] ) && $config['selecting'] == 'on';
			$options['heading']   = isset( $config['heading'] ) && $config['heading'] == 'on';
			$options['paging']    = isset( $config['paging'] ) && $config['paging'] == 'on';
			if ( isset( $config['pageSize'] ) && ! empty( $config['pageSize'] ) ) {
				$options['pageSize'] = $config['pageSize'];
			}
			if ( isset( $config['theme'] ) ) {
				$options['theme'] = $config['theme'];
			}
			update_post_meta( $post_id, 'wpt_options', WPTables::json_encode( $options ) );
		}
		if (isset($error_msg) && !empty($error_msg)) {
			$redirect['error_msg'] = $error_msg;
		}
		WPTables::redirect($redirect);
	}

	private function merge_fields($fields, $new_fields) {
		$fields_idx = array();
		foreach ($fields as $field) {
			$fields_idx[] = $field['name'];
		}
		foreach ($new_fields as $field) {
			if (!in_array($field['name'], $fields_idx)) {
				$fields[] = $field;
			}
		}
		return $fields;
	}

	public function action_delete_table() {
		$post_id = $_GET['table'];
		check_admin_referer('wpt-delete-table-'.$post_id);
		WPTables_Admin::delete_table($post_id);
		WPTables::redirect(array('page' => 'wptables'));
	}

	public function action_clone_table() {
		$post_id = $_GET['table'];
		check_admin_referer('wpt-clone-table-'.$post_id);
		$post = get_post($post_id);
		$new_post = array(
			'post_title' => $post->post_title.' '.__('Copy', 'wptables'),
			'post_content' => $post->post_content,
			'post_type' => WPT_POST_TYPE_TABLE,
			'post_mime_type' => $post->post_mime_type
		);
		$new_post_id = wp_insert_post($new_post);
		update_post_meta($new_post_id, 'wpt_fields', get_post_meta($post_id, 'wpt_fields', true));
		update_post_meta($new_post_id, 'wpt_options', get_post_meta($post_id, 'wpt_options', true));
		WPTables::redirect(array('page' => 'wptables'));
	}

	public static function delete_table($post_id) {
		delete_post_meta($post_id, 'wpt_fields');
		delete_post_meta($post_id, 'wpt_options');
		wp_delete_post($post_id, true);
	}

	public static function delete_tables($ids) {
		foreach ($ids as $id) {
            WPTables_Admin::delete_table($id);
        }
	}

	public function filter_mce_buttons($buttons) {
		array_push( $buttons, 'WPTables_insert_table' );
   		return $buttons;
	}

	public function filter_mce_external_plugins() {
		$plugin_array['wptables_tinymce'] = WPT_BASE_URL.'build/js/wptables-tinymce-plugin.min.js';
   		return $plugin_array;
	}

	public function ajax_tinymce_get_tables() {
		$output = array();
		$query = new WP_Query( array( 'post_type' => WPT_POST_TYPE_TABLE ) );
		while ( $query->have_posts() ) { 
			$query->the_post();
			$output[] = array(
				'text' 	=> get_the_title(), 
				'value'	=> WPTables::shortcode_table(get_the_ID())
			);
		}
		echo json_encode($output);
		die();
	}

	public function action_export_csv() {
		if (isset($_GET['table'])) {
			$post_id = $_GET['table'];
			check_admin_referer('wpt-export-csv-'.$post_id);
			$post = get_post($post_id);
			$title = $post->post_title;
			header("Content-type: text/plain");
			header("Content-Disposition: attachment; filename={$title}.csv");
			$csv = new WPTables_CsvExport($post_id);
			echo $csv->export();
		}
		die();
	}

	public function action_add_table_field() {
		if (isset($_GET['table'])) {
			$post_id = $_GET['table'];
			check_admin_referer('wpt-add-table-field-'.$post_id);
			$fields = json_decode(get_post_meta($post_id, 'wpt_fields', true), true);
			$field_id = 'f'.time();
			$fields[] = array(
				'name' => $field_id,
				'title' => __('Field', 'wptables').' '.(count($fields) + 1),
				'type' => 'text',
				'visible' => true
			);
			update_post_meta($post_id, 'wpt_fields', WPTables::json_encode($fields));

			$post = get_post($post_id);
			$data = json_decode($post->post_content, true);
			foreach ($data as &$row) {
				$row[$field_id] = '';
			}
			$post->post_content = WPTables::json_encode($data);
			wp_update_post($post);
			WPTables::redirect(array('page' => 'wptables', 'action' => 'edit', 'table' => $post_id));
		}
	}

	public function action_remove_table_field() {
		if (isset($_GET['table']) && isset($_GET['field'])) {
			$post_id = $_GET['table'];
			check_admin_referer('wpt-remove-table-field-'.$post_id);
			$fields = json_decode(get_post_meta($post_id, 'wpt_fields', true), true);
			$field_id = $_GET['field'];
			foreach ($fields as $key => $value) {
				if ($value['name'] == $field_id) {
					unset($fields[$key]);
				}
			}
			$fields = array_values($fields);
			update_post_meta($post_id, 'wpt_fields', WPTables::json_encode($fields));

			$post = get_post($post_id);
			$data = json_decode($post->post_content, true);
			foreach ($data as &$row) {
				unset($row[$field_id]);
			}
			$post->post_content = WPTables::json_encode($data);
			wp_update_post($post);
			WPTables::redirect(array('page' => 'wptables', 'action' => 'edit', 'table' => $post_id));
		}
	}

	public function action_hide_update_message() {
		check_admin_referer('wpt-hide-update-message');
		update_user_option(get_current_user_id(), "wpt_hide_update_message_".WPTables::get_instance()->get_version(), true);
		WPTables::redirect(array('page' => 'wptables'));
	}
}