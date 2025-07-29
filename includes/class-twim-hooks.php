<?php

// Exit if accessed directly.

if (!defined('ABSPATH')) {
    exit;
}

class TWIM_Hooks
{
    /* Returns class instance (singleton method) */
    private static $instance = null;
    /**
     * get_instance
     *
     * @return TWIM_Hooks
     */
    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    const PLUGIN_VERSION = '1.0.4';

    private static $PLUGIN_URI = null;
    private static $PLUGIN_PATH = null;
    private $sections;
    private $options;
    private $args;
    private $disable = false;
    private $mobile;
    // private $tablet;
    private $loggedin;
    private $user;
    private $post_types;
    private $posts;
    private $pages;
    private $taxonomies;
    private $color_theme;
    private $debug_mode;
    private $minified;

    /* private constructor ensures that the class can only be */
    /* created using the get_instance static function */
    private function __construct()
    {
        self::$PLUGIN_PATH = trailingslashit(plugin_dir_path(dirname(__FILE__)));
        self::$PLUGIN_URI = trailingslashit(plugin_dir_url(dirname(__FILE__)));
        $this->sections = [
            'pages',
            // 'posts types',
            'posts',
            'archives',
            // 'terms',
            'roles',
            'devices',
        ];
        // Get posts types

        $this->options = [];
        if (is_admin()) {
            $this->pages = $this->_get_all_pages();
            // $this->posts = $this->_get_all_posts();
            $this->post_types = $this->_get_post_types();
            $this->taxonomies = $this->_get_archive_pages();
            $this->hydrate_admin_options();
        }
        $this->args = [];
        $this->disable = false;
        // $this->mobile = wp_is_mobile();
        $this->mobile = TWIMH::is_mobile();
        // $this->tablet = false;
        $this->loggedin = is_user_logged_in();
        $this->user = wp_get_current_user();
        // Debug
        $this->debug_mode = get_option('twim_debug_mode');
        $this->minified = get_option('twim_minified_assets', true);
    }


    /**
     * Enqueue scripts and styles for the admin area.
     *
     * @param string $hook The current admin page.
     */

    public function enqueue_admin_scripts($hook)
    {
        if ($hook !== 'widgets.php') return;

        $vendor_path = 'vendor/selectize/';
        $min = $this->minified ? '.min' : '';

        // Enqueue vendor scripts and styles
        wp_enqueue_script('twim-selectize-scripts', self::$PLUGIN_URI . $vendor_path . "selectize{$min}.js", ['jquery'], self::PLUGIN_VERSION, true);
        wp_enqueue_style('twim-selectize-styles', self::$PLUGIN_URI . $vendor_path . 'selectize.default.css', [], self::PLUGIN_VERSION);

        // Enqueue custom scripts and styles
        wp_enqueue_script('twim-admin-scripts', self::$PLUGIN_URI . "assets/js/twim-scripts{$min}.js", ['jquery', 'twim-selectize-scripts'], self::PLUGIN_VERSION, true);
        wp_localize_script('twim-admin-scripts', 'cwmWidget', array('nonce' => wp_create_nonce('twim_widget_nonce')));
        wp_enqueue_style('twim-admin-styles', self::$PLUGIN_URI . "assets/css/twim-styles{$min}.css", [], self::PLUGIN_VERSION);
    }

    /**
     * hydrate_args
     *
     * @param  mixed $params
     * @return void
     */
    public function hydrate_args($params)
    {
        $widget_id = $params[0]['widget_id'];
        $this->args[$widget_id] = $params[0];
        return $params;
    }


