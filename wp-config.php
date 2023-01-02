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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'jha-luck' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'C$(!)^jng_3GgZJ_Y;Wd1:%!gn0nod+vk1x]M@Xa8]%2OF)X)7&,->hm^5BVh@eT' );
define( 'SECURE_AUTH_KEY',  'meWD^[>(:BN<C[UaR<P4|b5BtwzYl;ND(`V;a(A=_o5Rb:.xh~=2tsA8,rvhwPtp' );
define( 'LOGGED_IN_KEY',    'S]+}x9umF8]&,R[xI}7LvN9*:XBb.GB.E{oy+mV^saK($?,lOYOH%):6O@@P)i~M' );
define( 'NONCE_KEY',        '=Y?f%EE8DXIt$%E0cDiP5w?W6;])gM|N)?ylw1_.1^;%=$nJQqe7t!B,w~LWuEEH' );
define( 'AUTH_SALT',        'e63_K@f&1Rj4MwrLWr{j5cb6OqJqe5!OZjG_((<#A$w7j+B{K`f2!-$/-WtU$Ia1' );
define( 'SECURE_AUTH_SALT', '+w7 T1{7_&E&]hOhiw[kD|@+>!iqd.mIe>L{3fka$eH1,T5ND8jR|jhm[SZ[({<k' );
define( 'LOGGED_IN_SALT',   '>#ez}u-=9b>vfbG%lC:w|Fq4M1u>53Pf[2;yaUJIM,jzvZqM^>>(O[9-+eF.M#s ' );
define( 'NONCE_SALT',       '4u;RhN_0G_K)~>tN)]1+1e)|iz3]K7PpH5w~t%#(Byj$+Jwm7CQYDngmcm6s]V^r' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
