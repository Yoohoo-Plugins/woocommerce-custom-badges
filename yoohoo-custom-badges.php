<?php
/*
Plugin Name: Custom Badges For WooCommerce
Plugin URI: https://yoohooplugins.com/plugins/custom-badges-for-woocommerce/
Description: Easily create custom badges for your WooCommerce online store.
Version: 1.2
Author: Yoohoo Plugins
Author URI: https://yoohooplugins.com
Text Domain: yoohoo-custom-badges
Domain Path: /languages
*/

global $wdb_custom_badges_table, $wpdb, $wdb_has_hidden_sales_tag, $wdb_using_flatsome;
$wdb_custom_badges_table = $wpdb->prefix . "wdb_custom_badges";
$wdb_has_hidden_sales_tag = false;
$wdb_using_flatsome = false;

function wdb_activate() {
    wdb_custom_tags_database_tables();
}
register_activation_hook( __FILE__, 'wdb_activate' );

function wdb_init(){
    wdb_check_theme_framework_integrations();
    wdb_register_actions_filters();
}
add_action("init", "wdb_init");

function wdb_check_theme_framework_integrations(){
    global $wdb_using_flatsome;
    //Check if user is using flatsome
    if(class_exists("Flatsome_Option")){
        //The option class exists, safe to assume user is using flatsome.
        $wdb_using_flatsome = true;
    }
}

function wdb_register_actions_filters(){
    global $wdb_using_flatsome;
    if($wdb_using_flatsome){
        wdb_setup_flatsome_hooks();
    } else {
        wdb_setup_standard_hooks();
    }
}

function wdb_setup_flatsome_hooks(){
    add_filter("flatsome_product_labels", "wdb_add_discount_badge", 15, 3);
    add_filter("flatsome_product_labels", "wdb_custom_badge_shop_loop_opened_flatsome", 15);
}

function wdb_setup_standard_hooks(){
    add_filter("woocommerce_sale_flash", "wdb_add_discount_badge", 1, 3);
    add_action('woocommerce_before_shop_loop_item', 'wdb_custom_badge_shop_loop_opened', 15 );
    add_action('woocommerce_before_single_product_summary', 'wdb_custom_badge_shop_loop_opened', 15);
}

