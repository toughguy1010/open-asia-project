<?php
/**
 * BookYourTravel_VC_Byt_Widget_Social_Shortcode class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if ( ! defined( 'ABSPATH' ) ) { 
	exit; // Exit if accessed directly
}

class BookYourTravel_VC_Byt_Widget_Social_Shortcode extends BookYourTravel_BaseSingleton {

	protected function __construct() {
        // our parent class might contain shared code in its constructor
        parent::__construct();
	}

    public function init() {
		if ( class_exists('Vc_Manager') ) {
            add_action( 'vc_before_init', array($this, 'byt_widget_social_shortcode_vc' ));
            add_action( 'vc_backend_editor_render', array($this, 'vc_backend_editor_render'));
		}
    }

    function vc_backend_editor_render() {
		wp_enqueue_style( 'vc-byt-style-custom', BookYourTravel_Theme_Utils::get_file_uri ('/css/admin/composer_custom.css') );
    }

	function byt_widget_social_shortcode_vc() {

		$byt_vc_category_name = __("BookYourTravel", 'bookyourtravel');
		$byt_vc_category_name = apply_filters("bookyourtravel_vc_widgets_category_name", $byt_vc_category_name);

		$byt_widget_social_settings = array(
			'name'            => __('Social', 'bookyourtravel'),
			'base'            => 'byt_widget_social',
			"description" => __( "Display social network links", "bookyourtravel" ),
			'content_element' => true,
			"class"	  => "byt_widget",
			'weight'		  => 4,			
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
					"heading" => __("Facebook id", 'bookyourtravel'),
					"param_name" => "facebook_id",
					'holder' => 'div',
					'save_always' => true,
				),
				array(
					"type" => "textfield",
					"heading" => __("Twitter id", 'bookyourtravel'),
					"param_name" => "twitter_id",
					'holder' => 'div',
					'save_always' => true,
				),
				array(
					"type" => "textfield",
					"heading" => __("Youtube profile", 'bookyourtravel'),
					"param_name" => "youtube_profile",
					'holder' => 'div',
					'save_always' => true,
				),
				array(
					"type" => "textfield",
					"heading" => __("Rss feed", 'bookyourtravel'),
					"param_name" => "rss_feed",
					'holder' => 'div',
					'save_always' => true,
				),
				array(
					"type" => "textfield",
					"heading" => __("Linkedin profile", 'bookyourtravel'),
					"param_name" => "linked_in_profile",
					'holder' => 'div',
					'save_always' => true,
				),
				array(
					"type" => "textfield",
					"heading" => __("Gplus profile", 'bookyourtravel'),
					"param_name" => "gplus_profile",
					'holder' => 'div',
					'save_always' => true,
				),
				array(
					"type" => "textfield",
					"heading" => __("Vimeo profile", 'bookyourtravel'),
					"param_name" => "vimeo_profile",
					'holder' => 'div',
					'save_always' => true,
				),
				array(
					"type" => "textfield",
					"heading" => __("Pinterest profile", 'bookyourtravel'),
					"param_name" => "pinterest_profile",
					'holder' => 'div',
					'save_always' => true,
				),
				array(
					"type" => "textfield",
					"heading" => __("Whatsapp profile", 'bookyourtravel'),
					"param_name" => "whatsapp_profile",
					'holder' => 'div',
					'save_always' => true,
				),
				array(
					"type" => "textfield",
					"heading" => __("Instagram profile", 'bookyourtravel'),
					"param_name" => "instagram_profile",
					'holder' => 'div',
					'save_always' => true,
				),
				array(
					"type" => "textfield",
					"heading" => __("Skype profile", 'bookyourtravel'),
					"param_name" => "skype_profile",
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
                    <p>%s</p>
                </div>",
                __("Social", "bookyourtravel"),
                __("This is a social widget. To access all the features of this block please use the front editor.", "bookyourtravel")
            )
		);

		vc_map(
			$byt_widget_social_settings
		);
	}
}

$bookyourtravel_vc_byt_widget_social_shortcode = BookYourTravel_VC_Byt_Widget_Social_Shortcode::get_instance();
$bookyourtravel_vc_byt_widget_social_shortcode->init();

// A must for container functionality, replace Wbc_Item with your base name from mapping for parent container
if(class_exists('WPBakeryShortCode')){
    class WPBakeryShortCode_byt_widget_social extends WPBakeryShortCode {}
}