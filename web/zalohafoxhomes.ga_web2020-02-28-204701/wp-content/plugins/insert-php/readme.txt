=== PHP code snippets (Insert PHP) ===
Contributors: WillBontrager, webcraftic
Donate link: http://www.willmaster.com/plugindonate.php
Tags: run PHP, insert PHP, insert PHP page, insert PHP post, use PHP, PHP plugin
Requires at least: 4.2
Tested up to: 4.9.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin helps to use php code snippets in admin area without adding this code to function.php. You can use snippets shortcodes in posts, pages and widgets.

== Description ==

## Description of the new plugin (PHP code snippets) version 2.x.x ##

PHP code snippets is a new version of the popular Insert Php plugin. Insert php used to execute code inside [insert_php][/insert_php] tags on Wordpress pages and posts. For now, old [insert_php] shortcodes are still supported, and you can use them. Still, we recommend you to use the code inside special snippets visible and available to administrators only.

Snippet is a small piece of php code helping to extend your Wordpress capabilities. It’s a common thing in bloggers world – they usually add these pieces to the articles or write tutorials on how to solve a particular problem without plugins. However, you should know that placing the code to function.php is not the only option, and there is a better and cleaner way – to add snippets through the PHP code snippets plugin.

Our plugin has a very friendly interface helping you to place and execute fragments of code. You should add your code to the code editor, comment it and save the snippet. This snippet can be executed on all website or on the particular page where the shortcode is placed.

Example of using shortcodes:
`if( is_user_logged_in() ) {
	echo '<a href="http://site.dev/download.zip">Download file</a>';
} else {
	echo "You must be logged in to download the file.";
}`

Example of executing code for the whole website (global):
`remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rsd_link');
remove_action( 'wp_head', 'wlwmanifest_link');
remove_action( 'wp_head', 'wp_shortlink_wp_head');`

## Description of the old plugin (Insert php) version 1.3 ##

This plugin helps to use php code snippets in Admin panel without adding this code to function.php.  You can use snippets shortcodes in posts, pages and website widgets.

Run PHP code inserted into WordPress posts and pages.

The PHP code is between special tags ("[insert_php]" instead of "&lt;?php" and "[/insert_php]" instead of "?&gt;").

The PHP code runs as the page is sent to the browser. Output of the PHP code is published directly onto the post or page where the PHP code between the special tags is located.

The code between the tags must be complete in and of itself. References to variables or code blocks outside the area between the tags will fail. See the "more information" URL for an explanation of this.

Examples of use:

