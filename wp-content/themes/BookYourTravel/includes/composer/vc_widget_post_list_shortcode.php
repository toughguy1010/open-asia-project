<?php
/**
 * BookYourTravel_VC_Byt_Widget_Post_List_Shortcode class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BookYourTravel_VC_Byt_Widget_Post_List_Shortcode extends BookYourTravel_BaseSingleton {

	protected function __construct() {
        // our parent class might contain shared code in its constructor
        parent::__construct();
	}

  public function init() {
		if ( class_exists('Vc_Manager') ) {
			add_action( 'vc_before_init', array($this, 'byt_widget_post_list_shortcode_vc' ));

			add_action( 'wp_ajax_vc_bookyourtravel_get_category_ids', array(
				$this,
				'get_category_ids_ajax',
			) );
            add_action( 'vc_backend_editor_render', array($this, 'vc_backend_editor_render'));
		}
  }

  function vc_backend_editor_render() {
		wp_enqueue_style( 'vc-byt-style-custom', BookYourTravel_Theme_Utils::get_file_uri ('/css/admin/composer_custom.css') );
  }

	function get_category_ids_ajax(){

		vc_user_access()
			->checkAdminNonce()
			->validateDie()
			->wpAny( 'edit_posts', 'edit_pages' )
			->validateDie();

		$categories = get_categories( array(
			'orderby' => 'name',
			'order'   => 'ASC',
			'hide_empty'       => 0
		) );

		$assigned_ids = array();
		$assigned_ids_string = '';
		if (isset($_POST['categoryIds'])) {
			$assigned_ids_string = wp_kses($_POST['categoryIds'], array());
		}
		$assigned_ids = explode(',', $assigned_ids_string);

		$param = array(
			'param_name' => 'category_ids',
			'type' => 'checkbox',
		);
		$param_line = '';
		foreach ( $categories as $category ) {
			$label = $category->name;
			$v = $category->term_id;
			$checked = '';
			if (in_array($v, $assigned_ids)) {
				$checked = " checked='checked' ";
			}
			$param_line .= ' <label class="vc_checkbox-label"><input ' . $checked . ' id="' . $param['param_name'] . '-' . $v . '" value="' . $v . '" class="wpb_vc_param_value ' . $param['param_name'] . ' ' . $param['type'] . '" type="checkbox" name="' . $param['param_name'] . '"' . '> ' . $label . '</label>';
		}

		die( json_encode( $param_line ) );
	}

	function byt_widget_post_list_shortcode_vc() {

		$byt_vc_category_name = __("BookYourTravel", 'bookyourtravel');
		$byt_vc_category_name = apply_filters("bookyourtravel_vc_widgets_category_name", $byt_vc_category_name);

		$byt_widget_post_list_settings = array(
			'name'            => __('Post List', 'bookyourtravel'),
			'base'            => 'byt_widget_post_list',
			"description" => __( "Display a list of blog posts", "bookyourtravel" ),
			'content_element' => true,
			"class"	  => "byt_widget",
			'weight'		  => 11,			
			'category' 		  => $byt_vc_category_name,
			'params'          => array(
				array(
					"type" => "textfield",
					"heading" => __("Title", 'bookyourtravel'),
					"param_name" => "title",
					'holder' => 'div',
					'std' => __('Explore our latest offers', 'bookyourtravel'),
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
					"heading" => __("Category ids", 'bookyourtravel'),
					"param_name" => "category_ids",
					'holder' => 'div',
					'value' => array('empty' => 'empty'),
					'dependency' => array(
						'callback' => 'vcBookYourTravelPostCategoryDependencyCallback'
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
                __("Post List", "bookyourtravel"),
                __("This is a post list widget. To access all the features of this block please use the front editor.", "bookyourtravel")
            )
		);

		vc_map(
			$byt_widget_post_list_settings
		);
	}
}

$bookyourtravel_vc_byt_widget_post_list_shortcode = BookYourTravel_VC_Byt_Widget_Post_List_Shortcode::get_instance();;
$bookyourtravel_vc_byt_widget_post_list_shortcode->init();

// A must for container functionality, replace Wbc_Item with your base name from mapping for parent container
if(class_exists('WPBakeryShortCode')){
    class WPBakeryShortCode_byt_widget_post_list extends WPBakeryShortCode {
		public function __construct( $settings ) {
			parent::__construct( $settings ); // !Important to call parent constructor to active all logic for shortcode.
			wp_enqueue_script( 'composer_scripts', BookYourTravel_Theme_Utils::get_file_uri ('/js/admin/composer/composer_scripts.js'), array('jquery'), BOOKYOURTRAVEL_VERSION, true );
		}
	}
}