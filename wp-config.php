<?php
//Begin Really Simple Security key
define('RSSSL_KEY', 'pAKXe28AE3wzQgb0Ygop4eseo1NbtGihCvdS6UP8FHCooRBobDmzLwenll6qBiYv');
//END Really Simple Security key

//Begin Really Simple SSL session cookie settings
@ini_set('session.cookie_httponly', true);
@ini_set('session.cookie_secure', true);
@ini_set('session.use_only_cookies', true);
//END Really Simple SSL cookie settings
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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u831367588_Viktor' );
/** MySQL database username */
define( 'DB_USER', 'u831367588_meljkovfazli' );
/** MySQL database password */
define( 'DB_PASSWORD', 'cgZLUzLTF+R#7ti' );
/** MySQL hostname */
define( 'DB_HOST', 'localhost' );
/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );
/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );
/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'pz1bivz8wh4ngyxdseurpyvhwmxppuukggizofiinflywgvnbllxgj3momvue1ak' );
define( 'SECURE_AUTH_KEY',  'esfukycfznmm2s9bufeqsdgmpcntzfcgknmv4xfjhuoezz1bouorxx8ke80sikcn' );
define( 'LOGGED_IN_KEY',    'foqwsehklahrrxg75qa5xquxkznplu273npck5f1wtmof7nfaxtpw5j4xnb9enuy' );
define( 'NONCE_KEY',        'qzz0bitbqsoqnnlxjemvyuyya3rhi32atohrplk59kkhzsdnszbemn86yohq2quq' );
define( 'AUTH_SALT',        '3nrsragasz0rxldjbcdyu0b7izhcxi3saxzrph5s45wxrplfsifvmcm4p6ydie6z' );
define( 'SECURE_AUTH_SALT', '6lzgjmfx6vfvtyl3mrstsnifxia49qej9eadppiefqrwftfhkdky2bbkdgi5uihb' );
define( 'LOGGED_IN_SALT',   'b52l0k2qte5brngrianjjhurymyobqm4vpwn3jcf7ym86l0qy3rht8mgrt1ovxyc' );
define( 'NONCE_SALT',       'hnti3mlyoavybjd8rm93623iazl4yjtlvvnyg0kziptfj5ew81xuseq7ppmp9ujf' );
/**#@-*/
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpwd_';
/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
// Enable WP_DEBUG mode

define('WP_DEBUG', false);
// define( 'PLL_REMOVE_ALL_DATA', true ); 
// define( 'FS_METHOD', 'direct' );
// define( 'WP_DEBUG_LOG', false );
// define( 'WP_DEBUG_DISPLAY', false );

/* That's all, stop editing! Happy publishing. */
/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';