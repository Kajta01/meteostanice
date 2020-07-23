<?php
/**
 * WordPress Tables plugin.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */

class WPTables_EditTableView extends WPTables_View
{
	private $post_id;
	private $post;
    private $field_types;
    private $fields;

    function __construct($post_id) {
    	$this->post_id = $post_id;
    	$this->post = get_post($post_id);
        $this->field_types = array(
            'text' => __('Text', 'wptables'), 
            'number' => __('Number', 'wptables'),
            'link'  => __('Link', 'wptables')
        );

        $this->fields = json_decode(get_post_meta($this->post_id, 'wpt_fields', true), true);

        $this->setup(array(
            'title'     => __('Edit Table', 'wptables'),
            'action_url'=> admin_url('admin-post.php'),
            'action'    => 'wpt_update_table',
            'view_script'=> 'edit_table',
            'view_data' => array(
                'table_id'  => $this->post_id,
                'fields'    => $this->fields,
                'load_data_url' => WPTables::url(array('action' => 'wpt_load_data', 'table' => $post_id, 'raw' => 1), true, 'admin-ajax.php')
            )
        ));

        $this->add_meta_box('fields', __('Fields', 'wptables'), array($this, 'create_fields_metabox'));
        if (!$this->is_mysql()) {
            $this->add_meta_box('data', __('Data', 'wptables'), array($this, 'create_data_metabox'));
        } else if ($this->post->post_mime_type == 'mysql/mysql-query') {
	        $this->add_meta_box('query', __('Query', 'wptables'), array($this, 'create_query_metabox'));
        }
        $this->add_meta_box('table', __('Publish', 'wptables'), array($this, 'create_table_metabox'), 'side');
        $this->add_meta_box('options', __('Options', 'wptables'), array($this, 'create_options_metabox'), 'side');
    }

    protected function is_mysql() {
        return strpos($this->post->post_mime_type, 'mysql') !== false;
    }

    protected function get_content() {
	    if (isset($_GET['error_msg'])) {
		    echo "<div class='error'><p>".stripslashes($_GET['error_msg'])."</p></div>";
	    }
    	wp_nonce_field( 'wpt-update-table-'.$this->post_id );
        $this->create_hidden('table', $this->post_id);
    	$this->create_title_input(array(
            'id'    => 'title',
            'name'  => 'title',
            'placeholder'   => __('Enter title here', 'wptables'),
            'value' => $this->post->post_title
        ));
        do_meta_boxes( null, 'normal', null );
        $this->create_edit_field_dialog();
        $this->create_preview_dialog();
    }

    public function create_fields_metabox() { ?>
    	<div class='wpt-fields-container'>
    	    <table class='wpt-table wpt-alter-rows'>
        	<?php foreach ($this->fields as $field) {
        		$name = $field['name'];
    			echo "<tr data-field='{$name}'>";
    			$this->create_hidden("fields[{$name}][type]", $field['type']);
    			$this->create_hidden("fields[{$name}][format]", @$field['format']);
                $this->create_hidden("fields[{$name}][currency_symbol]", @$field['currency_symbol']);
    			$this->create_hidden("fields[{$name}][align]", @$field['align']);
    			$this->create_hidden("fields[{$name}][width]", @$field['width']);
    			$this->create_hidden("fields[{$name}][css]", @$field['css']);
    			// drag handle
                echo "<td class='wpt-col-drag'>";
        		echo "<span class='wpt-drag-handle'></span>";
        		echo "</td>";
        		// checkbox
        		echo "<td class='wpt-col-check'>";
        		$this->create_checkbox("fields[{$name}][visible]", $field['visible']);
        		echo "</td>";
        		// title
        		echo "<td>";
        		$this->create_input(array(
                    'id' => "wpt-title-input",
        			'name' => "fields[{$name}][title]",
        			'value' => $field['title'],
        			'class' => "large-text"
        		));
        		echo "</td>";
        		// edit
        		echo "<td class='wpt-col-actions'>";
        		echo "<span id='wpt-edit-field-btn' class='dashicons dashicons-edit wpt-icon-btn'></span>";
        		echo "</td>";
        		echo "</tr>";
        	} ?>
    	    </table>
    	</div>
        <?php if (!$this->is_mysql()) : ?>
            <div class="wpt-wrap">
                <a href="<?= WPTables::url(array('action' => 'wpt_add_table_field', 'table' => $this->post_id), true, 'admin-post.php'); ?>" class="button-link"><?= __('+ Add New Field', 'wptables') ?></a> 
            </div>
        <?php endif; ?>
	<?php }

