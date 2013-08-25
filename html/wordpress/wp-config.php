<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wp');

/** MySQL database username */
define('DB_USER', 'wp');

/** MySQL database password */
define('DB_PASSWORD', 'wproot');

/** MySQL hostname */
define('DB_HOST', 'db.c2teljdmlsvx.us-east-1.rds.amazonaws.com');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'Y+uv/tx0[.uc+9|t&/`|cv.:0A~.@~?sutmYq+xP5-E^dFb|jyz`2FE4.0P%n8l|');
define('SECURE_AUTH_KEY',  'F8-7irtNz<| 59wv[MPsV1&KekioY@Y-T-q}+h6?_^N8NJ3.->a+QJB`5o$eb}-,');
define('LOGGED_IN_KEY',    'Z X:,~dNgAohr%M!e]B6S|DHQN-Oba+SELg0eLT(qQzVv<OI`e?c(i}!r}PZ+|yF');
define('NONCE_KEY',        '8|}|r`G4-d[,48n JtgN2B0<6ML=8n+!o39]cshMBw^{SzkcP<09w|3>L[hk|}lF');
define('AUTH_SALT',        ';2n_Rx&/m|#Yt&H4 :r75O=1v/Jat3y+*/QD:{Cj{>v]P(s56ovXpBdJ9ZZLwuyu');
define('SECURE_AUTH_SALT', 'DSVPnzy4c4W|s[nH&<DM-PxiSKL]DTxV%N3TyKr~E#6Kd9&-%I`-MPv-,$-wxW<+');
define('LOGGED_IN_SALT',   '4K.[Gn|T$2|J9xFTzbl{fls-D-SSA;`Pd29fA(YH27Y-{ ):|D:=FxGv|-$GPq_3');
define('NONCE_SALT',       'D)<mqzfN.hm!rf %&qCXb~+-1lXt+btV{[?pxWdKX_&0=9A75^FO|l;2$.Ij]m/M');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');
define('FS_METHOD', 'direct');
/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