    /**
     * hydrate_options
     *
     * @return void
     */
    public function hydrate_admin_options()
    {
        $full_options = [
            'pages' => [
                'title' => esc_html__('Pages', 'tiny-widget-manager'),
                'label' => esc_html__('on Pages...', 'tiny-widget-manager'),
                'items' => $this->pages,
            ],
            'posts' => [
                'title' => esc_html__('Posts', 'tiny-widget-manager'),
                'label' => esc_html__('on posts of Type...', 'tiny-widget-manager'),
                'items' => $this->post_types,
            ],
            'archives' => [
                'title' => esc_html__('Archives', 'tiny-widget-manager'),
                'label' => esc_html__('on Archives...', 'tiny-widget-manager'),
                'items' => $this->taxonomies,
            ],
            'roles' => [
                'title' => esc_html__('Roles', 'tiny-widget-manager'),
                'label' => esc_html__('for User Roles...', 'tiny-widget-manager'),
                'items' => [
                    'logged_out'    => esc_html__('Logged-out', 'tiny-widget-manager'),
                    'logged_in'     => esc_html__('Logged-in', 'tiny-widget-manager'),
                    'administrator' => esc_html__('Admin', 'tiny-widget-manager'),
                    'editor'        => esc_html__('Editor', 'tiny-widget-manager'),
                    'subscriber'    => esc_html__('Subscriber', 'tiny-widget-manager'),
                ],
            ],
            'devices' => [
                'title' => esc_html__('Devices', 'tiny-widget-manager'),
                'label' => esc_html__('on devices...', 'tiny-widget-manager'),
                'items' => [
                    'desktop' => esc_html__('Computer', 'tiny-widget-manager'),
                    // 'tablet' => 'Tablette',
                    'mobile' => esc_html__('Mobile', 'tiny-widget-manager'),
                ],
            ],
        ];

        // Hydrate options
        foreach ($this->sections as $section) {
            $this->options[$section] = $full_options[$section];
        }

        // Color theme
        $this->color_theme = get_option('twim_color_theme', 'blue');
    }

    /**
     * add_visibility_controls
     *
     * @param  mixed $widget
     * @param  mixed $return
     * @param  mixed $instance
     * @return void
     */
    public function add_visibility_controls($widget, $return, $instance)
    {
        echo '<div class="twim-widget-controls color-theme-' . esc_attr($this->color_theme) . '" data-widget-id="' . esc_attr($widget->id) . '">';

        // Before tabs section (AND/OR input)
        $andor_value = $instance['twim_visibility_andor'] ?? 'and';

        // Maybe set disable class
        $class_disable = in_array($andor_value, ['show', 'hide']) ? 'twim-disabled' : '';

        echo '<div class="twim-tabs">';
        echo '<p class="twim-andor-wrap">';
        echo '<select name="widget-' . esc_attr($widget->id_base) . '[' . esc_attr($widget->number) . '][twim_visibility_andor]" class="twim-andor">';
        echo '<option value="and"' . selected($andor_value, 'and', false) . '>' . esc_html__('Show if all conditions are met', 'tiny-widget-manager') . '</option>';
        echo '<option value="or"' . selected($andor_value, 'or', false) . '>' . esc_html__('Show if any condition is met', 'tiny-widget-manager') . '</option>';
        echo '<option value="show"' . selected($andor_value, 'show', false) . '>' . esc_html__('Always show', 'tiny-widget-manager') . '</option>';
        echo '<option value="hide"' . selected($andor_value, 'hide', false) . '>' . esc_html__('Always hide', 'tiny-widget-manager') . '</option>';
        echo '</select>';
        echo '</p>';

        echo '<div class="twim-wrap ' . esc_attr($class_disable) . '">';
        echo '<ul class="twim-tab-nav">';

        // Display tabs for each section
        foreach ($this->options as $section => $data) {
            $mode = $instance['twim_visibility_' . $section . '_mode'] ?? 'hide';
            $has_items =  !empty($instance['twim_visibility_' . $section . '_items']);
            $has_settings = $has_items || ($mode === 'show');
            $title = $data['title'] ?? '';

            $classes = $has_settings ? 'has-settings setting-' . $mode : '';
            $classes .= ($section === 'pages') ? ' active' : '';
            echo '<li class="' . esc_attr($classes) . '" data-tab="' . esc_attr($section) . '">' . esc_attr($title) . '</li>';
        }
        echo '</ul>';

        echo '<div class="twim-tabs-content">';
        // Display content for each section
        foreach ($this->options as $section => $data) {
            $mode_val = $instance['twim_visibility_' . $section . '_mode'] ?? 'hide';
            $items_val = (array) ($instance['twim_visibility_' . $section . '_items'] ?? []);
            // $autocomplete = $data['autocomplete'] ?? false;
            // if ($autocomplete) {
            //     // Need to populate $data['items'] with actual values
            //     foreach ($items_val as $item_val) {
            //         $data['items'][$item_val] = get_the_title($item_val);
            //     }
            // }
            $this->render_tab($section, $widget, $mode_val, $items_val, $data);
        }
        echo '</div>'; // twim-tabs-content
        echo '</div>'; // twim-wrap

        // Display widget class input
        $class = $instance['twim_custom_classes'] ?? '';
        echo '<div class="twim-label">' . esc_html__('CSS classes (no dot, space-separated)', 'tiny-widget-manager') . '</div>';
        echo '<input class="twim-widget-classes" type="text" name="widget-' . esc_attr($widget->id_base) . '[' . esc_attr($widget->number) . '][twim_custom_classes]" value="' . esc_attr($class) . '" />';

        echo '</div></div>';
    }


