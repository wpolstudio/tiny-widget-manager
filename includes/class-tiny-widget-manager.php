<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class Tiny_Widget_Manager
{

    /* Returns class instance (singleton method) */
    private static $instance = null;
    /**
     * get_instance
     *
     * @return Tiny_Widget_Manager
     */
    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /* private constructor ensures that the class can only be */
    /* created using the get_instance static function */
    private function __construct()
    {
        $debug_mode = get_option('twim_debug_mode');

        $Settings = TWIM_Settings::get_instance();
        // Settings pages
        add_action('admin_menu',     [$Settings, 'create_option_page']);
        add_action('admin_init',     [$Settings, 'populate_option_page']);

        $Hooks = TWIM_Hooks::get_instance();
        if (is_admin()) {
            // Admin pages (twim block)
            // add_action('enqueue_block_editor_assets',   [$Hooks, 'enqueue_block_widget_editor_scripts']);

            // Admin pages (twim legacy)
            add_action('enqueue_block_editor_assets',   [$Hooks, 'maybe_display_notice_on_block_widget_page']);
            add_action('admin_enqueue_scripts',         [$Hooks, 'enqueue_admin_scripts']);
            add_action('in_widget_form',                [$Hooks, 'add_visibility_controls'], 10, 3);
            add_filter('widget_update_callback',        [$Hooks, 'save_widget_controls'], 10, 4);
            // add_action('wp_ajax_twim_search_posts',      [$Hooks, 'twim_search_posts_callback']);
            add_filter('use_widgets_block_editor',      [$Hooks, 'maybe_disable_block_editor']);
            add_action('admin_notices',                 [$Hooks, 'maybe_display_block_editor_notice']);
            add_filter('dynamic_sidebar_params',        [$Hooks, 'hydrate_args']);
        } else {
            // Public pages
            add_filter('sidebars_widgets',              [$Hooks, 'filter_widgets_before_output'], 10, 3);
            add_filter('dynamic_sidebar_params',        [$Hooks, 'add_custom_widget_classes'], 10, 3);
            if ($debug_mode) {
                add_filter('widget_display_callback',   [$Hooks, 'maybe_append_debug_info'], 10, 3);
            }
        }

    }
}
