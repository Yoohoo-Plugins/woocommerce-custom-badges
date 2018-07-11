<?php
/**
 * Frontend scripts
*/

/**
 * Style Enqueue
*/
function ycb_frontend_enqueue($hook) {
    if(is_admin()){
        return;
    }
    wp_enqueue_style( 'ycb_frontend_css', plugin_dir_url(__FILE__) . '../css/frontend.css' );
    wp_enqueue_style( 'ycb_frontend_fa_css', plugin_dir_url(__FILE__) . '../css/fa.css' );
    wp_enqueue_style( 'ycb_frontend_emoji_css', plugin_dir_url(__FILE__) . '../css/emoji.css' );


    $sale_tag_overrides = ycb_get_default_sales_tag_style_overrides();
    wp_add_inline_style( 'ycb_frontend_css', $sale_tag_overrides );
}
add_action( 'wp_enqueue_scripts', 'ycb_frontend_enqueue' );

/**
 * Register flatsome specific hooks
*/
function ycb_setup_flatsome_hooks(){
    add_filter("woocommerce_sale_flash", "ycb_add_discount_badge", 1, 3);

    add_action('woocommerce_before_shop_loop_item', 'ycb_custom_badge_shop_loop_opened', 15 );
    add_action('woocommerce_before_single_product_summary', 'ycb_custom_badge_shop_loop_opened', 15);
}

/**
 * Register standard hooks
*/
function ycb_setup_standard_hooks(){
    add_filter("woocommerce_sale_flash", "ycb_add_discount_badge", 1, 3);

    add_action('woocommerce_before_shop_loop_item', 'ycb_custom_badge_shop_loop_opened', 15 );
    add_action('woocommerce_before_single_product_summary', 'ycb_custom_badge_shop_loop_opened', 15);
}

/**
 * Determines what badge selection has been made, and overrides the styles for the woo sale badge
*/
function ycb_get_default_sales_tag_style_overrides(){
    global $ycb_using_flatsome;
    $ycb_settings = ycb_get_settings();
    $shape_setting = isset($ycb_settings["ycb_badge_shape"]) ? intval($ycb_settings["ycb_badge_shape"]) : false;

    $badge_border = '100px';
    switch ($shape_setting) {
        case 1:
            $badge_border = '0';
            break;
        case 2:
            $badge_border = '5px';
            break;
    }

    $storefront_secific = "";
    if($ycb_using_flatsome){
        $storefront_secific = "position: absolute !important; top: 0; left: 0;";
    }

    $overrides = "
            .woocommerce span.onsale{
                $storefront_secific
                border-radius: {$badge_border};
                min-height: 50px !important;
                min-width: 50px !important;
                max-height: 50px !important;
                font-weight: 700 !important;
                position: absolute !important;
                text-align: center !important;
                line-height: 44px !important;
                font-size: .857em !important;
                -webkit-font-smoothing: antialiased;
                z-index: 9;
            }";

    return $overrides;
}

/**
 * Adds a discount badge using the woo sale hook
 * Adds extension filter
*/
function ycb_add_discount_badge($content, $post, $product){
    global $ycb_using_flatsome;

    $ycb_settings = ycb_get_settings();
    $base_price = $product->regular_price;
    $discount_price = $product->sale_price;

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

    $percentage = round( ( ( floatval($base_price) - floatval($discount_price) ) / (floatval($base_price) > 0 ? floatval($base_price) : 1) ) * 100 );

    $sale_style = ycb_get_styles($ycb_settings, $percentage);
    $sale_tag = "<span class='onsale ycb_on_sale yoohoo_badge ".ycb_get_badge_shape_class(false, $ycb_settings)."' style='$sale_style'>-" . $percentage . "%</span>";

    $hide_sale_tag = isset($ycb_settings['ycb_hide_default_sale_tag']) && $ycb_settings['ycb_hide_default_sale_tag'] === "true" ? true : false;

    if($hide_sale_tag){
        $content = $sale_tag;
    } else {
        $content .= $sale_tag;
    }

    if($percentage > 0){
        if($hide_sale_tag){
            if($ycb_using_flatsome){
                ycb_custom_badge_hide_woo_sales_badge_on_page(true);
                $content .= $sale_tag;
            } else {
                $content = $sale_tag;
            }
        } else {
            $content .= $sale_tag;
        }
    }

    //$content = apply_filters("ycb_discount_badge_internal_filter", $content, $product, $hide_sale_tag, $ycb_settings);

    return $content;
}