    /**
     * render_tab
     *
     * @param  mixed $section
     * @param  mixed $widget
     * @param  mixed $mode_val
     * @param  mixed $items_val
     * @param  mixed $data
     * @return void
     */
    private function render_tab($section, $widget, $mode_val, $items_val, $data)
    {
        $pro = $data['pro'] ?? false;
        $autocomplete = $data['autocomplete'] ?? false;
        $autocomplete_class = $autocomplete ? 'autocomplete' : '';

        echo '<div class="twim-tab-content" data-tab="' . esc_attr($section) . '">';

        if ($pro) {
            echo '<p class="twim-notice">' . esc_html__('This feature is only available on Tiny Manager Pro.', 'tiny-widget-manager') . '</p>';
        } else {
            // echo '<label>' . ucfirst($section) . ' :</label><br />';
            echo '<select class="twim-selectize-showhide" name="widget-' . esc_attr($widget->id_base) . '[' . esc_attr($widget->number) . '][twim_visibility_' . esc_attr($section) . '_mode]" class="twim-mode">';
            echo '<option value="show"' . selected($mode_val, 'show', false) . '>' . esc_html__('Show ', 'tiny-widget-manager') . esc_html($data['label']) . '</option>';
            echo '<option value="hide"' . selected($mode_val, 'hide', false) . '>' . esc_html__('Hide ', 'tiny-widget-manager') . esc_html($data['label']) . '</option>';
            echo '</select><br />';

            echo '<select class="twim-selectize ' . esc_attr($autocomplete_class) . '" multiple name="widget-' . esc_attr($widget->id_base) . '[' . esc_attr($widget->number) . '][twim_visibility_' . esc_attr($section) . '_items][]" placeholder="' . esc_attr(esc_html__('Select items...', 'tiny-widget-manager')) . '">';
            foreach ($data['items'] as $value => $label) {
                $selected = in_array($value, $items_val) ? 'selected' : '';
                $level = str_contains($value, ':') ? '1' : '0';
                echo '<option data-level="' . esc_attr($level) . '" value="' . esc_attr($value) . '" ' . esc_attr($selected) . '>' . esc_html($label) . '</option>';
            }
            echo '</select>';
        }

        echo '</div>';
    }

    /**
     * get_all_pages
     *
     * @return array
     */
    private function _get_all_pages()
    {
        $pages = get_pages();
        $output = [];
        // Add frontpage
        $output['frontpage'] = 'Frontpage';
        $output['search'] = 'Search Page';
        $output['archives'] = 'Archive Page';
        $output['404'] = '404 Page';
        // Add other pages
        foreach ($pages as $page) {
            $output[$page->ID] = $page->post_title;
        }
        return $output;
    }

    /**
     * _get_all_posts
     *
     * @return void
     */
    private function _get_all_posts()
    {
        $posts = get_posts();
        $output = [];
        // Add other pages
        foreach ($posts as $post) {
            $output[$post->ID] = $post->post_title;
        }
        return $output;
    }