    public function create_data_metabox() { ?>
        <input type="hidden" name="data">
        <div id="wpt-data-sheet"></div>
        <div class="wpt-data-sheet-toolbar" style="margin-top: 10px;">
            <?= __('Insert', 'wptables') ?>
            <input id="wpt-add-rows-inp" type="number" style="width: 50px;" value="10">
            <?= __('row(s)', 'wptables') ?>
            <button id="wpt-add-rows-btn" type="button" class="button"><?= __('Insert Rows', 'wptables') ?></button>
        </div>
    <?php }

	public function create_query_metabox() { ?>
        <textarea name="data-query"
                  class="large-text wpt-code"
                  rows="8" autocomplete="off"
                  autocorrect="off"
                  autocapitalize="off"
                  spellcheck="false"><?= $this->post->post_content ?></textarea>
        <button id="wpt-update-query-btn" type="button" class="button button-large button-primary"><?= __('Update', 'wptables') ?></button>
        <p>
            <?= __('Available variables', 'wptables') ?>: <code>$user_id</code> - <?= __('current user id', 'wptables') ?><br>
            <?= sprintf(__("Also, you can use <code>%s</code> filter to extend supported variables using PHP.", 'wptables'), 'wptables_mysql_query') ?>
        </p>
	<?php }

    public function create_table_metabox() { ?>
        <div class="wpt-wrap">
            <?= __('To insert this table into the page, please copy and paste the following shortcode:') ?>
            <div class="wpt-shortcode"><?= WPTables::shortcode_table($this->post_id) ?></div>
        </div>
        <div class="wpt-metabox-actions">
            <button id="wpt-preview-btn" type="button" class="button button-large" style="float: left;"><?= __('Preview', 'wptables') ?></button>
            <button id="wpt-submit-btn" type="button" class="button button-large button-primary"><?= __('Update', 'wptables') ?></button>
        </div>

    <?php }

    public function create_options_metabox() { 
        $meta_options = get_post_meta($this->post_id, 'wpt_options', true);
        $options = json_decode($meta_options, true);
        ?>
        <div>
            <table class="wpt-table wpt-vform-table">
                <tr>
                    <th><?= __('Allow Sorting', 'wptables') ?></th>
                    <td><?php $this->create_checkbox('config[sorting]', wpt_get_val($options, 'sorting')); ?></td>
                </tr>
                <tr>
                    <th><?= __('Allow Selecting', 'wptables') ?></th>
                    <td><?php $this->create_checkbox('config[selecting]', wpt_get_val($options, 'selecting')); ?></td>
                </tr>
                <tr>
                    <th><?= __('Show Header', 'wptables') ?></th>
                    <td><?php $this->create_checkbox('config[heading]', wpt_get_val($options, 'heading', true)); ?></td>
                </tr>
                <tr>
                    <th><?= __('Paging', 'wptables') ?></th>
                    <td><?php $this->create_checkbox('config[paging]', wpt_get_val($options, 'paging')); ?></td>
                </tr>
                <tr>
                    <th><?= __('Page Size', 'wptables') ?></th>
                    <td><?php $this->create_number_input(array(
                            'name' => 'config[pageSize]', 
                            'value' => wpt_get_val($options, 'pageSize', 20),
                            'disabled' => !wpt_get_val($options, 'paging')
                        ));?>
                    </td>
                </tr>
                <tr>
                    <th><?= __('Theme', 'wptables') ?></th>
                    <td><?php $this->create_select(array(
                            'name' => 'config[theme]',
                            'selected' => wpt_get_val($options, 'theme', ''),
                            'options' => array(
                                ''  => __('Light', 'wptables'),
                                'jsgrid-theme-dark'   => __('Dark', 'wptables'),
                                'jsgrid-theme-bluegray'   => __('Blue Grey', 'wptables'),
                                'jsgrid-theme-navy'   => __('Navy', 'wptables'),
                                'jsgrid-theme-green'  => __('Green', 'wptables'),
                                'jsgrid-theme-purple'   => __('Purple', 'wptables')
                            )
                        )); ?>    
                    </td>
                </tr>
            </table>
        </div>
    <?php }


