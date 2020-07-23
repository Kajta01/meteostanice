<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp1540738803' );

/** MySQL database username */
define( 'DB_USER', 'wp1540738803' );

/** MySQL database password */
define( 'DB_PASSWORD', 'VC0aBTt5' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost:3306' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         ']VQ_Ss]1Z+d{|Zit`}l~Oc{cU.tuinWQ ds|p6c/`k1KhJk7o]kv,+DE]LK+cvCO');
define('SECURE_AUTH_KEY',  '!M7+v}+LiVyNYp} `;N3[d-}7m_mgYGFs8,T (E9whM%`|9YDMi=CXu}ZDNwaf/X');
define('LOGGED_IN_KEY',    ']wYU-mnVN95v-(Mhe:E[<-3-3jFr-XksJ{yB?#BEgoj05El)49Y!+CVU};cT_NzY');
define('NONCE_KEY',        '+3rW@MUN,G2nr+5{>!+(gprD&Ab!DvB<a!{B+9duT]a}~74ky@^9WN.-1T{nQCvz');
define('AUTH_SALT',        '?IWPJM)s-17evi)C(c|Bny,cd1)D6uOQ30;2bljbnql?^3Zs]ryYWwiObIZxiBoW');
define('SECURE_AUTH_SALT', 'i5Iaj?#,7UiP~,~Z-,pT,ugh*V)chs12YOY*u.P$CH$LOijJ=D0KS+pf`!{/y-cj');
define('LOGGED_IN_SALT',   'A[5b<,f)*(vPo{6GEj-pK<atm2IQ5-@fu1u^,Zx!,8yWN[] X%/&k-e&7~y!nfn5');
define('NONCE_SALT',       'iwdm[6, B5J<6OflhzzYK2xFaP`b8+O7|+4B{op&|g]WJ|iW|%hEriR`YLPFp4TV');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
