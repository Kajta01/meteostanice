<?php 
abstract class WPTables_View {
	protected $data = array();
	protected $action = '';
	protected $has_meta_boxes = false;
	protected $has_meta_boxes_side = false;

	public function __construct() {

	}

	protected function setup(array $data = array(), $action = '') {
		$this->action = $action;
		$this->data = $data;
	}

	protected function enqueue_script($name, array $dependencies = array()) {
		$js_file = "admin/js/{$name}.js";
		$js_url = WPT_BASE_URL . $js_file;
		wp_enqueue_script( "wptables-{$name}", $js_url, $dependencies, WPTables::version, true );
	}

	abstract protected function get_content();

	protected function get_title() {
		return isset($this->data['title']) ? $this->data['title'] : __('WPTables', 'wptables');
	}

	public function render() { 
		echo "<div class='wrap'>";
		echo "<h1 class='wp-heading-inline'>{$this->get_title()}</h1>";
		if (isset($this->data['title_action'])) {
			$url = $this->data['title_action']['url'];
			$label = $this->data['title_action']['label'];
			echo "<a href='{$url}' class='page-title-action'>{$label}</a>";
		}
		echo "<div id='poststuff'>";
		echo "<div id='post-body' class='metabox-holder".($this->has_meta_boxes_side ? " columns-2" : "")."'>";
		echo "<div id='post-body-content'>";
		echo "<form id='wpt-form' method='post' enctype='multipart/form-data'";
		if (isset($this->data['action_url'])) {
			echo " action='".esc_url($this->data['action_url'])."'";
		}
		echo ">";
		if (isset($this->data['action'])) {
			echo "<input type='hidden' name='action' value='{$this->data['action']}'>";
		}
		echo "<div id='postbox-container-2' class='postbox-container'>";
		echo $this->get_content();
		echo "</div>"; 	// #postbox-container-2
		if ($this->has_meta_boxes_side) {
			echo "<div id='postbox-container-1' class='postbox-container'>";
			do_meta_boxes( null, 'side', null );
			echo "</div>";
		}
		echo "</form>";
		echo "</div>";	// post-body-content
		echo "</div>";	// post-body
		echo "<br class='clear'>";
		echo "</div>";	// poststuff
		echo "</div>";	// wrap
		$this->get_script();

	}

	protected function create_title_input($params) {
		echo '<div id="titlediv">';
		echo '<div id="titlewrap">';
		$this->create_input($params);
		echo '</div>';
		echo '</div>';
	}

	protected function create_input($params) {
		$id = wpt_get_val($params, 'id', '', true);
		$name = wpt_get_val($params, 'name', '', true);
		$value = wpt_get_val($params, 'value', '', true);
		$class = wpt_get_val($params, 'class', '', true);
		$disabled = wpt_get_val($params, 'disabled', false, true);
		$placeholder = wpt_get_val($params, 'placeholder', '', true);
		if ($disabled === true) {
			$disabled = 'disabled';
		}
		echo "<input type='text' id='{$id}' name='{$name}' value='{$value}' placeholder='{$placeholder}' class='{$class}' {$disabled}>";
	}

	protected function create_number_input($params) {
		$id = wpt_get_val($params, 'id', '', true);
		$name = wpt_get_val($params, 'name', '', true);
		$value = wpt_get_val($params, 'value', '', true);
		$class = wpt_get_val($params, 'class', '', true);
		$disabled = wpt_get_val($params, 'disabled', false, true);
		if ($disabled === true) {
			$disabled = 'disabled';
		}
		echo "<input type='number' id='{$id}' name='{$name}' value='{$value}' class='{$class}' {$disabled}>";
	}

	protected function create_checkbox($name, $checked = false) {
    	echo "<input type='checkbox' name='{$name}'".($checked ? " checked" : "").">";
    }

	protected function create_hidden($name, $value) {
		echo sprintf("<input type='hidden' name='%s' value='%s'>", $name, $value);
	}

	protected function create_select($params) {
		$id = wpt_get_val($params, 'id', '', true);
		$name = wpt_get_val($params, 'name', '', true);
		$value = wpt_get_val($params, 'value', '', true);
		$class = wpt_get_val($params, 'class', '', true);
		$disabled = wpt_get_val($params, 'disabled', false, true);
		$options = wpt_get_val($params, 'options');
		$selected = wpt_get_val($params, 'selected');
		echo "<select id='{$id}' name='{$name}' value='{$value}' class='{$class}' {$disabled}>";
		foreach($options as $key => $value) {
			echo "<option value='{$key}' ".selected($key, $selected, true).">{$value}</option>";
		}
		echo "</select>";
	}

	protected function create_submit_button($title) {
		echo sprintf("<input type='submit' class='button button-primary button-large wpt-btn' value='%s'>",
        	$title);
	}

	protected function add_meta_box( $id, $title, $callback, $context = 'normal', $priority = 'default', $callback_args = null ) {
		$this->has_meta_boxes = true;
		if ($context == 'side') {
			$this->has_meta_boxes_side = true;
		}
		add_meta_box( "wpt-{$id}", $title, $callback, null, $context, $priority, $callback_args );
	}

	protected function get_script() {
		if (isset($this->data['view_script'])) {
			$json_data = isset($this->data['view_data']) ? $this->data['view_data'] : array(); ?>
			<script type='text/javascript'>
				try {
					jQuery(function() {
                        window.wpt_admin.view["<?= $this->data['view_script']?>"](<?= WPTables::json_encode($json_data) ?>);
                    });
				} catch(e) {
                    console.error(e);
				}
			</script>
		<?php }
	}
}

function wpt_get_val($array, $key, $default = false, $escape = false) {
	$val = isset($array[$key]) ? $array[$key] : $default;
	if ($escape) {
		$val = esc_html($val);
	}
	return $val;
}

?>