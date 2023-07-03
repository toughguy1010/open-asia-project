<?php

/**
 * BookYourTravel_VC_Byt_Widget_Home_Searching_Shortcode class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class BookYourTravel_VC_Byt_Widget_Home_Searching_Shortcode extends BookYourTravel_BaseSingleton
{

	protected function __construct()
	{
		// our parent class might contain shared code in its constructor
		parent::__construct();
	}

	public function init()
	{
		if (class_exists('Vc_Manager')) {
			add_action('vc_before_init', array($this, 'byt_widget_home_searching_shortcode_vc'));
			add_action('vc_backend_editor_render', array($this, 'vc_backend_editor_render'));
		}
	}

	function vc_backend_editor_render()
	{
		wp_enqueue_style('vc-byt-style-custom', BookYourTravel_Theme_Utils::get_file_uri('/css/admin/composer_custom.css'));
	}

	function byt_widget_home_searching_shortcode_vc()
	{

		$byt_vc_category_name = __("BookYourTravel", 'bookyourtravel');
		$byt_vc_category_name = apply_filters("bookyourtravel_vc_widgets_category_name", $byt_vc_category_name);

		$byt_widget_home_searching_settings = array(
			'name'            => __('Home Searching', 'bookyourtravel'),
			'base'            => 'byt_widget_home_searching',
			"description" => __("Display searching section", "bookyourtravel"),
			'content_element' => true,
			"class"	  => "byt_widget",
			'weight'		  => 2,
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
					"type" => "textarea_html",
					"heading" => __("Content", 'bookyourtravel'),
					"param_name" => "content",
					'holder' => 'div',
					'save_always' => true, // Thêm tham số save_always để lưu giá trị
				),
				array(
					"type" => "textarea_html",
					"heading" => __("Span", 'bookyourtravel'),
					"param_name" => "span",
					'holder' => 'div',
					'save_always' => true, // Thêm tham số save_always để lưu giá trị
				),
				
			),
			"custom_markup" => sprintf(
				"
                <div class='byt_admin_vc_container'>
                    <div class='brand'>
                        <span class='icon-byt'></span>
                        <h3>%s</h3>
                    </div>
                    <p>%s</p>
                </div>",
				__("Home Searching", "bookyourtravel"),
				__("This is a home searching widget. To access all the features of this block please use the front editor.", "bookyourtravel")
			)
		);

		vc_map(
			$byt_widget_home_searching_settings
		);
	}
}

$bookyourtravel_vc_byt_widget_home_searching_shortcode = BookYourTravel_VC_Byt_Widget_Home_Searching_Shortcode::get_instance();
$bookyourtravel_vc_byt_widget_home_searching_shortcode->init();

// A must for container functionality, replace Wbc_Item with your base name from mapping for parent container
if (class_exists('WPBakeryShortCode')) {
	class WPBakeryShortCode_byt_widget_home_searching extends WPBakeryShortCode
	{
	}
}
