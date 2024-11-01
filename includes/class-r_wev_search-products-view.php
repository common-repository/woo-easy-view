<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class-easy_search-products-view
 *
 * @author HZ
 */
class R_Wev_Search_View {

    public $id;
    private $args;
    public $settings;
    public static $_search_valid_group;
    
    public static $_operator;
    public static $_no_proper_value;
    public static $_proper_value;
    public static $_table_lign;
    public static $_compt_view;
    public static $_count_condition_degree1;
    public static $_count_degree1_element;

    public function __construct($search_id=FALSE) {
        if ($search_id)
        {
            $this->_table_lign=array();
            $this->id = $search_id;
            $this->settings=get_post_meta($search_id, "easy_search", true);
           
        }
    }

    private function r_wev_get_category_name($value){
            global $wpdb;

                $get_product_cat=$wpdb->get_var("SELECT name 
                                        FROM  $wpdb->terms as terms,
                                        $wpdb->term_relationships as term_relationships,
                                        $wpdb->term_taxonomy as term_taxonomy
                                        WHERE terms.term_id=term_taxonomy.term_id 
                                        AND term_taxonomy.term_taxonomy_id=term_relationships.term_taxonomy_id
                                        AND term_relationships.object_id='$value' AND term_taxonomy.taxonomy='product_type'");
                                       
        return $get_product_cat;
    }

    function r_wev_get_product_value($product_id){
        global $wpdb;
        $product_data=array();
        $price=array();
        $product = wc_get_product($product_id); 
               if($product->product_type=='variable'){ 
                        $get_variation_id=$wpdb->get_results("SELECT ID
                                        FROM  $wpdb->posts 
                                        WHERE post_parent='$product_id'
                                        AND post_type='product_variation'
                                        AND post_status='publish'");

                        $meta_attribute=get_post_meta($product_id, "_product_attributes", true); 
                        array_push($product_data,$product_id,$meta_attribute['variantes']['value']);  

                    foreach ($get_variation_id as $key => $value) {
                        $meta_sale_price=get_post_meta($value->ID, "_sale_price", true);
                        array_push($price,$meta_sale_price);  
                    }

                    $meta_stock_state=get_post_meta($product_id, "_stock_status", true);
                    $image=$product->get_image();
                    $min=min($price);

                    $max=max($price);
                    array_push($product_data,$min,$max,$product->post->post_name,$meta_stock_state,$image);
                

               }else{
                    $meta_regular_price=get_post_meta($product_id, "_regular_price", true);
                    $meta_sale_price=get_post_meta($product_id, "_sale_price", true);
                    $meta_stock_state=get_post_meta($product_id, "_stock_status", true);
                    $image=$product->get_image();
                    array_push($product_data,$product_id,$product->post->post_name,$meta_regular_price,$meta_sale_price,$meta_stock_state,$image);

               } 
            return $product_data;
    }

    function r_wev_get_row_value($var_color,$value0,$value1,$value2,$value3,$value4,$value5,$value6,$all_attribute=FALSE){
        
                if ($all_attribute==FALSE){                                                            
                    
                echo '<tr style="background-color:'.$var_color.'"> <td><a href="'. get_post_permalink(esc_url($value0)).'/?product='.str_replace(' ', '-', esc_attr($value1)).'">'.esc_attr($value6).'</a></td>
                <td><a href="'. get_post_permalink(esc_url($value0)).'">'.$value5.'</a></td>
                <td><a href="'. get_post_permalink(esc_url($value0)).'">'.esc_attr($value1).'</a></td>
                <td><a href="'. get_post_permalink(esc_url($value0)).'">'.esc_attr($value4).'</a></td>
                <td><a href="'. get_post_permalink(esc_url($value0)).'">'.esc_attr($value2).'</a></td>
                <td><a href="'. get_post_permalink(esc_attr($value0)).'">'.esc_attr($value3).'</a></td>
                <td style="text-align:center;><a href="'. get_post_permalink(esc_url($value0)).'">--</a></td></tr>';
                }else{                                            
                   echo '<tr style="background-color:'.esc_attr($var_color).'">
                        <td><a href="'. get_post_permalink(esc_url($value0)).'">'.esc_attr($value4).'</a></td>
                        <td><a href="'. get_post_permalink(esc_url($value0)).'">'.$value3.'</a></td>
                        <td><a href="'. get_post_permalink(esc_url($value0)).'">'.esc_attr($value1).'</a></td>
                        <td><a href="'. get_post_permalink(esc_url($value0)).'">'.esc_attr($value2).'</a></td>
                        <td style="text-align:center;"><a href="'. get_post_permalink(esc_url($value0)).'">--</a></td>
                        <td><a href="'. get_post_permalink(esc_url($value0)).'">'.esc_attr($value5).'-'.esc_attr($value6).'</a></td>
                        <td><a href="'. get_post_permalink(esc_url($value0)).'">'.esc_attr($all_attribute).'</a></td></tr>';

                    }
   
    }
    
    function r_wev_get_view_table($all_product_data){
        global $compt_view;
        global $wev_settings;
        $wev_settings=get_option("easy_search-options");
        $compt_view+=1;
        $var_default='#e3d7e3';
            if (empty($all_product_data)) {
                goto out;
            }
         ?>
        <script>
            var  compt_view=<?php echo json_encode($compt_view); ?>;
        </script>
        <?php
            if (isset($wev_settings['header'])&& !empty($wev_settings['header'])) {
                $var_header='font-weight:normal;line-height:16px;background-color:'.$wev_settings['header'].';font-size:12px';
            }
            if (isset($wev_settings['footer'])&& !empty($wev_settings['footer'])) {
                $var_footer='font-weight:normal;line-height:16px;background-color:'.$wev_settings['footer'].';font-size:12px';
            }
            if ($compt_view%2!=0) {
                   echo "<div class='wpc-col-1-1'> <button class='view'></button></div>";
                    echo "<div class='table' style='display:none'>";
            }else{
                    echo"<div class='wpc-col-2-4'> <button class='visibility'></button></div>";
                    echo"<div class='visibility_table' style='display:none'>";
                }
        ?>
    
        <table class="data">
            <thead style=<?php if(isset($var_header)){echo $var_header;}else{echo 'font-weight:normal;line-height:16px;background-color:#d1bad1;font-size:12px';} ?>>
                <tr><td>CATEGORY</td><td>PHOTO</td><td>PRODUCT NAME</td><td>STATUS</td><td>REGULAR PRICE</td><td>SALE PRICE</td><td>VARIATION</td></tr>
            </thead>
            <tbody><?php
                        $count_lign=0;
                    foreach($all_product_data as $key =>$value) {
                        $count_lign+=1;
                        $count=$key+1;
                        while (isset($all_product_data[$count])) {//for avoid doublon in table
                            if ($value[1]!=$all_product_data[$count][0]) {
                                $count+=1;
                            }else{
                                goto endwhilepart;
                            }
                        }
                        if (isset($wev_settings['body']) && $wev_settings['body']=='step'){
                            if (isset($wev_settings['step']) && isset($wev_settings['color1']) && isset($wev_settings['color2'])) {
                                $count_step=$wev_settings['step'];
                                $var_color1=$wev_settings['color1'];
                                $var_color2=$wev_settings['color2'];
                                    
                                if ($count_step>=$count_lign || ($count_lign%2)!=0 && $count_lign%$count_step==0) {
                                        if (count($all_product_data[$key])==7) {
                                            $this->r_wev_get_row_value($var_color1,$value[0],$value[1],$value[2],$value[3],$value[4],$value[5],$value[6]);
                                            
                                        }else{
                                             $ref=count($all_product_data[$key])-6;
                            
                                            $n=1;
                                            $attribute=array();
                                            while($ref-$n >=0){
                                                $count+=1;
                                               $ctype=ctype_digit($value[$ref-$n]);

                                               if (!ctype_digit($value[$ref-$n])) {
                                                   array_push($attribute, $value[$ref-$n]); 
                                               }
                                               $n+=1; 
                                            }
                                            $all_attribute=implode(',',$attribute);
                                            $this->r_wev_get_row_value($var_default,$value[$ref-$n+1],$value[$ref+2],$value[$ref+3],$value[4+$ref],$value[5+$ref],$value[$ref],$value[$ref+1],$all_attribute);
                                        }

                                }else{
                                                                        
                                    if (count($all_product_data[$key])==7) {
                                            $this->r_wev_get_row_value($var_color2,$value[0],$value[1],$value[2],$value[3],$value[4],$value[5],$value[6]);
                                            
                                    }else{
                                             $ref=count($all_product_data[$key])-6;
                            
                                                $n=1;
                                                $attribute=array();
                                                while($ref-$n >=0){
                                                    $count+=1;
                                                   $ctype=ctype_digit($value[$ref-$n]);

                                                   if (!ctype_digit($value[$ref-$n])) {
                                                       array_push($attribute, $value[$ref-$n]); 
                                                   }
                                                   $n+=1; 
                                                }
                                                $all_attribute=implode(',',$attribute);
                                                $this->r_wev_get_row_value($var_default,$value[$ref-$n+1],$value[$ref+2],$value[$ref+3],$value[4+$ref],$value[5+$ref],$value[$ref],$value[$ref+1],$all_attribute);
                                    
                                    }
                                }

                            }
                        }elseif (isset($wev_settings['body']) && $wev_settings['body']=='intervals') {
                            if (isset($wev_settings['intervals']) && isset($wev_settings['color1']) && isset($wev_settings['color2'])) {
                                $type_intervals=$wev_settings['intervals'];
                                $var_color1=$wev_settings['color_intervals1'];
                                $var_color2=$wev_settings['color_intervals2'];
                                $number=$wev_settings['number'];
                                if ($type_intervals=='<') {
                                                                        
                                        if (count($all_product_data)>$number) {
                                           
                                            //can apply following intervals
                                            if ($key+1<$number) {
                                                //case for simple product
                                                if (count($all_product_data[$key])==7) {
                                                    $this->r_wev_get_row_value($var_color1,$value[0],$value[1],$value[2],$value[3],$value[4],$value[5],$value[6]);
                                            
                                                }else{//case for variable product
                                                     $ref=count($all_product_data[$key])-6;
                            
                                                        $n=1;
                                                        $attribute=array();
                                                        while($ref-$n >=0){
                                                            $count+=1;
                                                           $ctype=ctype_digit($value[$ref-$n]);

                                                           if (!ctype_digit($value[$ref-$n])) {
                                                               array_push($attribute, $value[$ref-$n]); 
                                                           }
                                                           $n+=1; 
                                                        }
                                                        $all_attribute=implode(',',$attribute);
                                                        $this->r_wev_get_row_value($var_default,$value[$ref-$n+1],$value[$ref+2],$value[$ref+3],$value[4+$ref],$value[5+$ref],$value[$ref],$value[$ref+1],$all_attribute);
                                                    }
                                            }else {//can't apply following this intervals thus apply this color for all part
                                                if (count($all_product_data[$key])==7) {
                                                $this->r_wev_get_row_value($var_color2,$value[0],$value[1],$value[2],$value[3],$value[4],$value[5],$value[6]);
                                                
                                                }else{
                                                     $ref=count($all_product_data[$key])-6;
                                                        $n=1;
                                                        $attribute=array();
                                                        while($ref-$n >=0){
                                                            $count+=1;
                                                           $ctype=ctype_digit($value[$ref-$n]);

                                                           if (!ctype_digit($value[$ref-$n])) {
                                                               array_push($attribute, $value[$ref-$n]); 
                                                           }
                                                           $n+=1; 
                                                        }
                                                        $all_attribute=implode(',',$attribute);
                                                        $this->r_wev_get_row_value($var_default,$value[$ref-$n+1],$value[$ref+2],$value[$ref+3],$value[4+$ref],$value[5+$ref],$value[$ref],$value[$ref+1],$all_attribute);
                                                    }
                                            }
                                        }
                                }if($type_intervals=='>') {
                                                                        
                                        if (count($all_product_data)>$number) {
                                            $intervals=count($all_product_data)-$number;
                                            if ($intervals>0 && $key+1>$number) {
                                                if (count($all_product_data[$key])==7) {
                                                    $this->r_wev_get_row_value($var_color1,$value[0],$value[1],$value[2],$value[3],$value[4],$value[5],$value[6]);
                                            
                                                }else{
                                                     $ref=count($all_product_data[$key])-6;
                            
                                                        $n=1;
                                                        $attribute=array();
                                                        while($ref-$n >=0){
                                                            $count+=1;
                                                           $ctype=ctype_digit($value[$ref-$n]);

                                                           if (!ctype_digit($value[$ref-$n])) {
                                                               array_push($attribute, $value[$ref-$n]); 
                                                           }
                                                           $n+=1; 
                                                        }
                                                        $all_attribute=implode(',',$attribute);
                                                        $this->r_wev_get_row_value($var_default,$value[$ref-$n+1],$value[$ref+2],$value[$ref+3],$value[4+$ref],$value[5+$ref],$value[$ref],$value[$ref+1],$all_attribute);
                                                    }
                                            }else {
                                                if (count($all_product_data[$key])==6) {
                                                    $this->r_wev_get_row_value($var_color2,$value[0],$value[1],$value[2],$value[3],$value[4],$value[5],$value[6]);
                                            
                                                }else{
                                                     $ref=count($all_product_data[$key])-6;
                            
                                                        $n=1;
                                                        $attribute=array();
                                                        while($ref-$n >=0){
                                                            $count+=1;
                                                           $ctype=ctype_digit($value[$ref-$n]);

                                                           if (!ctype_digit($value[$ref-$n])) {
                                                               array_push($attribute, $value[$ref-$n]); 
                                                           }
                                                           $n+=1; 
                                                        }
                                                        $all_attribute=implode(',',$attribute);
                                                        $this->r_wev_get_row_value($var_default,$value[$ref-$n+1],$value[$ref+2],$value[$ref+3],$value[4+$ref],$value[5+$ref],$value[$ref],$value[$ref+1],$all_attribute);
                                                    }
                                            }
                                        }
                                }if($type_intervals=='<=') {               
                                    if (count($all_product_data)>=$number) {
                                           
                                            if ($key+1<=$number){
                                                if (count($all_product_data[$key])==7) {
                                                    $this->r_wev_get_row_value($var_color1,$value[0],$value[1],$value[2],$value[3],$value[4],$value[5],$value[6]);
                                            
                                                }else{
                                                     $ref=count($all_product_data[$key])-6;
                            
                                                        $n=1;
                                                        $attribute=array();
                                                        while($ref-$n >=0){
                                                            $count+=1;
                                                           $ctype=ctype_digit($value[$ref-$n]);

                                                           if (!ctype_digit($value[$ref-$n])) {
                                                               array_push($attribute, $value[$ref-$n]); 
                                                           }
                                                           $n+=1; 
                                                        }
                                                        $all_attribute=implode(',',$attribute);
                                                        $this->r_wev_get_row_value($var_default,$value[$ref-$n+1],$value[$ref+2],$value[$ref+3],$value[4+$ref],$value[5+$ref],$value[$ref],$value[$ref+1],$all_attribute);
                                                    }
                                            }else{
                                               if (count($all_product_data[$key])==7) {
                                                    $this->r_wev_get_row_value($var_color2,$value[0],$value[1],$value[2],$value[3],$value[4],$value[5],$value[6]);
                                            
                                                }else{
                                                     $ref=count($all_product_data[$key])-6;
                                                        $n=1;
                                                        $attribute=array();
                                                        while($ref-$n >=0){
                                                            $count+=1;
                                                           $ctype=ctype_digit($value[$ref-$n]);

                                                           if (!ctype_digit($value[$ref-$n])) {
                                                               array_push($attribute, $value[$ref-$n]); 
                                                           }
                                                           $n+=1; 
                                                        }
                                                        $all_attribute=implode(',',$attribute);
                                                        $this->r_wev_get_row_value($var_default,$value[$ref-$n+1],$value[$ref+2],$value[$ref+3],$value[4+$ref],$value[5+$ref],$value[$ref],$value[$ref+1],$all_attribute);
                                                    }
                                            }
                                        }
                                }if($type_intervals=='>=') {                           
                                    if (count($all_product_data)>=$number) {
                                            $intervals=count($all_product_data)-$number;
                                            if ($intervals>=0 && $key+1>=$number) {
                                                if (count($all_product_data[$key])==7) {
                                                    $this->r_wev_get_row_value($var_color1,$value[0],$value[1],$value[2],$value[3],$value[4],$value[5],$value[6]);
                                            
                                                }else{
                                                     $ref=count($all_product_data[$key])-6;
                            
                                                        $n=1;
                                                        $attribute=array();
                                                        while($ref-$n >=0){
                                                            $count+=1;
                                                           $ctype=ctype_digit($value[$ref-$n]);

                                                           if (!ctype_digit($value[$ref-$n])) {
                                                               array_push($attribute, $value[$ref-$n]); 
                                                           }
                                                           $n+=1; 
                                                        }
                                                        $all_attribute=implode(',',$attribute);
                                                        $this->r_wev_get_row_value($var_default,$value[$ref-$n+1],$value[$ref+2],$value[$ref+3],$value[4+$ref],$value[5+$ref],$value[$ref],$value[$ref+1],$all_attribute);
                                                    }
                                                
                                            }else {
                                               
                                               if (count($all_product_data[$key])==7) {
                                                    $this->r_wev_get_row_value($var_color2,$value[0],$value[1],$value[2],$value[3],$value[4],$value[5],$value[6]);
                                            
                                                }else{
                                                     $ref=count($all_product_data[$key])-6;
                            
                                                    $n=1;
                                                    $attribute=array();
                                                    while($ref-$n >=0){
                                                        $count+=1;
                                                       $ctype=ctype_digit($value[$ref-$n]);

                                                       if (!ctype_digit($value[$ref-$n])) {
                                                           array_push($attribute, $value[$ref-$n]); 
                                                       }
                                                       $n+=1; 
                                                    }
                                                    $all_attribute=implode(',',$attribute);
                                                    $this->r_wev_get_row_value($var_default,$value[$ref-$n+1],$value[$ref+2],$value[$ref+3],$value[4+$ref],$value[5+$ref],$value[$ref],$value[$ref+1],$all_attribute);
                                                }
                                            }
                                        }
                                }
                                
                            }
                    }else {                       
                        if (count($all_product_data[$key])==7) {
                            $this->r_wev_get_row_value($var_default,$value[0],$value[1],$value[2],$value[3],$value[4],$value[5],$value[6]);
                                            
                        }else{                           
                            $ref=count($all_product_data[$key])-6;
                            
                            $n=1;
                            $attribute=array();
                            while($ref-$n >=0){
                                $count+=1;
                               $ctype=ctype_digit($value[$ref-$n]);
                              
                               if (!ctype_digit($value[$ref-$n])) {
                                   array_push($attribute, $value[$ref-$n]); 
                               }
                               $n+=1; 
                            }
                            $all_attribute=implode(',',$attribute);
                            $this->r_wev_get_row_value($var_default,$value[$ref-$n+1],$value[$ref+2],$value[$ref+3],$value[4+$ref],$value[5+$ref],$value[$ref],$value[$ref+1],$all_attribute);
                        }
                    }
                        endwhilepart:;
                    }
                ?>
            </tbody>
            <tfoot style=<?php 
                if(isset($var_footer)){echo $var_footer;}
                else{echo 'font-weight:normal;line-height:16px;background-color:#d1bad1;font-size:12px';} ?>>
                <tr>
                <?php 
                if ($compt_view%2!=0) {
                   echo "<td colspan='7'  class='visibility' style='text-align:center;' ><i>Show others products</i></td>";
            }else{
                 echo "<td colspan='7' class='view ' style='text-align:center;' ><i>Show others products</i></td>";

                }
                ?>
                    
                </tr>
            </tfoot>
        </table>
        </div>
        <div class='wpc-col-1-1'>
            <?php if ($compt_view==1 or $compt_view==4 ) {echo $this->settings["title"];}?>
         </div>
       <?php   out:;    
    }
    

    
    

function r_wev_get_all_group(){
    $all_group=count($this->settings['rules']);
     $get_first_part=$this->r_wev_group_analyse_part(0);
    if ($get_first_part!=false) {
            if ($this->settings["relationship"]=='OR') {
                return $get_first_part;
            }else{
                     //Insert others part
                for ($id=1; $id<$all_group ; $id++) {
                         $get_one_part=$this->r_wev_group_analyse_part($id);
                     if ($this->settings["relationship"]=='AND' &&  $get_one_part!=false) {
                            foreach ($get_one_part as $key => $value) {
                                if (!in_array($value, $get_first_part)){
                                    array_push($get_first_part, $value);
                                }
                            }
                     }
                }
            }    
            
    }
    return $get_first_part;
}

function r_wev_public_view(){
    $all_valid_id=$this->r_wev_get_all_group();
    foreach ($all_valid_id as $key => $value){
         $product_data=$this->r_wev_get_product_value($value);
         array_push($product_data,$this->r_wev_get_category_name($value));
         array_push($this->_table_lign, $product_data);
    }
    
 $this->r_wev_get_view_table($this->_table_lign);

}

function r_wev_group_analyse_part($number_group){
    if ($this->r_wev_is_applicable()) {
        $get_tri_condition=array();
        $get_all_condition=array();
        $align_value_for_condition=array();
        $align_value_for_condition4=array();
        $align_value_for_condition3=array();
        $align_value_for_condition2=array();
        $align_value_for_condition1=array();
        $tri_products_part=array();
        $tri_all_products_part=array();
        $combine_tri_array=array();
        $array_view=array();
        $condition_ass=array();
        $new_array_view=array();
        $new_array_view_name_product=array();
        $new_array_view_id=array();
        $new_array_view_category=array();
        $new_array_view_customer_role=array();
        $new_array_view_customer=array();
        $new_array_view_author=array();
        $new_array_view_id=array();
        $target=$this->settings["rules"][$number_group];
        
             foreach ($target as $key => $value) {
                    $count_condition+=1;
                        if ($value['condition']=='customer-role'|| $value['condition']=='customer' || $value['condition']=='author') {
                            $count_condition_degree1+=1;
                            array_push($get_all_condition, $value);
                            foreach ($get_all_condition as $key => $value) {
                                if ($value['condition']=='customer-role') {
                                    $count_degree1_element+=1;
                                }
                                elseif ($value['condition']=='customer') {
                                    $count_degree1_element+=1;
                                }elseif ($value['condition']=='author') {
                                    $count_degree1_element+=1;
                                }
                            }
                        }elseif ($value['condition']=='category') {
                             $count_condition_degree2+=1;
                             array_push($get_all_condition, $value);

                        }elseif ($value['condition']=='postID') {
                             $count_condition_degree3+=1;
                             array_push($get_all_condition, $value);

                        }
                        else{
                            $count_condition_degree4+=1;
                            array_push($get_all_condition, $value);
                        }
                   

            }
            $count_nbr_degree1=0;
            foreach ($get_all_condition as $key => $value) {
                if(in_array('customer-role', $value) || in_array('customer', $value) || in_array('author', $value)){
                        $count_nbr_degree1+=1;
                        array_push($get_tri_condition,$value);
                        $exhange_var=$get_all_condition[$count_nbr_degree1-1];
                        $get_all_condition[$count_nbr_degree1-1]=$value;
                        $get_all_condition[$key]=$exhange_var;
                }
            }
            
             for ($i=$count_nbr_degree1; $i<count($get_all_condition); $i++) {
                    if ('category'==$get_all_condition[$i]['condition']) {
                        $count_nbr_degree2+=1;
                        array_push($get_tri_condition,$get_all_condition[$i]);
                        $exhange_var=$get_all_condition[$count_nbr_degree1+$count_nbr_degree2-1];
                        $get_all_condition[$count_nbr_degree1+$count_nbr_degree2-1]=$get_all_condition[$i];
                        $get_all_condition[$i]=$exhange_var;
                    }
             }
             $key=$count_nbr_degree1+$count_nbr_degree2;
             for ($u=$key; $u <count($get_all_condition) ; $u++) { 
                if ('postID'==$get_all_condition[$u]['condition']) {
                     $count_nbr_degree3+=1;
                     array_push($get_tri_condition,$get_all_condition[$u]);
                     $exhange_var=$get_all_condition[$count_nbr_degree1+$count_nbr_degree2+$count_nbr_degree3-1];
                     $get_all_condition[$count_nbr_degree1+$count_nbr_degree2+$count_nbr_degree3-1]=$get_all_condition[$u];
                     $get_all_condition[$u]=$exhange_var;
                }
             }
             $last_key=$key+$count_nbr_degree3;
             for ($v=$last_key; $v<count($get_all_condition) ; $v++) { 
                  if ('name_products'==$get_all_condition[$v]['condition']) {
                     $count_nbr_degree4+=1;
                     array_push($get_tri_condition,$get_all_condition[$v]);
                     $exhange_var=$get_all_condition[$count_nbr_degree1+$count_nbr_degree2+$count_nbr_degree3+$count_nbr_degree4-1];
                     $get_all_condition[$count_nbr_degree1+$count_nbr_degree2+$count_nbr_degree3+$count_nbr_degree4-1]=$get_all_condition[$v];
                     $get_all_condition[$v]=$exhange_var;
                 }
             }
              $count_ass_cond_name_products=1;
              $count_ass_cond_postID=1;
              $count_ass_cond_category=1;
              $count_ass_cond_customer=1;
              $count_ass_cond_customer_role=1;
              $count_ass_cond_author=1;
              $tri_products_part=array();
             for ($w=count($get_all_condition);$w>count($get_all_condition)-$count_nbr_degree4; $w--) {
                    $align_value_for_condition1=array(); 
                    if ($get_tri_condition[$w-1]['condition']=='name_products') {
                        if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                                $count_ass_cond_name_products+=1;
                                array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                              array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_name_products);
                        }
                        array_push($align_value_for_condition1, $get_tri_condition[$w-1]['operator']);
                        foreach ($get_tri_condition[$w-1]['value'] as $key => $value) {
                           array_push($align_value_for_condition1, $value);
                        }
                        array_push($tri_products_part, $align_value_for_condition1);

                    }
                    if($get_tri_condition[$w-1]['condition']=='postID') {
                       if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                                $count_ass_cond_postID+=1;
                                array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                              array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_postID);
                        }
                        array_push($align_value_for_condition1, $get_tri_condition[$w-1]['operator']);
                        array_push($align_value_for_condition1,$get_tri_condition[$w-1]['value']);
                        array_push($tri_products_part, $align_value_for_condition1);
                    }
                    if($get_tri_condition[$w-1]['condition']=='category') {
                        if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                                $count_ass_cond_category+=1;
                                array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                              array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_category);
                        }
                        array_push($align_value_for_condition1, $get_tri_condition[$w-1]['operator']);
                        foreach ($get_tri_condition[$w-1]['value'] as $key => $value) {
                           array_push($align_value_for_condition1, $value);
                        }
                         array_push($tri_products_part, $align_value_for_condition1);
                    }
                    if($get_tri_condition[$w-1]['condition']=='customer-role') {
                        if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                                $count_ass_cond_customer_role+=1;
                                array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                              array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_customer_role);
                        }
                        array_push($align_value_for_condition1, $get_tri_condition[$w-1]['operator']);
                        foreach ($get_tri_condition[$w-1]['value'] as $key => $value) {
                           array_push($align_value_for_condition1, $value);
                        }
                         array_push($tri_products_part, $align_value_for_condition1);
                    }
                    if($get_tri_condition[$w-1]['condition']=='author') {
                        if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                            $count_ass_cond_author+=1;
                                array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                              array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_author);
                        }
                        array_push($align_value_for_condition1, $get_tri_condition[$w-1]['operator']);
                        foreach ($get_tri_condition[$w-1]['value'] as $key => $value) {
                           array_push($align_value_for_condition1, $value);
                        }
                         array_push($tri_products_part, $align_value_for_condition1);
                    }
                    if($get_tri_condition[$w-1]['condition']=='customer'){
                       if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                                $count_ass_cond_customer+=1;
                                array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                              array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_customer);
                        }
                        array_push($align_value_for_condition1, $get_tri_condition[$w-1]['operator']);
                        foreach ($get_tri_condition[$w-1]['value'] as $key => $value) {
                           array_push($align_value_for_condition1, $value);
                        }
                         array_push($tri_products_part, $align_value_for_condition1);
                    }
                    
             }
             if (!empty($tri_products_part) && isset($count_nbr_degree4) && $count_nbr_degree4!=0) {
                    array_push($tri_all_products_part, $tri_products_part); 
                }
                $tri_products_part=array();
                for ($w=count($get_all_condition)-$count_nbr_degree4;$w>count($get_all_condition)-$count_nbr_degree4-$count_nbr_degree3; $w--) {
                       $align_value_for_condition2=array();
                    if ($get_tri_condition[$w-1]['condition']=='name_products') {
                        if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                                $count_ass_cond_name_products+=1;
                                array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                              array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_name_products);
                        }
                        array_push($align_value_for_condition2, $get_tri_condition[$w-1]['operator']);
                        foreach ($get_tri_condition[$w-1]['value'] as $key => $value) {
                           array_push($align_value_for_condition2, $value);
                        }
                         array_push($tri_products_part, $align_value_for_condition2);
                    }
                    if($get_tri_condition[$w-1]['condition']=='postID') { 
                        if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                                $count_ass_cond_category+=1;
                                array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                              array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_postID);
                        }
                        array_push($align_value_for_condition2, $get_tri_condition[$w-1]['operator']);
                        array_push($align_value_for_condition2, $get_tri_condition[$w-1]['value']);
                        array_push($tri_products_part, $align_value_for_condition2);   
                    }
                    if($get_tri_condition[$w-1]['condition']=='category') {
                        if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                            $count_ass_cond_category+=1;
                            array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                            array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_category);
                        }
                        array_push($align_value_for_condition2, $get_tri_condition[$w-1]['operator']);
                        foreach ($get_tri_condition[$w-1]['value'] as $key => $value) {
                           array_push($align_value_for_condition2, $value);
                        }
                        array_push($tri_products_part, $align_value_for_condition2);
                    }
                    if($get_tri_condition[$w-1]['condition']=='customer-role') {
                        if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                                $count_ass_cond_customer_role+=1;
                                array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                              array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_customer_role);
                        }
                        array_push($align_value_for_condition2, $get_tri_condition[$w-1]['operator']);
                        foreach ($get_tri_condition[$w-1]['value'] as $key => $value) {
                           array_push($align_value_for_condition2, $value);
                        }
                         array_push($tri_products_part, $align_value_for_condition2);
                    }
                    if($get_tri_condition[$w-1]['condition']=='author') {
                        if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                            $count_ass_cond_author+=1;
                            array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                            array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_author);
                        }
                        array_push($align_value_for_condition2, $get_tri_condition[$w-1]['operator']);
                        foreach ($get_tri_condition[$w-1]['value'] as $key => $value) {
                           array_push($align_value_for_condition2, $value);
                        }
                         array_push($tri_products_part, $align_value_for_condition2);
                    }
                    if($get_tri_condition[$w-1]['condition']=='customer'){
                       if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                            $count_ass_cond_customer+=1;
                            array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                            array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_customer);
                        }
                        array_push($align_value_for_condition2, $get_tri_condition[$w-1]['operator']);
                        foreach ($get_tri_condition[$w-1]['value'] as $key => $value) {
                           array_push($align_value_for_condition2, $value);
                        }
                        array_push($tri_products_part, $align_value_for_condition2);
                    }
                 
                }
                    
                   if (!empty($tri_products_part) && isset($count_nbr_degree3) && $count_nbr_degree3!=0) {
                    array_push($tri_all_products_part, $tri_products_part); 
                }
    
                $tri_products_part=array();

                for ($w=count($get_all_condition)-$count_nbr_degree4-$count_nbr_degree3;$w>count($get_all_condition)-$count_nbr_degree4-$count_nbr_degree3-$count_nbr_degree2; $w--) {
                       $align_value_for_condition3=array();
                    if ($get_tri_condition[$w-1]['condition']=='name_products') {
                        if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                                $count_ass_cond_name_products+=1;
                                array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                              array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_name_products);
                        }
                        array_push($align_value_for_condition3, $get_tri_condition[$w-1]['operator']);
                        foreach ($get_tri_condition[$w-1]['value'] as $key => $value) {
                           array_push($align_value_for_condition3, $value);
                        }
                         array_push($tri_products_part, $align_value_for_condition3);
                    }
                    if($get_tri_condition[$w-1]['condition']=='postID') {
                        if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {//But as a matter a fact isn't possible
                                $count_ass_cond_postID+=1;
                                array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                              array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_postID);
                        }
                        array_push($align_value_for_condition3, $get_tri_condition[$w-1]['operator']);
                        foreach ($get_tri_condition[$w-1]['value'] as $key => $value) {
                           array_push($align_value_for_condition3, $value);
                        }
                         array_push($tri_products_part, $align_value_for_condition3);
                    }
                    if($get_tri_condition[$w-1]['condition']=='category') {
                        if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                                $count_ass_cond_category+=1;
                                array_push($condition_ass,$get_tri_condition[$w-1]['condition']);       
                        }else{
                              array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_category);
                        }
                        array_push($align_value_for_condition3, $get_tri_condition[$w-1]['operator']);
                        foreach ($get_tri_condition[$w-1]['value'] as $key => $value) {
                           array_push($align_value_for_condition3, $value);
                        }
                        array_push($tri_products_part, $align_value_for_condition3);

                    }
                    if($get_tri_condition[$w-1]['condition']=='customer-role') {
                        if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                                $count_ass_cond_customer_role+=1;
                                array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                              array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_customer_role);
                        }
                        array_push($align_value_for_condition3, $get_tri_condition[$w-1]['operator']);
                        foreach ($get_tri_condition[$w-1]['value'] as $key => $value) {
                           array_push($align_value_for_condition3, $value);
                        }
                         array_push($tri_products_part, $align_value_for_condition3);
                    }
                    if($get_tri_condition[$w-1]['condition']=='author') {
                       if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                                $count_ass_cond_author+=1;
                                array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                              array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_author);
                        }
                        array_push($align_value_for_condition3, $get_tri_condition[$w-1]['operator']);
                        foreach ($get_tri_condition[$w-1]['value'] as $key => $value) {
                           array_push($align_value_for_condition3, $value);
                        }
                         array_push($tri_products_part, $align_value_for_condition3);
                    }
                    if($get_tri_condition[$w-1]['condition']=='customer'){
                        if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                                $count_ass_cond_customer+=1;
                                array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                              array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_customer);
                        }
                        array_push($align_value_for_condition3, $get_tri_condition[$w-1]['operator']);
                        foreach ($get_tri_condition[$w-1]['value'] as $key => $value) {
                           array_push($align_value_for_condition3, $value);
                        }
                        array_push($tri_products_part, $align_value_for_condition3);
                    }
                   
                }
                if (!empty($tri_products_part) && isset($count_nbr_degree2) && $count_nbr_degree2!=0) {
                    array_push($tri_all_products_part, $tri_products_part); 
                }
                     $tri_products_part=array();
                for ($w=count($get_all_condition)-$count_nbr_degree4-$count_nbr_degree3-$count_nbr_degree2;$w>count($get_all_condition)-$count_nbr_degree4-$count_nbr_degree3-$count_nbr_degree2-$count_nbr_degree1; $w--) { 
                       $align_value_for_condition4=array();
                    if ($get_tri_condition[$w-1]['condition']=='name_products') {
                        if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                                $count_ass_cond_name_products+=1;
                                array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                              array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_name_products);
                        }
                        array_push($align_value_for_condition4, $get_tri_condition[$w-1]['operator']);
                        foreach ($get_tri_condition[$w-1]['value'] as $key => $value) {
                           array_push($align_value_for_condition4, $value);
                        }
                         array_push($tri_products_part, $align_value_for_condition4);
                    }
                    if($get_tri_condition[$w-1]['condition']=='postID') {
                        if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                                $count_ass_cond_postID+=1;
                                array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                              array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_postID);
                        }
                        array_push($align_value_for_condition4, $get_tri_condition[$w-1]['operator']);
                        array_push($align_value_for_condition4,$get_tri_condition[$w-1]['value']);
        
                         array_push($tri_products_part, $align_value_for_condition4);
                    }
                    if($get_tri_condition[$w-1]['condition']=='category') {
                        if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                                $count_ass_cond_category+=1;
                                array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                              array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_category);
                        }
                        array_push($align_value_for_condition4, $get_tri_condition[$w-1]['operator']);
                        foreach ($get_tri_condition[$w-1]['value'] as $key => $value) {
                           array_push($align_value_for_condition4, $value);
                        }
                         array_push($tri_products_part, $align_value_for_condition4);

                    }
                    if($get_tri_condition[$w-1]['condition']=='customer-role') {
                        if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                                $count_ass_cond_customer_role+=1;
                                array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                              array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_customer_role);
                        }
                        array_push($align_value_for_condition4, $get_tri_condition[$w-1]['operator']);
                        foreach ($get_tri_condition[$w-1]['value'] as $key => $value) {
                           array_push($align_value_for_condition4, $value);
                        }
                         array_push($tri_products_part, $align_value_for_condition4);
                    }
                    if($get_tri_condition[$w-1]['condition']=='author') {
                        if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                                $count_ass_cond_author+=1;
                                array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                              array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_author);
                        }
                        array_push($align_value_for_condition4, $get_tri_condition[$w-1]['operator']);
                        foreach ($get_tri_condition[$w-1]['value'] as $key => $value) {
                           array_push($align_value_for_condition4, $value);
                        }
                         array_push($tri_products_part, $align_value_for_condition4);
                    }
                    if($get_tri_condition[$w-1]['condition']=='customer'){
                        if (!in_array($get_tri_condition[$w-1]['condition'], $condition_ass)) {
                                $count_ass_cond_customer+=1;
                                array_push($condition_ass,$get_tri_condition[$w-1]['condition']);
                        }else{
                              array_push($condition_ass,$get_tri_condition[$w-1]['condition'].$count_ass_cond_customer);
                        }
                        array_push($align_value_for_condition4, $get_tri_condition[$w-1]['operator']);
                        foreach ($get_tri_condition[$w-1]['value'] as $key => $value) {
                           array_push($align_value_for_condition4, $value);
                        }
                         array_push($tri_products_part, $align_value_for_condition4);
                    }   
                }
                if (!empty($tri_products_part) && isset($count_nbr_degree1) && $count_nbr_degree1!=0) {
                    array_push($tri_all_products_part, $tri_products_part); 
                }


            $combine_tri_array=array();
            foreach ($condition_ass as $key => $value) {
                $combine_tri_array[$value]=$tri_all_products_part[$key];
            }
           
            if (isset($combine_tri_array['name_products'])) {
              
                  $count_part=count($combine_tri_array['name_products']);
                  $count_array=count($combine_tri_array['name_products'][0])-1;
                  $new_name_product=array();
                  $new_name_product_wt_point=array();
                if ($combine_tri_array['name_products'][0][0]=='IN') {
                    foreach ($combine_tri_array['name_products'][0] as $key => $value) {
                        if ($key<=$count_array-1) {
                            array_push($new_array_view, $combine_tri_array['name_products'][0][$key+1]);
                        }
                    }
                }elseif ($combine_tri_array['name_products'][0][0]=='NOT IN'){
                       $all_product=$this->r_wev_get_existing_product_name();
                            foreach ($combine_tri_array['name_products'][0] as $key => $value){
                                if ($key!=0) {
                                     array_push($new_name_product, substr_replace($value,'',0,2));   
                                }
                            }
                            foreach ($all_product as $key2 => $value2){  
                                   if (!in_array($value2, $new_name_product)) {
                                        array_push($new_name_product_wt_point, $value2);     
                                   }    
                            }  
                }
                $new_name_product=array();
                    for ($x=1; $x<$count_array; $x++) { 
                         $count_array=count($combine_tri_array['name_products'][$x])-1;
                         if ($combine_tri_array['name_products'][$x][0]=='IN') {   
                            foreach ($combine_tri_array['name_products'][$x] as $key => $value) {
                                if (!in_array($value, $new_array_view)) {
                                        array_push($new_array_view, $value);
                                }
                            }
                        }elseif ($combine_tri_array['name_products'][$x][0]=='NOT IN') {
                            foreach ($combine_tri_array['name_products'][$x] as $key => $value) {
                                if ($key!=0) {
                                    array_push($new_name_product, $value);
                                }
                                   
                             }    
                            
                            foreach ($all_product as $key2 => $value2) {
                                   if (!in_array($value2,$new_name_product)) {
                                        array_push($array_view, $value2);
                                   }
                            }
                        }
                    }

                if (!empty($array_view) && !empty($new_name_product_wt_point)) {
                    $new_name_product_wt_point=array_merge($array_view,$new_name_product_wt_point);
                }elseif (empty($new_name_product_wt_point) && !empty($array_view)) {
                    $new_name_product_wt_point=$array_view;
                }elseif (!empty($new_name_product_wt_point)) {
                    $new_array_view=$new_name_product_wt_point;                    
                }
                elseif(!empty($new_array_view)) {
                    foreach ($new_array_view as $key => $value) {
                        $new_array_view[$key]=substr_replace($value,'',0,2);
                    }
                    if (!empty($new_name_product_wt_point)) {
                        foreach ($new_array_view as $key => $value) {
                            if (!in_array($value,$new_name_product_wt_point)) {
                                array_push($new_array_view, $value);
                            }
                        }
                    }
                }
               
                $all_id=$this->r_wev_get_products_id();
                foreach ($all_id as $key => $value) {
                        $products=wc_get_product($value);
                        if (in_array($products->post->post_name,$new_array_view)) {
                               array_push($new_array_view_name_product, $products->post->ID); 
                        }
                }
            }
                if (isset($combine_tri_array['postID'])) {
                    switch($combine_tri_array['postID'][0][0]) {
                                case '=':
                                        
                                        $all_product_id=$this->r_wev_get_products_id();
                                        foreach($all_product_id as $key => $val) {
                                            //$product = wc_get_product($val);
                                            if($val == $combine_tri_array['postID'][0][1] ){
                                                array_push($new_array_view_id, $val);
                                            }
                                        } 
                                break;
                                case '<':
                                        
                                        $all_product_id=$this->r_wev_get_products_id();
                                        foreach($all_product_id as $key => $val) {
                                            //$product = wc_get_product($val);
                                            if($val < $combine_tri_array['postID'][0][1] ){
                                                array_push($new_array_view_id, $val);
                                            }
                                        } 
                                break;
                                case '>':
                                    $all_product_id=$this->r_wev_get_products_id();
                                        foreach($all_product_id as $key => $val) {
                                            //$product = wc_get_product($val);
                                            if($val > $combine_tri_array['postID'][0][1] ){
                                                array_push($new_array_view_id, $val);
                                            }
                                        } 
                                break;
                                case '<=':
                                    $all_product_id=$this->r_wev_get_products_id();
                                        foreach($all_product_id as $key => $val) {
                                            //$product = wc_get_product($val);
                                            if($val <= $combine_tri_array['postID'][0][1] ){
                                                array_push($new_array_view_id, $val);
                                            }
                                        }
                                break;
                                case '>=':
                                    $all_product_id=$this->r_wev_get_products_id();
                                        foreach($all_product_id as $key => $val) {
                                            //$product = wc_get_product($val);
                                            if($val >= $combine_tri_array['postID'][0][1] ){
                                                array_push($new_array_view_id, $val);
                                            }
                                        } 
                                break;         

                }
            }
            if (isset($combine_tri_array['category'])) {
                
                   if ($combine_tri_array['category'][0][0]=='IN' && substr_replace($combine_tri_array['category'][0][1],'',0,2)=='simple') {
                       $all_product_id=$this->r_wev_get_products_id();
                       foreach ($all_product_id as $key => $value) {
                           $real_value=$this->r_wev_product_type($value);
                           if ($real_value=='simple') {
                               array_push($new_array_view_category, $value);
                           }
                       }
                   }elseif ($combine_tri_array['category'][0][0]=='IN' && substr_replace($combine_tri_array['category'][0][1],'',0,2)=='variable') {
                       $all_product_id=$this->r_wev_get_products_id();
                       foreach ($all_product_id as $key => $value) {
                           $real_value=$this->r_wev_product_type($value);
                           if ($real_value=='variable') {
                               array_push($new_array_view_category, $value);
                           }
                       }                  
                   } elseif ($combine_tri_array['category'][0][0]=='NOT IN' && substr_replace($combine_tri_array['category'][0][1],'',0,2)=='simple') {
                       $all_product_id=$this->r_wev_get_products_id();
                       foreach ($all_product_id as $key => $value) {
                           $real_value=$this->r_wev_product_type($value);
                           if ($real_value=='variable') {
                               array_push($new_array_view_category, $value);
                           }
                       }
                   }elseif ($combine_tri_array['category'][0][0]=='NOT IN' && substr_replace($combine_tri_array['category'][0][1],'',0,2)=='variable') {
                       $all_product_id=$this->r_wev_get_products_id();
                       foreach ($all_product_id as $key => $value) {
                           $real_value=$this->r_wev_product_type($value);
                           if ($real_value=='simple') {
                               array_push($new_array_view_category, $value);
                           }
                       }                 
                   }

                   if (count($combine_tri_array['category'])>1) {
                        for ($q=1; $q<=count($combine_tri_array['category']) ; $q++) { 
                           if ($combine_tri_array['category'][0][1]!=$combine_tri_array['category'][$q][1]){
                                $new_array_view_category=$this->r_wev_get_products_id();
                                goto end_part;
                           }
                       } end_part:;
                   }
                   

                                     
            }
            if (isset($combine_tri_array['customer-role'])) {
                if ($combine_tri_array['customer-role'][0][0]=='IN') {
                      $all_product_id=$this->r_wev_get_products_id();
                        foreach ($all_product_id as $key => $value) {
                                $product = wc_get_product($value);
                                $author_id=$product->post->post_author;
                                $rule=$this->r_wev_get_role_about($author_id);
                                if ($rule==$combine_tri_array['customer-role'][0][1]) {
                                    array_push($new_array_view_customer_role, $value);
                                }
                        }
                    
                }elseif ($combine_tri_array['customer-role'][0][0]=='NOT IN') {
                     $all_product_id=$this->r_wev_get_products_id();
                        foreach ($all_product_id as $key => $value) {
                                $product = wc_get_product($value);
                                $author_id=$product->post->post_author;
                                $rule=$this->r_wev_get_role_about($author_id);
                                if ($rule!=$combine_tri_array['customer-role'][0][1]) {
                                    array_push($new_array_view_customer_role, $value);
                                }
                        }
                }
                for ($p=1; $p<count($combine_tri_array['customer-role']) ; $p++) { 
                            if ($combine_tri_array['customer-role'][$p][0]=='IN') {
                              $all_product_id=$this->r_wev_get_products_id();
                                foreach ($all_product_id as $key => $value){
                                        $product = wc_get_product($value);
                                        $author_id=$product->post->post_author;
                                        $rule=$this->r_wev_get_role_about($author_id);
                                        if ($rule==$combine_tri_array['customer-role'][$p][1]) {
                                            if (!in_array($value,$new_array_view_customer_role)) {
                                                array_push($new_array_view_customer_role, $value);
                                            }
                                           
                                        }
                                }

                            }elseif ($combine_tri_array['customer-role'][$p][0]=='NOT IN') {
                                     $all_product_id=$this->r_wev_get_products_id();
                                        foreach ($all_product_id as $key => $value){
                                                $product = wc_get_product($value);
                                                $author_id=$product->post->post_author;
                                                $rule=$this->r_wev_get_role_about($author_id);
                                                if ($rule!=$combine_tri_array['customer-role'][$p][1]) {
                                                    if (!in_array($value,$new_array_view_customer_role)) {
                                                        array_push($new_array_view_customer_role, $value);
                                                    }
                                                   
                                                }
                                        }
                            }
                }
            }
            if (isset($combine_tri_array['customer'])) {

                    if ($combine_tri_array['customer'][0][0]=='IN') {
                        $all_product_id=$this->r_wev_get_products_id();
                        foreach ($all_product_id as $key => $value) {
                             $product = wc_get_product($value);
                              $author_id=$product->post->post_author;
                              if ($author_id==$combine_tri_array['customer'][0][1]) {
                                  array_push($new_array_view_customer, $value);
                              }
                        }
                    }elseif ($combine_tri_array['customer'][0][0]=='NOT IN') {
                             $all_product_id=$this->r_wev_get_products_id();
                        foreach ($all_product_id as $key => $value) {
                             $product = wc_get_product($value);
                              $author_id=$product->post->post_author;
                              if ($author_id!=$combine_tri_array['customer'][0][1]) {
                                  array_push($new_array_view_customer, $value);
                              }
                        }
                    }
              
                 for ($y=1; $y<count($combine_tri_array['customer']) ; $y++){
                            if ($combine_tri_array['customer'][$y][0]=='IN'){
                              $all_product_id=$this->r_wev_get_products_id();
                                foreach ($all_product_id as $key => $value){
                                        $product = wc_get_product($value);
                                        $author_id=$product->post->post_author;
                                        if ($author_id==$combine_tri_array['customer'][$y][1]) {
                                             if (!in_array($value,$new_array_view_customer)) {
                                                 array_push($new_array_view_customer, $value);
                                            }
                                        }
                                         
                                }

                            }elseif ($combine_tri_array['customer'][$y][0]=='NOT IN') {
                                      $all_product_id=$this->r_wev_get_products_id();
                                        foreach ($all_product_id as $key => $value){
                                                $product = wc_get_product($value);
                                                $author_id=$product->post->post_author;
                                                if ($author_id!=$combine_tri_array['customer'][$y][1]) {
                                                     if (!in_array($value,$new_array_view_customer)) {
                                                         array_push($new_array_view_customer, $value);
                                                    }
                                                }       
                                        }
                            }
                }

            }

            if (isset($combine_tri_array['author'])) {

                    if ($combine_tri_array['author'][0][0]=='IN') {
                        $all_product_id=$this->r_wev_get_products_id();
                        foreach ($all_product_id as $key => $value) {
                             $product = wc_get_product($value);
                              $author_id=$product->post->post_author;
                              if ($author_id==$combine_tri_array['author'][0][1]) {
                                  array_push($new_array_view_author, $value);
                              }
                        }
                    }elseif ($combine_tri_array['author'][0][0]=='NOT IN') {
                             $all_product_id=$this->r_wev_get_products_id();
                        foreach ($all_product_id as $key => $value) {
                             $product = wc_get_product($value);
                              $author_id=$product->post->post_author;
                              if ($author_id!=$combine_tri_array['author'][0][1]) {
                                  array_push($new_array_view_author, $value);
                              }
                        }
                    }
                 for ($y=1; $y<count($combine_tri_array['author']) ; $y++){
                            if ($combine_tri_array['author'][$y][0]=='IN'){
                              $all_product_id=$this->r_wev_get_products_id();
                                foreach ($all_product_id as $key => $value){
                                        $product = wc_get_product($value);
                                        $author_id=$product->post->post_author;
                                        if ($author_id==$combine_tri_array['author'][$y][1]){
                                             if (!in_array($value,$new_array_view_author)) {
                                                 array_push($new_array_view_author, $value);
                                            }
                                        }
                                         
                                }

                            }elseif ($combine_tri_array['author'][$y][0]=='NOT IN') {
                                      $all_product_id=$this->r_wev_get_products_id();
                                        foreach ($all_product_id as $key => $value){
                                                $product = wc_get_product($value);
                                                $author_id=$product->post->post_author;
                                                if ($author_id!=$combine_tri_array['author'][$y][1]) {
                                                     if (!in_array($value,$new_array_view_author)) {
                                                         array_push($new_array_view_author, $value);
                                                    }
                                                }       
                                        }
                            }
                }

            }
            $new_pivot=array();
            $count=0;
          for ($nbr=1; $nbr<=count($combine_tri_array) ; $nbr++) { 
                if (isset($new_array_view_name_product) && !empty($new_array_view_name_product)) {
                        $pivot=$new_array_view_name_product;
                        if (isset($new_array_view_id) && !empty($new_array_view_id)) {
                            foreach ($pivot as $key => $value) {
                                if (in_array($value, $new_array_view_id) && !in_array($value, $new_pivot)) {
                                    $count+=1;
                                    array_push($new_pivot, $value);
                                }
                            }
                        }
                        if (isset($new_array_view_category) && !empty($new_array_view_category)) {
                                if (isset($new_array_view_id) && !empty($new_array_view_id)) {
                                    if ($count!=0) {
                                            $pivot=$new_pivot;
                                            $count=0;
                                            foreach ($pivot as $key => $value) {
                                                if (in_array($value, $new_array_view_category)&& !in_array($value, $new_pivot)) {
                                                    $count+=1;
                                                    array_push($new_pivot, $value);
                                                }
                                            }
                                    }else{
                                        return $new_pivot=array();
                                    }
                                    
                                }else{
                                    foreach ($pivot as $key => $value) {
                                        if (in_array($value, $new_array_view_category)&& !in_array($value, $new_pivot)) {
                                            $count+=1;
                                            array_push($new_pivot, $value);
                                        }
                                    }
                                }
                        }
                        if (isset($new_array_view_customer_role) && !empty($new_array_view_customer_role)) {
                                if (isset($new_array_view_category) && !empty($new_array_view_category) || isset($new_array_view_id) && !empty($new_array_view_id)) {
                                    if ($count!=0) {
                                            $pivot=$new_pivot;
                                            $count=0;
                                            foreach ($pivot as $key => $value) {
                                                if (in_array($value, $new_array_view_customer_role) && !in_array($value, $new_pivot)) {
                                                        $count+=1;
                                                        array_push($new_pivot, $value);
                                                }
                                            }
                                    }else{
                                        return $new_pivot=array();
                                    }
                                    
                                }else {
                                    foreach ($pivot as $key => $value) {
                                        if (in_array($value, $new_array_view_customer_role) && !in_array($value, $new_pivot)) {
                                                $count+=1;
                                                array_push($new_pivot, $value);
                                        }
                                    }
                                }
                                
                        }
                        if (isset($new_array_view_customer) && !empty($new_array_view_customer)) {
                                if (isset($new_array_view_customer_role) && !empty($new_array_view_customer_role)|| isset($new_array_view_category)&& !empty($new_array_view_category) || isset($new_array_view_id) && !empty($new_array_view_id)) {
                                    if ($count!=0) {
                                        $pivot=$new_pivot;
                                        $count=0;
                                        foreach ($pivot as $key => $value) {
                                            if (in_array($value, $new_array_view_customer) && !in_array($value, $new_pivot)) {
                                                $count+=1;
                                                array_push($new_pivot, $value);
                                            }
                                        }
                                    }else{
                                        return $new_pivot=array();
                                    }
                                }else{
                                    foreach ($pivot as $key => $value) {
                                            if (in_array($value, $new_array_view_customer) && !in_array($value, $new_pivot)) {
                                                array_push($new_pivot, $value);
                                            }
                                    }
                                }
                           
                        }
                        if (isset($new_array_view_author) && !empty($new_array_view_author)) {
                                if (isset($new_array_view_customer) && !empty($new_array_view_customer)|| isset($new_array_view_category)&& !empty($new_array_view_category) || isset($new_array_view_id) && !empty($new_array_view_id)) {
                                    if ($count!=0) {
                                        $pivot=$new_pivot;
                                        $count=0;
                                        foreach ($pivot as $key => $value) {
                                            if (in_array($value, $new_array_view_author) && !in_array($value, $new_pivot)) {
                                                $count+=1;
                                                array_push($new_pivot, $value);
                                            }
                                        }
                                    }else{
                                        return $new_pivot=array();
                                    }
                                }else{
                                    foreach ($pivot as $key => $value) {
                                            if (in_array($value, $new_array_view_author) && !in_array($value, $new_pivot)) {
                                                array_push($new_pivot, $value);
                                            }
                                    }
                                }
                           
                        }
                        if (empty($new_pivot)) {
                            $new_pivot=$new_array_view_name_product;
                        }
                }elseif (isset($new_array_view_id) && !empty($new_array_view_id)){
                        $pivot=$new_array_view_id;
                        if (isset($new_array_view_category) && !empty($new_array_view_category)) {
                            foreach ($pivot as $key => $value) {
                                if (in_array($value, $new_array_view_category) && !in_array($value, $new_pivot)) {
                                    $count+=1;
                                    array_push($new_pivot, $value);
                                }
                            }
                        }
                        if (isset($new_array_view_customer_role) && !empty($new_array_view_customer_role)) {
                                if (isset($new_array_view_category) && !empty($new_array_view_category) || isset($new_array_view_id) && !empty($new_array_view_id)) {
                                    if ($count!=0) {
                                            $pivot=$new_pivot;
                                            $count=0;
                                            foreach ($pivot as $key => $value) {
                                                if (in_array($value, $new_array_view_customer_role) && !in_array($value, $new_pivot)) {
                                                        $count+=1;
                                                        array_push($new_pivot, $value);
                                                }
                                            }
                                    }else{
                                        return $new_pivot=array();
                                    }
                                    
                                }else {
                                    foreach ($pivot as $key => $value) {
                                        if (in_array($value, $new_array_view_customer_role) && !in_array($value, $new_pivot)) {
                                                $count+=1;
                                                array_push($new_pivot, $value);
                                        }
                                    }
                                }
                                
                        }
                        if (isset($new_array_view_customer) && !empty($new_array_view_customer)) {
                                if (isset($new_array_view_customer_role) && !empty($new_array_view_customer_role)|| isset($new_array_view_category)&& !empty($new_array_view_category) || isset($new_array_view_id) && !empty($new_array_view_id)) {
                                    if ($count!=0) {
                                        $pivot=$new_pivot;
                                        $count=0;
                                        foreach ($pivot as $key => $value) {
                                            if (in_array($value, $new_array_view_customer) && !in_array($value, $new_pivot)) {
                                                $count+=1;
                                                array_push($new_pivot, $value);
                                            }
                                        }
                                    }else{
                                        return $new_pivot=array();
                                    }
                                }else{
                                    foreach ($pivot as $key => $value) {
                                            if (in_array($value, $new_array_view_customer) && !in_array($value, $new_pivot)) {
                                                array_push($new_pivot, $value);
                                            }
                                    }
                                }
                           
                        }
                        if (isset($new_array_view_author) && !empty($new_array_view_author)) {
                                if (isset($new_array_view_customer) && !empty($new_array_view_customer)|| isset($new_array_view_category)&& !empty($new_array_view_category) || isset($new_array_view_id) && !empty($new_array_view_id)) {
                                    if ($count!=0) {
                                        $pivot=$new_pivot;
                                        $count=0;
                                        foreach ($pivot as $key => $value) {
                                            if (in_array($value, $new_array_view_author) && !in_array($value, $new_pivot)) {
                                                $count+=1;
                                                array_push($new_pivot, $value);
                                            }
                                        }
                                    }else{
                                        return $new_pivot=array();
                                    }
                                }else{
                                    foreach ($pivot as $key => $value) {
                                            if (in_array($value, $new_array_view_author) && !in_array($value, $new_pivot)) {
                                                array_push($new_pivot, $value);
                                            }
                                    }
                                }
                           
                        }
                         if (empty($new_pivot)) {
                                   $new_pivot=$pivot;             
                         }                   
                }elseif (isset($new_array_view_category) && !empty($new_array_view_category)){
                        $pivot=$new_array_view_category;
                        if (isset($new_array_view_customer_role) && !empty($new_array_view_customer_role)) {
                            foreach ($pivot as $key => $value) {
                                if (!in_array($value, $new_array_view_customer_role)) {
                                    array_push($new_pivot, $value);
                                }
                            }
                            $pivot=$new_pivot;
                        }
                        if (isset($new_array_view_customer) && !empty($new_array_view_customer)) {
                                        foreach ($pivot as $key => $value) {
                                            if (!in_array($value, $new_array_view_customer)) {   
                                                array_push($new_pivot, $value);
                                            }
                                        }   
                        }
                        if (isset($new_array_view_author) && !empty($new_array_view_author)) {
                                        foreach ($pivot as $key => $value) {
                                            if (!in_array($value, $new_array_view_author)) {   
                                                array_push($new_pivot, $value);
                                            }
                                        }
                        }
                        if (empty($new_pivot)) {
                            $new_pivot=$new_array_view_category;
                        }

                }elseif (isset($new_array_view_customer_role) && !empty($new_array_view_customer_role)||isset($new_array_view_customer) && !empty($new_array_view_customer)||isset($new_array_view_author) && !empty($new_array_view_author)){
                        if (!empty($new_array_view_customer_role)) {
                                $pivot=$new_array_view_customer_role;
                        }elseif (!empty($new_array_view_customer)) {
                            $pivot=$new_array_view_customer;
                        }elseif (!empty($new_array_view_author)) {
                            $pivot=$new_array_view_author;
                        }
                        if (isset($new_array_view_customer) && !empty($new_array_view_customer)) {
                            foreach ($pivot as $key => $value) {
                                if (!in_array($value, $new_array_view_customer)) {
                                    array_push($pivot, $value);
                                }
                            }
                        }
                        if (isset($new_array_view_author) && !empty($new_array_view_author)) {
                                foreach ($pivot as $key => $value) {
                                    if (!in_array($value, $new_array_view_author)) {
                                        array_push($pivot, $value);
                                    }
                                }               
                        }

                        $new_pivot=$pivot;
                }
            }

    return $new_pivot;
    }
    return false;
}


