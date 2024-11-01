<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       hermannzossou.fr
 * @since      1.0.0
 *
 * @package    Easy_search
 * @subpackage Easy_search/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Easy_search
 * @subpackage Easy_search/admin
 * @author     hermann Zossou <prohermannzosou91@gmail.com>
 */
class R_Wev_Search_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

	   wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/r_wev_search-admin.min.css', array(), $this->version, 'all' );
       
        wp_enqueue_style("acd-flexgrid", plugin_dir_url(__FILE__) . 'css/flexiblegs.min.css', array(), $this->version, 'all');
        wp_enqueue_style("r-ui", plugin_dir_url(__FILE__) . 'css/UI.css', array(), $this->version, 'all');
        wp_enqueue_style("wes-simplegrid", plugin_dir_url(__FILE__) . 'css/simplegrid.min.css', array(), $this->version, 'all');

        wp_enqueue_style("wes-datetimepicker-css", plugin_dir_url(__FILE__) . 'js/datetimepicker/jquery.datetimepicker.min.css', array(), $this->version, 'all');
    	wp_enqueue_style("wes-colorpicker-css", plugin_dir_url(__FILE__) . 'js/colorpicker/css/colorpicker.min.css', array(), $this->version, 'all');


	}

	/**
	 * Register the JavaScript for the admin area.
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

	   wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/r_wev_search-admin.min.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script("wes-datetimepicker-js", plugin_dir_url(__FILE__) . 'js/datetimepicker/jquery.datetimepicker.full.min.js', array('jquery'), $this->version, false);
        wp_enqueue_script('wes-colorpicker-js', plugin_dir_url(__FILE__) . 'js/colorpicker/js/colorpicker.min.js', array('jquery'), $this->version, false);
    }


    /**
     * Builds all the plugin menu and submenu
     */
    public function add_easy_search_menu() {
        $parent_slug = "edit.php?post_type=easy_search";
        add_submenu_page($parent_slug, __('All public view', 'wad'), __('All public view', 'wad'), 'manage_product_terms', 'easy_search-view', array($this, "r_wev_get_view_page"));
        add_submenu_page($parent_slug, __('Settings', 'easy_search'), __('Settings', 'easy_search'), 'manage_product_terms', 'easy_search-manage-settings', array($this, 'r_wev_settings_page'));
    }


public function r_wev_get_view_page(){

    $valide_search=new R_Wev_Search_Define();
    $valide_id=$valide_search->r_wev_get_active_search();
    $u=2;
    $count=0;
    while ( $count<2){
        if (isset($valide_id[$count])) {
             $view_page=new R_Wev_Search_View($valide_id[$count]);
            $view=$view_page->r_wev_public_view();
            
        }
        $count++;
       
    }
}

function r_wev_get_intervals_actions(){
		return array(
			"<"=>"is less than",
			"<="=>"is less or equal to",
			">"=>"is more than",
			">="=>"is more or equal to");
}

