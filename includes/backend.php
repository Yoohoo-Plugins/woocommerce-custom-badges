<?php
/**
 * Backend scripts
*/

/**
 * Style Enqueue Admin
*/
function ycb_admin_enqueue($hook) {
    if ( strpos($hook, 'ycb_') === false) {
        return;
    }

    wp_enqueue_style( 'ycb_admin_css', plugin_dir_url(__FILE__) . '../css/admin.css' );
    wp_enqueue_style( 'ycb_admin_fa_css', plugin_dir_url(__FILE__) . '../css/fa.css' );
    wp_enqueue_style( 'ycb_admin_emoji_css', plugin_dir_url(__FILE__) . '../css/emoji.css' );

    wp_enqueue_script( 'ycb_admin_js', plugin_dir_url(__FILE__) . '../js/admin.js', array('jquery') );
    wp_enqueue_script( 'ycb_admin_emoji_ref_js', plugin_dir_url(__FILE__) . '../js/emoji_ref_array.js', array('jquery') );
    wp_enqueue_script( 'ycb_admin_emoji_js', plugin_dir_url(__FILE__) . '../js/emoji_picker.js', array('jquery', 'ycb_admin_emoji_ref_js') );
}
add_action( 'admin_enqueue_scripts', 'ycb_admin_enqueue' );

