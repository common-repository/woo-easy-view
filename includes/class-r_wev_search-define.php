<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class-easy_search-define
 *
 * @author HZ
 */
class R_Wev_Search_Define {

public $id;
public $settings;
public static $_search_count;

public function __construct($search_id=FALSE) {
             if ($search_id){
                $this->id = $search_id;
                $this->settings = get_post_meta($search_id, "easy_search", true);
            }
}

/**
     * Register the search custom post type
     */

    public function r_wev_register_cpt_search() {
        $labels = array(
            'name' => _x('Views', 'easy_search'),
            'singular_name' => _x('View', 'easy_search'),
            'add_new' => _x('New view', 'easy_search'),
            'add_new_item' => _x('New view', 'easy_search'),
            'edit_item' => _x('Edit view', 'easy_search'),
            'new_item' => _x('New view', 'easy_search'),
            'view_item' => _x('View search', 'easy_search'),
            'not_found' => _x('No view found', 'easy_search'),
            'not_found_in_trash' => _x('No view in the trash', 'easy_search'),
            'menu_name' => _x('View', 'easy_search'),
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => false,
            'description' => 'Views',
            'supports' => array('title'),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'has_archive' => false,
            'query_var' => false,
            'can_export' => true,
            'menu_icon' => R_WEV_SEARCH_URL. 'admin/images/WES_icon2.png',
        );

        register_post_type('easy_search', $args);
    }

    /**
     * Adds the metabox for the search CPT
     */
    public function r_wev_get_search_metabox() {

        $screens = array('easy_search');
        foreach ($screens as $screen) {
        
        add_meta_box('reverti-search-settings-box', __('Search settings', 'easy_search'),
                 array($this, 'r_wev_get_settings_app'),
                 $screen);
        }
    }

    public function r_wev_get_settings_app(){

        ?>

        <div class='block-form'>
            <?php
        $begin = array(
                'type' => 'sectionbegin',
                'id' => 'easy_search-datasource-container',
            );

        $relationship = array(
                'title' => __('Rules groups relationship', 'easy_search'),
                'name' => 'easy_search[relationship]',
                'type' => 'radio',
                'desc' => __('AND: All groups rules must be considered to have the search action applied.', 'easy_search') . "<br" . __('OR: AT least one group rules must be verified to have the search action applied.', 'easy_search'),
                'default' => 'AND',
                'options' => array(
                    "AND" => "AND",
                    "OR" => "OR",
                )
        );

        $rules = array(
                'title' => __('Rules', 'easy_search'),
                'desc' => __('Allows you to define which rules should be checked in order to apply the search.', 'easy_search'),
                'name' => 'easy_search[rules]',
                'type' => 'custom',
                'callback' => array($this, "r_wev_get_search_fields_callback"),
            );
            
            $start_date = array(
                'title' => __('Start date', 'easy_search'),
                'name' => 'easy_search[start-date]',
                'type' => 'text',
                'class' => 'o-date',
                'desc' => __('Date from which the search view is applied.<br>Format: <strong>YYYY-MM-DD</strong>.', 'easy_search'),
                'default' => '',
            );

            $end_date = array(
                'title' => __('End date', 'easy_search'),
                'name' => 'easy_search[end-date]',
                'type' => 'text',
                'class' => 'o-date',
                'desc' => __('Date when the search view ends.<br>Format: <strong>YYYY-MM-DD</strong>.', 'easy_search'),
                'default' => '',
            );
            $title = array(
                'title' => __('Title of view', 'easy_search'),
                'name' => 'easy_search[title]',
                'type' => 'textarea',
                'class' => '',
                'desc' => __('Make a short description of view list.<br/><strong><i>This description must be fetching</i></strong>', 'easy_search'),
                'default' => 'Important',
            );

        $end = array('type' => 'sectionend');

        $settings = array(
                $begin,
                $title,
                $relationship,
                $rules,
                $start_date,
                $end_date,
                $end
            );
            
            echo r_s_admin_tools($settings);
             wp_nonce_field( 'wev_nonce', 'verify_wev' );
            ?>
        </div>

        <?php
        global $o_row_templates;
        ?>
        <script>
            var o_rows_tpl =<?php echo json_encode($o_row_templates); ?>;
        </script>
        <?php
        return;

    }
    