    /**
     * get_post_types
     *
     * @return array
     */
    private function _get_post_types()
    {
        $args = [
            'public'   => true,
            '_builtin' => false,
        ];
        $custom_types = get_post_types($args, 'names');
        $types = array_merge(['page', 'post'], $custom_types);
        $output = [];
        foreach ($types as $type) {
            $post_type_obj = get_post_type_object($type);
            if ($post_type_obj)
                $output[$type] = $post_type_obj->labels->singular_name;
        }
        return $output;
    }

    /**
     * get_archives_and_taxonomies
     *
     * @return array
     */
    private function _get_archive_pages()
    {

        $archives = [];
        $archives['all'] = esc_html__('Any Archive', 'tiny-widget-manager');
        foreach ($this->post_types as $post_type => $label) {
            $archives[$post_type] = ucfirst($label);
        }
        $archives['author'] = esc_html__('Author', 'tiny-widget-manager');


        $taxonomies = get_taxonomies(
            array(
                'public'   => true,
                'show_ui'  => true, // ensures only UI-visible ones appear
                // '_builtin' => false // optional: hide WP core ones like 'post_tag'
            ),
            'names'
        );
        foreach ($taxonomies as $tax) {
            $tax_obj = get_taxonomy($tax);
            if ($tax_obj) {
                $archives[$tax] = $tax_obj->labels->name;
                // Get terms for this taxonomy
                $terms = get_terms([
                    'taxonomy' => $tax,
                    'hide_empty' => false,
                ]);
                if (!is_wp_error($terms) && !empty($terms)) {
                    foreach ($terms as $term) {
                        $archives[$tax . ':' . $term->term_id] = $tax_obj->labels->name . ': ' . $term->name;
                    }
                }
            }
        }
        return $archives;
    }


    /**
     * save_widget_controls
     *
     * @param  mixed $instance
     * @param  mixed $new_instance
     * @param  mixed $old_instance
     * @param  mixed $widget
     * @return void
     */
    public function save_widget_controls($instance, $new_instance, $old_instance, $widget)
    {
        $instance['twim_visibility_andor'] = $new_instance['twim_visibility_andor'] ?? 'and';
        foreach ($this->options as $section => $data) {
            $instance['twim_visibility_' . $section . '_mode'] = $new_instance['twim_visibility_' . $section . '_mode'] ?? 'hide';
            $instance['twim_visibility_' . $section . '_items'] = $new_instance['twim_visibility_' . $section . '_items'] ?? [];

            // Cleanup classes input
            $classes = trim(preg_replace('/\s+/', ' ',  $new_instance['twim_custom_classes'] ?? ''));
            $classes = esc_attr($classes);
            $classes = sanitize_html_class($classes);
            $instance['twim_custom_classes'] = $classes;
        }
        return $instance;
    }

    /* ----------------------------------------------------------------------------------------------------------------*/
    /*                                                 ADMIN-SIDE AJAX CALLBACKS
    /* ----------------------------------------------------------------------------------------------------------------*/

    /**
     * twim_search_posts_callback
     *
     * @return void
     */
    // public function twim_search_posts_callback()
    // {
    //     check_ajax_referer('twim_widget_nonce', 'nonce');

    //     $query = isset($_POST['q']) ? sanitize_text_field($_POST['q']) : '';

    //     $results = [];

    //     if (!empty($query)) {
    //         $posts = get_posts([
    //             's' => $query,
    //             'post_type' => 'post', // or 'any'
    //             'posts_per_page' => 10,
    //         ]);

    //         foreach ($posts as $post) {
    //             $results[] = [
    //                 'id' => $post->ID,
    //                 'title' => $post->post_title,
    //             ];
    //         }
    //     }

    //     wp_send_json_success($results);
    // }


    /* ----------------------------------------------------------------------------------------------------------------*/
    /*                                   BLOCK EDITOR COMPATIBILITY CALLBACKS
    /* ----------------------------------------------------------------------------------------------------------------*/


    /**
     * maybe_display_notice_on_block_widget_page
     *
     * @return void
     */
    public function maybe_display_notice_on_block_widget_page()
    {
        $screen = get_current_screen();
        if (
            $screen && $screen->id === 'widgets' &&
            !get_option('twim_disable_block_editor')
        ) {
            wp_enqueue_script(
                'twim-widget-notice',
                self::$PLUGIN_URI . 'assets/js/widget-notice.js',
                ['wp-data', 'wp-url'], // Required for wp.data.dispatch & wp.url
                '1.0',
                true
            );
        }
    }

