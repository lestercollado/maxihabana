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
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'maxihabana' );

/** Database username */
define( 'DB_USER', 'app' );

/** Database password */
define( 'DB_PASSWORD', 'Usuario1.' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define('FS_METHOD', 'direct');
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
define( 'AUTH_KEY',         'EgA>T,_js9|.X8S#J8!<lDch^JFslCg/y+OKCRg2pK^4VXyt?-,mGr1O7|<o(=8H' );
define( 'SECURE_AUTH_KEY',  '3u|@R?,[%<Y/6r:;bM_YY(3D}8<JrCm4#$bP-sq+:,(Af[$Z0t:ti^dq|G:3mn$e' );
define( 'LOGGED_IN_KEY',    '-IIq^k]5t0UqVtM^a,^}V&|F)%dP@gw3lqbtA|d(R!Sh&8Lp`KCw?N{C}<a3{_e+' );
define( 'NONCE_KEY',        'c*{x*GfYnJ,)bZ}+uU0dGa9llStVE`i_V.)$rYOqtitBPq0Lz@I{D(LTr nNeq`H' );
define( 'AUTH_SALT',        ']uAx#]QwBjP4{~A^3G:s  }6SL93x$!-MV#%Dhd>uhGfc>>vWGV?G#D>X%pe>>~!' );
define( 'SECURE_AUTH_SALT', 'JjdqN.bpm#Mc0m;-iW4{qC{wtIx+9=Sm}1RGQkZ}j9qhjk#^lf{kPiyy./P[}/y/' );
define( 'LOGGED_IN_SALT',   'M#Fj}XmQ[-9/(l M&MV!-$y`MBZPpx;*7r-/TEL{h^1.>g9fH<_#;7Q&7<@{hIeA' );
define( 'NONCE_SALT',       '>e(w!9Z?K,Ct40~J^1L}=}u%#o9.Rv(dq?]sgr 2TgQ&!)&>fb H-[az`~Gh6L?7' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'max_';

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