    function r_wev_get_active_search($group_by_types = false) {
        global $wpdb;
        $args = array(
            "post_type" => "easy_search",
            "post_status" => "publish",
            "nopaging" => true,
        );
        
            $valid_searchs = array();
        $searchs = get_posts($args);
        $today = date('Y-m-d');
        $today = date('Y-m-d', strtotime($today));
       
        foreach ($searchs as $search) {
            $metas = get_post_meta($search->ID, "easy_search", true);

            //We make sure empty dates are marked as active
            if (empty($metas["start-date"]))
                $start_date = date('Y-m-d');
                
            else
                $start_date = date('Y-m-d', strtotime($metas["start-date"]));

            if (empty($metas["start-date"]))
                $end_date = date('Y-m-d');

            else
                $end_date = date('Y-m-d', strtotime($metas["end-date"]));

            if ( ($today >= $start_date) && ($today <= $end_date) )
                    array_push($valid_searchs, $search->ID);
            
        }

        return $valid_searchs;
    }

            /**
             * Adds the Custom column to the default products list to help identify which ones are custom
             * @param array $defaults Default columns
             * @return array
             */
            function r_wev_get_columns($defaults) {
                $defaults['easy_search_start_date'] = __('Start Date', 'easy_search');
                $defaults['easy_search_end_date'] = __('End Date', 'easy_search');
                $defaults['easy_search_active'] = __('Active', 'easy_search');
                return $defaults;
            }

            /**
             * Sets the Custom column value on the products list to help identify which ones are custom
             * @param type $column_name Column name
             * @param type $id Product ID
             */
            function r_wev_get_columns_values($column_name, $id) {
                
                global $attende_id;
                if ($column_name === 'easy_search_active') {
                     $attente=array();
                    $class = "";
                    $active_search=$this->r_wev_get_active_search();
                    if(in_array($id,$active_search)){
                        $position=array_search($id, $active_search);
                        if($position<=1)
                        {
                            $class = "active";
                            echo "<span class='easy_search-status $class'></span>";
                        }else{
                            array_push($attente, $id);
                            $attente_id=$attente;
                            $class = "attente";
                             echo "<span class='easy_search-status $class'></span>";
                             echo'<p>Waiting</p><h5>More than two view is not possible</h5>';
                        }
                    }else{
                        echo "<span class='easy_search-status'></span>";
                    }

                }
                else if ($column_name === "easy_search_start_date") {
                    $search = new R_Wev_Search_Define($id);
                    if (!$search->settings) {
                        echo "-";
                        return;
                    }
                    $formatted_date = mysql2date(get_option('date_format'), $search->settings["start-date"], false);
                    echo $formatted_date;
                } else if ($column_name === "easy_search_end_date") {
                    $search = new R_Wev_Search_Define($id);
                    if (!$search->settings) {
                        echo "-";
                        return;
                    }
                    $formatted_date = mysql2date(get_option('date_format'), $search->settings["end-date"], false);
                    echo $formatted_date;
                }
            }
  
    /**
     * Saves the search data
     * @param type $post_id
    */

    public function r_wev_save_search($post_id) {
        
       $meta_key = "easy_search";
      
        if (isset($_POST[$meta_key])) {
            foreach ($_POST[$meta_key] as $key => $value) {
                    $_POST[$meta_key][$key]=$value;
            }
            update_post_meta($post_id, $meta_key, $_POST[$meta_key]);
        }
    }
    

public function r_wev_get_search_fields_callback(){

     $conditions = $this->r_wev_get_search_conditions();
        $first_rule = $this->r_wev_get_rule_tpl($conditions, "category");
        $values_match = $this->r_wev_get_value_fields_match(-1);
        $operators_match = $this->r_wev_get_operator_fields_match(-1);
        ?>
        <script>
            var search_values_matches =<?php echo json_encode($values_match); ?>;
            var search_operators_matches =<?php echo json_encode($operators_match); ?>;
        </script>
        <div class='easy_search-rules-table-container'>
            <textarea id='easy_search-rule-tpl' style='display: none;'>
                <?php echo $first_rule; ?>
            </textarea>
            <textarea id='easy_search-first-rule-tpl' style='display: none;'>
                <?php echo $first_rule; ?>
            </textarea>
            <?php
            $search_id = get_the_ID();
            $metas = get_post_meta($search_id, 'easy_search', true);
            $all_rules = array();
            if (isset($metas['rules']))
                $all_rules = $metas['rules'];

            if (is_array($all_rules) && !empty($all_rules)) {
                $rule_group = 0;
                foreach ($all_rules as $rules){
                    $rule_index = 0;
                    ?>
                    <table class="easy_search-rules-table widefat easy_search-rules-table">
                        <tbody>
                            <?php
                            foreach ($rules as $rule_arr) {
                                $rule_arr["condition"] = r_wev_get_proper_value($rule_arr, "condition");
                                $rule_arr["operator"] = r_wev_get_proper_value($rule_arr, "operator");
                                $rule_arr["value"] = r_wev_get_proper_value($rule_arr, "value");
                                $rule_html = $this->r_wev_get_rule_tpl($conditions, $rule_arr["condition"], $rule_arr["operator"], $rule_arr["value"]);
                               
                                $r1 = str_replace("{rule-group}", $rule_group, $rule_html);
                                $r2 = str_replace("{rule-index}", $rule_index, $r1);
                                echo $r2;
                                $rule_index++;
                            }
                            $rule_group++;
                            ?>
                        </tbody>
                    </table>
                    <?php
                }
            }
            ?>

        </div>
        <a class="button easy_search-add-group mg-top">Add search group</a>
        <?php
    }





function r_wev_get_search_conditions() {
        return array(
             "name_products"=>__("Items name","easy_search"),
            "category"=>__("Categories","easy_search"),
            "postID"=>__("Product ID","easy_search"),
            "author" => __("Retrieves only the elements created by the specified authors", "easy_search"),
            "customer-role" => __("If Customer role", "easy_search"),
            "customer" => __("If Customer", "easy_search"),         
            "customer-group" => __("If Customer belongs to specified groups", "easy_search"),
           
        );
}
    