    /**
     * maybe_display_block_editor_notice
     *
     * @return void
     */
    public function maybe_display_block_editor_notice()
    {
        // // Only show on widgets-related pages or plugin settings page
        // $screen = get_current_screen();
        // if (!in_array($screen->id, ['widgets', 'customize', 'settings_page_twim-settings'])) {
        //     return;
        // }

        if (!get_option('twim_disable_block_editor')) {
            echo '<div class="notice notice-warning is-dismissible">';
            // translators: %s: URL to Tiny Widget Manager settings page.
            echo '<p>' . sprintf(wp_kses_post(__('<strong>Notice:</strong> Tiny Widget Manager will not be operational because the block-based widget editor is currently <strong>enabled</strong>. You can disable it in <a href="%s">Tiny Widget Manager settings</a>', 'tiny-widget-manager')),  esc_url(admin_url('options-general.php?page=twim-settings'))) . '.</p>';
            echo '</div>';
        }
    }

    /**
     * maybe_disable_block_editor
     *
     * @return void
     */
    public function maybe_disable_block_editor()
    {
        return !get_option('twim_disable_block_editor'); // Return false if the option is checked
    }


    /* ----------------------------------------------------------------------------------------------------------------*/
    /*                                                 PUBLIC CALLBACKS
    /* ----------------------------------------------------------------------------------------------------------------*/


     /**
     * maybe_append_debug_info
     *
     * @param  mixed $instance
     * @param  mixed $widget
     * @param  mixed $args
     * @return void
     */
    public function maybe_append_debug_info($instance, $widget, $args)
    {
        $id_base = $widget->id_base;
        $widget_id = $widget->id;

        echo '<div class="twim-debug-info" style="border:1px dashed red; padding:5px; font-size:11px; margin-top:5px;">';
        echo '<div class="twim-debug-section" style="padding: 10px;">';
        echo 'Widget ID: <strong>' . esc_html($widget_id) . '</strong><br>';
        echo 'ID Base: <strong>' . esc_html($id_base) . '</strong><br>';
        if (isset($instance['twim_visibility_andor'])) {
           echo 'Visibility AND/OR: <strong>' . esc_html($instance['twim_visibility_andor']) . '</strong><br>';
        }
        echo 'wp_is_mobile() : <strong>' . (TWIMH::is_mobile() ? 'Is mobile' : 'Is not mobile') . '</strong><br>';
        echo '</div>';
        foreach ($this->sections as $section) {
            $mode = $instance['twim_visibility_' . $section . '_mode'] ?? 'none';
            $items = $instance['twim_visibility_' . $section . '_items'] ?? [];
            echo '<div class="twim-debug-section section-' . esc_attr($section) . '" style="border-top: 1px dashed gray;padding: 10px;">';
            echo esc_html(ucfirst($section)) . ' Mode: <strong>' . esc_html($mode) . '</strong><br>';
            echo esc_html(ucfirst($section)) . ' Items: <strong>' . implode(', ', array_map('esc_html', (array)$items)) . '</strong><br>';
            echo '</div>';
        }
        echo '</div>';

        return $instance;
    }


