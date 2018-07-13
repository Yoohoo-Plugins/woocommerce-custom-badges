<?php
/**
 * Plugin Name: Yoohoo Custom Badges For WooCommerce
 * Description: Creates custom badges for WooCommerce products
 * Plugin URI: https://yoohooplugins.com/plugins/custom-badges-for-woocommerce/
 * Author: Yoohoo Plugins
 * Author URI: https://yoohooplugins.com
 * Version: 1.2
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: yoohoo-custom-badges
 * Domain Path: /languages
 * Network: false
 * WC requires at least: 3.2
 * WC tested up to: 3.4.3
 *
 *
 * Yoohoo Custom Badges For WooCommerce is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Yoohoo Custom Badges For WooCommerce is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Yoohoo Custom Badges For WooCommerce. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 */

defined( 'ABSPATH' ) or exit;


define( 'YOOHOO_STORE', 'https://yoohooplugins.com/edd-sl-api/' );
define( 'YH_PLUGIN_ID', 152 );
define( 'YCB_VERSION', 1.2 );

require_once( "includes/initialize.php" );
require_once( "includes/backend.php" );
require_once( "includes/frontend.php" );

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