function r_wev_product_type($id) {
    $product = wc_get_product($id);
    if(!$product)
        return false;
    $class_name = get_class($product);
    if ($class_name == "WC_Product_Variable") {
        return 'variable';
    } else if ($class_name != "WC_Product_Variation" && $class_name != "WC_Product_Variable" ) {
        return 'simple';
    }
    return false; 
}

public function r_wev_get_role_about($uid) {
        global $wpdb;
        $role = $wpdb->get_var("SELECT meta_value FROM {$wpdb->usermeta} 
            WHERE meta_key = '{$wpdb->prefix}capabilities' 
            AND user_id = {$uid}");
       
        if (!$role)
            return 'non-user';
        $rarr = unserialize ($role);
        $roles = is_array($rarr) ? array_keys($rarr) : array('non-user');
        return $roles[0];
}

    function r_wev_is_applicable() {
        $is_valid = true;
        if (!isset($this->settings["rules"]) || !is_array($this->settings["rules"])) {
            $this->settings["rules"] = array();
        }
        $_search_valid_group=0;
        foreach ($this->settings["rules"] as $group){
            foreach ($group as $rule){ 
                $is_valid = $this->r_wev_is_rule_valid($rule);
                if (!$is_valid) {
                    break;
                }
            }
            if ($this->settings["relationship"] == "AND" && !$is_valid){
                break;
            }
            if ($this->settings["relationship"] == "AND" && $is_valid) {
                $this->_search_valid_group+=1;
            }
            if ($this->settings["relationship"] == "OR" && $is_valid){
                 $this->_search_valid_group+=1;
                break;
            }
        }
        return $is_valid;
    }

private function r_wev_get_arr_value($value,$rule_condition){
    $rule_value=array();
        if($rule_condition=="customer" ){
                $first_arr_value=$value;
        }elseif($rule_condition=="customer-role"){
                return $value[0];
        }elseif ($rule_condition=="author") {
            return $value;
        }
        else{
            foreach ($value as $key => $val) {
                    $position=strpos($val,'.');
                    $first_arr_value=substr($val, $position+1);
                    array_push($rule_value, $first_arr_value);
            }
            return $rule_value;
        } 
   return $first_arr_value; 
} 
    
    function r_wev_is_rule_valid($rule) {
        $is_valid = false;
        $condition = $this->r_wev_get_evaluable_condition($rule);      
        $value = r_wev_get_proper_value($rule, "value"); 
        if(is_bool($condition))
            goto part;
        if(!empty($value) && is_array($value)){
            $rule_value=$this->r_wev_get_arr_value($value,$rule["condition"]);          
        }else{
            $rule_value=$value;
        }

        part:;
        /*"customer-role": Customer role in/not in
            "customer": Customer in/not in
            customer-group:Customer belongs to one of the specified groups
            We check if the condition is IN or NOT IN the value*/
       if($rule["condition"]=="customer-role"){
            if($condition==$rule_value)
                $is_valid=true;
            /*if ($rule["operator"] == "NOT IN") 
                $is_valid = (!$is_valid);
            */

        }elseif($rule["condition"]=="customer"){
            $is_valid = in_array($condition, $rule_value);
        }elseif($rule["condition"]=="category"){
            if(isset($rule["value"])){
                $is_valid=true; 
            }else{
                $is_valid=false;
            }

        }elseif($rule["condition"] == "name_products"){
            if(is_array($rule_value)){ 
                foreach ($rule_value as $key => $cle_numerique) {
                  $is_valid=in_array($cle_numerique, $condition);
                  if($is_valid==false)
                    break;
                }
            }   
        }elseif($rule["condition"] == "postID"){
            if(isset($rule_value)){
                $is_valid=true;
               
            }else{
                $is_valid=false;
            }

        }elseif($rule["condition"] == "author"){
            if(is_array($rule_value)){ 

                foreach ($rule_value as $key => $cle_numerique) {
                    foreach ($condition as $id => $condtion_val) {
                        if($cle_numerique==$condtion_val){
                            $is_valid=true;
                            
                            break;
                       } else{
                        $is_valid=false;
                       }
                    }
                }
            }
        }
         else if ($rule["condition"] == "customer-group") {
            if (isset($rule["value"])) {
                $selected_groups = $rule["value"];
                $diff = array_intersect($condition, $selected_groups);
                $is_valid = count($diff);
            } else
                $is_valid = false;
        }   
        return $is_valid;
    }

private function r_wev_get_evaluable_condition($rule) {
        $condition = $rule["condition"];
        $evaluable_condition=false;
        global $woocommerce;
       
        switch ($condition) {
        	case "category":
                $evaluable_condition = true;
                break;
            case "name_products":
                    $evaluable_condition =$this->r_wev_get_existing_product_name();
                break;
            case "postID":
                $evaluable_condition = $this->r_wev_get_products_id();
                break; 
              case "author":
                $evaluable_condition = $this->r_wev_get_author_id();
                break;       
            case "customer-role":
                $evaluable_condition = $this->r_wev_get_user_role();
                break;
            case "customer":
                if (is_user_logged_in())
                    $evaluable_condition = get_current_user_id();
                break;
            case "customer-group":
                $evaluable_condition = $this->r_wev_get_user_groups();
                break;
            default :
                $evaluable_condition = false;
                break;
        }

        return $evaluable_condition;
}

private function r_wev_get_author_id(){
    global $wpdb;
    $author_arr=array();
    $r_wev_get_author_id=$wpdb->get_results("SELECT DISTINCT post_author FROM $wpdb->posts as posts, $wpdb->postmeta as postmeta 
            WHERE posts.ID=postmeta.post_id 
            AND post_status='publish' AND meta_key='_product_attributes'");

        foreach ($r_wev_get_author_id as $key => $value) {
            array_push($author_arr, $value->post_author);
        }
    return $author_arr;
}

public function r_wev_get_products_id(){
 global $wpdb;
      $product_id_arr=array();
        $get_post_id=$wpdb->get_results("SELECT ID FROM $wpdb->posts as posts,$wpdb->postmeta as postmeta 
                WHERE posts.ID=postmeta.post_id 
                AND post_status='publish' AND meta_key='_product_attributes'");
            foreach ($get_post_id as $key => $value) {
                array_push($product_id_arr, $value->ID);
            }
                return $product_id_arr;
}

public function r_wev_get_user_role() {
        $uid = get_current_user_id();
        global $wpdb;
        $role = $wpdb->get_var("SELECT meta_value FROM {$wpdb->usermeta} 
            WHERE meta_key = '{$wpdb->prefix}capabilities' 
            AND user_id = {$uid}");
       
        if (!$role)
            return 'non-user';
        $rarr = unserialize ($role);
        $roles = is_array($rarr) ? array_keys($rarr) : array('non-user');
        return $roles[0];
}

private function r_wev_get_existing_product_name(){
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

    private function r_wev_get_user_groups(){
            global $wpdb;
            if (!function_exists("_groups_get_tablename") || !is_user_logged_in())
                return array();
            $user_id = get_current_user_id();
            $user_group_table = _groups_get_tablename('user_group');
            $query = "SELECT distinct group_id FROM $user_group_table where user_id=$user_id ORDER BY group_id asc";

            $results = $wpdb->get_results($query);
            $groups = array_map(create_function('$o', 'return $o->group_id;'), $results);
            return $groups;
    }
   
    
}