    /**
     * filter_widgets_before_output
     *
     * @param  mixed $sidebars_widgets
     * @return void
     */
    public function filter_widgets_before_output($sidebars_widgets)
    {
        if (is_admin()) return $sidebars_widgets; // Do not interfere in admin
        if ($this->disable) return $sidebars_widgets; // Do not interfere if disabled set in options

        foreach ($sidebars_widgets as $sidebar_id => &$widget_ids) {
            foreach ($widget_ids as $index => $widget_id) {
                $parsed = wp_parse_widget_id($widget_id);
                $id_base = $parsed['id_base'];
                $number = $parsed['number'];

                $option = get_option('widget_' . $id_base);
                if (!isset($option[$number])) continue;

                $instance = $option[$number];

                // Get visibility settings
                $andor = $instance['twim_visibility_andor'] ?? 'and';

                // Start visibility check

                if ($andor === 'show') {
                    $show = true;
                } elseif ($andor === 'hide') {
                    $show = false;
                } else {
                    $show = ($andor === 'and');
                    foreach ($this->sections as $section) {
                        $mode = $instance['twim_visibility_' . $section . '_mode'] ?? false;
                        if (!$mode) continue;

                        $items = $instance['twim_visibility_' . $section . '_items'] ?? [];
                        $match = $this->match_section($section, $items);

                        if ($andor === 'or') {
                            // Exit loop and set show to true as soon as "show" is identified
                            if ($mode === 'show' && $match) {
                                $show = true;
                                break;
                            }
                        } else {
                            // Exit loop and set show to false as soon as "not show" is identified
                            if (($mode === 'show' && !$match) || ($mode === 'hide' && $match)) {
                                $show = false;
                                break;
                            }
                        }
                    }
                }

                // Check visibility using your logic here
                if (!$show) {
                    unset($widget_ids[$index]);
                }
            }
        }

        return $sidebars_widgets;
    }

    /**
     * match_section
     *
     * @param  mixed $section
     * @param  mixed $items
     * @return void
     */
    private function match_section($section, $items)
    {
        if ( empty($items )) return false;

        $post_id = get_the_ID();
        $post_type = get_post_type($post_id);

        switch ($section) {
            case 'pages':
                if (in_array('frontpage', $items) && is_front_page()) return true;
                if (in_array('search', $items) && is_search()) return true;
                if (in_array('404', $items) && is_404()) return true;
                if (in_array('archives', $items) && is_archive()) return true;
                return !empty($items) && is_page($items);
                break;
            case 'posts':
                return is_singular($items);
                break;
            case 'archives':
                foreach ($items as $item) {
                    // Check if term archive
                    if ($item == 'all') {
                        return is_archive();
                    } elseif (str_contains($item, ':')) {
                        [$tax, $term_id] = explode(':', $item);
                        if (($tax == 'category' && is_category($term_id)) || ($tax == 'post_tag' && is_tag($term_id)) || is_tax($tax, $term_id)) return true;
                    } elseif (
                        (post_type_exists($item) && is_post_type_archive($item)) ||
                        (taxonomy_exists($item) && is_tax($item)) ||
                        ($item == 'author' && is_author()) ||
                        ($item == 'category' && is_category()) ||
                        ($item == 'post_tag' && is_tag())
                    ) return true;
                }
                return false;
            case 'roles':
                if (!$this->loggedin) return in_array('logged_out', $items);
                if (in_array('logged_in', $items)) return true;
                foreach ($this->user->roles as $role) {
                    if (in_array($role, $items)) return true;
                }
                return false;
            case 'devices':
                if (in_array('mobile', $items) && $this->mobile) return true;
                // if (in_array('tablet', $items) && $this->tablet ) return true;
                if (in_array('desktop', $items) && !$this->mobile /* && !$this->tablet */) return true;
                return false;
        }
        return false;
    }


    /**
     * add_custom_widget_classes
     *
     * @param  mixed $params
     * @return void
     */
    public function add_custom_widget_classes($params)
    {
        global $wp_registered_widgets;

        $widget_id = $params[0]['widget_id'];
        $widget_obj = $wp_registered_widgets[$widget_id];

        if (!is_array($widget_obj['callback']) || !is_object($widget_obj['callback'][0])) {
            return $params;
        }

        $widget_instance = $widget_obj['callback'][0];
        $option_name = $widget_instance->option_name ?? '';

        if ($option_name) {
            $all_instances = get_option($option_name);
            $widget_number = $widget_obj['params'][0]['number'] ?? null;

            if ($widget_number !== null && isset($all_instances[$widget_number])) {
                $instance = $all_instances[$widget_number];
                $custom_class = trim(preg_replace('/\s+/', ' ', $instance['twim_custom_classes'] ?? ''));

                if (!empty($custom_class)) {
                    $params[0]['before_widget'] = preg_replace(
                        '/class=["\']([^"\']*)["\']/',
                        'class="$1 ' . esc_attr($custom_class) . '"',
                        $params[0]['before_widget']
                    );
                }
            }
        }

        return $params;
    }
}
