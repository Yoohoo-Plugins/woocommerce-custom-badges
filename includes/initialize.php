<?php
/**
 * Holds global variables and functions used by both backend and front end
*/
global $ycb_custom_badges_table, $wpdb, $ycb_has_hidden_sales_tag, $ycb_using_flatsome;

$ycb_custom_badges_table = $wpdb->prefix . "ycb_custom_badges";
$ycb_has_hidden_sales_tag = false;
$ycb_using_flatsome = false;

/**
 * Register activate hook
*/
function ycb_activate() {
    ycb_custom_tags_database_tables();
}

/**
 * Couple of quick init checks
 */
function ycb_init(){
    ycb_check_theme_framework_integrations();
    ycb_register_actions_filters();
}
add_action("init", "ycb_init");

/**
 * Load plugin text domain
 */
function ycb_load_text_domain() {
    load_plugin_textdomain( 'yoohoo-custom-badges', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'ycb_load_text_domain' );

/**
 * Check if any known theme integrations are at play
 */
function ycb_check_theme_framework_integrations(){
    global $ycb_using_flatsome;
    //Check if user is using flatsome
    if(class_exists("Storefront")){
        //The option class exists, safe to assume user is using flatsome.
        $ycb_using_flatsome = true;
    }
}

/**
 * Register all the pretty product hooks
 */
function ycb_register_actions_filters(){
    global $ycb_using_flatsome;
    if($ycb_using_flatsome){
        ycb_setup_flatsome_hooks();
    } else {
        ycb_setup_standard_hooks();
    }
}



/**
 * Generates custom badges tables
 * Will later hold other tables if needed
 */
function ycb_custom_tags_database_tables(){
    global $ycb_custom_badges_table, $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "
    CREATE TABLE $ycb_custom_badges_table (
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

/**
 * Adds Menus
*/
function ycb_admin_menu() {
    $show_in_menu = current_user_can( 'manage_woocommerce' ) ? 'ycb_main_menu' : false;

    add_menu_page( __('Custom Badges', 'yoohoo-custom-badges' ),  __( 'Custom Badges', 'yoohoo-custom-badges' ), 'manage_woocommerce', 'ycb_main_menu', 'ycb_custom_badges', plugin_dir_url( __FILE__ ) . '../assets/ycb-white-16.png');

    $discount_slug = add_submenu_page( $show_in_menu, __( 'Sale Badges', 'yoohoo-custom-badges' ), __( 'Sale Badges', 'yoohoo-custom-badges' ), 'manage_woocommerce', 'ycb_primary_settings#tab_2', "ycb_setting_area");

    $settings_slug = add_submenu_page( $show_in_menu, __( 'Settings', 'yoohoo-custom-badges' ), __( 'Settings', 'yoohoo-custom-badges' ), 'manage_woocommerce', 'ycb_primary_settings', "ycb_setting_area");

    $license_slug = add_submenu_page( $show_in_menu, __( 'License', 'yoohoo-custom-badges' ), __( 'License', 'yoohoo-custom-badges' ), 'manage_woocommerce', 'ycb_primary_settings#tab_3', "ycb_setting_area");

    do_action("ycb_admin_menu_below");
}
add_action("admin_menu", "ycb_admin_menu", 99);


/**
 * Settings retriever
*/
function ycb_get_settings(){
    $ycb_settings = get_option("ycb_primary_settings", false);
    if($ycb_settings === false){
        //Setup defaults
        $ycb_settings = array();
        $ycb_settings['ycb_hide_default_sale_tag'] = "false";

        $ycb_settings['ycb_0_20_color'] = "#ED1C24";
        $ycb_settings['ycb_20_40_color'] = "#ED1C24";
        $ycb_settings['ycb_40_60_color'] = "#ED1C24";
        $ycb_settings['ycb_60_80_color'] = "#ED1C24";
        $ycb_settings['ycb_80_100_color'] = "#ED1C24";

        //added increments
        $ycb_settings['ycb_10_color'] = "#ED1C24";
        $ycb_settings['ycb_30_color'] = "#ED1C24";
        $ycb_settings['ycb_50_color'] = "#ED1C24";
        $ycb_settings['ycb_70_color'] = "#ED1C24";
        $ycb_settings['ycb_90_color'] = "#ED1C24";

        $ycb_settings['ycb_text_color'] = "#FFFFFF";
        $ycb_settings['ycb_custom_css'] = "";
        $ycb_settings['ycb_badge_shape'] = 0;

        $ycb_settings['ycb_license_key'] = '';
        $ycb_settings['ycb_license_expires'] = '';

        $ycb_settings = apply_filters("ycb_default_settings_array", $ycb_settings);
    } else {
        $ycb_settings = maybe_unserialize($ycb_settings);
    }
    return $ycb_settings;
}

/**
 * Custom badges, get options
 */
function ycb_custom_badge_get_global_options(){
    $ycb_settings = get_option("ycb_custom_badge_global_options", false);
    if($ycb_settings === false){
        //Setup defaults
        $ycb_settings = array();
        $ycb_settings['ycb_hide_default_sale_tag'] = "false";

        $ycb_settings = apply_filters("ycb_custom_badge_global_options_array", $ycb_settings);
    } else {
        $ycb_settings = maybe_unserialize($ycb_settings);
    }
    return $ycb_settings;
}

/**
 * Get the class associated with the badge shape setting
 */
function ycb_get_badge_shape_class($is_admin = false, $ycb_settings = false){
    if($ycb_settings === false){ $ycb_settings = ycb_get_settings(); }

    $shape_setting = isset($ycb_settings["ycb_badge_shape"]) ? intval($ycb_settings["ycb_badge_shape"]) : false;

    $shape_class = $is_admin ? 'circle' : 'yoohoo_badge_circular';
    switch ($shape_setting) {
        case 1:
            $shape_class = $is_admin ? 'square' : 'yoohoo_badge_square';
            break;
        case 2:
            $shape_class = $is_admin ? 'rounded' : 'yoohoo_badge_rounded';
            break;
        case 3:
            $shape_class = $is_admin ? 'square_wide' : 'yoohoo_badge_square_wide';
            break;
    }

    return $shape_class;

}