<?php
/**
 * WordPress Tables plugin.
 *
 * @package    WPTables
 * @author     Ian Sadovy <ian.sadovy@gmail.com>
 */
?>
<?php 
add_meta_box( 'wptables_about', __( 'Overview', 'wptables' ), 'wptables_about', null, 'normal' );
add_meta_box( 'wptables_usage', __( 'Usage', 'wptables' ), 'wptables_usage', null, 'normal' );
add_meta_box( 'wptables_support', __( 'Help and Support', 'wptables' ), 'wptables_support', null, 'normal' );
add_meta_box( 'wptables_links', __( 'Resources', 'wptables' ), 'wptables_links', null, 'side' );

function wptables_about() {
	echo "<p>";
	_e("Need to insert a table into your page or post?", 'wptables');echo "<br>";
	_e("WordPress Tables plugin will take your data in CSV format or directly from MySQL table and create an interactive data table.", 'wptables');echo "<br>";
	_e("You can easily create and manage your tables from the WordPress administration.", 'wptables');echo "<br>";
	_e("Then, just simply insert a shortcode into your page, article or post. That’s it!", 'wptables');
	echo "</p>";
}

function wptables_usage() {
	echo "<p>";
	_e("After installing the plugin, tables are created in few very basic steps:", 'wptables');
	echo "<ul>";
	echo "<li> · ";_e("Navigate to the <code>WPTables</code> menu in the WordPress administration dashboard", 'wptables');echo "</li>";
	echo "<li> · ";_e("Click <code>Add New Table</code> and enter a name for the new table", 'wptables');echo "</li>";
	echo "<li> · ";_e("Choose a data source format and insert your data (for example the CSV file)", 'wptables');echo "</li>";
	echo "<li> · ";_e("After importing the data, you can edit table fields and other options", 'wptables');echo "</li>";
	echo "<li> · ";_e("Copy and paste the generated shortcode into your page or post (also you can use a button in the WordPress text editor to insert a table)", 'wptables');echo "</li>";
	echo "</ul>";
	echo "</p>";
}

function wptables_support() {
	echo '<p>' . sprintf(__( 'Please visit <a href="%s" target="_blank">WordPress Tables page</a> to find more information about the plugin.', 'wptables'), WPTables::URL_PLUGIN_PAGE). '</p>';
	echo '<p>' . sprintf(__( 'You are welcome to leave your feedbacks and report issues on the <a href="%s" target="_blank">Support Forum</a>.', 'wptables'), WPTables::URL_SUPPORT) . '</p>';
	echo '<p>' . __( 'Also, any ideas and possible improvements are highly appreciated!', 'wptables' ) . '</p>';
	echo '<p>' . sprintf(__( 'Follow us on <a href="%1$s" target="_blank">Facebook</a> and <a href="%2$s" target="_blank">Twitter</a> to get the latest news and tutorials.', 'wptables'), WPTables::URL_FACEBOOK, WPTables::URL_TWITTER ). '</p>';
}

function wptables_links() {
	echo "<p><a href='".WPTables::URL_PLUGIN_PAGE."' target='_blank'>Documentation</a></p>";
	echo "<p><a href='".WPTables::URL_SUPPORT."' target='_blank'>Support Forum</a></p>";
	echo "<p><a href='".WPTables::URL_FACEBOOK."' target='_blank'>Facebook</a></p>";
	echo "<p><a href='".WPTables::URL_TWITTER."' target='_blank'>Twitter</a></p>";
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?= esc_html(get_admin_page_title()); ?></h1>
    <div id="poststuff">
    	<div id="post-body" class="metabox-holder columns-2">
    		<div id="postbox-container-2" class="postbox-container">
    			<?php do_meta_boxes(null, 'normal', null); ?>
    		</div>
    		<div id="postbox-container-1" class="postbox-container"> 
    			<?php do_meta_boxes(null, 'side', null); ?>
    		</div>
		</div>
    </div>
</div>