<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       hermannzossou.fr
 * @since      1.0.0
 *
 * @package    Easy_search
 * @subpackage Easy_search/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Easy_search
 * @subpackage Easy_search/public
 * @author     hermann Zossou <prohermannzosou91@gmail.com>
 */
class R_Wev_Search_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in R_Wev_Search_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The R_Wev_Search_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
        wp_enqueue_style("wes-simplegrid", plugin_dir_url(__FILE__) . 'css/simplegrid.min.css', array(), $this->version, 'all');
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/r_wev_search-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in R_Wev_Search_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The R_Wev_Search_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/r_wev_search-public.js', array( 'jquery' ), $this->version, false );

	}

function init_globals() {
  
        global $wad_settings;
     
        $wad_settings = get_option("easy_search-options");
        $current_user = wp_get_current_user();
        $email = $current_user->user_email;
    
}
        
function  R_Wev_Search_Public_general_view(){
	$admin_part=new R_Wev_Search_Admin('woocommerce easy view','1.0');
	$admin_part->r_wev_get_view_page();
}


    


}	