/**
 * Discount badge settings segment
*/
function ycb_setting_area(){
    ycb_save_settings_head();
    $ycb_settings = ycb_get_settings();

    ?>
    <div class="wrap yoohoo_wrap">
        <form method="POST" action="">

            <div class='header'>
                <img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/custom_badges_banner.png'; ?>" />

                <div class='tab-container'>
                    <div data-tab-id="tab_1" class='tab-clickable active'><?php _e("Discount Badges"); ?></div>
                    <div data-tab-id="tab_2" class='tab-clickable'><?php _e("Global Settings"); ?></div>
                </div>
            </div>

            <div id="tab_1">
                <table class="widefat tab-content-table">
                    <tr>
                        <td style="width:50%">
                            <strong><?php _e("Badge Styling"); ?></strong>
                        </td>
                        <td style="width:50%"></td>
                    </tr>

                    <tr>
                        <td>
                            <label><?php _e("0-10% Off Color"); ?>:</label>
                        </td>
                        <td>
                            <input type="color" value="<?php echo ( isset($ycb_settings['ycb_10_color']) ? $ycb_settings['ycb_10_color'] : "#ED1C24" ); ?>" name="ycb_10_color" id="ycb_10_color">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label><?php _e("10-20% Off Color"); ?>:</label>
                        </td>
                        <td>
                            <input type="color" value="<?php echo ( isset($ycb_settings['ycb_0_20_color']) ? $ycb_settings['ycb_0_20_color'] : "#ED1C24" ); ?>" name="ycb_0_20_color" id="ycb_0_20_color">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label><?php _e("20-30% Off Color"); ?>:</label>
                        </td>
                        <td>
                            <input type="color" value="<?php echo ( isset($ycb_settings['ycb_30_color']) ? $ycb_settings['ycb_30_color'] : "#ED1C24" ); ?>" name="ycb_30_color" id="ycb_30_color">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label><?php _e("30-40% Off Color"); ?>:</label>
                        </td>
                        <td>
                            <input type="color" value="<?php echo ( isset($ycb_settings['ycb_20_40_color']) ? $ycb_settings['ycb_20_40_color'] : "#ED1C24" ); ?>" name="ycb_20_40_color" id="ycb_20_40_color">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label><?php _e("40-50% Off Color"); ?>:</label>
                        </td>
                        <td>
                            <input type="color" value="<?php echo ( isset($ycb_settings['ycb_50_color']) ? $ycb_settings['ycb_50_color'] : "#ED1C24" ); ?>" name="ycb_50_color" id="ycb_50_color">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label><?php _e("50-60% Off Color"); ?>:</label>
                        </td>
                        <td>
                            <input type="color" value="<?php echo ( isset($ycb_settings['ycb_40_60_color']) ? $ycb_settings['ycb_40_60_color'] : "#ED1C24" ); ?>" name="ycb_40_60_color" id="ycb_40_60_color">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label><?php _e("60-70% Off Color"); ?>:</label>
                        </td>
                        <td>
                            <input type="color" value="<?php echo ( isset($ycb_settings['ycb_70_color']) ? $ycb_settings['ycb_70_color'] : "#ED1C24" ); ?>" name="ycb_70_color" id="ycb_70_color">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label><?php _e("70-80% Off Color"); ?>:</label>
                        </td>
                        <td>
                            <input type="color" value="<?php echo ( isset($ycb_settings['ycb_60_80_color']) ? $ycb_settings['ycb_60_80_color'] : "#ED1C24" ); ?>" name="ycb_60_80_color" id="ycb_60_80_color">
                        </td>
                    </tr>



                    <tr>
                        <td>
                            <label><?php _e("80-90% Off Color"); ?>:</label>
                        </td>
                        <td>
                            <input type="color" value="<?php echo ( isset($ycb_settings['ycb_80_100_color']) ? $ycb_settings['ycb_80_100_color'] : "#ED1C24" ); ?>" name="ycb_80_100_color" id="ycb_80_100_color">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label><?php _e("90-100% Off Color"); ?>:</label>
                        </td>
                        <td>
                            <input type="color" value="<?php echo ( isset($ycb_settings['ycb_90_color']) ? $ycb_settings['ycb_90_color'] : "#ED1C24" ); ?>" name="ycb_90_color" id="ycb_90_color">
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label><?php _e("Text Color"); ?>:</label>
                        </td>
                        <td>
                            <input type="color" value="<?php echo ( isset($ycb_settings['ycb_text_color']) ? $ycb_settings['ycb_text_color'] : "#FFFFFF" ); ?>" name="ycb_text_color" id="ycb_text_color">
                        </td>
                    </tr>
                </table>
            </div>

            <div id='tab_2' style='display: none;'>
                <table class="widefat tab-content-table">

                    <tr>
                        <td style="width:50%">
                            <strong><?php _e("General Options"); ?></strong>
                        </td>
                        <td style="width:50%"></td>
                    </tr>

                    <tr>
                        <td>
                            <label><?php _e("Hide Default WooCommerce 'Sale' tag"); ?>:</label>
                        </td>
                        <td>
                            <input type="checkbox" value="<?php echo ( isset($ycb_settings["ycb_hide_default_sale_tag"]) && $ycb_settings["ycb_hide_default_sale_tag"] === "true" ? "true" : "" ); ?>" <?php echo ( isset($ycb_settings["ycb_hide_default_sale_tag"]) && $ycb_settings["ycb_hide_default_sale_tag"] === "true" ? "checked='checked'" : "" ); ?> name="ycb_hide_default_sale_tag" id="ycb_hide_default_sale_tag">
                        </td>
                    </tr>

                    <tr>
                        <td style="width:50%">
                            <strong><?php _e("Badge Shape"); ?></strong>
                        </td>
                        <td style="width:50%"></td>
                    </tr>

                    <tr>
                        <td>
                            <?php
                                $shape_setting = isset($ycb_settings["ycb_badge_shape"]) ? intval($ycb_settings["ycb_badge_shape"]) : false;
                                $shape_class_default = 'circle';
                                switch ($shape_setting) {
                                    case 1:
                                        $shape_class_default = 'square';
                                        break;
                                    case 2:
                                        $shape_class_default = 'rounded';
                                        break;
                                }
                            ?>
                            <select id='ycb_badge_shape' name='ycb_badge_shape'>
                                <option value='0' <?php echo($shape_setting === 0 || $shape_setting === false ? 'selected' : ''); ?> ><?php _e('Round'); ?></option>
                                <option value='1' <?php echo($shape_setting === 1 ? 'selected' : ''); ?> ><?php _e('Square'); ?></option>
                                <option value='2' <?php echo($shape_setting === 2 ? 'selected' : ''); ?> ><?php _e('Rounded Square'); ?></option>
                            </select>
                        </td>
                        <td>
                            <small><em><?php _e('Preview'); ?></em></small>
                            <div class='badge_shape_preview <?php echo $shape_class_default; ?>'>YooHoo</div>
                        </td>
                    </tr>
                </table>
            </div>

            <?php
                do_action("ycb_settings_area_below_hook", $ycb_settings);
            ?>




            <div class='yoohoo_footer'>
                <div class='company'>
                    <img src="<?php echo plugin_dir_url( __FILE__ ) . '../assets/yoohooplugins-logo-white.png'; ?>" />
                    <input type="submit" class="button-primary" value="<?php _e("Save"); ?>" name="ycb_save_settings">
                </div>
            </div>
        </form>


    </div>
    <?php
}