function wdb_custom_tags_database_tables(){
    global $wdb_custom_badges_table, $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "
    CREATE TABLE $wdb_custom_badges_table (
      id int(11) NOT NULL AUTO_INCREMENT,
      cat_id int(11) NOT NULL,
      name varchar(700) NOT NULL,
      content varchar(700) NOT NULL,
      color varchar(700) NOT NULL,
      bg_color varchar(700) NOT NULL,
      PRIMARY KEY  (id)
    ) $charset_collate ;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function wdb_add_discount_badge($content, $post, $product){
    global $wdb_using_flatsome;

    $wdb_settings = wbd_get_settings();

    $base_price = get_post_meta( get_the_ID(), '_regular_price', true );
    $discount_price = get_post_meta( get_the_ID(), '_sale_price', true );

    if(class_exists("WC_Dynamic_Pricing")){
        $discount_price = apply_filters("woocommerce_product_get_price", $base_price, $product, false);
    }

    if($product->is_type( 'variable' )){
        $variations = $product->get_available_variations();
        if(isset($variations[0])){
            $variation = $variations[0];

            $base_price = $variation["display_regular_price"];
            $discount_price = $variation["display_price"];
        }
    }

    $percentage = round( ( ( floatval($base_price) - floatval($discount_price) ) / floatval($base_price) ) * 100 );

    $sale_style = wdb_get_styles($wdb_settings, $percentage);
    $sale_tag = "<span class='onsale wdb_on_sale badge callout badge-circle' style='$sale_style'>-" . $percentage . "%</span>";

    $hide_sale_tag = isset($wdb_settings['wdb_hide_default_sale_tag']) && $wdb_settings['wdb_hide_default_sale_tag'] === "true" ? true : false;

    if($percentage > 0){
        if($hide_sale_tag){
            if($wdb_using_flatsome){
                wpd_custom_badge_hide_woo_sales_badge_on_page(true);
                $content .= $sale_tag;
            } else {
                $content = $sale_tag;
            }
        } else {
            $content .= $sale_tag;
        }
    }

    //$content = apply_filters("wdb_discount_badge_internal_filter", $content, $product, $hide_sale_tag, $wdb_settings);

    return $content;
}

function wdb_custom_badge_shop_loop_opened_flatsome($content){
    $content .= wdb_custom_badge_shop_loop_opened(true);
    return $content;
}

function wdb_custom_badge_shop_loop_opened($return = false){
    if(class_exists("WC_Product_Factory")){
        $_pf = new WC_Product_Factory();
        $product = $_pf->get_product(get_the_ID());

        $base_price = get_post_meta( get_the_ID(), '_regular_price', true );
        $discount_price = get_post_meta( get_the_ID(), '_sale_price', true );

        if(class_exists("WC_Dynamic_Pricing")){
            $discount_price = apply_filters("woocommerce_product_get_price", $base_price, $product, false);
        }

        $is_on_sale = true;
        if(!isset($discount_price) || floatval($base_price) === floatval($discount_price)){
            $is_on_sale = false;
        }

        $wdb_settings = wbd_get_settings();
        $hide_sale_tag = isset($wdb_settings['wdb_hide_default_sale_tag']) && $wdb_settings['wdb_hide_default_sale_tag'] === "true" ? true : false;

        $hide_sale_tag = $is_on_sale !== false ? $hide_sale_tag : false;

        $addition = apply_filters("wdb_discount_badge_internal_filter", "", $product, $hide_sale_tag, $wdb_settings, $is_on_sale);

        if($return){
            return $addition;
        } else {
            echo $addition;
        }
    }
}

function wdb_get_styles($wdb_settings, $percentage){
    global $wdb_has_hidden_sales_tag, $wdb_using_flatsome;
    $styles = "";
    if($wdb_using_flatsome){
        $styles .= "display:table;";
    } else {
        if($wdb_has_hidden_sales_tag){
            $styles .= "top: -.5em; position: absolute;";
        } else {
            if(isset($wdb_settings['wdb_hide_default_sale_tag']) && $wdb_settings['wdb_hide_default_sale_tag'] === "true"){
                $styles .= "top: -.5em; position: absolute;";
            } else {
                $styles .= "top:3.5em; position: absolute;";
            }
        }
    }

    if($percentage <= 10){
        $styles .= "background:" . $wdb_settings['wdb_10_color'] . ";" ;
    } else if ($percentage > 10 && $percentage <= 20){
        $styles .= "background:" . $wdb_settings['wdb_0_20_color'] . ";" ;
    } else if ($percentage > 20 && $percentage <= 30){
        $styles .= "background:" . $wdb_settings['wdb_30_color'] . ";" ;
    } else if($percentage > 30 && $percentage <= 40){
        $styles .= "background:" . $wdb_settings['wdb_20_40_color'] . ";" ;
    } else if ($percentage > 40 && $percentage <= 50){
        $styles .= "background:" . $wdb_settings['wdb_50_color'] . ";" ;
    } else if($percentage > 50 && $percentage <= 60){
        $styles .= "background:" . $wdb_settings['wdb_40_60_color'] . ";" ;
    } else if ($percentage > 60 && $percentage <= 70){
        $styles .= "background:" . $wdb_settings['wdb_70_color'] . ";" ;
    } else if($percentage > 70 && $percentage <= 80){
        $styles .= "background:" . $wdb_settings['wdb_60_80_color'] . ";" ;
    } else if ($percentage > 80 && $percentage <= 90){
        $styles .= "background:" . $wdb_settings['wdb_80_100_color'] . ";" ;
    } else if($percentage > 90){
        $styles .= "background:" . $wdb_settings['wdb_90_color'] . ";" ;
    }

    if(isset($wdb_settings['wdb_text_color'])){
        $styles .= "color:" . $wdb_settings['wdb_text_color'] . ";" ;
    } else {
         $styles .= "color:#FFFFFF;" ;
    }

    //Force default woo styles incase
    $forced_styles = "min-height: 50px; min-width: 50px; max-height: 50px; border-radius:50px; font-weight: 700; text-align: center; line-height: 2.9em; font-size: .857em; -webkit-font-smoothing: antialiased;z-index: 9; width: inherit;";
    $styles .= $forced_styles;

    $styles = apply_filters("wdb_styles_content", $styles);

    return $styles;
}

function wdb_add_custom_badge_frontend($content, $product, $hide_sale_tag, $wdb_settings, $is_on_sale){
    global $wdb_has_hidden_sales_tag;

    $categories = get_the_terms( get_the_ID(), 'product_cat' );

    if ( isset( $categories ) ) {

        if ( is_array( $categories ) && count( $categories ) > 0 ) {

            $increment = 0;

            foreach( $categories as $key => $value ) {
                $cat_id = $value->term_id;
                $matches = wdb_custom_badge_get_matches_for_category( $cat_id );
                if ( false !== $matches ) {
                    foreach ( $matches as $match_key => $match ) { 
                        if($wdb_has_hidden_sales_tag){
                            $hide_sale_tag = true;
                        }
                        $custom_style = wdb_custom_badge_frontend_styles($match, $hide_sale_tag, $wdb_settings, $increment, $is_on_sale);
                        $custom_tag = "<span class='onsale wdb_on_sale badge callout badge-circle' style='$custom_style'>";
                        $custom_tag .= "<span style='padding-left: 3px; padding-right: 3px;'>" . $match->content . "</span>";
                        $custom_tag .= "</span>";
                        $content .= $custom_tag;
                        $increment++;
                    }
                }
            }
        }
    }

    return $content;
}
add_filter("wdb_discount_badge_internal_filter", "wdb_add_custom_badge_frontend", 10, 5);

function wpd_custom_badge_hide_woo_sales_badge_on_page($force_hide = false){
    global $wdb_has_hidden_sales_tag, $wdb_using_flatsome;
    $wdb_global_options = wdb_custom_badge_get_global_options();
    $should_hide = isset($wdb_global_options["wdb_hide_default_sale_tag"]) && $wdb_global_options["wdb_hide_default_sale_tag"] === "true" ? true : false;

    if($should_hide || $force_hide){
        if(!$wdb_has_hidden_sales_tag){
            if($wdb_using_flatsome){
                ?>
                <style>
                     .badge-container .badge:first-child{
                        display:none;
                     }

                     .callout{
                        display:none;
                     }
                </style>
                <?php
            } else {
                ?>
                <style>
                     span[class=onsale]:first-child{
                        display:none;
                     }
                </style>
                <?php
            }
            $wdb_has_hidden_sales_tag = true;
        }
    }

}

function wdb_custom_badge_frontend_styles($badge_data, $hide_sale_tag, $wdb_settings, $increment, $is_on_sale){
    global $wdb_using_flatsome;
    $styles = "";
    $top = 0.0;
    if($hide_sale_tag){
        $top = 3.5;
    } else {
        $top = 7.5;
    }

    if($is_on_sale === false){
        $top = 0.0;
    } else {
        $top += 4.0;
    }

    $top += $increment * 4.0;

    if($wdb_using_flatsome){
        $styles .= "display:table;";
    } else {
        $styles .= "top: " . $top . "em; position: absolute; "; //Only add if not using flatsome
    }

    $styles .= "background:" . $badge_data->bg_color . ";" ;
    $styles .= "color:" . $badge_data->color . ";" ;

    //Force default woo styles incase
    $forced_styles = "min-height: 50px; min-width: 50px; max-height: 50px; border-radius:50px; font-weight: 700; text-align: center; line-height: 2.9em; font-size: .857em; -webkit-font-smoothing: antialiased;z-index: 9; width: inherit;";
    $styles .= $forced_styles;

    $styles = apply_filters("wdb_custom_badge_styles_content", $styles);

    return $styles;
}

function wdb_admin_menu() {
    $show_in_menu = current_user_can( 'manage_woocommerce' ) ? 'woocommerce' : false;
    $discount_slug = add_submenu_page( $show_in_menu, __( 'Discount Badges' ), __( 'Discount Badges' ), 'manage_woocommerce', 'wdb_primary_settings', "wdb_setting_area");
    $custom_slug = add_submenu_page( $show_in_menu, __( 'Custom Badges' ), __( 'Custom Badges' ), 'manage_woocommerce', 'wdb_custom_badges', "wdb_custom_badges");

    do_action("wdb_admin_menu_below");
}
add_action("admin_menu", "wdb_admin_menu", 99);

function wbd_get_settings(){
    $wdb_settings = get_option("wdb_primary_settings", false);
    if($wdb_settings === false){
        //Setup defaults
        $wdb_settings = array();
        $wdb_settings['wdb_hide_default_sale_tag'] = "false";

        $wdb_settings['wdb_0_20_color'] = "#ED1C24";
        $wdb_settings['wdb_20_40_color'] = "#ED1C24";
        $wdb_settings['wdb_40_60_color'] = "#ED1C24";
        $wdb_settings['wdb_60_80_color'] = "#ED1C24";
        $wdb_settings['wdb_80_100_color'] = "#ED1C24";

        //added increments
        $wdb_settings['wdb_10_color'] = "#ED1C24";
        $wdb_settings['wdb_30_color'] = "#ED1C24";
        $wdb_settings['wdb_50_color'] = "#ED1C24";
        $wdb_settings['wdb_70_color'] = "#ED1C24";
        $wdb_settings['wdb_90_color'] = "#ED1C24";

        $wdb_settings['wdb_text_color'] = "#FFFFFF";

        $wdb_settings = apply_filters("wdb_default_settings_array", $wdb_settings);
    } else {
        $wdb_settings = maybe_unserialize($wdb_settings);
    }
    return $wdb_settings;
}

function wdb_setting_area(){
    wdb_save_settings_head();
    $wdb_settings = wbd_get_settings();

    ?>
    <div class="wrap">
        <h3><?php _e("Discount Badges - Settings"); ?></h3>
        <form method="POST" action="">
            <table class="widefat striped">

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
                        <input type="checkbox" value="<?php echo ( isset($wdb_settings["wdb_hide_default_sale_tag"]) && $wdb_settings["wdb_hide_default_sale_tag"] === "true" ? "true" : "" ); ?>" <?php echo ( isset($wdb_settings["wdb_hide_default_sale_tag"]) && $wdb_settings["wdb_hide_default_sale_tag"] === "true" ? "checked='checked'" : "" ); ?> name="wdb_hide_default_sale_tag" id="wdb_hide_default_sale_tag">
                    </td>
                </tr>
            </table>

            <br>

            <br>

            <table class="widefat striped">
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
                        <input type="color" value="<?php echo ( isset($wdb_settings['wdb_10_color']) ? $wdb_settings['wdb_10_color'] : "#ED1C24" ); ?>" name="wdb_10_color" id="wdb_10_color">
                    </td>
                </tr>

                <tr>
                    <td>
                        <label><?php _e("10-20% Off Color"); ?>:</label>
                    </td>
                    <td>
                        <input type="color" value="<?php echo ( isset($wdb_settings['wdb_0_20_color']) ? $wdb_settings['wdb_0_20_color'] : "#ED1C24" ); ?>" name="wdb_0_20_color" id="wdb_0_20_color">
                    </td>
                </tr>

                <tr>
                    <td>
                        <label><?php _e("20-30% Off Color"); ?>:</label>
                    </td>
                    <td>
                        <input type="color" value="<?php echo ( isset($wdb_settings['wdb_30_color']) ? $wdb_settings['wdb_30_color'] : "#ED1C24" ); ?>" name="wdb_30_color" id="wdb_30_color">
                    </td>
                </tr>

                <tr>
                    <td>
                        <label><?php _e("30-40% Off Color"); ?>:</label>
                    </td>
                    <td>
                        <input type="color" value="<?php echo ( isset($wdb_settings['wdb_20_40_color']) ? $wdb_settings['wdb_20_40_color'] : "#ED1C24" ); ?>" name="wdb_20_40_color" id="wdb_20_40_color">
                    </td>
                </tr>

                <tr>
                    <td>
                        <label><?php _e("40-50% Off Color"); ?>:</label>
                    </td>
                    <td>
                        <input type="color" value="<?php echo ( isset($wdb_settings['wdb_50_color']) ? $wdb_settings['wdb_50_color'] : "#ED1C24" ); ?>" name="wdb_50_color" id="wdb_50_color">
                    </td>
                </tr>

                <tr>
                    <td>
                        <label><?php _e("50-60% Off Color"); ?>:</label>
                    </td>
                    <td>
                        <input type="color" value="<?php echo ( isset($wdb_settings['wdb_40_60_color']) ? $wdb_settings['wdb_40_60_color'] : "#ED1C24" ); ?>" name="wdb_40_60_color" id="wdb_40_60_color">
                    </td>
                </tr>

                <tr>
                    <td>
                        <label><?php _e("60-70% Off Color"); ?>:</label>
                    </td>
                    <td>
                        <input type="color" value="<?php echo ( isset($wdb_settings['wdb_70_color']) ? $wdb_settings['wdb_70_color'] : "#ED1C24" ); ?>" name="wdb_70_color" id="wdb_70_color">
                    </td>
                </tr>

                <tr>
                    <td>
                        <label><?php _e("70-80% Off Color"); ?>:</label>
                    </td>
                    <td>
                        <input type="color" value="<?php echo ( isset($wdb_settings['wdb_60_80_color']) ? $wdb_settings['wdb_60_80_color'] : "#ED1C24" ); ?>" name="wdb_60_80_color" id="wdb_60_80_color">
                    </td>
                </tr>



                <tr>
                    <td>
                        <label><?php _e("80-90% Off Color"); ?>:</label>
                    </td>
                    <td>
                        <input type="color" value="<?php echo ( isset($wdb_settings['wdb_80_100_color']) ? $wdb_settings['wdb_80_100_color'] : "#ED1C24" ); ?>" name="wdb_80_100_color" id="wdb_80_100_color">
                    </td>
                </tr>

                <tr>
                    <td>
                        <label><?php _e("90-100% Off Color"); ?>:</label>
                    </td>
                    <td>
                        <input type="color" value="<?php echo ( isset($wdb_settings['wdb_90_color']) ? $wdb_settings['wdb_90_color'] : "#ED1C24" ); ?>" name="wdb_90_color" id="wdb_90_color">
                    </td>
                </tr>

                <tr>
                    <td>
                        <label><?php _e("Text Color"); ?>:</label>
                    </td>
                    <td>
                        <input type="color" value="<?php echo ( isset($wdb_settings['wdb_text_color']) ? $wdb_settings['wdb_text_color'] : "#FFFFFF" ); ?>" name="wdb_text_color" id="wdb_text_color">
                    </td>
                </tr>
            </table>

            <br>

            <br>
            <?php
                do_action("wdb_settings_area_below_hook", $wdb_settings);
            ?>

            <br>

            <br>

            <input type="submit" class="button-primary" value="<?php _e("Save"); ?>" name="wdb_save_settings">
        </form>
    </div>
    <?php
}

function wdb_save_settings_head(){
    if(isset($_POST['wdb_save_settings'])){
        //User is trying to save
        $wdb_settings_array = array();
        if(isset($_POST['wdb_hide_default_sale_tag'])){
            $wdb_settings_array['wdb_hide_default_sale_tag'] = "true";
        } else {
            $wdb_settings_array['wdb_hide_default_sale_tag'] = "false";
        }

        if(isset($_POST['wdb_0_20_color'])){
            $wdb_settings_array['wdb_0_20_color'] = esc_attr($_POST['wdb_0_20_color']);
        } else {
            $wdb_settings_array['wdb_0_20_color'] = "#ED1C24";
        }

        if(isset($_POST['wdb_20_40_color'])){
            $wdb_settings_array['wdb_20_40_color'] = esc_attr($_POST['wdb_20_40_color']);
        } else {
            $wdb_settings_array['wdb_20_40_color'] = "#ED1C24";
        }

        if(isset($_POST['wdb_40_60_color'])){
            $wdb_settings_array['wdb_40_60_color'] = esc_attr($_POST['wdb_40_60_color']);
        } else {
            $wdb_settings_array['wdb_40_60_color'] = "#ED1C24";
        }

        if(isset($_POST['wdb_60_80_color'])){
            $wdb_settings_array['wdb_60_80_color'] = esc_attr($_POST['wdb_60_80_color']);
        } else {
            $wdb_settings_array['wdb_60_80_color'] = "#ED1C24";
        }

        if(isset($_POST['wdb_80_100_color'])){
            $wdb_settings_array['wdb_80_100_color'] = esc_attr($_POST['wdb_80_100_color']);
        } else {
            $wdb_settings_array['wdb_80_100_color'] = "#ED1C24";
        }

        //Added increments
        if(isset($_POST['wdb_10_color'])){$wdb_settings_array['wdb_10_color'] = esc_attr($_POST['wdb_10_color']);} else {$wdb_settings_array['wdb_10_color'] = "#ED1C24";}
        if(isset($_POST['wdb_30_color'])){$wdb_settings_array['wdb_30_color'] = esc_attr($_POST['wdb_30_color']);} else {$wdb_settings_array['wdb_30_color'] = "#ED1C24";}
        if(isset($_POST['wdb_50_color'])){$wdb_settings_array['wdb_50_color'] = esc_attr($_POST['wdb_50_color']);} else {$wdb_settings_array['wdb_50_color'] = "#ED1C24";}
        if(isset($_POST['wdb_70_color'])){$wdb_settings_array['wdb_70_color'] = esc_attr($_POST['wdb_70_color']);} else {$wdb_settings_array['wdb_70_color'] = "#ED1C24";}
        if(isset($_POST['wdb_90_color'])){$wdb_settings_array['wdb_90_color'] = esc_attr($_POST['wdb_90_color']);} else {$wdb_settings_array['wdb_90_color'] = "#ED1C24";}

        if(isset($_POST['wdb_text_color'])){
            $wdb_settings_array['wdb_text_color'] = esc_attr($_POST['wdb_text_color']);
        } else {
            $wdb_settings_array['wdb_text_color'] = "#FFFFFF";
        }

        $wdb_settings_array = apply_filters("wdb_save_settings_array_filter", $wdb_settings_array);

        update_option("wdb_primary_settings", maybe_serialize($wdb_settings_array));
    }
}

function wdb_custom_badges(){
    ?>
    <div class="wrap">
        <h3><?php _e("Custom Badges"); ?> </h3>
        <?php wdb_custom_badges_check_header(); wdb_custom_badges_action_check(); wdb_custom_badges_table(); ?>
        <br>
        <a href="admin.php?page=wdb_custom_badges&action=new" class="button button-primary"><?php _e("Add New"); ?></a>
    </div>
    <?php
}

function wdb_custom_badges_check_header(){
    if(isset($_POST['wdb_custom_badge_new_add'])){
        //User wants to add a badge
        wdb_custom_badge_add_new_insert();
    }

    if(isset($_POST['wdb_custom_badge_edit'])){
        //User wants to edit a badge
        wdb_custom_badge_edit_update();
    }

    wdb_custom_badge_global_options();

}

function wdb_custom_badge_global_options(){
    wdb_custom_badge_global_options_headers();
    $wdb_global_options = wdb_custom_badge_get_global_options();

    ?>
    <form method="POST" action="">
        <table class="widefat striped">

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
                    <input type="checkbox" value="<?php echo ( isset($wdb_global_options["wdb_hide_default_sale_tag"]) && $wdb_global_options["wdb_hide_default_sale_tag"] === "true" ? "true" : "" ); ?>" <?php echo ( isset($wdb_global_options["wdb_hide_default_sale_tag"]) && $wdb_global_options["wdb_hide_default_sale_tag"] === "true" ? "checked='checked'" : "" ); ?> name="wdb_hide_default_sale_tag" id="wdb_hide_default_sale_tag"> <small><?php _e("Hides default sales tag on the page"); ?></small>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="wdb_save_global_options" class="button button-primary" value="<?php _e("Save Global Options"); ?>" type="submit">
                </td>
                <td></td>
            </tr>
        </table>
        <br>
    </form>
    <?php
}

function wdb_custom_badge_global_options_headers(){
    if(isset($_POST['wdb_save_global_options'])){
        //User is trying to save
        $wdb_settings_array = array();
        if(isset($_POST['wdb_hide_default_sale_tag'])){
            $wdb_settings_array['wdb_hide_default_sale_tag'] = "true";
        } else {
            $wdb_settings_array['wdb_hide_default_sale_tag'] = "false";
        }

        $wdb_settings_array = apply_filters("wdb_save_global_options_array_filter", $wdb_settings_array);

        update_option("wdb_custom_badge_global_options", maybe_serialize($wdb_settings_array));
    }
}

function wdb_custom_badge_get_global_options(){
    $wdb_settings = get_option("wdb_custom_badge_global_options", false);
    if($wdb_settings === false){
        //Setup defaults
        $wdb_settings = array();
        $wdb_settings['wdb_hide_default_sale_tag'] = "false";

        $wdb_settings = apply_filters("wdb_custom_badge_global_options_array", $wdb_settings);
    } else {
        $wdb_settings = maybe_unserialize($wdb_settings);
    }
    return $wdb_settings;
}

function wdb_custom_badges_action_check(){
    if(isset($_GET['action'])){
        if($_GET['action'] === "new"){
            wdb_custom_action_add_new_form();
        }
        if($_GET['action'] === "edit" && isset($_GET['id'])){
            $badge_id = intval($_GET['id']);
            wdb_custom_action_edit_form($badge_id);
        }
        if($_GET['action'] === "delete" && isset($_GET['id'])){
            $badge_id = intval($_GET['id']);
            wdb_custom_action_delete_prompt($badge_id);
        }
        if($_GET['action'] === "confirm_delete" && isset($_GET['id'])){
            $badge_id = intval($_GET['id']);
            wdb_custom_action_delete_confirm($badge_id);
        }


    }
}

function wdb_custom_action_add_new_form(){
    ?>
    <form method="POST" action="">
        <table class="widefat striped">
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
                    <input name="wdb_custom_badge_new_name" type="text" style="width:70%;" placeholder="<?php _e("This is only for identification, will not be used in the badge"); ?>" <?php echo (isset($_POST['wdb_custom_badge_new_name']) ? "value='" . $_POST["wdb_custom_badge_new_name"] . "'" : ""); ?> >
                </td>
            </tr>

            <tr>
                <td>
                    <label><?php _e("Category"); ?>:</label>
                </td>
                <td>
                    <?php wdb_get_woo_categories_select("wdb_custom_badge_new_cat", (isset($_POST['wdb_custom_badge_new_cat']) ? $_POST['wdb_custom_badge_new_cat'] : false) , "width:70%;")?>
                </td>
            </tr>

             <tr>
                <td>
                    <label><?php _e("Content"); ?>:</label>
                </td>
                <td>
                    <input name="wdb_custom_badge_new_content" type="text"  id="wdb_custom_preview_content" style="width:70%;" placeholder="<?php _e("Shown in your badge"); ?>" <?php echo (isset($_POST['wdb_custom_badge_new_content']) ? "value='" . $_POST["wdb_custom_badge_new_content"] . "'" : ""); ?> >
                </td>
            </tr>

            <tr>
                <td>
                    <label><?php _e("Text Color"); ?>:</label>
                </td>
                <td>
                    <input name="wdb_custom_badge_new_text_color" type="color" id="wdb_custom_preview_color" <?php echo (isset($_POST['wdb_custom_badge_new_text_color']) ? "value='" . $_POST["wdb_custom_badge_new_text_color"] . "'" : ""); ?>>
                </td>
            </tr>

            <tr>
                <td>
                    <label><?php _e("Badge Color"); ?>:</label>
                </td>
                <td>
                    <input name="wdb_custom_badge_new_badge_color" type="color" id="wdb_custom_preview_bg_color" <?php echo (isset($_POST['wdb_custom_badge_new_badge_color']) ? "value='" . $_POST["wdb_custom_badge_new_badge_color"] . "'" : ""); ?>>
                </td>
            </tr>

            <tr>
                <td>
                    <label><?php _e("Preview"); ?>:</label>
                </td>
                <td>
                    <?php wdb_custom_badge_previewer("#wdb_custom_preview_content", "#wdb_custom_preview_color", "#wdb_custom_preview_bg_color"); ?>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="wdb_custom_badge_new_add" class="button button-primary" value="<?php _e("Add Badge"); ?>" type="submit">
                    <a href="admin.php?page=wdb_custom_badges" class="button"><?php _e("Close"); ?></a>
                </td>
                <td></td>
            </tr>
        </table>
    </form>
    <br>
    <?php
}

function wdb_custom_action_edit_form($badge_id){
    $badge_data = wdb_custom_badge_get_data($badge_id);
    if($badge_data === false){ return false; }
    ?>
    <form method="POST" action="">
        <input name="wdb_custom_badge_edit_id" type="hidden" value="<?php echo $badge_data->id; ?>" >
        <table class="widefat striped">
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
                    <input name="wdb_custom_badge_edit_name" type="text" style="width:70%;" placeholder="<?php _e("This is only for identification, will not be used in the badge"); ?>" value="<?php echo $badge_data->name; ?>" >
                </td>
            </tr>

            <tr>
                <td>
                    <label><?php _e("Category"); ?>:</label>
                </td>
                <td>
                    <?php wdb_get_woo_categories_select("wdb_custom_badge_edit_cat", $badge_data->cat_id , "width:70%;")?>
                </td>
            </tr>

             <tr>
                <td>
                    <label><?php _e("Content"); ?>:</label>
                </td>
                <td>
                    <input name="wdb_custom_badge_edit_content" id="wdb_custom_preview_content" type="text" style="width:70%;" placeholder="<?php _e("Shown in your badge"); ?>" value="<?php echo $badge_data->content; ?>" >
                </td>
            </tr>

            <tr>
                <td>
                    <label><?php _e("Text Color"); ?>:</label>
                </td>
                <td>
                    <input name="wdb_custom_badge_edit_text_color" id="wdb_custom_preview_color" type="color" value="<?php echo $badge_data->color; ?>" >
                </td>
            </tr>

            <tr>
                <td>
                    <label><?php _e("Badge Color"); ?>:</label>
                </td>
                <td>
                    <input name="wdb_custom_badge_edit_badge_color" id="wdb_custom_preview_bg_color" type="color" value="<?php echo $badge_data->bg_color; ?>" >
                </td>
            </tr>

            <tr>
                <td>
                    <label><?php _e("Preview"); ?>:</label>
                </td>
                <td>
                    <?php wdb_custom_badge_previewer("#wdb_custom_preview_content", "#wdb_custom_preview_color", "#wdb_custom_preview_bg_color"); ?>
                </td>
            </tr>

            <tr>
                <td>
                    <input name="wdb_custom_badge_edit" class="button button-primary" value="<?php _e("Edit Badge"); ?>" type="submit">
                    <a href="admin.php?page=wdb_custom_badges" class="button"><?php _e("Close"); ?></a>
                </td>
                <td></td>
            </tr>
        </table>
    </form>
    <br>

    <script>
    jQuery(function(){
        jQuery("input[name=wdb_custom_badge_edit_name],input[name=wdb_custom_badge_edit_cat],input[name=wdb_custom_badge_edit_content],input[name=wdb_custom_badge_edit_text_color],input[name=wdb_custom_badge_edit_badge_color]").on("change", function(){
            jQuery("input[name=wdb_custom_badge_edit]").val("<?php _e('Save Changes'); ?>");
        });
    });
    </script>
    <?php
}

function wdb_custom_action_delete_prompt($badge_id){
    ?>
    <table class="widefat striped">
        <tr>
            <td style="width:30%">
                <strong><?php _e("Are you sure you want to delete this badge?"); ?></strong>
            </td>
            <td style="width:70%"></td>
        </tr>
        <tr>
            <td>
                <a href="admin.php?page=wdb_custom_badges&action=confirm_delete&id=<?php echo $badge_id; ?>" class="button"><?php _e("Confirm"); ?></a>
                <a href="admin.php?page=wdb_custom_badges" class="button"><?php _e("Cancel"); ?></a>
            </td>
            <td></td>
        </tr>
    </table>
    <br>
    <?php
}

function wdb_custom_action_delete_confirm($badge_id){
    global $wdb_custom_badges_table, $wpdb;

    $wpdb->query("DELETE FROM `$wdb_custom_badges_table` WHERE `id` = '$badge_id'");

    echo "<div class='error'><p>".__("Badge Deleted") . "</p></div>";
}

function wdb_get_woo_categories_select($name, $selected_val = false, $styles = false){
    $categories = wdb_get_woo_categories();
    if($selected_val !== false){ $selected_val = intval($selected_val); }
    if($categories !== false){
        echo "<select name='$name' ".($selected_val !== false ? "value='$selected_val'" : "")." ".($styles !== false ? "style='$styles'" : "").">";
        foreach ($categories as $cat) {
            echo "<option value='" . $cat->term_id . "' ".($selected_val !== false && $selected_val === $cat->term_id ? "selected" : "")." >" . $cat->name . "</option>";
        }
        echo "</select>";
    }
}

function wdb_custom_badge_previewer($badge_title_element, $badge_color_element, $badge_bg_color_element){
    ?>
    <span id="badge_preview" style="display:inline-block;min-width:50px;min-height:50px;max-height: 50px;border-radius:50px;">
        <span style="display: block;text-align: center;line-height: 47px; padding-left:10px; padding-right:10px;" id="badge_preview_content"></span>
    </span>
    <br>
    <small><?php _e("Preview results may vary, as site styling may influence results"); ?></small>
    <script>
        jQuery(function(){
            jQuery(document).ready(function(){
                jQuery("#badge_preview_content").text(jQuery("<?php echo $badge_title_element?>").val());
                jQuery("#badge_preview").css("background", jQuery("<?php echo $badge_bg_color_element?>").val());
                jQuery("#badge_preview").css("color", jQuery("<?php echo $badge_color_element?>").val());

                jQuery("<?php echo $badge_title_element?>").on("keyup", function(){
                    jQuery("#badge_preview_content").text(jQuery("<?php echo $badge_title_element?>").val());
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

function wdb_get_woo_categories(){
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

function wdb_custom_badges_table(){

    $custom_badges = wdb_get_all_custom_badges();

    echo '<table class="widefat striped">';

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

            echo "<a href='admin.php?page=wdb_custom_badges&action=edit&id=".$badge->id."' class='button'>" . __("Edit") . "</a> ";
            echo "<a href='admin.php?page=wdb_custom_badges&action=delete&id=".$badge->id."' class='button'>" . __("Delete") . "</a>";

            echo "</td>";
            echo "</tr>";
        }

    } else {
        echo "<tr><td>" . __("No Results Found...") . "</td><td></td></tr>";
    }

    echo '</table>';
}

function wdb_get_all_custom_badges(){
    global $wdb_custom_badges_table, $wpdb;
    $results = $wpdb->get_results( "SELECT * FROM $wdb_custom_badges_table" );

    if(count($results) > 0){
        return $results;
    }

    return false;
}

function wdb_custom_badge_get_data($badge_id){
    global $wdb_custom_badges_table, $wpdb;
    $results = $wpdb->get_results( "SELECT * FROM $wdb_custom_badges_table WHERE `id` = '$badge_id' LIMIT 1" );

    if(count($results) > 0){
        return $results[0];
    }

    return false;
}

function wdb_custom_badge_get_matches_for_category($cat_id){
    global $wdb_custom_badges_table, $wpdb;
    $results = $wpdb->get_results( "SELECT * FROM $wdb_custom_badges_table WHERE `cat_id` = '$cat_id'" );

    if(count($results) > 0){
        return $results;
    }

    return false;
}

function wdb_custom_badge_add_new_insert(){
    global $wdb_custom_badges_table, $wpdb;
    if(isset($_POST['wdb_custom_badge_new_add'])){
        $new_badge_name = isset($_POST['wdb_custom_badge_new_name']) ? esc_attr($_POST['wdb_custom_badge_new_name']) : false;
        $new_badge_cat = isset($_POST['wdb_custom_badge_new_cat']) ? intval($_POST['wdb_custom_badge_new_cat']) : false;
        $new_badge_content = isset($_POST['wdb_custom_badge_new_content']) ? esc_attr($_POST['wdb_custom_badge_new_content']) : false;
        $new_badge_text_col = isset($_POST['wdb_custom_badge_new_text_color']) ? esc_attr($_POST['wdb_custom_badge_new_text_color']) : false;
        $new_badge_badge_color = isset($_POST['wdb_custom_badge_new_badge_color']) ? esc_attr($_POST['wdb_custom_badge_new_badge_color']) : false;

        $error_found = false;
        if(!empty($new_badge_name) && $new_badge_name !== false){
            if($new_badge_cat !== false){
                if(!empty($new_badge_content) && $new_badge_content !== false){
                    if(!empty($new_badge_text_col) && $new_badge_text_col !== false){
                        if(!empty($new_badge_badge_color) && $new_badge_badge_color !== false){
                            $insert_custom = $wpdb->query(
                                "INSERT INTO `$wdb_custom_badges_table` SET
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

function wdb_custom_badge_edit_update(){
    global $wdb_custom_badges_table, $wpdb;
    if(isset($_POST['wdb_custom_badge_edit'])){
        $new_badge_id = isset($_POST['wdb_custom_badge_edit_id']) ? esc_attr($_POST['wdb_custom_badge_edit_id']) : false;
        $new_badge_name = isset($_POST['wdb_custom_badge_edit_name']) ? esc_attr($_POST['wdb_custom_badge_edit_name']) : false;
        $new_badge_cat = isset($_POST['wdb_custom_badge_edit_cat']) ? intval($_POST['wdb_custom_badge_edit_cat']) : false;
        $new_badge_content = isset($_POST['wdb_custom_badge_edit_content']) ? esc_attr($_POST['wdb_custom_badge_edit_content']) : false;
        $new_badge_text_col = isset($_POST['wdb_custom_badge_edit_text_color']) ? esc_attr($_POST['wdb_custom_badge_edit_text_color']) : false;
        $new_badge_badge_color = isset($_POST['wdb_custom_badge_edit_badge_color']) ? esc_attr($_POST['wdb_custom_badge_edit_badge_color']) : false;

        $error_found = false;
        if(!empty($new_badge_name) && $new_badge_name !== false){
            if($new_badge_cat !== false){
                if(!empty($new_badge_content) && $new_badge_content !== false){
                    if(!empty($new_badge_text_col) && $new_badge_text_col !== false){
                        if(!empty($new_badge_badge_color) && $new_badge_badge_color !== false){
                            $insert_custom = $wpdb->query(
                                "UPDATE `$wdb_custom_badges_table` SET
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

