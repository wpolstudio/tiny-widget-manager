<?php
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

class TWIM_Settings
{
    private static $instance = null;

    /**
     * get_instance
     *
     * @return TWIM_Settings
     */
    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function create_option_page()
    {
        add_options_page(
            'Tiny Widget Manager Settings',
            'Tiny Widget Manager',
            'manage_options',
            'twim-settings',
            'twim_Settings::twim_render_settings_page'
        );
    }

    public function populate_option_page()
    {
        register_setting('twim_settings_group', 'twim_disable_block_editor', [
            'sanitize_callback' => 'sanitize_text_field'
        ]);

        register_setting('twim_settings_group', 'twim_color_theme', [
            'sanitize_callback' => 'sanitize_text_field'
        ]);

        // register_setting('twim_settings_group', 'twim_minified_assets', [
        //     'sanitize_callback' => 'sanitize_text_field'
        // ]);

        // register_setting('twim_settings_group', 'twim_debug_mode', [
        //     'sanitize_callback' => 'sanitize_text_field'
        // ]);

        add_settings_section(
            'twim_main_section',
            'General Settings',
            null,
            'twim-settings'
        );

        add_settings_field(
            'twim_disable_block_editor',
            __('Disable Block Widgets Editor', 'tiny-widget-manager'),
            'twim_Settings::twim_render_block_editor_field',
            'twim-settings',
            'twim_main_section'
        );

        // add_settings_field(
        //     'twim_minified_assets',
        //     __('Minified Assets', 'tiny-widget-manager'),
        //     'twim_Settings::twim_render_minify_field',
        //     'twim-settings',
        //     'twim_main_section'
        // );

        // add_settings_field(
        //     'twim_debug_mode',
        //     __('Debug Mode', 'tiny-widget-manager'),
        //     'twim_Settings::twim_render_debug_mode_field',
        //     'twim-settings',
        //     'twim_main_section'
        // );

        add_settings_field(
            'twim_color_theme',
            __('Color Theme', 'tiny-widget-manager'),
            'twim_Settings::twim_render_color_theme_field',
            'twim-settings',
            'twim_main_section'
        );
    }

    public static function twim_render_block_editor_field()
    {
        $checked = get_option('twim_disable_block_editor') ? 'checked' : '';
        echo '<input type="checkbox" name="twim_disable_block_editor" value="1" ' . esc_attr($checked) . '>' . esc_html__('Use classic widget editor', 'tiny-widget-manager');
    }

    public static function twim_render_minify_field()
    {
        $checked = get_option('twim_minified_assets', true) ? 'checked' : '';
        echo '<input type="checkbox" name="twim_minified_assets" value="1" ' . esc_attr($checked) . '>' . esc_html__('Enqueue minified assets', 'tiny-widget-manager');
    }

    public static function twim_render_debug_mode_field()
    {
        $checked = get_option('twim_debug_mode') ? 'checked' : '';
        echo '<input type="checkbox" name="twim_debug_mode" value="1" ' . esc_attr($checked) . '>' . esc_html__('Enable debug mode', 'tiny-widget-manager');
    }

    public static function twim_render_color_theme_field()
    {
        $value = get_option('twim_color_theme', 'blue');
        $options = [
            'blue'   => __('Blue', 'tiny-widget-manager'),
            'gray'   => __('Gray', 'tiny-widget-manager'),
            'orange' => __('Orange', 'tiny-widget-manager'),
            'lime'   => __('Lime', 'tiny-widget-manager'),
        ];

        echo '<select name="twim_color_theme">';
        foreach ($options as $key => $label) {
            printf(
                '<option value="%s"%s>%s</option>',
                esc_attr($key),
                selected($value, $key, false),
                esc_html($label)
            );
        }
        echo '</select>';
    }

    public static function twim_render_settings_page()
    {
        ?>
        <div class="wrap">
            <h1>Tiny Widget Manager Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('twim_settings_group');
                do_settings_sections('twim-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}
