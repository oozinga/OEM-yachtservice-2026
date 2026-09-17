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
define( 'DB_NAME', 'wp_oem' );

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
define( 'AUTH_KEY',          'DA*6zm6No5GEr;*JP1E!W-h!FW#DKX4N^p$^Qbr=A,Vt&$c256L=Fm1nP4;._hF/' );
define( 'SECURE_AUTH_KEY',   'OLt1{=bFivK~@y@G;X)X/uaNm1 OlUA$bokSgl H.|m8*BkdUJ)3CB0anOc*qvU2' );
define( 'LOGGED_IN_KEY',     'ZGN2}bsO:&3v9]9n 8<rN@1;c2B&$Esd`mP(}66X<9LNh<7 ;rk-aOrWdu~QJ %p' );
define( 'NONCE_KEY',         '+**ng$}s3y:@8(DSruY7cGG?yLT9eicr],vd?!L[IE##<;EQg5)kai,pd$|NeR}e' );
define( 'AUTH_SALT',         '/c:rgZav5UGMilor+W9-xetkm$=e<8Z9B J21M=WV&1mbV;]3hP,I$M~O[RVw,m2' );
define( 'SECURE_AUTH_SALT',  'Pdg/K;zYM2 MCiOX*61uEIla4kz6+=pROAN|J4VB{YGHE80wUBiX?!Y{-.GPUIQ5' );
define( 'LOGGED_IN_SALT',    '-`UzO^QF7Bqcv`0<JQTq%9;R_lXLqs@jt)Wdp:;rzkK(5YE,fS+vhcR5/~^{-YkJ' );
define( 'NONCE_SALT',        'gl(|_Z1e^^,<: #;m~ZsNYA,X(;,[f8b{pEu@7)%?C5C3OMqIL)Lg$eiudoo*iIS' );
define( 'WP_CACHE_KEY_SALT', '->rW=K=:,3WZw4BHcl:@m#M]|&MHcg5J+nNO@lo3r;GuesMmOPUxV.3#o+4EdvrT' );


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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
