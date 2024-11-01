<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              hermannzossou.fr
 * @since             1.0
 * @package           Easy_search
 *
 * @wordpress-plugin
 * Plugin Name:       woocommerce-esay-view
 * Plugin URI:        www.revertisoft.com
 * Description:        Manage visibility and marketing shop view like a pro,make publicity of specific products...
 * Version:           1.0
 * Author:            hermann Zossou
 * Author URI:        hermannzossou.fr
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       easy_view
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'R_WEV_SEARCH_VERSION', '1.0' );
define( 'R_WEV_SEARCH_URL', plugins_url('/', __FILE__) );
define( 'R_WEV_SEARCH_DIR', dirname(__FILE__) );
define( 'R_WEV_SEARCH_MAIN_FILE', 'woo_easy_view/r_wev_search.php' );



/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-easy_search-activator.php
 */
function activate_r_wev_search() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-r_wev_search-activator.php';
	R_Wev_Search_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woo_ev_easy_search-deactivator.php
 */
function deactivate_r_wev_search() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-r_wev_search-deactivator.php';
	R_Wev_Search_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_r_wev_search' );
register_deactivation_hook( __FILE__, 'deactivate_r_wev_search' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-r_wev_search.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-r_wev_search-define.php';
require plugin_dir_path(__FILE__) . 'includes/class-r_wev_search-products-view.php';
require plugin_dir_path(__FILE__) . 'includes/r_wev_search_functions.php';

if(!function_exists("r_s_admin_tools"))
require plugin_dir_path(__FILE__) . 'includes/r_s_flibs.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function r_wev_run() {

	$plugin = new R_Wev_Search();
	$plugin->run();

}
r_wev_run();
