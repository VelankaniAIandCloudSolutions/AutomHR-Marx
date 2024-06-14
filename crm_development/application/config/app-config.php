<?php

defined('BASEPATH') or exit('No direct script access allowed');
/*
* --------------------------------------------------------------------------
* Base Site URL
* --------------------------------------------------------------------------
*
* URL to your CodeIgniter root. Typically this will be your base URL,
* WITH a trailing slash:
*
*   http://example.com/
*
* If this is not set then CodeIgniter will try guess the protocol, domain
* and path to your installation. However, you should always configure this
* explicitly and never rely on auto-guessing, especially in production
* environments.
*
*/

$root=(isset($_SERVER['HTTPS']) ? "https://" : "http://").$_SERVER['HTTP_HOST'];
$root.= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);


define('APP_BASE_URL', $root);
/*
* --------------------------------------------------------------------------
* Encryption Key
* IMPORTANT: Do not change this ever!
* --------------------------------------------------------------------------
*
* If you use the Encryption class, you must set an encryption key.
* See the user guide for more info.
*
* http://codeigniter.com/user_guide/libraries/encryption.html
*
* Auto added on install
*/
define('APP_ENC_KEY', '7e8bdb5dc422154675ce075ac715a614');

/**
 * Database Credentials
 * The hostname of your database server
 */
// define('APP_DB_HOSTNAME', 'localhost');

if($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SCRIPT_NAME'] === '/crm_development/index.php')
{
    define('APP_DB_HOSTNAME','192.168.10.200');
    define('APP_DB_USERNAME', 'ubuntu');
    define('APP_DB_PASSWORD', 'Velankanidb@2123');
    define('APP_DB_NAME', 'marx_db_development');
}
else{
    define('APP_DB_HOSTNAME','localhost');
    define('APP_DB_USERNAME', 'root');
    define('APP_DB_PASSWORD', 'Velankanidb@2123');
    define('APP_DB_NAME', 'marx_db');
}


/**
 * @since  2.3.0
 * Database charset
 */
define('APP_DB_CHARSET', 'utf8');
/**
 * @since  2.3.0
 * Database collation
 */
define('APP_DB_COLLATION', 'utf8_general_ci');

/**
 *
 * Session handler driver
 * By default the database driver will be used.
 *
 * For files session use this config:
 * define('SESS_DRIVER', 'files');
 * define('SESS_SAVE_PATH', NULL);
 * In case you are having problem with the SESS_SAVE_PATH consult with your hosting provider to set "session.save_path" value to php.ini
 *
 */
define('SESS_DRIVER', 'database');
define('SESS_SAVE_PATH', 'sessions');
define('APP_SESSION_COOKIE_SAME_SITE', 'Lax');

/**
 * Enables CSRF Protection
 */
define('APP_CSRF_PROTECTION', true);
