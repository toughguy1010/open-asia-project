<?php
/**
 * BookYourTravel_VC_Byt_Widget_Iconic_Features_Shortcode class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BookYourTravel_VC_Byt_Widget_Iconic_Features_Shortcode extends BookYourTravel_BaseSingleton {

	protected function __construct() {
        // our parent class might contain shared code in its constructor
        parent::__construct();
	}

    public function init() {
		if ( class_exists('Vc_Manager') ) {
			add_action( 'vc_before_init', array($this, 'byt_widget_iconic_features_shortcode_vc' ));
			add_action( 'vc_frontend_editor_render', array($this, 'vc_frontend_editor_render'));

			if (function_exists('vc_add_shortcode_param')) {
			 	vc_add_shortcode_param('features_control', array($this, 'features_control_settings_field'));
			}
            add_action( 'vc_backend_editor_render', array($this, 'vc_backend_editor_render'));
		}
    }

    function vc_backend_editor_render() {
		wp_enqueue_style( 'vc-byt-style-custom', BookYourTravel_Theme_Utils::get_file_uri ('/css/admin/composer_custom.css') );
	}

	function vc_frontend_editor_render() {
		wp_enqueue_script( 'vc-widget-iconic-features', BookYourTravel_Theme_Utils::get_file_uri ('/js/admin/composer/vc_widget_iconic_features.js'), array('vc-frontend-editor-min-js'), BOOKYOURTRAVEL_VERSION, true );
	}

	function features_control_settings_field( $settings, $value ) {
	   return '<div class="features_control_block"></div>';
	}

	function byt_widget_iconic_features_shortcode_vc() {

		$byt_vc_category_name = __("BookYourTravel", 'bookyourtravel');
		$byt_vc_category_name = apply_filters("bookyourtravel_vc_widgets_category_name", $byt_vc_category_name);

		$byt_widget_iconic_features_settings = array(
			'name'            => __('Iconic Features', 'bookyourtravel'),
			'base'            => 'byt_widget_iconic_features',
			"description" => __( "Display a list of features with icons", "bookyourtravel" ),
			'content_element' => true,
			"class"	  => "byt_widget",
			'weight'		  => 3,			
			'category' 		  => $byt_vc_category_name,
			'params'          => array(
				array(
					"type" => "textfield",
					"heading" => __("Title", 'bookyourtravel'),
					"param_name" => "title",
					'holder' => 'div',
					'save_always' => true,
				),
				array(
					"type" => "dropdown",
					"heading" => __("Number of features", 'bookyourtravel'),
					"param_name" => "number_of_features",
					'holder' => 'div',
					"value" => range(1, 20),
					"std" => "6",
					'save_always' => true,
				),
				array(
					"type" => "dropdown",
					"heading" => __("Display mode?", 'bookyourtravel'),
					"param_name" => "display_mode",
					'holder' => 'div',
					"value" => array(
						__('List', 'bookyourtravel') => "small",
						__('Grid', 'bookyourtravel') => "card"
					),
					"std" => 'card',
					'save_always' => true,
				),
				array(
					"type" => "dropdown",
					"heading" => __("Features per row", 'bookyourtravel'),
					"param_name" => "features_per_row",
					'holder' => 'div',
					"value" => range(1, 5),
					"std" => "3",
					'save_always' => true,
				),
				array(
					"type" => "featurescontrol",
					"heading" => __("Features", 'bookyourtravel'),
					"param_name" => "features",
					'holder' => 'div',
					"value" => array(),
					"std" => array(),
					'dependency' => array(
						'callback' => 'vcBookYourTravelFeaturesDependencyCallback'
					),
					'save_always' => true,
				),
				array(
					'type' => 'css_editor',
					'heading' => __( 'Css', 'bookyourtravel' ),
					'param_name' => 'css',
					'group' => __( 'Design options', 'bookyourtravel' ),
					'save_always' => true,
				),
            ),
            "custom_markup" => sprintf("
                <div class='byt_admin_vc_container'>
                    <div class='brand'>
                        <span class='icon-byt'></span>
                        <h3>%s</h3>
                    </div>
                    <p>%s</p>
                </div>",
                __("Iconic Features", "bookyourtravel"),
                __("This is an iconic features widget. To access all the features of this block please use the front editor.", "bookyourtravel")
            )
		);

		vc_map(
			$byt_widget_iconic_features_settings
		);
	}
}

$bookyourtravel_vc_byt_widget_iconic_features_shortcode = BookYourTravel_VC_Byt_Widget_Iconic_Features_Shortcode::get_instance();;
$bookyourtravel_vc_byt_widget_iconic_features_shortcode->init();

// A must for container functionality, replace Wbc_Item with your base name from mapping for parent container
if(class_exists('WPBakeryShortCode')){
    class WPBakeryShortCode_byt_widget_iconic_features extends WPBakeryShortCode {
		public function __construct( $settings ) {
			parent::__construct( $settings ); // !Important to call parent constructor to active all logic for shortcode.

			global $bookyourtravel_theme_globals;

			wp_enqueue_script( 'composer-scripts', BookYourTravel_Theme_Utils::get_file_uri ('/js/admin/composer/composer_scripts.js'), array('jquery'), BOOKYOURTRAVEL_VERSION, true );
			wp_localize_script('composer-scripts', 'vc_byt_iconic', array(
					'iconicTitleLabel' => json_encode(__('Title', 'bookyourtravel')),
					'iconicTextLabel' => json_encode(__('Text', 'bookyourtravel')),
					'iconicClassLabel' => json_encode(__('Iconic class', 'bookyourtravel')),
					'iconicClassLinkLabel' => json_encode(__('Select icon', 'bookyourtravel')),
					'iconicClasses' => json_encode($bookyourtravel_theme_globals->get_iconic_features_icon_classes())
				)
			);
		}
	}
}