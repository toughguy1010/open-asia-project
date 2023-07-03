<?php
/**
 * BookYourTravel_VC_Byt_Widget_Car_Rental_List_Shortcode class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BookYourTravel_VC_Byt_Widget_Car_Rental_List_Shortcode extends BookYourTravel_BaseSingleton {

	protected function __construct() {
		// our parent class might contain shared code in its constructor
		parent::__construct();
	}

  public function init() {
		if ( class_exists('Vc_Manager') ) {
			add_action( 'vc_before_init', array($this, 'byt_widget_car_rental_list_shortcode_vc' ));

			add_action( 'wp_ajax_vc_bookyourtravel_get_car_rental_tag_ids', array(
				$this,
				'get_car_rental_tag_ids_ajax',
			) );

			add_action( 'vc_backend_editor_render', array($this, 'vc_backend_editor_render'));
		}
  }

	function vc_backend_editor_render() {
		wp_enqueue_style( 'vc-byt-style-custom', BookYourTravel_Theme_Utils::get_file_uri ('/css/admin/composer_custom.css') );
	}

	function get_car_rental_tag_ids_ajax(){

		vc_user_access()
			->checkAdminNonce()
			->validateDie()
			->wpAny( 'edit_posts', 'edit_pages' )
			->validateDie();

		$tags = get_categories( array(
			'taxonomy'		=>'car_rental_tag',
			'orderby' 		=> 'name',
			'order'   		=> 'ASC',
			'hide_empty'    => 0
		) );

		$assigned_ids = array();
		$assigned_ids_string = '';
		if (isset($_POST['tagIds'])) {
			$assigned_ids_string = wp_kses($_POST['tagIds'], array());
		}
		$assigned_ids = explode(',', $assigned_ids_string);

		$param = array(
			'param_name' => 'car_rental_tag_ids',
			'type' => 'checkbox',
		);
		$param_line = '';
		foreach ( $tags as $tag ) {
			$label = $tag->name;
			$term_id = $tag->term_id;
			$checked = '';
			if (in_array($term_id, $assigned_ids)) {
				$checked = " checked='checked' ";
			}
			$param_line .= ' <label class="vc_checkbox-label"><input ' . $checked . ' id="' . $param['param_name'] . '-' . $term_id . '" value="' . $term_id . '" class="wpb_vc_param_value ' . $param['param_name'] . ' ' . $param['type'] . '" type="checkbox" name="' . $param['param_name'] . '"' . '> ' . $label . '</label>';
		}

		die( json_encode( $param_line ) );
	}

	function byt_widget_car_rental_list_shortcode_vc() {

		wp_enqueue_script('jquery');
		wp_enqueue_script( 'bookyourtravel-scripts', BookYourTravel_Theme_Utils::get_file_uri ('/js/scripts.js'), array('jquery'), BOOKYOURTRAVEL_VERSION, true );
		wp_enqueue_script( 'bookyourtravel-car_rentals', BookYourTravel_Theme_Utils::get_file_uri (BOOKYOURTRAVEL_CAR_RENTALS_JS_PATH),  array('bookyourtravel-scripts'), BOOKYOURTRAVEL_VERSION, true );

		$car_rental_types = get_categories( array(
			'taxonomy'		=>'car_type',
			'orderby' 		=> 'name',
			'order'   		=> 'ASC',
			'hide_empty'    => 0
		) );

		$car_rental_type_params = array();
		$car_rental_type_params[__('No filter', 'bookyourtravel')] = '';
		foreach ( $car_rental_types as $car_rental_type ) {
			$label = $car_rental_type->name;
			$term_id = $car_rental_type->term_id;
			$car_rental_type_params[$label] = $term_id;
		}

		$byt_vc_category_name = __("BookYourTravel", 'bookyourtravel');
		$byt_vc_category_name = apply_filters("bookyourtravel_vc_widgets_category_name", $byt_vc_category_name);

		$byt_widget_car_rental_list_settings = array(
			'name'            => __('Car Rental List', 'bookyourtravel'),
			'base'            => 'byt_widget_car_rental_list',
			"description" => __( "Display a list of car rentals", "bookyourtravel" ),
			'content_element' => true,
			"class"	  => "byt_widget",
			'weight'		  => 15,			
			'category' 		  => $byt_vc_category_name,
			'params'          => array(
				array(
					"type" => "textfield",
					"heading" => __("Title", 'bookyourtravel'),
					"param_name" => "title",
					'holder' => 'div',
					'std' => __('Explore our latest car rentals', 'bookyourtravel'),
					'save_always' => true,
				),
				array(
					"type" => "dropdown",
					"heading" => __("Number of posts", 'bookyourtravel'),
					"param_name" => "number_of_posts",
					'holder' => 'div',
					"value" => range(1, 100),
					"std" => "4",
					'save_always' => true,
				),
				array(
					"type" => "dropdown",
					"heading" => __("Sort by", 'bookyourtravel'),
					"param_name" => "sort_by",
					'holder' => 'div',
					"value" => array(
						__('Post Title', 'bookyourtravel') => 'title',
						__('Post ID', 'bookyourtravel') => 'ID',
						__('Random', 'bookyourtravel') => 'rand',
						__('Publish Date', 'bookyourtravel') => 'date',
						__('Comment count', 'bookyourtravel') => 'comment_count',
						__('Order attribute', 'bookyourtravel') => 'menu_order'
					),
					"std" => "title",
					'save_always' => true,
				),
				array(
					"type" => "checkbox",
					"heading" => __("Sort descending?", 'bookyourtravel'),
					"param_name" => "sort_descending",
					'holder' => 'div',
					"value" => array(
						__('Yes', 'bookyourtravel') => '1'
					),
					"std" => "1",
					'save_always' => true,
				),
				array(
					"type" => "checkbox",
					"heading" => __("Show featured car rentals only?", 'bookyourtravel'),
					"param_name" => "show_featured_only",
					'holder' => 'div',
					"value" => array(
						__('Yes', 'bookyourtravel') => '1'
					),
					"std" => "0",
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
					"heading" => __("Posts per row", 'bookyourtravel'),
					"param_name" => "posts_per_row",
					'holder' => 'div',
					"value" => range(1, 5),
					"std" => "4",
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
					   'Address'=>'address',
					   'Rating'=>'rating',
					   'Price'=>'price',
					),
					"std" => 'title,image,actions,price',
					"description" => __( "Select which car rental fields you want to show", "bookyourtravel" ),
					'save_always' => true,
				),
				array(
					"type" => "checkbox",
					"heading" => __("Car rental tag ids", 'bookyourtravel'),
					"param_name" => "car_rental_tag_ids",
					'holder' => 'div',
					'value' => array('empty' => 'empty'),
					'dependency' => array(
						'callback' => 'vcBookYourTravelCarRentalTagsDependencyCallback'
					),
					'save_always' => true,
				),
				array(
					"type" => "dropdown",
					"heading" => __("Car type", 'bookyourtravel'),
					"param_name" => "car_rental_type_ids",
					'holder' => 'div',
					"value" => $car_rental_type_params,
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
                __("Car Rental List", "bookyourtravel"),
                __("This is a car rental list widget. To access all the features of this block please use the front editor.", "bookyourtravel")
            )
		);

		vc_map(
			$byt_widget_car_rental_list_settings
		);
	}
}

$bookyourtravel_vc_byt_widget_car_rental_list_shortcode = BookYourTravel_VC_Byt_Widget_Car_Rental_List_Shortcode::get_instance();;
$bookyourtravel_vc_byt_widget_car_rental_list_shortcode->init();

// A must for container functionality, replace Wbc_Item with your base name from mapping for parent container
if(class_exists('WPBakeryShortCode')){
    class WPBakeryShortCode_byt_widget_car_rental_list extends WPBakeryShortCode {
		public function __construct( $settings ) {
			parent::__construct( $settings ); // !Important to call parent constructor to active all logic for shortcode.
			wp_enqueue_script( 'composer_scripts', BookYourTravel_Theme_Utils::get_file_uri ('/js/admin/composer/composer_scripts.js'), array('jquery'), BOOKYOURTRAVEL_VERSION, true );
		}
	}
}