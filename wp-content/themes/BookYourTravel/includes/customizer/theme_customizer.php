<?php
/**
 * BookYourTravel_Theme_Customizer class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require_once BookYourTravel_Theme_Utils::get_file_path('/includes/customizer/theme_schemes.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/customizer/theme_customizer_slider_control.php');

// Include the Alpha Color Picker control file.
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/customizer/alpha-color-picker/alpha-color-picker.php');

class BookYourTravel_Theme_Customizer extends BookYourTravel_BaseSingleton
{
    protected function __construct()
    {
        // our parent class might contain shared code in its constructor
        parent::__construct();
    }

    public function init()
    {
        add_action('bookyourtravel_customize_register', array($this, 'customize_register'), 10, 1);
        add_action('bookyourtravel_wp_head', array($this, 'wp_head'), 0);
        add_action('bookyourtravel_customize_preview_init', array($this, 'customize_preview_init'));
        add_action('bookyourtravel_customize_controls_enqueue_scripts', array($this, 'customize_controls_enqueue_scripts'));
        add_action('wp_ajax_bookyourtravel_customizer_color_scheme_request', array($this, 'customizer_color_scheme_request'));
    }

    public function customizer_color_scheme_request($scheme_id)
    {

        $scheme_id = '';
        if (isset($_REQUEST['scheme_id'])) {
            $scheme_id = intval($_REQUEST['scheme_id']);
        }

        $scheme = BookYourTravel_Theme_Schemes::get_scheme($scheme_id);

        echo json_encode($scheme);

        die();
    }

    public function customize_preview_init()
    {
        wp_register_script('bookyourtravel-customize-preview', BookYourTravel_Theme_Utils::get_file_uri('/js/admin/customize_preview.js'), array('customize-preview', 'jquery'), BOOKYOURTRAVEL_VERSION);
        wp_enqueue_script('bookyourtravel-customize-preview');

        wp_localize_script('bookyourtravel-customize-preview', 'BYT', array(
            'themePath' => get_template_directory_uri(),
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bookyourtravel_nonce'),
        ));

        do_action('bookyourtravel_after_customize_preview_init');
    }

    public function customize_controls_enqueue_scripts()
    {
        wp_register_script('bookyourtravel-customize-controls', BookYourTravel_Theme_Utils::get_file_uri('/js/admin/customize_controls.js'), array('customize-controls', 'jquery'), BOOKYOURTRAVEL_VERSION);
        wp_enqueue_script('bookyourtravel-customize-controls');

        wp_localize_script('bookyourtravel-customize-controls', 'BYT', array(
            'themePath' => get_template_directory_uri(),
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bookyourtravel_nonce'),
        ));

        do_action('bookyourtravel_after_customize_controls_enqueue_scripts');
    }

    public function wp_head()
    {
        self::output();
    }

    public function customize_register($wp_customize)
    {
        $transport = ($wp_customize->selective_refresh ? 'postMessage' : 'refresh');
        $scheme = BookYourTravel_Theme_Schemes::get_default_scheme();

        if (isset($scheme['sections'])) {
            foreach ($scheme['sections'] as $section_id => $section) {
                self::customizer_section($wp_customize, $transport, $section_id, $section);
            }
        }

        $widgets_panel = $wp_customize->get_panel('widgets');
        if (isset($widgets_panel)) {
            $widgets_panel->title = __('Sidebars', 'bookyourtravel');
        }
        $background_image_section = $wp_customize->get_section('background_image');
        if (isset($background_image_section)) {
            $background_image_section->title = __('Background', 'bookyourtravel');
        }

        $background_color_control = $wp_customize->get_control('background_color');
        if (isset($background_color_control)) {
            $background_color_control->section = 'background_image';
        }

        $content_wrapper_background_color_control = $wp_customize->get_control('content_wrapper_background_color');
        if (isset($content_wrapper_background_color_control)) {
            $content_wrapper_background_color_control->section = 'background_image';
        }

        $content_background_color_control = $wp_customize->get_control('content_background_color');
        if (isset($content_background_color_control)) {
            $content_background_color_control->section = 'background_image';
        }

        $content_background_shadow_opacity_control = $wp_customize->get_control('content_background_shadow_opacity');
        if (isset($content_background_shadow_opacity_control)) {
            $content_background_shadow_opacity_control->section = 'background_image';
        }

        self::sidebar_settings($wp_customize);
    }

    public static function customize_alpha_color_setting($wp_customize, $transport, $section_id, $setting_id, $label, $default_color)
    {
        $wp_customize->add_setting(
            $setting_id,
            array(
                'default' => $default_color,
                'transport' => $transport,
                'sanitize_callback' => 'esc_attr'
            )
        );

        $wp_customize->add_control(
            new Customize_Alpha_Color_Control(
                $wp_customize,
                $setting_id,
                array(
                    'label' => $label,
                    'section' => $section_id,
                    'settings' => $setting_id,
                    'show_opacity'  => true, // Optional.
                )
            )
        );
    }    

    public static function sidebar_settings($wp_customize)
    {

        $wp_customize->add_setting('sidebar_under_header_number_of_columns', array('default' => '4', 'transport' => 'refresh', 'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control('sidebar_under_header_number_of_columns', array('type' => 'select', 'choices' => self::get_column_count_choices(), 'label' => 'Number of columns', 'section' => 'sidebar-widgets-under-header', 'description' => __('Controls the number of columns in the Under Header sidebar', 'bookyourtravel')));

        $wp_customize->add_setting('sidebar_hero_number_of_columns', array('default' => '1', 'transport' => 'refresh', 'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control('sidebar_hero_number_of_columns', array('type' => 'select', 'choices' => self::get_column_count_choices(), 'label' => 'Number of columns', 'section' => 'sidebar-widgets-hero', 'description' => __('Controls the number of columns in the Hero sidebar', 'bookyourtravel')));

        $wp_customize->add_setting('sidebar_left_number_of_columns', array('default' => '1', 'transport' => 'refresh', 'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control('sidebar_left_number_of_columns', array('type' => 'select', 'choices' => self::get_column_count_choices(), 'label' => 'Number of columns', 'section' => 'sidebar-widgets-left', 'description' => __('Controls the number of columns in the Left sidebar', 'bookyourtravel')));

        $wp_customize->add_setting('sidebar_right_number_of_columns', array('default' => '1', 'transport' => 'refresh', 'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control('sidebar_right_number_of_columns', array('type' => 'select', 'choices' => self::get_column_count_choices(), 'label' => 'Number of columns', 'section' => 'sidebar-widgets-right', 'description' => __('Controls the number of columns in the Right sidebar', 'bookyourtravel')));

        $wp_customize->add_setting('sidebar_right_accommodation_number_of_columns', array('default' => '1', 'transport' => 'refresh', 'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control('sidebar_right_accommodation_number_of_columns', array('type' => 'select', 'choices' => self::get_column_count_choices(), 'label' => 'Number of columns', 'section' => 'sidebar-widgets-right-accommodation', 'description' => __('Controls the number of columns in the Right Accommodation sidebar', 'bookyourtravel')));

        $wp_customize->add_setting('sidebar_right_tour_number_of_columns', array('default' => '1', 'transport' => 'refresh', 'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control('sidebar_right_tour_number_of_columns', array('type' => 'select', 'choices' => self::get_column_count_choices(), 'label' => 'Number of columns', 'section' => 'sidebar-widgets-right-tour', 'description' => __('Controls the number of columns in the Right Tour sidebar', 'bookyourtravel')));

        $wp_customize->add_setting('sidebar_right_cruise_number_of_columns', array('default' => '1', 'transport' => 'refresh', 'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control('sidebar_right_cruise_number_of_columns', array('type' => 'select', 'choices' => self::get_column_count_choices(), 'label' => 'Number of columns', 'section' => 'sidebar-widgets-right-cruise', 'description' => __('Controls the number of columns in the Right Cruise sidebar', 'bookyourtravel')));

        $wp_customize->add_setting('sidebar_right_car_rental_number_of_columns', array('default' => '1', 'transport' => 'refresh', 'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control('sidebar_right_car_rental_number_of_columns', array('type' => 'select', 'choices' => self::get_column_count_choices(), 'label' => 'Number of columns', 'section' => 'sidebar-widgets-right-car-rental', 'description' => __('Controls the number of columns in the Right Car rental sidebar', 'bookyourtravel')));

        $wp_customize->add_setting('sidebar_footer_number_of_columns', array('default' => '4', 'transport' => 'refresh', 'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control('sidebar_footer_number_of_columns', array('type' => 'select', 'choices' => self::get_column_count_choices(), 'label' => 'Number of columns', 'section' => 'sidebar-widgets-footer', 'description' => __('Controls the number of columns in the Footer sidebar', 'bookyourtravel')));

        $wp_customize->add_setting('sidebar_above_footer_number_of_columns', array('default' => '4', 'transport' => 'refresh', 'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control('sidebar_above_footer_number_of_columns', array('type' => 'select', 'choices' => self::get_column_count_choices(), 'label' => 'Number of columns', 'section' => 'sidebar-widgets-above-footer', 'description' => __('Controls the number of columns in the Above Footer sidebar', 'bookyourtravel')));

        $wp_customize->add_setting('sidebar_home_content_number_of_columns', array('default' => '4', 'transport' => 'refresh', 'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control('sidebar_home_content_number_of_columns', array('type' => 'select', 'choices' => self::get_column_count_choices(), 'label' => 'Number of columns', 'section' => 'sidebar-widgets-home-content', 'description' => __('Controls the number of columns in the Home Content sidebar', 'bookyourtravel')));

        $wp_customize->add_setting('sidebar_home_footer_number_of_columns', array('default' => '4', 'transport' => 'refresh', 'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control('sidebar_home_footer_number_of_columns', array('type' => 'select', 'choices' => self::get_column_count_choices(), 'label' => 'Number of columns', 'section' => 'sidebar-widgets-home-footer', 'description' => __('Controls the number of columns in the Home Footer sidebar', 'bookyourtravel')));
    }

    public static function customizer_section($wp_customize, $transport, $section_id, $section)
    {
        $in_panel = isset($section['in_panel']) ? $section['in_panel'] : '';

        if (!empty($in_panel)) {
            $wp_customize->add_section($section_id, array(
                'title' => $section['title'],
                'description' => $section['description'],
                'priority' => $section['priority'],
                'panel' => $in_panel,
            ));
        } else {
            $wp_customize->add_section($section_id, array(
                'title' => $section['title'],
                'description' => $section['description'],
                'priority' => $section['priority'],
            ));
        }

        if (isset($section['settings'])) {
            foreach ($section['settings'] as $setting_id => $setting) {
                switch ($setting['type']) {
                    case 'color':{
                            self::customize_alpha_color_setting($wp_customize, $transport, $section_id, $setting_id, $setting['label'], $setting['color']);
                            break;
                        }
                    case 'text':{
                            self::customizer_text_setting($wp_customize, $transport, $section_id, $setting['selector'], $setting_id,
                                isset($setting['label']) ? $setting['label'] : '',
                                isset($setting['description']) ? $setting['description'] : '',
                                isset($setting['default']) ? $setting['default'] : '',
                                isset($setting['render_callback']) ? $setting['render_callback'] : '');
                            break;
                        }
                    case 'number_text':{
                            self::customizer_number_text_setting($wp_customize, 'refresh', $section_id, $setting['selector'], $setting_id,
                                isset($setting['label']) ? $setting['label'] : '',
                                isset($setting['description']) ? $setting['description'] : '',
                                isset($setting['default']) ? $setting['default'] : '');
                            break;
                        }
                    case 'color_scheme_selector':{
                            self::customizer_color_scheme_setting($wp_customize, $transport, $section_id, $setting_id,
                                isset($setting['label']) ? $setting['label'] : '',
                                isset($setting['description']) ? $setting['description'] : '');
                            break;
                        }
                    case 'single_post_sidebar_selector':{
                            self::customizer_single_post_sidebar_setting($wp_customize, 'refresh', $section_id, $setting_id,
                                isset($setting['label']) ? $setting['label'] : '',
                                isset($setting['description']) ? $setting['description'] : '');
                            break;
                        }
                    case 'single_post_layout_selector':{
                            self::customizer_single_post_layout_setting($wp_customize, 'refresh', $section_id, $setting_id,
                                isset($setting['label']) ? $setting['label'] : '',
                                isset($setting['description']) ? $setting['description'] : '');
                            break;
                        }
                    case 'layout_selector':{
                            self::customizer_layout_setting($wp_customize, 'refresh', $section_id, $setting_id,
                                isset($setting['label']) ? $setting['label'] : '',
                                isset($setting['description']) ? $setting['description'] : '');
                            break;
                        }
                    case 'header_layout_selector':{
                            self::customizer_header_layout_setting($wp_customize, 'refresh', $section_id, $setting_id,
                                isset($setting['label']) ? $setting['label'] : '',
                                isset($setting['description']) ? $setting['description'] : '');
                            break;
                        }
                    case 'font_family_selector':{
                            self::customizer_font_family_setting($wp_customize, 'refresh', $section_id, $setting_id,
                                isset($setting['default']) ? $setting['default'] : '',
                                isset($setting['label']) ? $setting['label'] : '',
                                isset($setting['description']) ? $setting['description'] : '');
                            break;
                        }
                    case 'yes_no_checkbox':{
                            self::customizer_yes_no_checkbox_setting($wp_customize, 'refresh', $section_id, $setting_id, $setting['label'], $setting['description']);
                            break;
                        }
                    case 'custom_slider':{
                            self::customize_custom_slider_setting(
                                $wp_customize,
                                'refresh',
                                $section_id,
                                $setting_id,
                                isset($setting['default']) ? $setting['default'] : '',
                                isset($setting['label']) ? $setting['label'] : '',
                                isset($setting['description']) ? $setting['description'] : '',
                                isset($setting['min']) ? $setting['min'] : 0,
                                isset($setting['max']) ? $setting['max'] : 10,
                                isset($setting['step']) ? $setting['step'] : 1,
                                isset($setting['property']) ? $setting['property'] : '');
                            break;
                        }
                    default:{}
                }
            }
        }
    }

    public static function customize_custom_slider_setting($wp_customize, $transport, $section_id, $setting_id, $default_value, $label, $description, $min, $max, $step, $property)
    {
        $wp_customize->add_setting($setting_id, array('default' => $default_value, 'transport' => $transport, 'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control(
            new Customize_Slider_Control(
                $wp_customize, $setting_id, array(
                    'label' => $label,
                    'section' => $section_id,
                    'settings' => $setting_id,
                    'min' => $min,
                    'max' => $max,
                    'step' => $step,
                )
            )
        );
    }

    public static function customizer_color_scheme_setting($wp_customize, $transport, $section_id, $setting_id, $label, $description)
    {
        $wp_customize->add_setting($setting_id, array('default' => 'default', 'transport' => $transport, 'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control($setting_id, array('label' => $label, 'section' => $section_id, 'type' => 'select', 'description' => $description, 'choices' => self::get_color_scheme_choices()));
    }

    public static function customizer_layout_setting($wp_customize, $transport, $section_id, $setting_id, $label, $description)
    {
        $wp_customize->add_setting($setting_id, array('default' => 'wide', 'transport' => $transport, 'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control($setting_id, array('label' => $label, 'section' => $section_id, 'type' => 'select', 'description' => $description, 'choices' => self::get_layout_choices()));
    }

    public static function customizer_single_post_sidebar_setting($wp_customize, $transport, $section_id, $setting_id, $label, $description)
    {
        $wp_customize->add_setting($setting_id, array('default' => 'right', 'transport' => $transport, 'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control($setting_id, array('label' => $label, 'section' => $section_id, 'type' => 'select', 'description' => $description, 'choices' => self::get_single_post_sidebar_choices()));
    }

    public static function customizer_single_post_layout_setting($wp_customize, $transport, $section_id, $setting_id, $label, $description)
    {
        $wp_customize->add_setting($setting_id, array('default' => 'wide', 'transport' => $transport, 'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control($setting_id, array('label' => $label, 'section' => $section_id, 'type' => 'select', 'description' => $description, 'choices' => self::get_single_post_layout_choices()));
    }

    public static function customizer_header_layout_setting($wp_customize, $transport, $section_id, $setting_id, $label, $description)
    {
        $wp_customize->add_setting($setting_id, array('default' => 'header1', 'transport' => $transport, 'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control($setting_id, array('label' => $label, 'section' => $section_id, 'type' => 'select', 'description' => $description, 'choices' => self::get_header_layout_choices()));
    }

    public static function customizer_yes_no_checkbox_setting($wp_customize, $transport, $section_id, $setting_id, $label, $description)
    {
        $wp_customize->add_setting($setting_id, array('transport' => $transport, 'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control($setting_id, array('label' => $label, 'section' => $section_id, 'type' => 'checkbox', 'description' => $description));
    }

    public static function customizer_font_family_setting($wp_customize, $transport, $section_id, $setting_id, $default_value, $label, $description)
    {
        $wp_customize->add_setting($setting_id, array('default' => $default_value, 'transport' => $transport, 'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control($setting_id, array('label' => $label, 'section' => $section_id, 'type' => 'select', 'description' => $description, 'choices' => self::get_font_family_choices()));
    }

    public static function customizer_number_text_setting($wp_customize, $transport, $section_id, $selector, $setting_id, $label, $description, $default_value)
    {
        $wp_customize->add_setting($setting_id, array('selector' => $selector, 'transport' => $transport, 'default' => $default_value, 'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control($setting_id, array('label' => $label, 'section' => $section_id, 'type' => 'number', 'description' => $description, 'format' => 'number'));

        if (isset($wp_customize->selective_refresh)) {
            $wp_customize->selective_refresh->add_partial($setting_id, array('selector' => $selector, 'settings' => $setting_id));
        }
    }

    public static function customizer_text_setting($wp_customize, $transport, $section_id, $selector, $setting_id, $label, $description, $default_value, $render_callback)
    {
        $wp_customize->add_setting($setting_id, array('selector' => $selector, 'transport' => $transport, 'default' => $default_value, 'sanitize_callback' => 'esc_attr'));
        $wp_customize->add_control($setting_id, array('label' => $label, 'section' => $section_id, 'type' => 'text', 'description' => $description));

        if (isset($wp_customize->selective_refresh)) {
            $wp_customize->selective_refresh->add_partial($setting_id, array('selector' => $selector, 'settings' => $setting_id, 'render_callback' => $render_callback));
        }
    }

    public static function customizer_color_setting($wp_customize, $transport, $section_id, $setting_id, $label, $default_color)
    {
        $wp_customize->add_setting($setting_id, array('default' => $default_color, 'sanitize_callback' => 'sanitize_hex_color', 'transport' => $transport));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $setting_id, array('label' => $label, 'section' => $section_id, 'settings' => $setting_id)));
    }

    public static function output()
    {
        $scheme = BookYourTravel_Theme_Schemes::get_default_scheme();

        if (isset($scheme['sections'])) {
            echo '<style id="bookyourtravel-customizer-css">';

            foreach ($scheme['sections'] as $section_id => $section) {
                if (isset($section['settings'])) {
                    foreach ($section['settings'] as $setting_id => $setting) {

                        if (isset($setting['type']) && $setting['type'] == 'color') {

                            if (isset($setting['property']) && $setting['property'] == 'button-background-color') {
                                if (isset($setting['selector']) && isset($setting_id)) {

                                    $stored_value = get_theme_mod($setting_id);
                                    // $darker_color = BookYourTravel_Theme_Utils::color_luminance($stored_value, -0.4);
                                    // $combo_values = sprintf("%s; background:-moz-linear-gradient(top, %s 0%%, %s 100%%);background:-webkit-gradient(linear, left top, left bottom, color-stop(0%%, %s), color-stop(100%%, %s));background:-webkit-linear-gradient(top, %s 0%%, %s 100%%);background:-o-linear-gradient(top, %s 0%%, %s 100%%);background:-ms-linear-gradient(top, %s 0%%, %s 100%%);background:linear-gradient(to bottom, %s 0%%, %s 100%%)", $stored_value, $stored_value, $darker_color, $stored_value, $darker_color, $stored_value, $darker_color, $stored_value, $darker_color, $stored_value, $darker_color, $stored_value, $darker_color);
                                    echo self::print_css($setting['selector'], 'background', $stored_value, isset($setting['force']));

                                    if (isset($setting['dependents'])) {
                                        foreach ($setting['dependents'] as $key => $dependent) {
                                            echo self::print_css($dependent['selector'], isset($dependent['property']) ? $dependent['property'] : 'color', $stored_value, isset($dependent['force']));
                                        }
                                    }
                                }
                            } else if (isset($setting['property']) && $setting['property'] == 'placeholder-color') {
                                if (isset($setting['selector']) && isset($setting_id)) {
                                    $stored_value = get_theme_mod($setting_id);

                                    $selectors = explode(',', $setting['selector']);
                                    foreach ($selectors as $sel) {
                                        echo self::print_css($sel, 'color', $stored_value, isset($setting['force']));
                                    }
                                }
                            } else {
                                if (isset($setting['selector']) && isset($setting_id)) {
                                    $property = isset($setting['property']) ? $setting['property'] : 'color';
                                    $stored_value = get_theme_mod($setting_id);
                                    echo self::print_css($setting['selector'], $property, $stored_value, isset($setting['force']));

                                    if (isset($setting['dependents'])) {
                                        foreach ($setting['dependents'] as $key => $dependent) {
                                            $property = isset($dependent['property']) ? $dependent['property'] : 'color';
                                            if (strpos($property, 'color') > -1 || strpos($property, 'background') > -1) {
                                                echo self::print_css($dependent['selector'], $property, $stored_value, isset($dependent['force']));
                                            } else {
                                                echo self::print_css($dependent['selector'], $property, $stored_value . 'px', isset($dependent['force']));
                                            }
                                        }
                                    }
                                }
                            }
                        } else if (isset($setting['type']) && $setting['type'] == 'custom_slider') {
                            if (isset($setting['selector']) && isset($setting_id)) {
                                $property = isset($setting['property']) ? $setting['property'] : 'font-size';
                                $force = isset($setting['force']) && $setting['force'] == '1';
                                $force_str = $force ? ' !important' : '';

                                if (isset($setting['property']) &&
                                    (
                                        $setting['property'] == 'widget-box-shadow-opacity' ||
                                        $setting['property'] == 'content-background-shadow-opacity'
                                    )) {
                                    $stored_value = floatval(get_theme_mod($setting_id, '-1'));
                                    if ($stored_value > -1) {
                                        $combo_values = sprintf("0 3px 10px rgba(0, 0, 0, %0.1f)%s;", $stored_value, $force_str);
                                        $combo_values .= sprintf("-webkit-box-shadow:0 3px 10px rgba(0, 0, 0, %0.1f)%s;", $stored_value, $force_str);
                                        $combo_values .= sprintf("-moz-box-shadow:0 3px 10px rgba(0, 0, 0, %0.1f);", $stored_value);
                                        echo self::print_css($setting['selector'], 'box-shadow', $combo_values, isset($setting['force']));
                                    }
                                } else if (isset($setting['property']) && ($setting['property'] == 'button-border-radius' || $setting['property'] == 'text-border-radius')) {
                                    $stored_value = intval(get_theme_mod($setting_id, '-1'));
                                    if ($stored_value > -1) {
                                        $combo_values = sprintf("%dpx%s;", $stored_value, $force_str);
                                        $combo_values .= sprintf("-moz-border-radius: %dpx%s;", $stored_value, $force_str);
                                        $combo_values .= sprintf("-webkit-border-radius: %dpx", $stored_value);
                                        echo self::print_css($setting['selector'], 'border-radius', $combo_values, isset($setting['force']));
                                    }
                                } else if (isset($setting['property']) && $setting['property'] == 'font-size') {
                                    $stored_value = intval(get_theme_mod($setting_id, '-1'));
                                    if ($stored_value > -1) {
                                        $stored_value = get_theme_mod($setting_id);
                                        echo self::print_css($setting['selector'], $property, sprintf("%dpx", $stored_value), isset($setting['force']));
                                    }
                                } else if (strpos($setting['property'], 'padding') !== false ||
                                    strpos($setting['property'], 'margin') !== false ||
                                    $setting['property'] == 'width' ||
                                    $setting['property'] == 'height') {

                                    $stored_value = get_theme_mod($setting_id);
                                    $properties = explode(',', $setting['property']);
                                    foreach ($properties as $prop) {
                                        if (strlen($prop) > 0) {
                                            if ($stored_value == 0 || $stored_value == '0') {
                                                echo self::print_css($setting['selector'], $prop, $stored_value, isset($setting['force']));
                                            } else {
                                                echo self::print_css($setting['selector'], $prop, $stored_value . 'px', isset($setting['force']));
                                            }
                                        }
                                    }
                                } else {
                                    $stored_value = get_theme_mod($setting_id);
                                    echo self::print_css($setting['selector'], $property, $stored_value, isset($setting['force']));
                                }
                            }
                        } else if (isset($setting['type']) && $setting['type'] == 'number_text') {
                            if (isset($setting['selector']) && isset($setting_id)) {
                                $stored_value = get_theme_mod($setting_id);

                                if (isset($setting['property'])) {
                                    if (strpos($setting['property'], 'padding') !== false ||
                                        strpos($setting['property'], 'margin') !== false ||
                                        $setting['property'] == 'width' ||
                                        $setting['property'] == 'height') {

                                        $properties = explode(',', $setting['property']);
                                        foreach ($properties as $prop) {
                                            if ($stored_value == 0 || $stored_value == '0') {
                                                echo self::print_css($setting['selector'], $prop, $stored_value, isset($setting['force']));
                                            } else {
                                                echo self::print_css($setting['selector'], $prop, $stored_value . 'px', isset($setting['force']));
                                            }
                                        }
                                    } else {
                                        echo self::print_css($setting['selector'], $setting['property'], $stored_value, isset($setting['force']));
                                    }
                                }
                            }
                        } else if (isset($setting['type']) && $setting['type'] == 'font_family_selector') {
                            if (isset($setting['selector']) && isset($setting_id)) {
                                $stored_value = get_theme_mod($setting_id);

                                if (!empty($stored_value)) {
                                    $stored_value = str_replace('+', ' ', $stored_value);
                                    if ($stored_value == 'Open Sans') {
                                        $stored_value = "'Open Sans', Helvetica, Arial, sans-serif";
                                    } else {
                                        $stored_value = "'" . $stored_value . "', sans-serif";
                                    }

                                    echo self::print_css($setting['selector'], 'font-family', $stored_value, isset($setting['force']));
                                }
                            }
                        }
                    }
                }
            }

            echo '</style>';
        }
    }

    public static function print_css($selector, $property, $stored_value, $important = false)
    {
        $stored_value = trim($stored_value);
        if ($stored_value != '' && $stored_value != 'px') {
            if ($important) {
                return sprintf('%s { %s:%s !important; }', $selector, $property, $stored_value) . "\n";
            } else {
                return sprintf('%s { %s:%s; }', $selector, $property, $stored_value) . "\n";
            }

        }

        return '';
    }

	public static function get_font_family_choices() {
		$google_fonts = BookYourTravel_Theme_Utils::get_google_font_family_choices();
		$web_safe_fonts = BookYourTravel_Theme_Utils::get_web_safe_family_choices();

        $combined_fonts = array_merge($google_fonts, $web_safe_fonts);
        ksort($combined_fonts);
        return $combined_fonts;
	}

    public static function get_header_layout_choices()
    {

        $header_layout_control_options = array();

        $header_layout_control_options['header1'] = __('Header 1', 'bookyourtravel');
        $header_layout_control_options['header2'] = __('Header 2', 'bookyourtravel');
        $header_layout_control_options['header3'] = __('Header 3', 'bookyourtravel');
        $header_layout_control_options['header4'] = __('Header 4', 'bookyourtravel');
        $header_layout_control_options['header5'] = __('Header 5', 'bookyourtravel');
        $header_layout_control_options['header6'] = __('Header 6', 'bookyourtravel');
        $header_layout_control_options['header7'] = __('Header 7', 'bookyourtravel');
        $header_layout_control_options['header8'] = __('Header 8', 'bookyourtravel');
        $header_layout_control_options['header9'] = __('Header 9', 'bookyourtravel');
        $header_layout_control_options['header10'] = __('Header 10', 'bookyourtravel');
        $header_layout_control_options['header11'] = __('Header 11', 'bookyourtravel');

        return apply_filters('bookyourtravel_customizer_header_layout_choices', $header_layout_control_options);
    }

    public static function get_color_scheme_choices()
    {
        $schemes = BookYourTravel_Theme_Schemes::get_schemes();

        $color_scheme_control_options = array();

        $default_scheme = BookYourTravel_Theme_Schemes::get_default_scheme();

        $color_scheme_control_options['default'] = $default_scheme['label'];

        foreach ($schemes as $scheme_id => $value) {
            $color_scheme_control_options[$scheme_id] = $value['label'];
        }

        return apply_filters('bookyourtravel_customizer_color_scheme_choices', $color_scheme_control_options);
    }

    public static function get_layout_choices()
    {
        $layout_control_options = array();

        $layout_control_options['wide'] = __('Wide (default)', 'bookyourtravel');
        $layout_control_options['boxed'] = __('Boxed', 'bookyourtravel');
        $layout_control_options['full-screen'] = __('Full screen', 'bookyourtravel');

        return apply_filters('bookyourtravel_customizer_layout_choices', $layout_control_options);
    }

    public static function get_single_post_layout_choices()
    {
        $layout_control_options = array();

        $layout_control_options['left'] = __('Left (default)', 'bookyourtravel');
        $layout_control_options['right'] = __('Right', 'bookyourtravel');
        $layout_control_options['above'] = __('Above', 'bookyourtravel');

        return apply_filters('bookyourtravel_customizer_single_post_layout_choices', $layout_control_options);
    }

    public static function get_single_post_sidebar_choices()
    {

        $sidebar_control_options = array();

        $sidebar_control_options['left'] = __('Left', 'bookyourtravel');
        $sidebar_control_options['right'] = __('Right (default)', 'bookyourtravel');
        $sidebar_control_options['both'] = __('Both', 'bookyourtravel');
        $sidebar_control_options['none'] = __('None', 'bookyourtravel');

        return apply_filters('bookyourtravel_customizer_single_post_sidebar_choices', $sidebar_control_options);
    }

    public static function get_column_count_choices()
    {

        $column_count_choices = array();
        $i = 1;

        for ($i = 1; $i <= 5; $i++) {
            $column_count_choices[$i] = $i;
        }

        return apply_filters('bookyourtravel_column_count_choices', $column_count_choices);
    }
}

// store the instance in a variable to be retrieved later and call init
$bookyourtravel_theme_customizer = BookYourTravel_Theme_Customizer::get_instance();
$bookyourtravel_theme_customizer->init();
