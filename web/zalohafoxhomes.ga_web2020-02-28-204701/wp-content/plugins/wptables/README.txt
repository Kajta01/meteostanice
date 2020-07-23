=== WordPress Tables ===
Contributors: iansadovy
Tags: tables, data tables, csv to table, mysql to table, excel table, responsive tables
Requires at least: 3.0
Tested up to: 4.9.4
Requires PHP: 5.4
Stable tag: 1.3.9
License: GPLv2+
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: https://wpwebtools.com/wptables/donate/

Create, manage and design interactive tables without writing any code. Sorting, paging, formatting and lot more options.

== Description ==
Need to insert a table into your page or post? WordPress Tables plugin will take your data in CSV/JSON format or directly from MySQL table and create an interactive data table. With WPTables plugin, you can easily create and manage your tables from the WordPress administration. Simply insert a shortcode into your page, article or post, and you are ready to go. That’s it! Enjoy your new table.

[DEMO](https://wpwebtools.com/wptables/demo/) | [SUPPORT](https://wordpress.org/support/plugin/wptables) | [REVIEWS](https://wordpress.org/support/plugin/wptables/reviews/) | [WEBSITE](https://wpwebtools.com/wptables/) 

WP Tables plugin’s main features are:
<ul>
<li>Inserting data using powerful spreadsheet editor</li>
<li>Importing data from CSV and JSON files</li>
<li>Showing data directly from MySQL database: by selecting a table or writing custom SQL query</li>
<li>Changing column titles, order and visibility</li>
<li>Number formatting</li>
<li>Sorting alphabetically and numerically</li>
<li>Pagination with options</li>
<li>Configurable width and height</li>
<li>Exporting created table to CSV file</li>
<li>6 predefined color themes</li>
<li>Supporting shortcodes from other plugins inside the table</li>
<li>Supporting links and images</li>
</ul>

After installing the plugin, tables are created in few very basic steps:
<ul>
<li>Navigate to the <code>WPTables</code> menu in the WordPress administration dashboard</li>
<li>Click <code>Add New Table</code> and enter a name for the new table </li>
<li>Choose a data source format and insert your data (for example the CSV file)</li>
<li>After importing the data, you can edit table fields and other options</li>
<li>Copy and paste the generated shortcode into your page or post (you can also use a button in the WordPress text editor to insert a table)</li>
</ul>

== Screenshots ==
1. Visualise your data in the table
2. Admin > Adding new table
3. Admin > Managing the table
4. Admin > Editing table data
5. Admin > All tables list
6. Admin > Table live preview

== Frequently Asked Questions ==
= How to change table width/height? =
Width of the table is 100% by default. Height is auto. But you can override these parameters by modifying a shortcode as following: 
<code>[wp_table id="77" width="640px" height="480px"]</code>

= How to format numbers? =
You can define a custom number format for columns with type `Number`.
1. In the WordPress administration open the table for editing.
2. In the Fields section find the necessary field and make sure that the type is `Number`
3. You will see the Formatting button next to the Type drop-down.
4. Set the desired format and click `Apply`.

= How to add currency symbol to values? =
1. In the WordPress administration open the table for editing.
2. In the Fields section find the necessary field and make sure that the type is `Number`
3. You will see the Formatting button next to the Type drop-down.
4. Add currency symbol to the format (i.e. '$0,0.00').
5. Click `Apply`.

= How to set link text? =
With `Link` field type you can use [Markdown syntax](https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet#links) to set the link text.

== Changelog ==
= VERSION 1.3.9 =
* Added abitity to assign HTML ID to the table in the shortcode using `html_id` attribute.
* Fixed the issue with exporting tables that are created using MySQL query.
* Fixed the issue with exporting tables that contain characters like Chinese or Korean.
* Fixed the issue with exporting column names insted of titles.
* WordPress 4.9.4 support.

= VERSION 1.3.8 =
* Fixed the issue with MySQL when correct (but empty) query results "Invalid query. Please check the syntax and try again." error.

= VERSION 1.3.7 =
* Added support for variables in the MySQL queries.

= VERSION 1.3.6 =
* Allow Editors to access WPTables.
* WordPress 4.9.2 support.

= VERSION 1.3.5 =
* Fixed the issue when table appears at the top of the page.
* Added compatibility with LABJS.
* Added compatibility with shortcodes plugins.

= VERSION 1.3.4 =
* Fixed the issue when jQuery is loaded in the footer.

= VERSION 1.3.3 =
* Fixed the issue with MySQL queries.
* Fixed the issue with removing rows.

= VERSION 1.3.2 =
* Added support for custom MySQL queries.

= VERSION 1.3.1 =
* Fixed horizontal alignment.
* Fixed mixing up tables with other post types.
* Fixed the issue with Unicode symbols.

= VERSION 1.3.0 =
* Brand new admin UI with drag'n'drop file import and much more.
* Brand new powerful data editor for imported files, including CSV and JSON.
* Support of importing JSON data.
* Improved CSV parsing.
* Adding/removing new columns.
* Search and bulk actions for created tables.
* Cloning tables.

= VERSION 1.2.10 =
* Improved MySQL support: now it is possible to load data from any database that is available for the DB user, not only WP tables.
* WordPress 4.9 support.

= VERSION 1.2.9 =
* Fix fatal error on PHP < 5.5.

= VERSION 1.2.8 =
* Added support for custom currency symbols in number formatting.
* Fixed the issue with saving table updates.

= VERSION 1.2.7 =
* Fixed compatibility with FancyBox library prior version 3.

= VERSION 1.2.6 =
* Fixed the issue with adding new table using CSV/MySQL.

= VERSION 1.2.5 =
* Added support for custom number format (decimal places, thousand separator, currency symbol, percent, etc.).
* Added support for links inside the table.

= VERSION 1.2.4 =
* Added support for shortcodes inside the table.
* Fixed issue with missing sort icons.

= VERSION 1.2.3 =
* Added support for Unicode characters.
* Fixed issue with an apostrophe (').

= VERSION 1.2.2 =
Fixed CSV parsing with dots in column names.

= VERSION 1.2.1 =
Fixed editing HTML-like data.

= VERSION 1.2.0 =
Added 6 predefined color themes.

= VERSION 1.1.0 =
Added a "manual" mode for creating tables. It is possible now to edit the data in a spreadsheet-like interface.

= VERSION 1.0.1 =
Added help and about sections.

= VERSION 1.0.0 =
Initial version where most features are ready and pretty stable.