 private function r_wev_get_rule_tpl($conditions, $default_condition = false, $default_operator = "", $default_value = "") {
        ob_start();
        $operators = $this->r_wev_get_operator_fields_match($default_condition, $default_operator);
        $value_field = $this->r_wev_get_value_fields_match($default_condition, $default_value);
        ?>
        <tr data-id="rule_{rule-group}">
            <td class="param">
                <select class="select easy_search-group-param" style="width:100px;" name="easy_search[rules][{rule-group}][{rule-index}][condition]" data-group="{rule-group}" data-rule="{rule-index}">
                    <?php
                    foreach ($conditions as $condition_key => $condition_val) {
                        if ($condition_key == $default_condition) {
                            ?><option value='<?php echo $condition_key; ?>' selected="selected"><?php echo $condition_val; ?></option><?php
                        } else {
                            ?><option value='<?php echo $condition_key; ?>'><?php echo $condition_val; ?></option><?php
                        }
                    }
                    ?>
                </select>
            </td>
            <td class="operator">
                <?php echo $operators; ?>
            </td>
            <td class="value">
                <?php echo $value_field; ?>
            </td>
            <td class="add">
                <a class="easy_search-add-rule button" data-group='{rule-group}'><?php echo __("and", "easy_search"); ?></a>
            </td>
            <td class="remove">
                <a class="remove-rule"></a>
            </td>
        </tr>
        <?php
        $rule_tpl =ob_get_contents();
        ob_end_clean();
        return $rule_tpl;
}


private function r_wev_get_operator_fields_match($condition = false, $selected_value = "") {
        $field_name = "easy_search[rules][{rule-group}][{rule-index}][operator]";
        $arrays_operators = array(
            "IN" => "IN",
            "NOT IN" => "NOT IN",
        );
        $arrays_operators_select = r_wev_get_html_select($field_name, false, "", $arrays_operators, $selected_value);


        $number_operators = array(
            "<" => __("is less than", "easy_search"),
            "<=" => __("is less or equal to", "easy_search"),
            "==" => __("equals", "easy_search"),
            ">" => __("is more than", "easy_search"),
            ">=" => __("is more or equal to", "easy_search"),
        );
        $number_operators_select = r_wev_get_html_select($field_name, false, "", $number_operators, $selected_value);
        
        $operators_match = array(
            "name_products"=>$arrays_operators_select,
            "category"=>$arrays_operators_select,
            "postID"=>$number_operators_select,
            "author"=>$arrays_operators_select,
            "customer-role" => $arrays_operators_select,
            "customer" => $arrays_operators_select,
            "customer-group" => $arrays_operators_select,   
        );

        if (isset($operators_match[$condition]))
            return $operators_match[$condition];
        else
            return $operators_match;
    }

