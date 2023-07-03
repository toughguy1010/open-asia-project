<?php
/**
 * BookYourTravel_VC_Cruise_Card_Shortcode class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BookYourTravel_VC_Cruise_Card_Shortcode extends BookYourTravel_BaseSingleton {

	protected function __construct() {
        // our parent class might contain shared code in its constructor
        parent::__construct();
	}

    public function init() {
		if ( class_exists('Vc_Manager') ) {
		    add_action('vc_after_init', array($this, 'vc_map_init') );
            add_action('vc_frontend_editor_render', array($this, 'vc_frontend_editor_render'));
            add_action('vc_backend_editor_render', array($this, 'vc_backend_editor_render'));
        }
    }

    function vc_backend_editor_render() {
        wp_enqueue_style( 'vc-byt-style-custom', BookYourTravel_Theme_Utils::get_file_uri ('/css/admin/composer_custom.css') );
    }

	function vc_frontend_editor_render() {
		wp_enqueue_script( 'vc-cruise-card', BookYourTravel_Theme_Utils::get_file_uri ('/js/admin/composer/vc_cruise_card.js'), array('vc-frontend-editor-min-js'), time(), true );
		wp_enqueue_style( 'vc-byt-style-custom', BookYourTravel_Theme_Utils::get_file_uri ('/css/admin/composer_custom.css') );
	}

	function vc_map_init() {

		wp_enqueue_script('jquery');
		wp_enqueue_script( 'bookyourtravel-scripts', BookYourTravel_Theme_Utils::get_file_uri ('/js/scripts.js'), array('jquery'), BOOKYOURTRAVEL_VERSION, true );
		wp_enqueue_script( 'bookyourtravel-cruises', BookYourTravel_Theme_Utils::get_file_uri (BOOKYOURTRAVEL_CRUISES_JS_PATH),  array('bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true );

		$byt_vc_category_name = __("BookYourTravel", 'bookyourtravel');
		$byt_vc_category_name = apply_filters("bookyourtravel_vc_widgets_category_name", $byt_vc_category_name);

		vc_map(array(
			"name" => __( "Cruise Card", "bookyourtravel" ),
			"description" => __( "Display a single cruise", "bookyourtravel" ),
			"base" => "byt_cruise_card",
			"class"	  => "byt_widget",
			'weight'  => 8,	
			"category" => $byt_vc_category_name,
			"params" => array(
				array(
					"type" => "autocomplete",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Cruise", "bookyourtravel" ),
					"param_name" => "cruise_id",
					"description" => __( "Type the name of the cruise you want to display", "bookyourtravel" ),
					'settings' => array(
						'multiple' => false,
						'min_length' => 1,
						'groups' => false,
						// In UI show results grouped by groups, default false
						'unique_values' => true,
						// In UI show results except selected. NB! You should manually check values in backend, default false
						'display_inline' => true,
						// In UI show results inline view, default false (each value in own line)
						'delay' => 500,
						// delay for search. default 500
						'auto_focus' => true,
						// auto focus input, default true
					),
					'save_always' => true,
				),
				array(
					"type" => "checkbox",
					"holder" => "div",
					"class" => "",
					"heading" => __( "Show fields", "bookyourtravel" ),
					"param_name" => "show_fields",
					"value" => array(
					   'Title'=>'title',
					   'Image'=>'image',
					   'Buttons'=>'actions',
					   'Description'=>'description',
					   'Rating'=>'rating',
					   'Price'=>'price',
					   'Address'=>'address',
					),
					"std" => 'title,image,actions',
					"description" => __( "Select which cruise fields you want to show", "bookyourtravel" ),
					'save_always' => true,
				),
				array(
					'type' => 'css_editor',
					'heading' => __( 'Css', 'bookyourtravel' ),
					'param_name' => 'css',
					'group' => __( 'Design options', 'bookyourtravel' ),
					'save_always' => true,
				)
            ),
            "custom_markup" => sprintf("
                <div class='byt_admin_vc_container'>
                    <div class='brand'>
                        <span class='icon-byt'></span>
                        <h3>%s</h3>
                    </div>
                    <p>%s</p>
                </div>",
                __("Cruise Card", "bookyourtravel"),
                __("This is a cruise card widget. To access all the features of this block please use the front editor.", "bookyourtravel")
            )
		));
	}
}

$bookyourtravel_vc_cruise_card_shortcode = BookYourTravel_VC_Cruise_Card_Shortcode::get_instance();;
$bookyourtravel_vc_cruise_card_shortcode->init();