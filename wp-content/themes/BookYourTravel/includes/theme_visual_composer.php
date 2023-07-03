<?php
/**
 * BookYourTravel_Theme_Visual_Composer class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once BookYourTravel_Theme_Utils::get_file_path('/includes/composer/vc_accommodation_card_shortcode.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/composer/vc_location_card_shortcode.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/composer/vc_cruise_card_shortcode.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/composer/vc_car_rental_card_shortcode.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/composer/vc_tour_card_shortcode.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/composer/vc_widget_address_shortcode.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/composer/vc_widget_call_to_action_shortcode.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/composer/vc_widget_home_feature_shortcode.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/composer/vc_widget_post_list_shortcode.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/composer/vc_widget_location_list_shortcode.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/composer/vc_widget_accommodation_list_shortcode.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/composer/vc_widget_cruise_list_shortcode.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/composer/vc_widget_car_rental_list_shortcode.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/composer/vc_widget_tour_list_shortcode.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/composer/vc_widget_social_shortcode.php');
// custom
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/composer/vc_widget_home_searching_shortcode.php');
// custom

require_once BookYourTravel_Theme_Utils::get_file_path('/includes/composer/vc_widget_iconic_features_shortcode.php');

class BookYourTravel_Theme_Visual_Composer extends BookYourTravel_BaseSingleton {
	
	protected function __construct() {
		// our parent class might contain shared code in its constructor
		parent::__construct();		
	}
	
  	public function init() {
		if ( class_exists('Vc_Manager') ) {
			add_action('vc_before_init', array($this, 'vc_before_init') );	
			add_action('vc_after_init', array($this, 'vc_after_init') );
			add_filter('bookyourtravel_vc_encode_string_for_editor', array($this, 'vc_encode_string_for_editor'));
			add_filter('bookyourtravel_vc_decode_string_from_editor', array($this, 'vc_decode_string_from_editor'));
			add_filter('vc_after_mapping', array($this, 'vc_after_mapping'));
		}
	}
	
	function vc_after_mapping() {
		add_filter( 'vc_autocomplete_byt_location_card_location_id_callback', array($this, 'vc_location_autocomplete_suggester'), 10, 1 );			
		add_filter( 'vc_autocomplete_byt_location_card_location_id_render', array($this, 'vc_location_autocomplete_render'), 10, 1 );

		add_filter( 'vc_autocomplete_byt_accommodation_card_accommodation_id_callback', array($this, 'vc_accommodation_autocomplete_suggester'), 10, 1 );			
		add_filter( 'vc_autocomplete_byt_accommodation_card_accommodation_id_render', array($this, 'vc_accommodation_autocomplete_render'), 10, 1 );
		
		add_filter( 'vc_autocomplete_byt_tour_card_tour_id_callback', array($this, 'vc_tour_autocomplete_suggester'), 10, 1 );			
		add_filter( 'vc_autocomplete_byt_tour_card_tour_id_render', array($this, 'vc_tour_autocomplete_render'), 10, 1 );
		
		add_filter( 'vc_autocomplete_byt_car_rental_card_car_rental_id_callback', array($this, 'vc_car_rental_autocomplete_suggester'), 10, 1 );			
		add_filter( 'vc_autocomplete_byt_car_rental_card_car_rental_id_render', array($this, 'vc_car_rental_autocomplete_render'), 10, 1 );

		add_filter( 'vc_autocomplete_byt_cruise_card_cruise_id_callback', array($this, 'vc_cruise_autocomplete_suggester'), 10, 1 );			
		add_filter( 'vc_autocomplete_byt_cruise_card_cruise_id_render', array($this, 'vc_cruise_autocomplete_render'), 10, 1 );		
	}

	function my_module_add_grid_shortcodes( $shortcodes ) {
		$shortcodes['vc_post_price'] = array(
			'name' => __( 'Post id', 'my-text-domain' ),
			'base' => 'vc_post_price',
			'category' => __( 'Content', 'my-text-domain' ),
			'description' => __( 'Show current post id', 'my-text-domain' ),
			'post_type' => Vc_Grid_Item_Editor::postType(),
		); 
		
		return $shortcodes;
	}
	 
	// output function
	function byt_post_price_render($atts) {
		return '{{ byt_post_price }}'; // usage of template variable post_data with argument "ID"
	}
	
	function vc_gitem_template_attribute_byt_post_price( $value, $data ) {
		global $bookyourtravel_accommodation_helper, $bookyourtravel_car_rental_helper, $bookyourtravel_cruise_helper, $bookyourtravel_tour_helper, $bookyourtravel_theme_globals;
		$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
		$default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
		$show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();
	
		extract( array_merge( array(
			'post' => null,
			'data' => '',
		), $data ) );
	
		switch ($post->post_type) {
			case 'accommodation': {
				$price = $bookyourtravel_accommodation_helper->get_min_future_price($post->ID);
				$price = BookYourTravel_Theme_Utils::get_price_in_current_currency($price);
			}; break;
			case 'car_rental': {
				$price = $bookyourtravel_car_rental_helper->get_min_future_price($post->ID);
				$price = BookYourTravel_Theme_Utils::get_price_in_current_currency($price);
			}; break;
			case 'cruise': {
				$price = $bookyourtravel_cruise_helper->get_min_future_price($post->ID);
				$price = BookYourTravel_Theme_Utils::get_price_in_current_currency($price);
			}; break;
			case 'tour': {
				$price = $bookyourtravel_tour_helper->get_min_future_price($post->ID);
			}; break;
			default: break;
		}
	
		$price_str = '';
		if ($price > 0) {
			ob_start();
			BookYourTravel_Theme_Controls::the_entity_price($price, esc_html__('From', 'bookyourtravel'),  "");
			$price_str = ob_get_clean();
		}
	
		return $price_str;
	}
	
	function vc_car_rental_autocomplete_suggester($query) {
		global $wpdb;
		$car_rental_id = (int) $query;
		$car_rental_id = $car_rental_id > 0 ? $car_rental_id : -1;

		$sql = $wpdb->prepare( "SELECT l.ID AS id, l.post_title AS title
					FROM {$wpdb->posts} AS l
					WHERE l.post_type = 'car_rental' AND ( l.ID = '%d' OR l.post_title LIKE '%s' )", 
					$car_rental_id, '%' . $wpdb->esc_like($query) . '%' );
		
		$car_rentals_array = $wpdb->get_results($sql, ARRAY_A);

		$results = array();
		if ( is_array( $car_rentals_array ) && ! empty( $car_rentals_array ) ) {
			foreach ( $car_rentals_array as $value ) {
				$data = array();
				$data['value'] = $value['id'];
				$title = __( 'Id', 'bookyourtravel' ) . ': ' . $value['id'] . (( strlen( $value['title'] ) > 0 ) ? ' - ' . __( 'Title', 'bookyourtravel' ) . ': ' . $value['title'] : '' );
				$data['label'] = $title;
				$results[] = $data;
			}
		}
		
		return $results;		
	}
	
	function vc_car_rental_autocomplete_render($query) {
		global $wpdb;
		$car_rental_id = (int) $query['value'];
		$car_rental_id = $car_rental_id > 0 ? $car_rental_id : -1;

		$sql = $wpdb->prepare( "SELECT l.ID AS id, l.post_title AS title
					FROM {$wpdb->posts} AS l
					WHERE l.post_type = 'car_rental' AND l.ID = '%d'", 
					$car_rental_id);
		
		$car_rentals_array = $wpdb->get_results($sql, ARRAY_A);

		$results = array();
		if ( is_array( $car_rentals_array ) && ! empty( $car_rentals_array ) ) {
			foreach ( $car_rentals_array as $value ) {
				$data = array();
				$data['value'] = $value['id'];
				$title = __( 'Id', 'bookyourtravel' ) . ': ' . $value['id'] . (( strlen( $value['title'] ) > 0 ) ? ' - ' . __( 'Title', 'bookyourtravel' ) . ': ' . $value['title'] : '' );
				$data['label'] = $title;
				$results = $data;
			}
		}
		
		return $results;		
	}	

	function vc_cruise_autocomplete_suggester($query) {
		global $wpdb;
		$cruise_id = (int) $query;
		$cruise_id = $cruise_id > 0 ? $cruise_id : -1;

		$sql = $wpdb->prepare( "SELECT l.ID AS id, l.post_title AS title
					FROM {$wpdb->posts} AS l
					WHERE l.post_type = 'cruise' AND ( l.ID = '%d' OR l.post_title LIKE '%s' )", 
					$cruise_id, '%' . $wpdb->esc_like($query) . '%' );
		
		$cruises_array = $wpdb->get_results($sql, ARRAY_A);

		$results = array();
		if ( is_array( $cruises_array ) && ! empty( $cruises_array ) ) {
			foreach ( $cruises_array as $value ) {
				$data = array();
				$data['value'] = $value['id'];
				$title = __( 'Id', 'bookyourtravel' ) . ': ' . $value['id'] . (( strlen( $value['title'] ) > 0 ) ? ' - ' . __( 'Title', 'bookyourtravel' ) . ': ' . $value['title'] : '' );
				$data['label'] = $title;
				$results[] = $data;
			}
		}
		
		return $results;		
	}
	
	function vc_cruise_autocomplete_render($query) {
		global $wpdb;
		$cruise_id = (int) $query['value'];
		$cruise_id = $cruise_id > 0 ? $cruise_id : -1;

		$sql = $wpdb->prepare( "SELECT l.ID AS id, l.post_title AS title
					FROM {$wpdb->posts} AS l
					WHERE l.post_type = 'cruise' AND l.ID = '%d'", 
					$cruise_id);
		
		$cruises_array = $wpdb->get_results($sql, ARRAY_A);

		$results = array();
		if ( is_array( $cruises_array ) && ! empty( $cruises_array ) ) {
			foreach ( $cruises_array as $value ) {
				$data = array();
				$data['value'] = $value['id'];
				$title = __( 'Id', 'bookyourtravel' ) . ': ' . $value['id'] . (( strlen( $value['title'] ) > 0 ) ? ' - ' . __( 'Title', 'bookyourtravel' ) . ': ' . $value['title'] : '' );
				$data['label'] = $title;
				$results = $data;
			}
		}
		
		return $results;		
	}		

	function vc_tour_autocomplete_suggester($query) {
		global $wpdb;
		$tour_id = (int) $query;
		$tour_id = $tour_id > 0 ? $tour_id : -1;

		$sql = $wpdb->prepare( "SELECT l.ID AS id, l.post_title AS title
					FROM {$wpdb->posts} AS l
					WHERE l.post_type = 'tour' AND ( l.ID = '%d' OR l.post_title LIKE '%s' )", 
					$tour_id, '%' . $wpdb->esc_like($query) . '%' );
		
		$tours_array = $wpdb->get_results($sql, ARRAY_A);

		$results = array();
		if ( is_array( $tours_array ) && ! empty( $tours_array ) ) {
			foreach ( $tours_array as $value ) {
				$data = array();
				$data['value'] = $value['id'];
				$title = __( 'Id', 'bookyourtravel' ) . ': ' . $value['id'] . (( strlen( $value['title'] ) > 0 ) ? ' - ' . __( 'Title', 'bookyourtravel' ) . ': ' . $value['title'] : '' );
				$data['label'] = $title;
				$results[] = $data;
			}
		}
		
		return $results;		
	}
	
	function vc_tour_autocomplete_render($query) {
		global $wpdb;
		$tour_id = (int) $query['value'];
		$tour_id = $tour_id > 0 ? $tour_id : -1;

		$sql = $wpdb->prepare( "SELECT l.ID AS id, l.post_title AS title
					FROM {$wpdb->posts} AS l
					WHERE l.post_type = 'tour' AND l.ID = '%d'", 
					$tour_id);
		
		$tours_array = $wpdb->get_results($sql, ARRAY_A);

		$results = array();
		if ( is_array( $tours_array ) && ! empty( $tours_array ) ) {
			foreach ( $tours_array as $value ) {
				$data = array();
				$data['value'] = $value['id'];
				$title = __( 'Id', 'bookyourtravel' ) . ': ' . $value['id'] . (( strlen( $value['title'] ) > 0 ) ? ' - ' . __( 'Title', 'bookyourtravel' ) . ': ' . $value['title'] : '' );
				$data['label'] = $title;
				$results = $data;
			}
		}
		
		return $results;		
	}	

	function vc_accommodation_autocomplete_suggester($query) {
		global $wpdb;
		$accommodation_id = (int) $query;
		$accommodation_id = $accommodation_id > 0 ? $accommodation_id : -1;

		$sql = $wpdb->prepare( "SELECT l.ID AS id, l.post_title AS title
					FROM {$wpdb->posts} AS l
					WHERE l.post_type = 'accommodation' AND ( l.ID = '%d' OR l.post_title LIKE '%s' )", 
					$accommodation_id, '%' . $wpdb->esc_like($query) . '%' );
		
		$accommodations_array = $wpdb->get_results($sql, ARRAY_A);

		$results = array();
		if ( is_array( $accommodations_array ) && ! empty( $accommodations_array ) ) {
			foreach ( $accommodations_array as $value ) {
				$data = array();
				$data['value'] = $value['id'];
				$title = __( 'Id', 'bookyourtravel' ) . ': ' . $value['id'] . (( strlen( $value['title'] ) > 0 ) ? ' - ' . __( 'Title', 'bookyourtravel' ) . ': ' . $value['title'] : '' );
				$data['label'] = $title;
				$results[] = $data;
			}
		}
		
		return $results;		
	}
	
	function vc_accommodation_autocomplete_render($query) {
		global $wpdb;
		$accommodation_id = (int) $query['value'];
		$accommodation_id = $accommodation_id > 0 ? $accommodation_id : -1;

		$sql = $wpdb->prepare( "SELECT l.ID AS id, l.post_title AS title
					FROM {$wpdb->posts} AS l
					WHERE l.post_type = 'accommodation' AND l.ID = '%d'", 
					$accommodation_id);
		
		$accommodations_array = $wpdb->get_results($sql, ARRAY_A);

		$results = array();
		if ( is_array( $accommodations_array ) && ! empty( $accommodations_array ) ) {
			foreach ( $accommodations_array as $value ) {
				$data = array();
				$data['value'] = $value['id'];
				$title = __( 'Id', 'bookyourtravel' ) . ': ' . $value['id'] . (( strlen( $value['title'] ) > 0 ) ? ' - ' . __( 'Title', 'bookyourtravel' ) . ': ' . $value['title'] : '' );
				$data['label'] = $title;
				$results = $data;
			}
		}
		
		return $results;		
	}	
	
	function vc_location_autocomplete_suggester($query) {
		global $wpdb;
		$location_id = (int) $query;
		$location_id = $location_id > 0 ? $location_id : -1;

		$sql = $wpdb->prepare( "SELECT l.ID AS id, l.post_title AS title
					FROM {$wpdb->posts} AS l
					WHERE l.post_type = 'location' AND ( l.ID = '%d' OR l.post_title LIKE '%s' )", 
					$location_id, '%' . $wpdb->esc_like($query) . '%' );
		
		$locations_array = $wpdb->get_results($sql, ARRAY_A);

		$results = array();
		if ( is_array( $locations_array ) && ! empty( $locations_array ) ) {
			foreach ( $locations_array as $value ) {
				$data = array();
				$data['value'] = $value['id'];
				$title = __( 'Id', 'bookyourtravel' ) . ': ' . $value['id'] . (( strlen( $value['title'] ) > 0 ) ? ' - ' . __( 'Title', 'bookyourtravel' ) . ': ' . $value['title'] : '' );
				$data['label'] = $title;
				$results[] = $data;
			}
		}
		
		return $results;		
	}
	
	function vc_location_autocomplete_render($query) {
		global $wpdb;
		$location_id = (int) $query['value'];
		$location_id = $location_id > 0 ? $location_id : -1;

		$sql = $wpdb->prepare( "SELECT l.ID AS id, l.post_title AS title
					FROM {$wpdb->posts} AS l
					WHERE l.post_type = 'location' AND l.ID = '%d'", 
					$location_id);
		
		$locations_array = $wpdb->get_results($sql, ARRAY_A);

		$results = array();
		if ( is_array( $locations_array ) && ! empty( $locations_array ) ) {
			foreach ( $locations_array as $value ) {
				$data = array();
				$data['value'] = $value['id'];
				$title = __( 'Id', 'bookyourtravel' ) . ': ' . $value['id'] . (( strlen( $value['title'] ) > 0 ) ? ' - ' . __( 'Title', 'bookyourtravel' ) . ': ' . $value['title'] : '' );
				$data['label'] = $title;
				$results = $data;
			}
		}
		
		return $results;		
	}	
	
	function vc_encode_string_for_editor($value) {
		$value = str_replace('"', '``', $value);
		$value = str_replace('[', '`{`', $value);
		$value = str_replace(']', '`}`', $value);
		return $value;
	}
	
	function vc_decode_string_from_editor($value) {
		$value = preg_replace('/^`{`/', '[', $value);
		$value = preg_replace('/`}`$/', ']', $value);
		$value = str_replace('``', '"', $value);		
		return $value;
	}
	
	/**
	 * Force Visual Composer to initialize as "built into the theme". This will hide certain tabs under the Settings->Visual Composer page
	 */
	function vc_before_init() {
		if (function_exists('vc_set_as_theme')) {
			vc_set_as_theme();
		}
	}
	
	function vc_after_init() {
		if (function_exists('vc_set_default_editor_post_types')) {
			$post_types = array(
				'page',
				'post',
				'location'
			);
			
			add_filter( 'vc_grid_item_shortcodes', array($this, 'my_module_add_grid_shortcodes' ) );
			add_filter( 'vc_gitem_template_attribute_byt_post_price', array($this, 'vc_gitem_template_attribute_byt_post_price'), 10, 2 );
			add_shortcode ( 'byt_post_price',  array($this, 'byt_post_price_render' ) );
		}
	}
}

// store the instance in a variable to be retrieved later and call init
$bookyourtravel_theme_visual_composer = BookYourTravel_Theme_Visual_Composer::get_instance();