    function r_wev_get_authors()
    {
        $all_users=  get_users();
        foreach ($all_users as $user)
        {
            $authors_arr[$user->ID]=$user->user_nicename;
        }

        return $authors_arr;
    }

private function r_wev_get_value_fields_match($condition = false, $selected_value = "") {
        $selected_value_arr = array();
        $selected_value_str = "";
        if (is_array($selected_value))
            $selected_value_arr = $selected_value;
        else
            $selected_value_str = $selected_value;

        $field_name = "easy_search[rules][{rule-group}][{rule-index}][value]";
         $text = '<input type="number" name="' . $field_name . '" value="' . $selected_value_str . '" required>';
        $roles = $this->r_wev_get_existing_user_roles();
        $roles_select = r_wev_get_html_select($field_name . "[]", false, "", $roles, $selected_value_arr, true, true);
        
        $product_name=$this->r_wev_get_existing_product_name();
        $product_name_selector=r_wev_get_html_select($field_name. "[]",false,"",$product_name,$selected_value_arr,true,true);

        $authors=$this->r_wev_get_authors();
        $authors_select=r_wev_get_html_select($field_name. "[]",false,"",$authors,$selected_value_arr,true,true);

        $category=$this->r_wev_get_wad_tax_query_data();
        foreach ($category['values_arr'] as $key => $value) {
           $all_taxonomy[$key.".".$value]=$value;
        }
        $category_select=r_wev_get_html_select($field_name. "[]",false,"",$all_taxonomy,$selected_value_arr,false);

        
        $users = $this->r_wev_get_existing_users();
        $users_select = r_wev_get_html_select($field_name . "[]", false, "", $users, $selected_value_arr, true, true);

        $pages_IDs = '<input type="text" placeholder="Pages IDs separated by comma" name="' . $field_name . '" value="' . $selected_value_str . '" required>';       
        
        $groups_arr = $this->r_wev_get_available_groups();
        $groups_select = r_wev_get_html_select($field_name . "[]", false, "", $groups_arr, $selected_value_arr, true);

        $values_match = array(
             "name_products"=>$product_name_selector,
            "category"=>$category_select,
            "postID"=>$text,
            "author"=>$authors_select,
            "customer-role" => $roles_select,
            "customer" => $users_select,
            "customer-group" => $groups_select,    
        );

        if (isset($values_match[$condition]))
            return $values_match[$condition];
        else
            return $values_match;
    }    

    public function r_wev_get_existing_product_name(){
      global $wpdb;
      $product_name_arr=array();
        $get_post_id=$wpdb->get_results("SELECT post_name FROM $wpdb->posts as posts,$wpdb->postmeta as postmeta 
                WHERE posts.ID=postmeta.post_id 
                AND post_status='publish' AND meta_key='_product_attributes'");
        foreach ($get_post_id as $key => $value) {
            $product_name_arr[$key.".".$value->post_name]=$value->post_name;
        }
            return $product_name_arr;
    }

    
    
 function r_wev_get_existing_user_roles() {
        global $wp_roles;
        $roles_arr = array();
        $all_roles = $wp_roles->roles;
        foreach ($all_roles as $role_key => $role_data) {
            $roles_arr[$role_key] = $role_data["name"];
        }
        return $roles_arr;
    }

private function r_wev_get_wad_tax_query_data()
    {
        $tax_terms = get_taxonomies(array(), 'objects');
        $params=array();
        $values=array();
        $values_arr=array();
        $values_arr_by_key=array();

        foreach ($tax_terms as $tax_key=>$tax_obj)
        {
            //We ignore everything that has nothing to do with products
            if(!r_wev_startWith($tax_key, "product_")&&!r_wev_startWith($tax_key, "pa_"))
                    continue;
            $params[$tax_key]=$tax_obj->labels->singular_name;
            $terms=  get_terms($tax_key);
            $terms_select="";
            foreach ($terms as $term)
            {
                $terms_select.='<option value="'.$term->term_id.'">'.$term->name.'</option>';
                $values_arr[$term->term_id]=$term->name;
                if(!isset($values_arr_by_key[$tax_key]))
                    $values_arr_by_key[$tax_key]=array();
                $values_arr_by_key[$tax_key][$term->term_id]=$term->name;
            }
            if($terms_select)
            {
                $values[$tax_key]=$terms_select;
            }
            else
                unset ($params[$tax_key]);
        }
        
       return array(
            "params"=>$params,
            "values"=>$values,
            "values_arr"=>$values_arr,
            "values_arr_by_key"=>$values_arr_by_key,
        );
    }
private function r_wev_get_available_groups() {
        global $wpdb;
        if (!function_exists("_groups_get_tablename"))
            return array();
        $group_table = _groups_get_tablename('group');
        $query = "SELECT distinct group_id, name FROM $group_table ORDER BY group_id asc";

        $results = $wpdb->get_results($query);
        $groups = array();
        foreach ($results as $result) {
            $groups[$result->group_id.".".$result->name] = $result->name;
        }
}

public function r_wev_get_existing_users() {
        $users = get_users(array('fields' => array('ID', 'display_name', 'user_email')));
        $users_arr = array();
        foreach ($users as $user) {
            $users_arr[$user->ID] = "$user->display_name($user->user_email)";
        }
        return $users_arr;
    }
    
}