* Publish local time (users' computer clock settings being unreliable).

* Insert output of a PHP script, or just to run a script whether or not it generates output.

* Check/manipulate cookies or other actions that JavaScript can accomplish when using JavaScript is undesirable.

More information about the Insert PHP plugin can be found here: 
http://www.willmaster.com/software/WPplugins/go/iphphome_iphplugin

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.

2. Activate the plugin through the 'Plugins' screen in WordPress

3. Use the PHP Snippets -> Add snippet, to create a new snippet

== Frequently Asked Questions ==
= Why is it better to use PHP code snippets plugin instead of  function.php? (For last version) =

With a new update of Wordpress theme, you can lose all changes in the function.php file. Our plugin stores snippets in the database, so all data is safe until you remove the plugin. Also a huge amount of snippets in function.php looks messy and confusing; it is really hard to find something when the file is quite big.

= How to use other plugins’ shortcodes in PHP snippets? (For last version)=

You should call a shortcode using the following function:
`do_shortcode('[simple_shortcode]');`

Example:
`if ( is_user_logged_in() ) {
  echo do_shortcode('[contact_form id="297"]');
} else {
  echo "You must log in.";
}`

= The snippet code executed with an error and I cannot change it, what should I do? (For last version)=

Do not despair! Even an experienced person can make mistakes. We created a secure mode for this case. Go into it and your snippets code will not be executed.

1. Go to the safe mode by this link: http://your-site-name.dev/wp-admin/?wbcr-php-snippets-safe-mode
2. Edit the snippet in which you made a mistake;
3. Leave safe mode by clicking the link: http://your-site-name.dev/wp-admin/?wbcr-php-snippets-disable-safe-mode

Great, now you should not have any issues!

= Do I need to include the <?php, <? or ?> tags in my snippet? (For last version) =

No, just copy all the content inside those tags. If you accidentally forget (or just like being lazy), the tags will be stripped from the beginning and end of the snippet when you save it. You can, however, use those tags inside your snippets to start and end HTML sections

= Can the plugin be completely uninstalled? (For last version) =

Yes, when you delete PHP code snippets (Insert PHP) using the ‘Plugins’ menu in WordPress it will clean up the database table and a few other bits of data. Be careful not to remove PHP code snippets (Insert PHP) by deleting it from the Plugins menu unless you want this to happen.

= Will I lose my snippets if I change the theme or upgrade WordPress? (For last version) =

No, the snippets are stored in the WordPress database and are independent of the theme and unaffected by WordPress upgrades.

= Does plugin work with multisite? (For last version) =
No plugin does not support multisites. This is temporary and we will try to add support for networks in the future.

= How do I use this thing? (For old version) =

Make a copy of the working PHP code to be used in a post or a page.

Replace "&lt;?php" on the first line with "[insert_php]". Replace "?&gt;" on the last line with "[/insert_php]".

Paste the code into your post or page.

Examples are here: http://www.willmaster.com/software/WPplugins/go/iphpinstructions_iphplugin

= Can I have more than one place with PHP code on individual posts and pages? (For old version) =

Yes. I have found no limit to the number of places PHP code can be inserted into a post or page. Probably there is no WordPress software limit. There may be a server memory limit of your PHP code uses a lot of memory. 

= Does the PHP output need to have paragraph and line break HTML formatting codes? (For old version) =

No. HTML paragraph and line break formatting are applied to PHP output.

= Do I put the PHP code into content at the "Visual" tab or the "HTML/Text" tab? (For old version) =

Use the HTML/Text tab. While the Visual tab will, sometimes, the HTML/Text tab allows working with the code without the visual formatting.

= Why can't I set cookies or do a browser redirect? (For old version) =

With PHP, cookies are set in the web page header lines, before any page content is processed. Redirects, too, are done in the header lines. When PHP code is within a post or a page, all the header lines have already been sent, along with part of the content. At that point, it is too late to set cookies or redirect with PHP.

= I get a "Parse error: ..." What do I do now? (For old version) =

Unless the source code of the plugin has been changed or somehow became corrupted, the parse error is likely to be in the PHP code inserted into the post or page. Example:

Parse error: syntax error, unexpected T_STRING, expecting ',' or ';' in /public_html/wp331/wp-content/plugins/insert_php.php(48) : eval()'d code on line 5

Either within or at the end of the parse error message you'll find something like this:

eval()'d code on line 5

The error is on the indicated eval()'d code line number of the PHP code you are inserting ("5" in the example). At the PHP code you inserted, count down the number of lines indicated. You'll find the error at that line.

If you have PHP code inserted in more than one place, the error message may apply to any of those places. Temporarily remove or disable them, one at a time, until you determine which one the error message applies to.

If Insert PHP is used with an include() function, the include()'d file may be spawning the error message. In that case, the file name being include()'d and the line number of the error should be somewhere within the error message. 

When the error is located, correct it and try again.

== Screenshots ==

1. Snippets list
2. Edit snippet
3. Run snippet by a shortcode
4. Run snippet in widget
5. Old way to use the plugin

== Changelog ==

= 2.0.6 =
* Changed the way to safely save snippets. Now in case of an error, you will not lose the snippet changes. And also now there is no verification for snippets created for shortcodes, because of what many users had a problem with saving their old code.
* You can get the values of the variables from the shortcode attributes. For example, if you set the my_type attribute for the shortcode [wbcr_php_snippet id="2864" my_type="button"], you can get the value of the my_type attribute in the snippet by calling $my_type var.
* Added feature to set tags for snippets
* Added an instruction on how to export and import your own snippets
* Some bugs fixed.

= 2.0.4 =
* Fixed critical bug with $wp_query. It was a conflict with some plugins that overwritten the global variable $wp_query.
* All created and updated snippets by default, are now active.

= 2.0.2 =
Fixed a bug where you do not have enough permissions to view the page.

= 2.0.1 =
Attention! This new 2.0 plugin version, we added the ability to insert php code using snippets. This is a more convenient and secure way than using shortcodes [insert_php] code execute [/ insert_php]. However, for compatibility reasons, we left support for [insert_php] shortcodes, but we will depreciate them in the next versions of the plugin.

We strongly recommend you to transfer your php code to snippets and call them in your posts/pages and widgets using [wbcr_php_snippet id = "000"] shortcodes.

= 1.3 =
Fixed issue with str_replace() when haystack contained a slash character.

= 1.2 =
Changed handling of content.

= 1.1 =
Bug fix. Added ob_end_flush(); and changed variable names to remove opportunity for conflict with user-provided PHP code.

= 1.0 =
First public distribution version.