/**
 * Add custom badges to the shop loop
*/
function ycb_custom_badge_shop_loop_opened(){
    if(class_exists("WC_Product_Factory")){
        $_pf = new WC_Product_Factory();
        $product = $_pf->get_product(get_the_ID());

        $base_price = $product->regular_price;
        $discount_price = $product->sale_price;

        if(class_exists("WC_Dynamic_Pricing")){
            $discount_price = apply_filters("woocommerce_product_get_price", $base_price, $product, false);
        }

        $is_on_sale = true;
        if(!isset($discount_price) || floatval($base_price) === floatval($discount_price)){
            $is_on_sale = false;
        }

        $ycb_settings = ycb_get_settings();
        $hide_sale_tag = isset($ycb_settings['ycb_hide_default_sale_tag']) && $ycb_settings['ycb_hide_default_sale_tag'] === "true" ? true : false;

        $hide_sale_tag = $is_on_sale !== false ? $hide_sale_tag : false;

        $addition = apply_filters("ycb_discount_badge_internal_filter", "", $product, $hide_sale_tag, $ycb_settings, $is_on_sale);

        echo $addition;
    }
}


/**
 * Generate the styling for a badge based on percentage
*/
function ycb_get_styles($ycb_settings, $percentage){
    global $ycb_has_hidden_sales_tag, $ycb_using_flatsome;
    $styles = "";
    if($ycb_using_flatsome){
        //$styles .= "display:table;";
        if($ycb_has_hidden_sales_tag){
            $styles .= "top: 0em;";
        } else {
            if(isset($ycb_settings['ycb_hide_default_sale_tag']) && $ycb_settings['ycb_hide_default_sale_tag'] === "true"){
                $styles .= "top: 0em;";
            } else {
                $styles .= "top:4em;";
            }
        }
    } else {
        if($ycb_has_hidden_sales_tag){
            $styles .= "top: -.5em;";
        } else {
            if(isset($ycb_settings['ycb_hide_default_sale_tag']) && $ycb_settings['ycb_hide_default_sale_tag'] === "true"){
                $styles .= "top: -.5em;";
            } else {
                $styles .= "top:3.5em;";
            }
        }
    }

    if($percentage <= 10){
        $styles .= "background:" . $ycb_settings['ycb_10_color'] . ";" ;
    } else if ($percentage > 10 && $percentage <= 20){
        $styles .= "background:" . $ycb_settings['ycb_0_20_color'] . ";" ;
    } else if ($percentage > 20 && $percentage <= 30){
        $styles .= "background:" . $ycb_settings['ycb_30_color'] . ";" ;
    } else if($percentage > 30 && $percentage <= 40){
        $styles .= "background:" . $ycb_settings['ycb_20_40_color'] . ";" ;
    } else if ($percentage > 40 && $percentage <= 50){
        $styles .= "background:" . $ycb_settings['ycb_50_color'] . ";" ;
    } else if($percentage > 50 && $percentage <= 60){
        $styles .= "background:" . $ycb_settings['ycb_40_60_color'] . ";" ;
    } else if ($percentage > 60 && $percentage <= 70){
        $styles .= "background:" . $ycb_settings['ycb_70_color'] . ";" ;
    } else if($percentage > 70 && $percentage <= 80){
        $styles .= "background:" . $ycb_settings['ycb_60_80_color'] . ";" ;
    } else if ($percentage > 80 && $percentage <= 90){
        $styles .= "background:" . $ycb_settings['ycb_80_100_color'] . ";" ;
    } else if($percentage > 90){
        $styles .= "background:" . $ycb_settings['ycb_90_color'] . ";" ;
    }

    if(isset($ycb_settings['ycb_text_color'])){
        $styles .= "color:" . $ycb_settings['ycb_text_color'] . ";" ;
    } else {
         $styles .= "color:#FFFFFF;" ;
    }

    //Force default woo styles incase
    $forced_styles = "";
    $styles .= $forced_styles;


    $styles = apply_filters("ycb_styles_content", $styles);

    return $styles;
}

