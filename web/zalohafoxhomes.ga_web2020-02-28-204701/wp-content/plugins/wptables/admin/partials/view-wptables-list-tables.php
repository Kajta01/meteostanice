<?php
/**
 * WordPress Tables plugin.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */

class WPTables_ListView extends WPTables_View
{
    function __construct()
    {
        $this->setup(array(
            'title'     => __('Tables', 'wptables'),
            'title_action' => array(
                'label' => __('Add New', 'wptables'),
                'url'   => WPTables::url(array('page' => 'wptables-add-new'))
            ),
            'script'    => 'list_tables'
        ));
	    //$this->add_meta_box('wpt_subscribe', __('Stay Connected', 'wptables'), array($this, 'create_subscribe_metabox'), 'normal');
    }

    public function create_subscribe_metabox() { ?>
        <div class="wrap">
            <input id="wpt-subscribe-email" type="email" value="<?= wp_get_current_user()->user_email ?>" style="height: 28px;width: 200px;"/>
            <button id="wpt-subscribe-btn" type="button" class="button button-primary" style="margin-right: 5px;"><?= __('Subscribe', 'wptables') ?></button>
            <span class="description"><?= __('Interested in the WPTables major updates? Subscribe to the news!') ?></span>
        </div>
    <?php }

    protected function get_content() {
        $tables_count = count(query_posts(array( 'post_type'   => WPT_POST_TYPE_TABLE)));
        if (!get_user_option("wpt_hide_update_message_".WPTables::get_instance()->get_version()) && $tables_count > 0) {
            $this->create_nofit_new_version();
        }
        if ($this->has_meta_boxes) {
	        do_meta_boxes( null, 'normal', null );
        }
        $table = new WPTables_TablesTable();
        $table->prepare_items();
        $table->search_box(__('Search Tables', 'wptables'), 'wpt_search');
        $table->display();
    }

