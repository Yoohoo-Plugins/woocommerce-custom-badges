<?php

// cleanup after ourselves.
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

global $wpdb;

// Delete custom table.
$delete_table = $wpdb->prefix . 'ycb_custom_badges';
$sql = "DROP TABLE IF EXISTS `$delete_table`";
$wpdb->query( $sql );

// Delete settings
delete_option( 'ycb_primary_settings' );
