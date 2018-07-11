<?php
/*
Plugin Name: YooHoo Custom Basges
Description: Adds discount badges to items on sale - Works with Dynamic Pricing. Supports adding standard text tags based on categories of products
Version: 1.2
Author: Andrew Lima
*/
require_once("includes/initialize.php");
require_once("includes/backend.php");
require_once("includes/frontend.php");

register_activation_hook( __FILE__, 'ycb_activate' );