    public function create_preview_dialog() { ?>
        <div id="wpt-preview-dialog" style="display: none;">
            <div class="wpt-title">
                <?= __('Table Preview', 'wptables') ?>
                <span class="dashicons dashicons-no wpt-icon-btn wpt-close-btn"></span>
            </div>
            <div class="wpt-content">
                <div style="margin-bottom: 10px"><i><?= __('Please note that the table may look different when embedded to the actual page.', 'wptables') ?></i></div>
                <div id="wpt-preview"></div>
            </div>
        </div>
    <?php }

	public function create_edit_field_dialog() { ?>
		<div id="wpt-edit-field-dialog" style="display: none">
			<div class="wpt-title">
		        <?= __('Column Options', 'wptables') ?>: <span id="wpt-field-name"></span>
                <span class="dashicons dashicons-no wpt-icon-btn wpt-close-btn"></span>
		    </div>
		    <div class="wpt-content" style="width:400px;">
                <table class="wpt-table wpt-vform-table">
                    <tr>
                        <th style="width: 100px"><?= __('Type', 'wptables') ?></th>
                        <td>
                            <select id="wpt-type-select" style="width: 100%">
                                <?php foreach ($this->field_types as $type_value => $type_label) : ?>
                                <option value="<?= $type_value ?>">
                                    <?= $type_label ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr id="wpt-format-row">
                        <th><?= __('Format', 'wptables') ?></th>
                        <td style="padding-bottom: 0">
                            <div class="wpt-input-wrap">
                                <input id="wpt-format-input" type="text" style="width: 60%">
                                <span id="wpt-format-preview" style="width: 40%"></span>
                            </div>
                            <span class="wpt-info"><?= sprintf('<a href="%s" target="_blank">%s</a>', 'http://numeraljs.com/#format', __('View examples'), 'wptables') ?></span>
                        </td>
                    </tr>
                    <tr id="wpt-format-row">
                        <th><?= __('Currency Symbol', 'wptables') ?></th>
                        <td>
                            <input id="wpt-currency-input" type="text" style="width: 100%" list="wpt-currencies">
                            <datalist id="wpt-currencies">
                                <option value="$">USD ($)</option>
                                <option value="€">EUR (€)</option>
                                <option value="¥">JPY (¥)</option>
                                <option value="£">GBP (£)</option>
                                <option value="元">CNY (元)</option>
                                <option value="₩">KRW (₩)</option>
                                <option value="₺">TRY (₺)</option>
                                <option value="₽">RUB (₽)</option>
                                <option value="₹">INR (₹)</option>
                                <option value="₴">UAH (₴)</option>
                            </datalist>
                        </td>
                    </tr>
                    <tr>
                        <th><?= __('Align', 'wptables') ?></th>
                        <td>
                            <select id="wpt-align-select" style="width: 100%">
                                <option value=""><?= __('Default', 'wptables') ?></option>
                                <option value="left"><?= __('Left', 'wptables') ?></option>
                                <option value="center"><?= __('Center', 'wptables') ?></option>
                                <option value="right"><?= __('Right', 'wptables') ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><?= __('Width (px)', 'wptables') ?></th>
                        <td>
                            <input id="wpt-width-input" type="number" class="large-text">
                        </td>
                    </tr>
                    <tr>
                        <th><?= __('CSS class', 'wptables') ?></th>
                        <td>
                            <input id="wpt-class-input" type="text" class="large-text">
                        </td>
                    </tr>
                </table>
		    </div>
		    <div class="wpt-toolbar">
                <?php if (!$this->is_mysql()) : ?>
                    <a id="wpt-remove-field-btn" href="<?= WPTables::url(array('action' => 'wpt_remove_table_field', 'table' => $this->post_id, 'field' => '__field__'), true, 'admin-post.php'); ?>" class="button-link trash" style="float:left;padding: 4px 0;"><?= __('Remove Field', 'wptables') ?></a>
                <?php endif; ?>
		        <a id="wpt-apply-btn" class="button button-large button-primary"><?= __('Apply', 'wptables') ?></a>
		    </div>
		</div>
	<?php }
}

?>