/**
 * Add custom badge to the frontend
 * Ues an existing filter to add to functionality
*/
function ycb_add_custom_badge_frontend($content, $product, $hide_sale_tag, $ycb_settings, $is_on_sale){
    global $ycb_has_hidden_sales_tag;
    if(isset($product->category_ids)){
        if(is_array($product->category_ids)){
            $categories = $product->category_ids;
            if(count($categories) > 0){
                //Atleast one cat
                ycb_custom_badge_hide_woo_sales_badge_on_page();

                $increment = 0;
                foreach ($categories as $key => $cat_id) {
                    $matches = ycb_custom_badge_get_matches_for_category($cat_id);
                    if($matches !== false){
                        foreach ($matches as $match_key => $match) {
                            if($ycb_has_hidden_sales_tag){
                                $hide_sale_tag = true;
                            }
                            $custom_style = ycb_custom_badge_frontend_styles($match, $hide_sale_tag, $ycb_settings, $increment, $is_on_sale);
                            $custom_tag = "<span class='onsale ycb_on_sale yoohoo_badge ".ycb_get_badge_shape_class(false, $ycb_settings)."' style='$custom_style'>";
                            $custom_tag .= "<span style='padding-left: 3px; padding-right: 3px;'>" . ycb_custom_badge_fa_replace(ycb_custom_badge_emoji_replace($match->content)) . "</span>";
                            $custom_tag .= "</span>";
                            $content .= $custom_tag;
                            $increment++;
                        }
                    }
                }
            }
        }
    }

    return $content;
}
add_filter("ycb_discount_badge_internal_filter", "ycb_add_custom_badge_frontend", 10, 5);

/**
 * Handles regex search and replace of the font awesome icons
*/
function ycb_custom_badge_fa_replace($content){
        $pattern = '/%%(.+?)%%/i';
        $replacement = '<i class=\'$1\'></i>';
        return preg_replace($pattern, $replacement, $content);
}

function ycb_custom_badge_emoji_replace($content){
        $pattern = '/##(.+?)##/i';
        $replacement = '<i class=\'$1\'></i>';
        return preg_replace($pattern, $replacement, $content);
}

/**
 * Force hides the sales badge if needed
*/
function ycb_custom_badge_hide_woo_sales_badge_on_page($force_hide = false){
    global $ycb_has_hidden_sales_tag, $ycb_using_flatsome;
    $ycb_global_options = ycb_custom_badge_get_global_options();
    $should_hide = isset($ycb_global_options["ycb_hide_default_sale_tag"]) && $ycb_global_options["ycb_hide_default_sale_tag"] === "true" ? true : false;

    if($should_hide || $force_hide){
        if(!$ycb_has_hidden_sales_tag){
            if($ycb_using_flatsome){
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
                 span[class=onsale]{
                    display:none;
                 }
                </style>
                <?php
            }
            $ycb_has_hidden_sales_tag = true;
        }
    }

}

/**
 * Handles custom badge custom styles
*/


function ycb_custom_badge_frontend_styles($badge_data, $hide_sale_tag, $ycb_settings, $increment, $is_on_sale){
    global $ycb_using_flatsome;
    $styles = "";
    $top = 0.0;
    if($hide_sale_tag){
        if($ycb_using_flatsome){
            $top = 4;
        } else {
            $top = 3.0;
        }
    } else {
        if($ycb_using_flatsome){
            $top = 8;
        } else {
            $top = 7;
        }
    }

    if($is_on_sale === false){
        $top = 0.0;
    }

    if($ycb_using_flatsome){
        $top += $increment * 4;
    } else {
        $top += $increment * 3.5;
    }


    $styles .= "top: " . $top . "em;";


    $styles .= "background:" . $badge_data->bg_color . ";" ;
    $styles .= "color:" . $badge_data->color . ";" ;

    //Force default woo styles incase
    $forced_styles = "min-height: 50px; min-width: 50px; max-height: 50px; border-radius:50px; font-weight: 700; position: absolute; text-align: center; line-height: 2.9em; font-size: .857em; -webkit-font-smoothing: antialiased;z-index: 9;";
    $styles .= $forced_styles;

    $styles = apply_filters("ycb_custom_badge_styles_content", $styles);

    return $styles;
}