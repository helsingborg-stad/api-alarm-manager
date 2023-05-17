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

define( 'WP_SITEURL', 'http://' . $_SERVER['HTTP_HOST'] );
define( 'WP_HOME', WP_SITEURL );

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'dev');

/** Database username */
define('DB_USER', 'dev');

/** Database password */
define('DB_PASSWORD', 'dev');

/** Database hostname */
define('DB_HOST', 'db');

/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

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
define('AUTH_KEY',         'Z%dQR{ao$A8m|y= -%E.5=;EFA:PYaPwgaQABa6YAuk^}g6JJ)_Q/V$ Ym0[ ,SY');
define('SECURE_AUTH_KEY',  '_g)B6iyKN_Y.[gi-&AB[!|9$*l,z3P*9;M9!wj@k%|?9-p$c8)htC UA{sd%7e~[');
define('LOGGED_IN_KEY',    '_I(=||#A%1Qamp&{) zHVSG#|(i0lySb:K)NP`nG Tc!3RR(Ea?adgt%PE{D*BH=');
define('NONCE_KEY',        '21zs|}ojXS_mLGZ0]+kTsVD9[Nb@4~oj^]_!it3fEW$D&tN|2Hx%%lEK&eNWQ#03');
define('AUTH_SALT',        'Qb8_1>{c,&#Fgoe]|>t2CR1MKYD Gkn/lc7:YWJ7[g^C$c1 >=-HmgXIBHV7s8? ');
define('SECURE_AUTH_SALT', 'Q?{q1!%CJS?3f3Z6 ADS8,^:l55[}UW/{2v-@Wh q?udE,9HZhwDv)zg&{+h;peD');
define('LOGGED_IN_SALT',   'dYtN4_ufTSxo> NqCpyEVWcCeZTot|e^TwxT;?I-,1t] Hy_Ms6#H}GsZDc-;WY_');
define('NONCE_SALT',       ' 82.v<q++I}KHlBk+=TI=pjC0jw] 4/WWvSUI,SbgXZSD&Se60Lm0D>rn. jg#:{');

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define('WP_DEBUG', true);

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
