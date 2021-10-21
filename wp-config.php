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
define( 'DB_NAME', 'mywordpress' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
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
define( 'AUTH_KEY',         'd;g$fax%KtULcP|:h|VS%GuFh5,x:c5Jq_1:bn`-d}pg)x[j/JQViD=7d7u.Z]`y' );
define( 'SECURE_AUTH_KEY',  '*<$EV}dqedsbgut!IeO&&B (pI7).5PqAeY;M`;*NMS9A^B..-WS~5nPh2UJ:;un' );
define( 'LOGGED_IN_KEY',    '%Bz@Yjp^Q=x8f<zR8/IV6p,6>s xC7_D7<`]?oKRRfW2?KSzayz9(+a&3l:tR#U9' );
define( 'NONCE_KEY',        '41sw1z)*d=pMtMR)3bq):-E1&`-#/6`e5@}icFN<=53c,r@>2Z40qvZaRGc8Bxxi' );
define( 'AUTH_SALT',        'P0=}3|*kYiW%V)&,Roy(Gx<5}bpG2(gmjAcdZ$7oscAtU|^dA~OY}%5aG0vsk2&t' );
define( 'SECURE_AUTH_SALT', 'O(an7.W_l/2Xp)gM?FU@AxlH1W(TYxr%`DI;qQp)22$p)M;[c*$OZl.RZ*2q+ulo' );
define( 'LOGGED_IN_SALT',   'Ck0M?Zt6j]wCK=H]Bz7uqXM?0SQ}`S=&8IB%a}3Xo[*:%]f43+_4yu@(d_JDZAm)' );
define( 'NONCE_SALT',       '$.wq.I|MJ6YO|`mFesL.FoAM^baa:/B={lR-N13ymXTPy6%X3EjlSI^6k-8xp^^l' );

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
