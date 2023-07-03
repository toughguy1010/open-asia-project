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
define( 'DB_NAME', 'open_asia_db' );

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
define( 'AUTH_KEY',         'toS/N@9ScfH}/Zmx%DXHd?7Oo j^J@.w!DBp}~ef)jo3A@eQfBu;cUbqv b8tAFi' );
define( 'SECURE_AUTH_KEY',  'Q7oOYBy&;^013qZ2P^x@8!CumkK(?wx<AM:+,BVKVu;UT55U1+}_@WlN>Mx5Ki_w' );
define( 'LOGGED_IN_KEY',    '*MKLLbBJ-V_]ZO,@d20@Yg]xShXozffe/,-6f;e].r1v{pU`K3A5Yj1gfI6Q3Ibw' );
define( 'NONCE_KEY',        'aE;6`L<Fh7MPPpfHaO!V6i`s3V92%p~[X$$!ye6[J3JXvLYEr>aKZaO jzalLq@:' );
define( 'AUTH_SALT',        '30fUAdHj)O^t!dDIu0!sl.(0JBUR9,eg;-VSf}r7;w^;L|Q#WM?i]D7n%ICn}lpP' );
define( 'SECURE_AUTH_SALT', '.ha9!Q/`JA=:WF=bcxVpjMpt=o9zw3vMnq:V:Cpdl ApNVS.E:LQ[M4#-D@7@+rh' );
define( 'LOGGED_IN_SALT',   'G5~!?a[Ef<zvcG[5v~;DG`NYy;9D~=P:X+F^/|cGTQ2tY_fl4^NU<NS8![UmejHO' );
define( 'NONCE_SALT',       '~~u>jQ3EIE6fEf{/z>be4rT5PaR0H:M,>rXUib~EdUP6}ddOu!v{UOnVzBH~E4^)' );

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
