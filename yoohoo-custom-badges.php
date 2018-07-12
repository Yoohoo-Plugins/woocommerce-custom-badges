<?php
/*
Plugin Name: Yoohoo Custom Badges
Description: Adds discount badges to items on sale - Works with Dynamic Pricing. Supports adding standard text tags based on categories of products
Version: 1.2
Author: Andrew Lima
*/

define( 'YOOHOO_STORE', 'https://yoohooplugins.com/edd-sl-api/' );
define( 'YH_PLUGIN_ID', 152 );
define( 'YCB_VERSION', 1.2 );

require_once("includes/initialize.php");
require_once("includes/backend.php");
require_once("includes/frontend.php");

/**
 * License Key checks
 */
if ( ! class_exists( 'YCB_License_Checker' ) ) {
	include( dirname( __FILE__ ) . '/includes/license-update-checker.php' );
}

$license_key = trim( get_option( 'yoohoo_zapier_license_key' ) );

// setup the updater
$edd_updater = new YCB_License_Checker( YOOHOO_STORE, __FILE__, array( 
		'version' => YCB_VERSION,
		'license' => $license_key,
		'item_id' => YH_PLUGIN_ID,
		'author' => 'Yoohoo Plugins',
		'url' => home_url()
	)
);

register_activation_hook( __FILE__, 'ycb_activate' );
