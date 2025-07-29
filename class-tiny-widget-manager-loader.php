<?php

// Exit if accessed directly.

if (!defined('ABSPATH')) {
    exit;
}

/*
* Plugin Name: Tiny Widget Manager
* Description: Manage widget visibility.
* Version: 1.0.1
* Author: WPol Studio
* Author URI: https://wpolstudio.com
* Domain Path: /languages
* License: GPL2+
*/

/* Send admin notices whenever required plugins or theme aren't found */
add_action('init', function() {
    twim_start_plugin();
});

function twim_start_plugin() {
    // Load the plugin textdomain
    // load_plugin_textdomain('tiny-widget-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

    // Libraries
    require_once 'vendor/autoload.php';

    // Load the plugin class.
    require_once 'includes/class-tiny-widget-manager.php';
    require_once 'includes/class-twim-hooks.php';
    require_once 'includes/class-twim-settings.php';
    require_once 'includes/class-twim-helpers.php';

    // Initialize the plugin.
    Tiny_Widget_Manager::get_instance();
}