public function r_wev_settings_page(){
        if ((isset($_POST["easy_search-options"]) && !empty($_POST["easy_search-options"])) && check_admin_referer( 'settings_nonce', 'verify_settings' )) {
            foreach ($_POST["easy_search-options"] as $key => $value) {
               
                    $_POST["easy_search-options"][$key]=sanitize_text_field($value);
                    
            }
            update_option("easy_search-options", $_POST["easy_search-options"]);
        }
        ?>
        <div class="o-wrap cf">
        	<h1><?php _e("Woocommerce Easy Search Settings", "easy_search"); ?></h1>
            <form method="POST" action="" class="mg-top">
                <div class="postbox" id="easy_search-options-container">
                    
                    <?php
                    $begin = array(
                        'type' => 'sectionbegin',
                        'id' => 'easy_search-datasource-container',
                        'table' => 'options',
                    );
                    $header = array(
                        'title' => __('Header:', 'easy_search'),
                        'name' => 'easy_search-options[header]',
                        'type' => 'text',
                        'class'=>'color_field',
                        'desc' => __('Define color', 'WES'),
                        'default' => '',
                        
                    );

                    $footer = array(
                        'title' => __('Footer:', 'easy_search'),
                        'name' => 'easy_search-options[footer]',
                        'type' => 'text',
                        'class'=>'color_field',
                        'desc' => __('Define color', 'WES'),
                        'default' => '',
                        
                    );
		  
		            $border = array(
                        'title' => __('Border', 'WES'),
                        'desc' => __('Define header/footer color', 'WES'),
                        'type' => 'groupedfields',
                        'fields' => array($header, $footer),
                    );
		            $body=array(
		            	'title' => __('Body', 'easy_search'),
		                'name' => 'easy_search-options[body]', 
		                'type' => 'radio',
		                'default' => 'default',
		                'desc' => __('Customize table.', 'easy_search'),
		                'options' => array(
		                    "default" => "Default",
		                    "step" => "Step",
		                    "intervals" => "Intervals"
                		)
            		);
                    
                   $step = array(
		                'title' => __(' Step', 'easy_search'),
		                'name' => 'easy_search-options[step]',
		                'type' => 'number',
		                'custom_attributes' => array("step" => "any"),
		                'row_class' => 'percentage-row',
		                'desc' => __('Body color by step.', 'easy_search'),
		                'default' => '',
		            );

                    $color_step1 = array(
                        'title' => __('Color1', 'easy_search'),
                        'name' => 'easy_search-options[color1]',
                        'type' => 'text',
                        'desc' => __('Color for first step', 'easy_search'),
                        'id'=>'step_color_field1',
                        'class'=>'color_field',
                        'default' => '',
                    );
                    $color_step2 = array(
                        'title' => __('Color2', 'easy_search'),
                        'name' => 'easy_search-options[color2]',
                        'type' => 'text',
                        'id'=>'step_color_field2',
                        'class'=>'color_field',
                        'desc' => __('Color for second step', 'easy_search'),
                        'default' => '',
                    );

                    $step_groupfields = array(
                        'title' => __('Color by Step', 'easy_search'),
                        'desc' => __('Define color for body table', 'easy_search'),
                        'type' => 'groupedfields',
                        'class'=>'step_groupfields',
                        'fields' => array($step,$color_step1, $color_step2),
                    );
                    $intervals=array(
                    	'title' => __('Intervals', 'easy_search'),
		                'name' => 'easy_search-options[intervals]',
		                'type' => 'select',
		                'class' => 'discount-action',
		                'desc' => __('Type of intervals.', 'easy_search'),
		                'default' => '',
		                'options' => $this->r_wev_get_intervals_actions(),
            		);
                    $number_intervals=array(
                        'title' => __('Number', 'easy_search'),
                        'name' => 'easy_search-options[number]',
                        'type' => 'number',
                        'default' => '',
                    );
                    $color_intervals1 = array(
                        'title' => __('Color1', 'easy_search'),
                        'name' => 'easy_search-options[color_intervals1]',
                        'type' => 'text',
                        'id'=>'intervals_color_field1',
                        'class'=>'color_field',
                        'desc' => __('Color for first intervals', 'easy_search'),
                        'default' => '',
                    );
                    $color_intervals2 = array(
                        'title' => __('Color2', 'easy_search'),
                        'name' => 'easy_search-options[color_intervals2]',
                        'type' => 'text',
                        'id'=>'intervals_color_field2',
                        'class'=>'color_field',
                        'desc' => __('Color for second intervals', 'easy_search'),
                        'default' => '',
                    );
                    $intervals_groupfields = array(
                        'title' => __('Color by Intervals', 'easy_search'),
                        'desc' => __('Define color for body table following number for product view', 'easy_search'),
                        'type' => 'groupedfields',
                        'class'=>'intervals_groupfields',
                        'fields' => array($intervals,$number_intervals,$color_intervals1, $color_intervals2),
                    );
                
                    $end = array('type' => 'sectionend');

                     $settings = array(
                     	$begin,
                     	$border,
                     	 $body,
                     	 $step_groupfields,
                     	 $intervals_groupfields,
                        
                     	$end);
                        echo r_s_admin_tools($settings);
                        wp_nonce_field( 'settings_nonce', 'verify_settings' );
                    ?>
                </div>
 <input type="submit" class="button button-primary button-large" value="<?php _e("Save", "easy_search"); ?>">
            </form>
 </div>

        <?php
          global $o_row_templates;
        ?>
        <script>
            var o_rows_tpl =<?php echo json_encode($o_row_templates); ?>;
        </script>
        
    <?php
    }

}