    protected function create_nofit_new_version() { ?>
        <div class="wpt-notif wpt-notif-green">
            <table>
                <tr>
                    <td><img src="<?= WPT_BASE_URL.'admin/img/author.jpg' ?>" class="wpt-avatar"></img></td>
                    <td>
                        <div style="font-weight: bold;"><?= sprintf(__('Thank you for using WordPress Tables %s!', 'wptables'), WPTables::get_instance()->get_version()) ?></div>
                        <p>
                            <?= sprintf(__('Hello %s,', 'wptables'), wp_get_current_user()->display_name) ?>
                            <span style="padding: 8px 0;display: block;">
                                <?= __('My name is Ian Sadovy and I am an author of WordPress Tables plugin.', 'wptables') ?><br>
                                <?= __('As you may know, WPTables is free and I am developing and supporting it by myself.', 'wptables') ?><br>
                                <?= __('If you like the plugin, just a small donation of 10$ can help me to keep support and improvements.', 'wptables') ?><br>
                                <?= __('Also, sharing your feedback about your experience is valuable for me.', 'wptables') ?>
                            </span>
                            <?= __('Thank you and see you soon!:)', 'wptables') ?>
                        </p>
                        <div>
                            <a href="https://wpwebtools.com/wptables/donate/" target="_blank" class="button button-large button-attractive"><span class="dashicons dashicons-thumbs-up"></span><?= __('Donate', 'wptables') ?></a>
                            <a href="https://wordpress.org/support/plugin/wptables/reviews/#new-post" target="_blank" class="button button-large button-primary"><span class="dashicons dashicons-star-filled"></span><?= __('Leave a Review', 'wptables') ?></a>
                            <a href="<?= WPTables::url(array('action' => 'wpt_hide_update_message'), true, 'admin-post.php') ?>" class="link-btn"><?= __('Hide this message', 'wptables') ?></a>
                        </div>
                    </td>
                </tr>
            </table>
            
        </div>
    <?php }
}

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WPTables_TablesTable extends WP_List_Table {
    public function __construct() {
        parent::__construct( [
            'singular' => __( 'Table', 'sp' ),
            'plural'   => __( 'Tables', 'sp' ),
            'ajax'     => false
        ] );
    }

    function get_data() {
        $search = ( isset( $_REQUEST['s'] ) ) ? $_REQUEST['s'] : false;
        $args = array(
            'post_type'   => WPT_POST_TYPE_TABLE,
            'posts_per_page' => -1,
            's' => $search
        );
	    wp_reset_query();
        $posts = query_posts( $args );
        $result = array();
        foreach ($posts as $post) {
            $result[] = $post->to_array();
        }
	    wp_reset_query();
        return $result;
    }

    public function no_items() { ?>
        <div class="wpt-no-tables">
            <?= __('You do not have any tables.') ?><br>
	        <?= __('Create a new one?') ?><br>
            <br>
            <a href="<?= WPTables::url(array('page' => 'wptables-add-new')) ?>" class="button button-primary"><?= __("Add New Table", 'wptables') ?></a>
        </div>
    <?php }

    public function get_columns() {
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            'post_title'    => __('Title', 'wptables'),
            'shortcode'     => __('Shortcode', 'wptables'),
            'post_author'   => __('Author', 'wptables'),
            'date'          => __('Date', 'wptables')
        );
        return $columns;
    }

    public function get_sortable_columns()
    {
        return array(
            'post_title' => array('post_title', false),
            'post_author'=> array('post_author', false),
            'date'      => array('post_modified', false)
        );
    }

    public function get_bulk_actions() {
        return array(
            'delete'    => __('Delete', 'wptables')
        );
    }

    public function prepare_items() {
        $this->process_bulk_action();

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $perPage = 10;
        $data = $this->get_data();
        usort( $data, array( &$this, 'sort_data' ) );

        $this->set_pagination_args( array(
            'total_items' => count($data),
            'per_page'    => $perPage
        ) );
        $currentPage = $this->get_pagenum();
        $data = array_slice($data, (($currentPage - 1) * $perPage), $perPage);

        $this->items = $data;

    }

    public function process_bulk_action() {
        if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {
            $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
            $action = 'bulk-' . $this->_args['plural'];
            if ( ! wp_verify_nonce( $nonce, $action ) ) {
                wp_die( 'Nope! Security check failed!' );
            }
        }
        $action = $this->current_action();
        if ($action == "delete" && isset($_POST['bulk-delete'])) {
            WPTables_Admin::delete_tables($_POST['bulk-delete']);
        }
    }

    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
        );
    }

    function column_post_title ($item) {
        $url_edit = WPTables::url(array('page' => 'wptables', 'action' => 'edit', 'table' => $item['ID']));
        $url_export = WPTables::url(array('action' => 'wpt_export_csv', 'table' => $item['ID']), true, 'admin-post.php');
        $url_clone = WPTables::url(array('action' => 'wpt_clone_table', 'table' => $item['ID']), true, 'admin-post.php');
        $url_delete = WPTables::url(array('action' => 'wpt_delete_table', 'table' => $item['ID']), true, 'admin-post.php');
        $title = empty($item[ 'post_title' ]) ? __('(no title)', 'wptables') : $item[ 'post_title' ];
        $actions = array(
            'edit'      => "<a href='{$url_edit}'>".__('Edit', 'wptables')."</a>",
            'clone'     => "<a href='{$url_clone}'>".__('Duplicate', 'wptables')."</a>",
            'export'    => "<a href='{$url_export}'>".__('Export', 'wptables')."</a>",
            'delete'    => "<a href='{$url_delete}' class='wpt-delete'>".__('Delete', 'wptables')."</a>",
        );
        return "<a href='{$url_edit}' class='row-title'>{$title}</a>" . $this->row_actions( $actions );
    }

    function column_shortcode($item) {
        return sprintf(
            '<code class="wpt-shortcode">%s</code>', WPTables::shortcode_table($item['ID'])
        );
    }

    function column_post_author ($item) {
        return get_the_author_meta('display_name', $item['post_author']);
    }

    function column_date($item) {
        $date = date(get_option('date_format'), strtotime($item['post_modified']));
        $time = date(get_option('time_format'), strtotime($item['post_modified']));
        return __('Modified', 'wptables')."<br>".
            sprintf('<abbr title="%s">%s</abbr>', $date.' '.$time, $date);
    }

    function column_default( $item, $column_name ) {
        switch( $column_name ) { 
            case 'ID':
            case 'post_title':
                return $item[ $column_name ];
            default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }

    function sort_data( $a, $b ) {
        // Set defaults
        $orderby = 'ID';
        $order = 'desc';
        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))
        {
            $orderby = $_GET['orderby'];
        }
        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = $_GET['order'];
        }
        if ($orderby == 'ID') {
            $result = $a < $b ? -1 : 1;
        } else {
            $result = strcmp( $a[$orderby], $b[$orderby] );
        }
        if($order === 'asc')
        {
            return $result;
        }
        return -$result;
    }
}
?>