/**
 * Save settings head
*/
function ycb_save_settings_head(){
    if(isset($_POST['ycb_save_settings'])){
        //User is trying to save
        $ycb_settings_array = array();
        if(isset($_POST['ycb_hide_default_sale_tag'])){
            $ycb_settings_array['ycb_hide_default_sale_tag'] = "true";
        } else {
            $ycb_settings_array['ycb_hide_default_sale_tag'] = "false";
        }

        if(isset($_POST['ycb_0_20_color'])){
            $ycb_settings_array['ycb_0_20_color'] = esc_attr($_POST['ycb_0_20_color']);
        } else {
            $ycb_settings_array['ycb_0_20_color'] = "#ED1C24";
        }

        if(isset($_POST['ycb_20_40_color'])){
            $ycb_settings_array['ycb_20_40_color'] = esc_attr($_POST['ycb_20_40_color']);
        } else {
            $ycb_settings_array['ycb_20_40_color'] = "#ED1C24";
        }

        if(isset($_POST['ycb_40_60_color'])){
            $ycb_settings_array['ycb_40_60_color'] = esc_attr($_POST['ycb_40_60_color']);
        } else {
            $ycb_settings_array['ycb_40_60_color'] = "#ED1C24";
        }

        if(isset($_POST['ycb_60_80_color'])){
            $ycb_settings_array['ycb_60_80_color'] = esc_attr($_POST['ycb_60_80_color']);
        } else {
            $ycb_settings_array['ycb_60_80_color'] = "#ED1C24";
        }

        if(isset($_POST['ycb_80_100_color'])){
            $ycb_settings_array['ycb_80_100_color'] = esc_attr($_POST['ycb_80_100_color']);
        } else {
            $ycb_settings_array['ycb_80_100_color'] = "#ED1C24";
        }

        //Added increments
        if(isset($_POST['ycb_10_color'])){$ycb_settings_array['ycb_10_color'] = esc_attr($_POST['ycb_10_color']);} else {$ycb_settings_array['ycb_10_color'] = "#ED1C24";}
        if(isset($_POST['ycb_30_color'])){$ycb_settings_array['ycb_30_color'] = esc_attr($_POST['ycb_30_color']);} else {$ycb_settings_array['ycb_30_color'] = "#ED1C24";}
        if(isset($_POST['ycb_50_color'])){$ycb_settings_array['ycb_50_color'] = esc_attr($_POST['ycb_50_color']);} else {$ycb_settings_array['ycb_50_color'] = "#ED1C24";}
        if(isset($_POST['ycb_70_color'])){$ycb_settings_array['ycb_70_color'] = esc_attr($_POST['ycb_70_color']);} else {$ycb_settings_array['ycb_70_color'] = "#ED1C24";}
        if(isset($_POST['ycb_90_color'])){$ycb_settings_array['ycb_90_color'] = esc_attr($_POST['ycb_90_color']);} else {$ycb_settings_array['ycb_90_color'] = "#ED1C24";}

        if(isset($_POST['ycb_text_color'])){
            $ycb_settings_array['ycb_text_color'] = esc_attr($_POST['ycb_text_color']);
        } else {
            $ycb_settings_array['ycb_text_color'] = "#FFFFFF";
        }

        if(isset($_POST['ycb_badge_shape'])){
            $ycb_settings_array['ycb_badge_shape'] = intval($_POST['ycb_badge_shape']);
        } else {
            $ycb_settings_array['ycb_badge_shape'] = 0;
        }

        $ycb_settings_array = apply_filters("ycb_save_settings_array_filter", $ycb_settings_array);

        update_option("ycb_primary_settings", maybe_serialize($ycb_settings_array));
    }
}
/**
 * Custom Badges settings area
*/
function ycb_custom_badges(){
    ?>
    <div class="yoohoo_wrap wrap">
        <div class='header'>
            <h3><?php _e("Custom Badges"); ?> </h3>
        </div>
        <?php ycb_custom_badges_check_header(); ycb_custom_badges_action_check(); ycb_custom_badges_table(); ?>
        <div class='yoohoo_footer'>
            <div class='company'>
                <a href="admin.php?page=ycb_main_menu&action=new" class="button button-primary"><?php _e("Add New"); ?></a>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Header handler for custom badges
*/
function ycb_custom_badges_check_header(){
    if(isset($_POST['ycb_custom_badge_new_add'])){
        //User wants to add a badge
        ycb_custom_badge_add_new_insert();
    }

    if(isset($_POST['ycb_custom_badge_edit'])){
        //User wants to edit a badge
        ycb_custom_badge_edit_update();
    }

    //ycb_custom_badge_global_options();

}

/**
 * Custom abdges global option
*/
function ycb_custom_badge_global_options(){
    ycb_custom_badge_global_options_headers();
    $ycb_global_options = ycb_custom_badge_get_global_options();

    ?>
    <form method="POST" action="">
        <table class="widefat">

            <tr>
                <td style="width:50%">
                    <strong><?php _e("Global Options"); ?></strong>
                </td>
                <td style="width:50%"></td>
            </tr>

            <tr>
                <td>
                    <label><?php _e("Hide Default WooCommerce 'Sale' tag where custom badges appears"); ?>:</label>
                </td>
                <td>
                    <input type="checkbox" value="<?php echo ( isset($ycb_global_options["ycb_hide_default_sale_tag"]) && $ycb_global_options["ycb_hide_default_sale_tag"] === "true" ? "true" : "" ); ?>" <?php echo ( isset($ycb_global_options["ycb_hide_default_sale_tag"]) && $ycb_global_options["ycb_hide_default_sale_tag"] === "true" ? "checked='checked'" : "" ); ?> name="ycb_hide_default_sale_tag" id="ycb_hide_default_sale_tag"> <small><?php _e("Hides default sales tag on the page"); ?></small>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="ycb_save_global_options" class="button button-primary" value="<?php _e("Save Global Options"); ?>" type="submit">
                </td>
                <td></td>
            </tr>
        </table>
        <br>
    </form>
    <?php
}

/**
 * Custom badges global options header handler
*/
function ycb_custom_badge_global_options_headers(){
    if(isset($_POST['ycb_save_global_options'])){
        //User is trying to save
        $ycb_settings_array = array();
        if(isset($_POST['ycb_hide_default_sale_tag'])){
            $ycb_settings_array['ycb_hide_default_sale_tag'] = "true";
        } else {
            $ycb_settings_array['ycb_hide_default_sale_tag'] = "false";
        }


        $ycb_settings_array = apply_filters("ycb_save_global_options_array_filter", $ycb_settings_array);

        update_option("ycb_custom_badge_global_options", maybe_serialize($ycb_settings_array));
    }
}


/**
 * Action checker
*/
function ycb_custom_badges_action_check(){
    if(isset($_GET['action'])){
        if($_GET['action'] === "new"){
            ycb_custom_action_add_new_form();
        }
        if($_GET['action'] === "edit" && isset($_GET['id'])){
            $badge_id = intval($_GET['id']);
            ycb_custom_action_edit_form($badge_id);
        }
        if($_GET['action'] === "delete" && isset($_GET['id'])){
            $badge_id = intval($_GET['id']);
            ycb_custom_action_delete_prompt($badge_id);
        }
        if($_GET['action'] === "confirm_delete" && isset($_GET['id'])){
            $badge_id = intval($_GET['id']);
            ycb_custom_action_delete_confirm($badge_id);
        }


    }
}

/**
 * Custom badge add new form
*/
function ycb_custom_action_add_new_form(){
    ?>
    <form method="POST" action="">
        <table class="widefat">
            <tr>
                <td style="width:30%">
                    <strong><?php _e("New Custom Badge"); ?></strong>
                </td>
                <td style="width:70%"></td>
            </tr>

            <tr>
                <td>
                    <label><?php _e("Name"); ?>:</label>
                </td>
                <td>
                    <input name="ycb_custom_badge_new_name" type="text" style="width:70%;" placeholder="<?php _e("This is only for identification, will not be used in the badge"); ?>" <?php echo (isset($_POST['ycb_custom_badge_new_name']) ? "value='" . $_POST["ycb_custom_badge_new_name"] . "'" : ""); ?> >
                </td>
            </tr>

            <tr>
                <td>
                    <label><?php _e("Category"); ?>:</label>
                </td>
                <td>
                    <?php ycb_get_woo_categories_select("ycb_custom_badge_new_cat", (isset($_POST['ycb_custom_badge_new_cat']) ? $_POST['ycb_custom_badge_new_cat'] : false) , "width:70%;")?>
                </td>
            </tr>

             <tr>
                <td>
                    <label><?php _e("Content"); ?>:</label>
                </td>
                <td>
                    <input name="ycb_custom_badge_new_content" type="text" class='inline-input'  id="ycb_custom_preview_content" style="width:70%;" placeholder="<?php _e("Shown in your badge"); ?>" <?php echo (isset($_POST['ycb_custom_badge_new_content']) ? "value='" . $_POST["ycb_custom_badge_new_content"] . "'" : ""); ?> >

                    <?php ycb_create_emoji_css_picker('ycb_custom_preview_content'); ?>

                    <br>
                    <small><em><?php _e('Tip: You can add Font-Awesome icons by using our custom shortcodes (ex: %%fas fa-check%%)');?></em></small>
                </td>
            </tr>

            <tr>
                <td>
                    <label><?php _e("Text Color"); ?>:</label>
                </td>
                <td>
                    <input name="ycb_custom_badge_new_text_color" type="color" id="ycb_custom_preview_color" <?php echo (isset($_POST['ycb_custom_badge_new_text_color']) ? "value='" . $_POST["ycb_custom_badge_new_text_color"] . "'" : ""); ?>>
                </td>
            </tr>

            <tr>
                <td>
                    <label><?php _e("Badge Color"); ?>:</label>
                </td>
                <td>
                    <input name="ycb_custom_badge_new_badge_color" type="color" id="ycb_custom_preview_bg_color" <?php echo (isset($_POST['ycb_custom_badge_new_badge_color']) ? "value='" . $_POST["ycb_custom_badge_new_badge_color"] . "'" : ""); ?>>
                </td>
            </tr>

            <tr>
                <td>
                    <label><?php _e("Preview"); ?>:</label>
                </td>
                <td>
                    <?php ycb_custom_badge_previewer("#ycb_custom_preview_content", "#ycb_custom_preview_color", "#ycb_custom_preview_bg_color"); ?>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="ycb_custom_badge_new_add" class="button button-primary" value="<?php _e("Add Badge"); ?>" type="submit">
                    <a href="admin.php?page=ycb_main_menu" class="button"><?php _e("Close"); ?></a>
                </td>
                <td></td>
            </tr>
        </table>
    </form>
    <br>
    <?php
}

/**
 * Custom badge edit form
*/
function ycb_custom_action_edit_form($badge_id){
    $badge_data = ycb_custom_badge_get_data($badge_id);
    if($badge_data === false){ return false; }
    ?>
    <form method="POST" action="">
        <input name="ycb_custom_badge_edit_id" type="hidden" value="<?php echo $badge_data->id; ?>" >
        <table class="widefat">
            <tr>
                <td style="width:30%">
                    <strong><?php _e("Edit Custom Badge"); ?></strong>
                </td>
                <td style="width:70%"></td>
            </tr>

            <tr>
                <td>
                    <label><?php _e("Name"); ?>:</label>
                </td>
                <td>
                    <input name="ycb_custom_badge_edit_name" type="text" style="width:70%;" placeholder="<?php _e("This is only for identification, will not be used in the badge"); ?>" value="<?php echo $badge_data->name; ?>" >
                </td>
            </tr>

            <tr>
                <td>
                    <label><?php _e("Category"); ?>:</label>
                </td>
                <td>
                    <?php ycb_get_woo_categories_select("ycb_custom_badge_edit_cat", $badge_data->cat_id , "width:70%;")?>
                </td>
            </tr>

             <tr>
                <td>
                    <label><?php _e("Content"); ?>:</label>
                </td>
                <td>
                    <input name="ycb_custom_badge_edit_content" id="ycb_custom_preview_content" class='inline-input' type="text" style="width:70%;" placeholder="<?php _e("Shown in your badge"); ?>" value="<?php echo $badge_data->content; ?>" >

                    <?php ycb_create_emoji_css_picker('ycb_custom_preview_content'); ?>

                    <br>
                    <small><em><?php _e('Tip: You can add Font-Awesome icons by using our custom shortcodes (ex: %%fas fa-check%%)');?></em></small>
                </td>
            </tr>

            <tr>
                <td>
                    <label><?php _e("Text Color"); ?>:</label>
                </td>
                <td>
                    <input name="ycb_custom_badge_edit_text_color" id="ycb_custom_preview_color" type="color" value="<?php echo $badge_data->color; ?>" >
                </td>
            </tr>

            <tr>
                <td>
                    <label><?php _e("Badge Color"); ?>:</label>
                </td>
                <td>
                    <input name="ycb_custom_badge_edit_badge_color" id="ycb_custom_preview_bg_color" type="color" value="<?php echo $badge_data->bg_color; ?>" >

                </td>

            </tr>

            <tr>
                <td>
                    <label><?php _e("Preview"); ?>:</label>
                </td>
                <td>
                    <?php ycb_custom_badge_previewer("#ycb_custom_preview_content", "#ycb_custom_preview_color", "#ycb_custom_preview_bg_color"); ?>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="ycb_custom_badge_edit" class="button button-primary" value="<?php _e("Edit Badge"); ?>" type="submit">
                    <a href="admin.php?page=ycb_main_menu" class="button"><?php _e("Close"); ?></a>
                </td>
                <td></td>
            </tr>
        </table>
    </form>
    <br>

    <script>
    jQuery(function(){
        jQuery("input[name=ycb_custom_badge_edit_name],input[name=ycb_custom_badge_edit_cat],input[name=ycb_custom_badge_edit_content],input[name=ycb_custom_badge_edit_text_color],input[name=ycb_custom_badge_edit_badge_color]").on("change", function(){
            jQuery("input[name=ycb_custom_badge_edit]").val("<?php _e('Save Changes'); ?>");
        });
    });
    </script>
    <?php
}

/**
 * Custom badge delete prompt
*/
function ycb_custom_action_delete_prompt($badge_id){
    ?>
    <table class="widefat">
        <tr>
            <td style="width:30%">
                <strong><?php _e("Are you sure you want to delete this badge?"); ?></strong>
            </td>
            <td style="width:70%"></td>
        </tr>
        <tr>
            <td>
                <a href="admin.php?page=ycb_main_menu&action=confirm_delete&id=<?php echo $badge_id; ?>" class="button"><?php _e("Confirm"); ?></a>
                <a href="admin.php?page=ycb_main_menu" class="button"><?php _e("Cancel"); ?></a>
            </td>
            <td></td>
        </tr>
    </table>
    <br>
    <?php
}

/**
 * Delete badge with confirm notice
*/
function ycb_custom_action_delete_confirm($badge_id){
    global $ycb_custom_badges_table, $wpdb;

    $wpdb->query("DELETE FROM `$ycb_custom_badges_table` WHERE `id` = '$badge_id'");

    echo "<div class='error'><p>".__("Badge Deleted") . "</p></div>";
}

/**
 * Create a category selection dropdown from the woo categories
*/
function ycb_get_woo_categories_select($name, $selected_val = false, $styles = false){
    $categories = ycb_get_woo_categories();
    if($selected_val !== false){ $selected_val = intval($selected_val); }
    if($categories !== false){
        echo "<select name='$name' ".($selected_val !== false ? "value='$selected_val'" : "")." ".($styles !== false ? "style='$styles'" : "").">";
        foreach ($categories as $cat) {
            echo "<option value='" . $cat->term_id . "' ".($selected_val !== false && $selected_val === $cat->term_id ? "selected" : "")." >" . $cat->name . "</option>";
        }
        echo "</select>";
    }
}

/**
 * Custom badge previewer
*/
function ycb_custom_badge_previewer($badge_title_element, $badge_color_element, $badge_bg_color_element){
    ?>
    <span id="badge_preview" style="display:inline-block;min-width:50px;min-height:50px;max-height: 50px;" class='<?php echo ycb_get_badge_shape_class(true, false); ?>'>
        <span style="display: block;text-align: center;line-height: 47px; padding-left:10px; padding-right:10px;" id="badge_preview_content"></span>
    </span>
    <br>
    <small><?php _e("Preview results may vary, as site styling may influence results"); ?></small>
    <script>
        jQuery(function(){
            jQuery(document).ready(function(){
                let fa_regex = /%%(.+?)%%/g;
                let emoji_regex = /##(.+?)##/g;

                jQuery("#badge_preview_content").html(jQuery("<?php echo $badge_title_element?>").val().replace(fa_regex,"<i class='$1'></i>"));
                jQuery("#badge_preview_content").html(jQuery("<?php echo $badge_title_element?>").val().replace(emoji_regex,"<i class='$1'></i>"));
                jQuery("#badge_preview").css("background", jQuery("<?php echo $badge_bg_color_element?>").val());
                jQuery("#badge_preview").css("color", jQuery("<?php echo $badge_color_element?>").val());

                jQuery("<?php echo $badge_title_element?>").on("keyup", function(){
                    let current_title = jQuery("<?php echo $badge_title_element?>").val();
                    current_title = current_title.replace(fa_regex,"<i class='$1'></i>");
                    current_title = current_title.replace(emoji_regex,"<i class='$1'></i>");
                    jQuery("#badge_preview_content").html(current_title);
                });

                jQuery("<?php echo $badge_bg_color_element?>").on("change", function(){
                    jQuery("#badge_preview").css("background", jQuery("<?php echo $badge_bg_color_element?>").val());
                });

                jQuery("<?php echo $badge_color_element?>").on("change", function(){
                    jQuery("#badge_preview").css("color", jQuery("<?php echo $badge_color_element?>").val());
                });
            });
        });
    </script>
    <?php


}

/**
 * Get woo categories
*/
function ycb_get_woo_categories(){
    global $wp_version;
    if ( floatval($wp_version) >= 4.5 ) {
        $args = array(
            'taxonomy'   => "product_cat",
        );
        $product_categories = get_terms($args);
        return $product_categories;
    } else {
        //Backwards compat
        $args = array();
        $product_categories = get_terms( 'product_cat', $args );
        return $product_categories;
    }

    return false;

}

/**
 * Create custom badges table
 * Echo HTML
*/
function ycb_custom_badges_table(){

    $custom_badges = ycb_get_all_custom_badges();

    echo '<table class="widefat">';

    if( $custom_badges !== false ){
        echo "<tr>";
        echo "<td><strong>" . __("ID") . "</strong></td>";
        echo "<td><strong>" . __("Name") . "</strong></td>";
        echo "<td><strong>" . __("Action") . "</strong></td>";
        echo "</tr>";
        foreach ($custom_badges as $key => $badge) {
            echo "<tr>";
            echo "<td>" . $badge->id . "</td>";
            echo "<td>" . $badge->name . "</td>";
            echo "<td>";

            echo "<a href='admin.php?page=ycb_main_menu&action=edit&id=".$badge->id."' class='button'>" . __("Edit") . "</a> ";
            echo "<a href='admin.php?page=ycb_main_menu&action=delete&id=".$badge->id."' class='button'>" . __("Delete") . "</a>";

            echo "</td>";
            echo "</tr>";
        }

    } else {
        echo "<tr><td>" . __("No Results Found...") . "</td><td></td></tr>";
    }

    echo '</table>';
}

/**
 * Get all the custom badges
*/
function ycb_get_all_custom_badges(){
    global $ycb_custom_badges_table, $wpdb;
    $results = $wpdb->get_results( "SELECT * FROM $ycb_custom_badges_table" );

    if(count($results) > 0){
        return $results;
    }

    return false;
}

/**
 * Get custom badge data for a single badge
*/
function ycb_custom_badge_get_data($badge_id){
    global $ycb_custom_badges_table, $wpdb;
    $results = $wpdb->get_results( "SELECT * FROM $ycb_custom_badges_table WHERE `id` = '$badge_id' LIMIT 1" );

    if(count($results) > 0){
        return $results[0];
    }

    return false;
}

/**
 * Find matches based on a category ID
*/
function ycb_custom_badge_get_matches_for_category($cat_id){
    global $ycb_custom_badges_table, $wpdb;
    $results = $wpdb->get_results( "SELECT * FROM $ycb_custom_badges_table WHERE `cat_id` = '$cat_id'" );

    if(count($results) > 0){
        return $results;
    }

    return false;
}

/**
 * Custom badge insert
*/
function ycb_custom_badge_add_new_insert(){
    global $ycb_custom_badges_table, $wpdb;
    if(isset($_POST['ycb_custom_badge_new_add'])){
        $new_badge_name = isset($_POST['ycb_custom_badge_new_name']) ? esc_attr($_POST['ycb_custom_badge_new_name']) : false;
        $new_badge_cat = isset($_POST['ycb_custom_badge_new_cat']) ? intval($_POST['ycb_custom_badge_new_cat']) : false;
        $new_badge_content = isset($_POST['ycb_custom_badge_new_content']) ? esc_attr($_POST['ycb_custom_badge_new_content']) : false;
        $new_badge_text_col = isset($_POST['ycb_custom_badge_new_text_color']) ? esc_attr($_POST['ycb_custom_badge_new_text_color']) : false;
        $new_badge_badge_color = isset($_POST['ycb_custom_badge_new_badge_color']) ? esc_attr($_POST['ycb_custom_badge_new_badge_color']) : false;

        $error_found = false;
        if(!empty($new_badge_name) && $new_badge_name !== false){
            if($new_badge_cat !== false){
                if(!empty($new_badge_content) && $new_badge_content !== false){
                    if(!empty($new_badge_text_col) && $new_badge_text_col !== false){
                        if(!empty($new_badge_badge_color) && $new_badge_badge_color !== false){
                            $insert_custom = $wpdb->query(
                                "INSERT INTO `$ycb_custom_badges_table` SET
                                `name` = '$new_badge_name',
                                `cat_id` = '$new_badge_cat',
                                `content` = '$new_badge_content',
                                `color` = '$new_badge_text_col',
                                `bg_color` = '$new_badge_badge_color'
                                "
                            );


                            echo "<div class='updated'><p>".__("Success")."</p></div>";
                            unset($_POST);
                        } else {
                            //No Badge Color
                            $error_found = __("Please select a badge color");
                        }
                    } else {
                        //No Text Color
                        $error_found = __("Please select a badge text color");
                    }
                } else {
                    //No Content
                    $error_found = __("Please enter content for your badge");
                }
            } else {
                //No Cat
                $error_found = __("Please select a product category for your badge");
            }
        } else {
            //No Name error
            $error_found = __("Please enter a name for your badge");
        }

        if($error_found !== false){
             echo "<div class='error'><p>".__("Error").": ".$error_found."</p></div>";
        }
    }
}

/**
 * Custom Badge edit update
*/
function ycb_custom_badge_edit_update(){
    global $ycb_custom_badges_table, $wpdb;
    if(isset($_POST['ycb_custom_badge_edit'])){
        $new_badge_id = isset($_POST['ycb_custom_badge_edit_id']) ? esc_attr($_POST['ycb_custom_badge_edit_id']) : false;
        $new_badge_name = isset($_POST['ycb_custom_badge_edit_name']) ? esc_attr($_POST['ycb_custom_badge_edit_name']) : false;
        $new_badge_cat = isset($_POST['ycb_custom_badge_edit_cat']) ? intval($_POST['ycb_custom_badge_edit_cat']) : false;
        $new_badge_content = isset($_POST['ycb_custom_badge_edit_content']) ? esc_attr($_POST['ycb_custom_badge_edit_content']) : false;
        $new_badge_text_col = isset($_POST['ycb_custom_badge_edit_text_color']) ? esc_attr($_POST['ycb_custom_badge_edit_text_color']) : false;
        $new_badge_badge_color = isset($_POST['ycb_custom_badge_edit_badge_color']) ? esc_attr($_POST['ycb_custom_badge_edit_badge_color']) : false;

        $error_found = false;
        if(!empty($new_badge_name) && $new_badge_name !== false){
            if($new_badge_cat !== false){
                if(!empty($new_badge_content) && $new_badge_content !== false){
                    if(!empty($new_badge_text_col) && $new_badge_text_col !== false){
                        if(!empty($new_badge_badge_color) && $new_badge_badge_color !== false){
                            $insert_custom = $wpdb->query(
                                "UPDATE `$ycb_custom_badges_table` SET
                                `name` = '$new_badge_name',
                                `cat_id` = '$new_badge_cat',
                                `content` = '$new_badge_content',
                                `color` = '$new_badge_text_col',
                                `bg_color` = '$new_badge_badge_color'

                                WHERE `id` = '$new_badge_id'
                                "
                            );

                            echo "<div class='updated'><p>".__("Success")."</p></div>";
                            unset($_POST);
                        } else {
                            //No Badge Color
                            $error_found = __("Please select a badge color");
                        }
                    } else {
                        //No Text Color
                        $error_found = __("Please select a badge text color");
                    }
                } else {
                    //No Content
                    $error_found = __("Please enter content for your badge");
                }
            } else {
                //No Cat
                $error_found = __("Please select a product category for your badge");
            }
        } else {
            //No Name error
            $error_found = __("Please enter a name for your badge");
        }

        if($error_found !== false){
             echo "<div class='error'><p>".__("Error").": ".$error_found."</p></div>";
        }
    }
}

/**
 * Creates a little emoji picker, this is populated by JS entirely
*/
function ycb_create_emoji_css_picker($for_id){
    ?>
        <div class='emoji_selector_container'>
            <button class='button emoji_button'>
                <i class="em em-smirk"></i>
            </button>
            <div class='emoji_popup' data-input-asso='<?php echo $for_id; ?>'>
                <!-- GENERATED DYNAMICALLY -->
            </div>
        </div>
    <?php
}