<?php
/**
 * Plugin Name: Awesome Admin - Custom Wordpress Dashboard
 * Plugin URI: https://iqonicthemes.com
 * Description: Awesome Admin is an impressive wordpress custom dashboard.
 * Version: 1.0.1
 * Author: Iqonic Design
 * Text Domain: awesome-admin
 * Domain Path: /languages
 * Author URI: https://iqonic.design/ 
**/

use App\baseClasses\AADActivate;
use App\baseClasses\AADDeactivate;

defined( 'ABSPATH' ) or die( 'Something went wrong' );

// Require once the Composer Autoload
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
} else {
	die( 'Something went wrong' );
}

if (!defined('AWESOME_ADMIN_DIR'))
{
	define('AWESOME_ADMIN_DIR', plugin_dir_path(__FILE__));
}

if (!defined('AWESOME_ADMIN_DIR_URI'))
{
	define('AWESOME_ADMIN_DIR_URI', plugin_dir_url(__FILE__));
}


if (!defined('AWESOME_ADMIN_NAMESPACE'))
{
	define('AWESOME_ADMIN_NAMESPACE', "awesome-admin");
}

if (!defined('AWESOME_ADMIN_PREFIX'))
{
	define('AWESOME_ADMIN_PREFIX', "aad_");
}

/**
 * The code that runs during plugin activation
 */
register_activation_hook( __FILE__, [ AADActivate::class, 'activate'] );

/**
 * The code that runs during plugin deactivation
 */
register_deactivation_hook( __FILE__, [AADDeactivate::class, 'init'] );

( new AADActivate )->init();