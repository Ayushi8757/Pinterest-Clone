<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'pinterest_wb' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root@123#' );

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
define( 'AUTH_KEY',         ')6MeyZKVQc8(W05f*MOfcbkdM6^WWXWqR_&z1Xz7PTJFD3&Y#Q-t4@o20ZGVs+|k' );
define( 'SECURE_AUTH_KEY',  'gvq:)Lv&0a?XIzZPl#m?N Lp*BXOU |!_w%l>`@@(B8O.b5)qR? !NBg%U<sGaN%' );
define( 'LOGGED_IN_KEY',    '}ept[1O4$).P?oMA^:9;k|[D?$NKlB3uF:VRdVMskJ0S!Y&Ar-O*w,YpVit?Io<_' );
define( 'NONCE_KEY',        '6O`/k %Zk/+wO.)h-lbV>9+c;xpFeFWXEa>:JE,ApaZkr(0n8McLu:T=PN#)05.t' );
define( 'AUTH_SALT',        '&,58M{R{`td8ox0jY[*|Eg&L3ouSA~U6a*eiVIV7yoT3YuR qx7Ag{A_X~8x6/x[' );
define( 'SECURE_AUTH_SALT', 'Sw ]UeG+_;*+Yzl{v+i2P;Y8rHk!R)GRXd{eB!hvWlf~2 9D50;X=+j`nBtOLh4J' );
define( 'LOGGED_IN_SALT',   '>tW9WL++}Cn6cnK `emE!;X$e|3H9Zti1bnaOu_${AkY%.zc!: 1wiD2oR6.kD>O' );
define( 'NONCE_SALT',       '1sUoYDrS3K_XG~$`}>[9qB>MuGSZ58i-N?5FJsD^>s_0_GQYCd%Qu@k7-4~GaS!Z' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
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
