<?php
/**
 * Builds a select dropdpown
 * @param type $name Name
 * @param type $id ID
 * @param type $class Class
 * @param type $options Options
 * @param type $selected Selected value
 * @param type $multiple Can select multiple values
 * @return string HTML code
 */
function r_wev_get_html_select($name, $id, $class, $options, $selected = '', $multiple = false, $required = false) {
    ob_start();
    if ($multiple && !is_array($selected))
        $selected = array();
    ?>
    <select name="<?php echo $name; ?>" <?php echo ($id) ? "id=\"$id\"" : ""; ?> <?php echo ($class) ? "class=\"$class\"" : ""; ?> <?php echo ($multiple) ? "multiple" : ""; ?> <?php echo ($required) ? "required" : ""; ?> >
        <?php
        if (is_array($options) && !empty($options)) {
            foreach ($options as $value => $label) {
                if (!$multiple && $value == $selected) {
                    ?> <option value="<?php echo $value ?>"  selected="selected" > <?php echo $label; ?></option> <?php
                } else if ($multiple && in_array($value, $selected)) {
                    ?> <option value="<?php echo $value ?>"  selected="selected" > <?php echo $label; ?></option> <?php
                } else {
                    ?> <option value="<?php echo $value ?>"> <?php echo $label; ?></option> <?php
                }
            }
        }
        ?>
    </select>
    <?php
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}