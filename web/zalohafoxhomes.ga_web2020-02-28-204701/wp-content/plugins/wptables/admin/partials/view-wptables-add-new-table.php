<?php
/**
 * WordPress Tables plugin.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */

class WPTables_AddNewTableView extends WPTables_View
{
    function __construct()
    {
        $this->setup(array(
            'title'     => __('Add New Table', 'wptables'),
            'action_url'=> admin_url('admin-post.php'),
            'action'    => 'wpt_add_new_table',
            'view_script'    => 'add_new',
            'view_data' => array(
                'dbs' => WPTables::get_dbs()
            )
        ));
    }

    protected function get_content() {
        if (isset($_GET['error_msg'])) {
	        echo "<div class='error'><p>".stripslashes($_GET['error_msg'])."</p></div>";
        }
        wp_nonce_field("wpt-add-new-table");
        $this->create_title_input(array(
            'id'    => 'title',
            'name'  => 'title',
            'placeholder'   => __('Enter title here', 'wptables'),
            'value' => isset($_GET['title']) ? $_GET['title'] : ''
        ));
        $this->create_format_selector();
        $this->create_ds_boxes(array(
            array(
                'format' => 'manual',
                'render' => array($this, 'create_ds_box_manual')
            ),
            array(
                'format' => 'csv',
                'render' => array($this, 'create_ds_box_csv')
            ),
            array(
                'format' => 'json', 
                'render' => array($this, 'create_ds_box_json')
            ),
            array(
                'format' => 'mysql',
                'render' => array($this, 'create_ds_box_mysql')
            )
        ));
    }

    protected function create_format_selector() {
        echo "<h3>1. ".__('Select input format:', 'wptables')."</h3>";
        echo "<input type='hidden' id='wpt-format' name='format'>";
        echo "<ul class='wpt-ds-list'>";
        $this->create_ds_item(array(
            'title' => __('Enter Manually', 'wptables'),
            'image' => WPT_BASE_URL.'admin/img/manual.png',
            'value'  => 'manual',
            'info'  => __('Create an empty table and enter data in a spreadsheet-like interface.', 'wptables')
        ));
        $this->create_ds_item(array(
            'title' => __('CSV', 'wptables'), 
            'image' => WPT_BASE_URL.'admin/img/csv.png',
            'value'  => 'csv',
            'info'  => __('Create a table using existing CSV data.', 'wptables')
        ));
        $this->create_ds_item(array(
            'title' => __('JSON', 'wptables'), 
            'image' => WPT_BASE_URL.'admin/img/json.png',
            'value'  => 'json',
            'info'  => __('Create a table using existing JSON data.', 'wptables')
        ));
        $this->create_ds_item(array(
            'title' =>  __('MySQL Database', 'wptables'), 
            'image' => WPT_BASE_URL.'admin/img/mysql.png',
            'value'  => 'mysql',
            'info'  =>  __('Create a table from existing data in your MySQL database. Such data will be dynamically loaded every time into the table.', 'wptables')
        ));
        echo "</ul>";
    }

    protected function create_ds_item($args) {
        echo "<li class='wpt-ds-item' data-value='{$args['value']}'>";
        echo "<img src='{$args['image']}' class='wpt-logo'></img>";
        echo "<div class='wpt-ds-item-title'>";
        echo $args['title'];
        echo "<i class='dashicons-before dashicons-info' title='{$args['info']}'></i>";
        echo "</div>";
        echo "</li>";
    }

    protected function create_ds_boxes($config) {
        echo "<div id='wpt-ds-box' style='display:none'>";
        echo "<h3>2. ".__('Add data source:', 'wptables')."</h3>";
        echo "<div class='meta-box-sortables'>";
        echo "<div class='postbox'>";
        echo "<div class='inside'>";
        foreach ($config as $item) {
            call_user_func($item['render']);
        }
        echo "</div>"; // .inside
        echo "</div>"; // .postbox
        echo "</div>"; // .meta-box-sortables
        $this->create_submit_button(__('Create Table', 'wptables'));
        echo "</div>"; // #wpt-ds-box
    }

    protected function create_ds_box_manual() {
        echo "<div id='wpt-ds-box-manual' class='wpt-ds-box-content' style='display:none'>";
        echo "<p>";
        _e('Create an empty table and enter data in a spreadsheet-like interface.', 'wptables');
        echo "<br>";
        _e('To start, please enter the initail number of columns in the table.', 'wptables');
        echo "</p>";
        include('view-wptables-add-new-table-manual.php');
        echo "</div>";  // .wpt-ds-box-content
    }

    protected function create_ds_box_csv() {
        echo "<div id='wpt-ds-box-csv' class='wpt-ds-box-content' style='display:none'>";
        echo "<p>";
        _e('Create a table using existing CSV data.', 'wptables');
        echo "<br>";
        _e('You can also export Excel file to CSV or copy/paste data in CSV format.', 'wptables');
        echo "</p>";
        include('view-wptables-add-new-table-csv.php');
        echo "</div>";  // .wpt-ds-box-content
    }

    protected function create_ds_box_json() { ?>
        <div id='wpt-ds-box-json' class='wpt-ds-box-content' style='display:none'>
            <p><?= __('Create a table using existing JSON data.', 'wptables') ?></p>
            <?php include('view-wptables-add-new-table-json.php'); ?>
        </div>
    <?php }

    protected function create_ds_box_mysql() {
        echo "<div id='wpt-ds-box-mysql' class='wpt-ds-box-content' style='display:none'>";
        echo "<p>";
        _e('Create a table from existing data in your MySQL database.', 'wptables');
        echo "<br>";
        _e('Such data will be dynamically loaded every time into the table from data base.', 'wptables');
        echo "</p>";
        include('view-wptables-add-new-table-mysql.php');
        echo "</div>";  // .wpt-ds-box-content
    }
}
?>