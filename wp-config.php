<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          '|ceV/J@+S`TZy)/(FSf-kj/OVzpQ<sdT{[`u5oww`xhOb$]Ni/BCJj!@u@D]fc E' );
define( 'SECURE_AUTH_KEY',   ' XdqkV3ZxEH7xcP{3e9&]Z=xj>bL<mwnXf+DR;+~`y?!=`j*,eeBj4dakjVO9fuU' );
define( 'LOGGED_IN_KEY',     'p2`k$.$&@MoGh5dwuO$Prqy< 4Gqff0dDZ[J`F?->`D1nuLYNx^FtJ. S,.!mh|Z' );
define( 'NONCE_KEY',         'f!&V~J)mfOf%</L2xQ6?q%F~6m8PQ2{=b_/1+k8!|vT+ Y{aBwwXzrJh.iJ*[D:~' );
define( 'AUTH_SALT',         'x0/pGs}%qUO|<@?6r;;byS%ER^PXE|ne6z7aR n;.d&,=n=/v#<s[67]SCC+lU&)' );
define( 'SECURE_AUTH_SALT',  'W(_uLGB@?xa/GN_X_Am;9Q=<O)XZpm@SOAg%LfQSd wQ<jEg*$?*fFZU05i+Uem_' );
define( 'LOGGED_IN_SALT',    'T7zj;Vc.[T0Eq7xL:nawi;=PlmR~[+9TK4+XJH`$oidxP_Ok+1R-ks>OH~}~:&P4' );
define( 'NONCE_SALT',        '$V8Y=<AsS@T33`~$z! ?o[<TaB_zLGIS35uyiX-FEkj.1C[}6`7-qU/o$lB-_()5' );
define( 'WP_CACHE_KEY_SALT', 'D^p_NGI[a2rVe6G,?vk2XD9Sl$3~dUnS5tFmq |2k.gtWe0L.56|rg{VAQUKOPb1' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
define( 'WP_DEBUG_LOG', false );
define( 'WP_DEBUG_DISPLAY', false );
require_once ABSPATH . 'wp-settings.php';
