<?php

//Product Cat Create page
function wh_taxonomy_add_new_meta_field() {
    ?>

    <div class="form-field">
        <label for="wh_meta_code"><?php _e('Code', 'wh'); ?></label>
        <input type="text" name="wh_meta_code" id="wh_meta_code">
        <p class="description"><?php _e('Enter a code, <= 100 character', 'wh'); ?></p>
    </div>
    <?php
}

//Product Cat Edit page
function wh_taxonomy_edit_meta_field($term) {

    //getting term ID
    $term_id = $term->term_id;

    // retrieve the existing value(s) for this meta field.
    $wh_meta_code = get_term_meta($term_id, 'wh_meta_code', true);
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="wh_meta_code"><?php _e('Meta code', 'wh'); ?></label></th>
        <td>
            <input type="text" name="wh_meta_code" id="wh_meta_code" value="<?php echo esc_attr($wh_meta_code) ? esc_attr($wh_meta_code) : ''; ?>">
            <p class="description"><?php _e('Enter a code, <= 100 character', 'wh'); ?></p>
        </td>
    </tr>
    <?php
}

add_action('product_cat_add_form_fields', 'wh_taxonomy_add_new_meta_field', 10, 1);
add_action('product_cat_edit_form_fields', 'wh_taxonomy_edit_meta_field', 10, 1);

// Save extra taxonomy fields callback function.
function wh_save_taxonomy_custom_meta($term_id) {
    $wh_meta_code = filter_input(INPUT_POST, 'wh_meta_code');
    update_term_meta($term_id, 'wh_meta_code', $wh_meta_code);
}

add_action('edited_product_cat', 'wh_save_taxonomy_custom_meta', 10, 1);
add_action('create_product_cat', 'wh_save_taxonomy_custom_meta', 10, 1);
// USAGE, to retrive data:
//
// echo $productCatMetacode = get_term_meta($term_id, 'wh_meta_code', true);
// echo $productCatMetaDesc = get_term_meta($term_id, 'wh_meta_desc', true);
