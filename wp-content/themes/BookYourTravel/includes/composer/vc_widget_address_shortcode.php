<?php
/**
 * BookYourTravel_VC_Byt_Widget_Address_Shortcode class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BookYourTravel_VC_Byt_Widget_Address_Shortcode extends BookYourTravel_BaseSingleton {

	protected function __construct() {
        // our parent class might contain shared code in its constructor
        parent::__construct();
	}

    public function init() {
		if ( class_exists('Vc_Manager') ) {
            add_action( 'vc_before_init', array($this, 'byt_widget_address_shortcode_vc' ));
            add_action( 'vc_backend_editor_render', array($this, 'vc_backend_editor_render'));
		}
    }

    function vc_backend_editor_render() {
        wp_enqueue_script( 'vc-byt-custom-element-view', BookYourTravel_Theme_Utils::get_file_uri ('/js/admin/composer/vc_byt_custom_element_view.js'), array('vc-backend-min-js'), time(), true );
		wp_enqueue_style( 'vc-byt-style-custom', BookYourTravel_Theme_Utils::get_file_uri ('/css/admin/composer_custom.css') );
    }

	function byt_widget_address_shortcode_vc() {

		$byt_vc_category_name = __("BookYourTravel", 'bookyourtravel');
		$byt_vc_category_name = apply_filters("bookyourtravel_vc_widgets_category_name", $byt_vc_category_name);

		$byt_widget_address_settings = array(
			'name'            => __('Address', 'bookyourtravel'),
			"description"     => __( "Display company data (address, phone, email)", "bookyourtravel" ),
			'base'            => 'byt_widget_address',
			'content_element' => true,
			"class"	          => "byt_widget",
			'weight'		  => 5,			
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
					"type" => "textfield",
					"heading" => __("Company name", 'bookyourtravel'),
					"param_name" => "company_name",
					'holder' => 'div',
					'save_always' => true,
				),
				array(
					"type" => "textfield",
					"heading" => __("Company address", 'bookyourtravel'),
					"param_name" => "company_address",
					'holder' => 'div',
					'save_always' => true,
				),
				array(
					"type" => "textfield",
					"heading" => __("Company phone", 'bookyourtravel'),
					"param_name" => "company_phone",
					'holder' => 'div',
					'save_always' => true,
				),
				array(
					"type" => "textfield",
					"heading" => __("Company email", 'bookyourtravel'),
					"param_name" => "company_email",
					'holder' => 'div',
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
                    [byt_widget_address
                        title='{{ params.title }}'
                        company_name='{{ params.company_name }}'
                        company_phone='{{ params.company_phone }}'
                        company_address='{{ params.company_address }}'
                        company_email='{{ params.company_email }}'
                    ]
                </div>"
                , __("Address", "bookyourtravel")
            ),
            "js_view" => "VcCustomElementView"
		);

		vc_map(
			$byt_widget_address_settings
		);
	}
}

$bookyourtravel_vc_byt_widget_address_shortcode = BookYourTravel_VC_Byt_Widget_Address_Shortcode::get_instance();
$bookyourtravel_vc_byt_widget_address_shortcode->init();

// A must for container functionality, replace Wbc_Item with your base name from mapping for parent container
if(class_exists('WPBakeryShortCode')){
    class WPBakeryShortCode_byt_widget_address extends WPBakeryShortCode {}
}