<?php

if ( file_exists( WP_PLUGIN_DIR .'/woocommerce/includes/api/legacy/v3/interface-wc-api-handler.php' ) ) {
	require_once( WP_PLUGIN_DIR .'/woocommerce/includes/api/legacy/v3/interface-wc-api-handler.php');
	require_once( WP_PLUGIN_DIR .'/woocommerce/includes/api/legacy/v3/class-wc-api-server.php');
	require_once( WP_PLUGIN_DIR .'/woocommerce/includes/api/legacy/v3/class-wc-api-json-handler.php');
	require_once( WP_PLUGIN_DIR .'/woocommerce/includes/api/legacy/v3/interface-wc-api-handler.php');
	require_once( WP_PLUGIN_DIR .'/woocommerce/includes/api/legacy/v3/class-wc-api-resource.php');
	require_once( WP_PLUGIN_DIR .'/woocommerce/includes/api/legacy/v3/class-wc-api-orders.php');
}

if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID', 'bookyourtravel_pa_accommodation_booking_id' );

if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID', 'bookyourtravel_pa_tour_booking_id' );

if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID', 'bookyourtravel_pa_cruise_booking_id' );

if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID', 'bookyourtravel_pa_car_rental_booking_id' );

if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY', 'bookyourtravel_booking_session_key' );

if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_ATT' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_ATT', 'bookyourtravel_pa_accommodation' );

if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_ROOM_TYPE_ATT' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_ROOM_TYPE_ATT', 'bookyourtravel_pa_room_type' );

if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_ATT' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_ATT', 'bookyourtravel_pa_tour' );

if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_ATT' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_ATT', 'bookyourtravel_pa_cruise' );

if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_CABIN_TYPE_ATT' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_CABIN_TYPE_ATT', 'bookyourtravel_pa_cabin_type' );

if ( ! defined( 'BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_ATT' ) )
    define( 'BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_ATT', 'bookyourtravel_pa_car_rental' );

class BookYourTravel_Theme_WooCommerce extends BookYourTravel_BaseSingleton {

	private $accommodation_product_slug = 'bookyourtravel-accommodation-product';
	private $tour_product_slug = 'bookyourtravel-tour-product';
	private $cruise_product_slug = 'bookyourtravel-cruise-product';
	private $car_rental_product_slug = 'bookyourtravel-car-rental-product';
	private $page_sidebar_positioning = '';
	private $default_product_placeholder_image_src = '';
	private	$date_format = '';
	private $use_woocommerce_for_checkout = false;

	protected function __construct() {

		global $bookyourtravel_theme_globals;

		$this->page_sidebar_positioning = $bookyourtravel_theme_globals->get_woocommerce_pages_sidebar_position();
		$this->page_sidebar_positioning = empty($this->page_sidebar_positioning) ? '' : $this->page_sidebar_positioning;
		$this->default_product_placeholder_image_src = $bookyourtravel_theme_globals->get_woocommerce_product_placeholder_image();
		$this->date_format = get_option('date_format');
		$this->use_woocommerce_for_checkout = $bookyourtravel_theme_globals->use_woocommerce_for_checkout();

        // our parent class might contain shared code in its constructor
        parent::__construct();
    }

    public function init() {

		if (BookYourTravel_Theme_Utils::is_woocommerce_active() && $this->use_woocommerce_for_checkout) {

			add_action('bookyourtravel_after_setup_theme', array($this, 'woocommerce_after_setup_theme'));
			add_action('init', array($this, 'setup'));
			add_action('bookyourtravel_before_delete_accommodation_booking', array( $this, 'before_delete_accommodation_booking'));
			add_action('bookyourtravel_before_delete_tour_booking', array( $this, 'before_delete_tour_booking'));
			add_action('bookyourtravel_before_delete_cruise_booking', array( $this, 'before_delete_cruise_booking'));
			add_action('bookyourtravel_before_delete_car_rental_booking', array( $this, 'before_delete_car_rental_booking'));

			add_action('wp_ajax_accommodation_booking_add_to_cart_ajax_request', array( $this, 'accommodation_booking_add_to_cart_ajax_request'));
			add_action('wp_ajax_nopriv_accommodation_booking_add_to_cart_ajax_request', array( $this, 'accommodation_booking_add_to_cart_ajax_request'));
			add_action('wp_ajax_tour_booking_add_to_cart_ajax_request', array( $this, 'tour_booking_add_to_cart_ajax_request'));
			add_action('wp_ajax_nopriv_tour_booking_add_to_cart_ajax_request', array( $this, 'tour_booking_add_to_cart_ajax_request'));
			add_action('wp_ajax_cruise_booking_add_to_cart_ajax_request', array( $this, 'cruise_booking_add_to_cart_ajax_request'));
			add_action('wp_ajax_nopriv_cruise_booking_add_to_cart_ajax_request', array( $this, 'cruise_booking_add_to_cart_ajax_request'));
			add_action('wp_ajax_car_rental_booking_add_to_cart_ajax_request', array( $this, 'car_rental_booking_add_to_cart_ajax_request'));
			add_action('wp_ajax_nopriv_car_rental_booking_add_to_cart_ajax_request', array( $this, 'car_rental_booking_add_to_cart_ajax_request'));
			add_filter('woocommerce_default_address_fields', array( $this, 'woocommerce_default_address_fields') );
			// add_action( 'pre_get_posts', array( $this, 'custom_pre_get_posts_query') );
			add_action('wp', array( $this, 'prevent_access_to_product_page'));

			add_filter('woocommerce_display_item_meta', array($this, 'woocommerce_display_item_meta'), 10, 3);
		}
	}

	function woocommerce_display_item_meta($html, $item = null, $args = null) {

		if (isset($item['variation_id'])) {
			$product_variation = new WC_Product_Variation(intval($item['variation_id']));
            if ($product_variation) {
                $variation_slug = $product_variation->get_slug();

                if (strpos($variation_slug, $this->accommodation_product_slug) !== false ||
                    strpos($variation_slug, $this->tour_product_slug) !== false ||
                    strpos($variation_slug, $this->cruise_product_slug) !== false ||
                    strpos($variation_slug, $this->car_rental_product_slug) !== false) {
                    return "";
                }
            }
		}

		return $html;
	}	

	function prevent_access_to_product_page(){
		global $post;

		if ($post && ($post->ID == $this->get_product_id('accommodation') || 
			$post->ID == $this->get_product_id('cruise') || 
			$post->ID == $this->get_product_id('car_rental') || 
			$post->ID == $this->get_product_id('tour'))) {
			global $wp_query;
			$wp_query->set_404();
			status_header(404);
		}
	}	

	function custom_pre_get_posts_query( $q ) {
		if ( ! $q->is_main_query() ) return;
		if ( ! $q->is_post_type_archive() ) return;

		$accommodation_product_id = $this->get_product_id("accommodation");
		$tour_product_id = $this->get_product_id("tour");
		$car_rental_product_id = $this->get_product_id("car_rental");
		$cruise_product_id = $this->get_product_id("cruise");

		if ( is_shop() ) {
			$q->set( 'post__not_in', array($accommodation_product_id, $tour_product_id, $car_rental_product_id, $cruise_product_id) ); // Replace 70 and 53 with your products IDs. Separate each ID with a comma.
		}
	}

	function woocommerce_default_address_fields( $fields ) {
		global $bookyourtravel_theme_globals, $bookyourtravel_theme_of_custom;

		$booking_form_fields = $bookyourtravel_theme_globals->get_booking_form_fields();

		foreach ($booking_form_fields as $booking_field) {

			$field_type = $booking_field['type'];
			$field_hidden = isset($booking_field['hide']) && $booking_field['hide'] == 1 ? true : false;
			$field_required = isset($booking_field['required']) && $booking_field['required'] == '1' ? true : false;
			$field_id = $booking_field['id'];
			$field_label = isset($booking_field['label']) ? $booking_field['label'] : '';
			$field_label = $bookyourtravel_theme_of_custom->get_translated_dynamic_string($bookyourtravel_theme_of_custom->get_option_id_context('booking_form_fields') . ' ' . $field_label, $field_label);

			$address_woo_field_id = '';

			if ($field_id == 'first_name' || $field_id == 'last_name' || $field_id == 'phone' || $field_id == 'email' || $field_id == 'country' || $field_id == 'state' || $field_id == 'address_2' || $field_id == 'company') {
				$address_woo_field_id = $field_id;
			} elseif ($field_id == 'zip') {
				$address_woo_field_id = 'postcode';
			} elseif ($field_id == 'town') {
				$address_woo_field_id = 'city';
			} elseif ($field_id == 'address') {
				$address_woo_field_id = 'address_1';
			}

			if (!empty($address_woo_field_id)) {
				if ($field_hidden) {
					if (isset($fields) && array_key_exists($address_woo_field_id, $fields)) {
						unset($fields[$address_woo_field_id]);
					}
				} else {
					$fields[$address_woo_field_id]['label'] = $field_label;
					$fields[$address_woo_field_id]['placeholder'] = $field_label;
					$fields[$address_woo_field_id]['required'] = $field_required;
				}
			}
		}

		return $fields;
	}

	function woocommerce_after_setup_theme() {
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}

	function deactivate_wcml_product_unduplicate( $not_active, $cart_content ) {
		return true;
	}

	/**
	 * Hook in woocommerce actions and filters
	 */
	function setup() {

		remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
		remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
		remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);

		add_action('woocommerce_before_main_content', array($this, 'before_main_content'), 30);
		add_action('woocommerce_after_main_content', array($this, 'after_main_content'), 30);
		add_action('woocommerce_before_main_content', array($this, 'customized_breadcrumbs'), 10, 0);
        add_filter('woocommerce_cart_item_name', array( $this, 'cart_item_name'), 20, 3);
        add_action('woocommerce_after_cart_item_name', array( $this, 'after_cart_item_name'), 20, 2);
		
		add_filter('woocommerce_cart_item_product', array($this, 'cart_item_product'), 20, 3);
		
		add_filter('woocommerce_order_item_name', array( $this, 'order_item_name'), 20, 3);
		add_filter('woocommerce_cart_item_thumbnail', array($this, 'cart_item_thumbnail'), 20, 3);
		add_filter('woocommerce_variation_is_purchasable', array($this, 'variation_is_purchasable'), 20, 2);
		add_action('woocommerce_before_calculate_totals', array( $this, 'set_custom_total_price'), 1, 101);
		add_action('woocommerce_before_order_itemmeta', array($this, 'before_order_itemmeta'), 20, 3);
		add_action( 'woocommerce_new_order_item', array( $this, 'new_order_item'), 10, 3 );

		add_action('woocommerce_checkout_order_processed', array( $this, 'checkout_order_processed'), 10, 3);
		add_action('woocommerce_order_status_changed', array( $this, 'order_status_changed'), 10, 4 );
		add_action('woocommerce_delete_order_items', array( $this, 'delete_order_items'), 10, 1);
		add_action('woocommerce_cart_updated', array( $this, 'cart_updated') );

		add_filter('loop_shop_columns', array($this, 'loop_shop_columns'));
		add_filter('post_class', array($this, 'post_class'));

		add_filter( 'woocommerce_checkout_fields' , array($this, 'override_checkout_fields' ));

		add_filter( 'wcml_exception_duplicate_products_in_cart', array( $this, 'deactivate_wcml_product_unduplicate'), 10, 2 );
		add_filter( 'woocommerce_email_recipient_new_order', array( $this, 'modify_email_headers_filter_function'), 10, 2);

		add_filter( 'woocommerce_order_formatted_billing_address', array( $this, 'override_woocommerce_order_formatted_billing_address'), 10, 2 );
		add_filter( 'woocommerce_formatted_address_replacements', array($this, 'override_woocommerce_formatted_address_replacements'), 10, 2);
		add_filter( 'woocommerce_localisation_address_formats', array($this, 'override_woocommerce_localisation_address_formats'), 10, 1);

		add_action( 'woocommerce_product_query', array($this, 'prefix_custom_pre_get_posts_query') );

		add_filter( 'woocommerce_get_undo_url', array($this, 'woocommerce_get_undo_url'), 10, 2);
		add_filter( 'woocommerce_cart_item_permalink', array($this, 'woocommerce_cart_item_permalink'), 10, 3);

		add_filter( 'woocommerce_cart_item_removed_title', array($this, 'woocommerce_cart_item_removed_title'), 10, 2);
		add_filter( 'woocommerce_add_message', array($this, 'woocommerce_add_message'), 10, 1);

		add_filter( 'woocommerce_order_item_get_name', array($this, 'woocommerce_order_item_get_name'), 10, 2);

		add_filter('wcml_update_cart_contents_lang_switch', array( $this, 'wcml_update_cart_contents_lang_switch'), 10, 4);

		add_filter('woocommerce_products_widget_query_args', array( $this, 'exclude_products_from_widget'), 10, 1 ) ;

		$this->woo_3_fix();
		$this->woo_shop_fix();
	}

	function exclude_products_from_widget($query_args) {
		$product_ids = array();
		$product_ids[] = $this->get_product_id('accommodation');
		$product_ids[] = $this->get_product_id('car_rental');
		$product_ids[] = $this->get_product_id('cruise');
		$product_ids[] = $this->get_product_id('tour');

		$query_args['post__not_in'] = $product_ids;
		return $query_args;
	}

	function wcml_update_cart_contents_lang_switch($cart_item = null, $key = null, $new_key = null, $current_language = null) {
		global $woocommerce;

		if ($woocommerce && !empty($key) && !empty($new_key)) {
			$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $key);

			if ($cart_item_meta) {
				$woocommerce->session->set(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $key, null);
				$woocommerce->session->set(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $new_key, $cart_item_meta);
			}
		}

		return $cart_item;
	}

	function woocommerce_order_item_get_name($value, $order_item) {
		if (method_exists($order_item, 'get_product_id')) {
			if ($this->get_product_id('accommodation') == $order_item->get_product_id()) {
				return __("Accommodation booking", "bookyourtravel");
			} else if ($this->get_product_id('car_rental') == $order_item->get_product_id()) {
				return __("Car rental booking", "bookyourtravel");
			} else if ($this->get_product_id('cruise') == $order_item->get_product_id()) {
				return __("Cruise booking", "bookyourtravel");
			} else if ($this->get_product_id('tour') == $order_item->get_product_id()) {
				return __("Tour booking", "bookyourtravel");
			}
		}

		return $value;
	}

	function woocommerce_add_message($message) {

		if ((strpos($message, __("Accommodation booking", "bookyourtravel")) !== false) ||
			(strpos($message, __("Cruise booking", "bookyourtravel")) !== false) ||
			(strpos($message, __("Car rental booking", "bookyourtravel")) !== false) ||
			(strpos($message, __("Tour booking", "bookyourtravel")) !== false)) {
			$message = __("Booking removed!", "bookyourtravel");
		}

		return $message;
	}

	function woocommerce_cart_item_removed_title($product_title, $cart_item) {

		if (isset($cart_item['product_id']) && isset($cart_item['product_id'])) {
			$product_id   	= $cart_item['product_id'];
			$variation_id   = $cart_item['variation_id'];

			$accommodation_product_id = $this->get_product_id('accommodation');
			$tour_product_id = $this->get_product_id('tour');
			$cruise_product_id = $this->get_product_id('cruise');
			$car_rental_product_id = $this->get_product_id('car_rental');

			switch ($product_id) {
				case $accommodation_product_id:
					$product_title = __("Accommodation booking", "bookyourtravel");
					break;
				case $tour_product_id:
					$product_title = __("Tour booking", "bookyourtravel");
					break;
				case $cruise_product_id:
					$product_title = __("Cruise booking", "bookyourtravel");
					break;
				case $car_rental_product_id:
					$product_title = __("Car rental booking", "bookyourtravel");
					break;
			}
		}

		return $product_title;
	}

	function woocommerce_cart_item_permalink($cart_item_permalink, $cart_item, $cart_item_key ) {
		global $woocommerce;
		$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key);
		if ($cart_item_meta != null) {
			if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID]) ||
				isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID]) ||
				isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID]) ||
				isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID])) {

				$cart_item_permalink = '#';
			}
		}

		return $cart_item_permalink;
	}

	function woocommerce_get_undo_url($cart_page_url, $cart_item_key) {

		global $woocommerce;
		$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key);

		if ($cart_item_meta != null) {
			$cart_page_url = '';
		}

		return $cart_page_url;
	}

	function prefix_custom_pre_get_posts_query( $q ) {

		if( is_shop() || is_archive() ) { // set conditions here
			$tax_query = (array) $q->get( 'tax_query' );

			$tax_query[] = array(
				   'taxonomy' => 'product_cat',
				   'field'    => 'slug',
				   'terms'    => array( 'bookyourtravel-products'), // set product categories here
				   'operator' => 'NOT IN'
			);

			$q->set( 'tax_query', $tax_query );
		}
	}

	function woo_shop_fix() {

		$bookyourtravel_woocommerce_categorized = get_option( '_byt_woocommerce_categorized', 0 );

		if (!$bookyourtravel_woocommerce_categorized) {
			$term = get_term_by('slug', 'bookyourtravel-products', 'product_cat', ARRAY_A);

			if (!$term) {
				$term = wp_insert_term( __('BookYourTravel Products', 'bookyourtravel'), 'product_cat', [
					'description'=> __('BookYourTravel Products category', 'bookyourtravel'),
					'slug' => 'bookyourtravel-products' ]
				);
			}

			$accommodation_product_id = $this->get_product_id('accommodation');
			$tour_product_id = $this->get_product_id('tour');
			$cruise_product_id = $this->get_product_id('cruise');
			$car_rental_product_id = $this->get_product_id('car_rental');

			wp_set_object_terms($accommodation_product_id, $term['term_id'], 'product_cat');
			wp_set_object_terms($tour_product_id, $term['term_id'], 'product_cat');
			wp_set_object_terms($cruise_product_id, $term['term_id'], 'product_cat');
			wp_set_object_terms($car_rental_product_id, $term['term_id'], 'product_cat');

			update_option( '_byt_woocommerce_categorized', 1 );
		}
	}

	function woo_3_fix() {

		$bookyourtravel_woo_3_fix = get_option('_bookyourtravel_woo_3_fix', null);

		if (!$bookyourtravel_woo_3_fix) {

			$accommodation_product_id = $this->get_product_id('accommodation');
			$tour_product_id = $this->get_product_id('tour');
			$cruise_product_id = $this->get_product_id('cruise');
			$car_rental_product_id = $this->get_product_id('car_rental');

			$product_attributes = array(
				BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_ATT => array(
					'name'			=> BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_ATT,
					'value'			=> '',
					'is_visible' 	=> '1',
					'is_variation' 	=> '1',
					'position' 		=> '1',
					'is_taxonomy' 	=> '0'
				),
				BOOKYOURTRAVEL_WOOCOMMERCE_ROOM_TYPE_ATT => array(
					'name'			=> BOOKYOURTRAVEL_WOOCOMMERCE_ROOM_TYPE_ATT,
					'value'			=> '',
					'is_visible' 	=> '1',
					'position' 		=> '1',
					'is_variation' 	=> '1',
					'is_taxonomy' 	=> '0'
				),
			);

			update_post_meta( $accommodation_product_id, '_product_attributes', $product_attributes);

			$product_attributes = array(
				BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_ATT => array(
					'name'			=> BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_ATT,
					'value'			=> '',
					'is_visible' 	=> '1',
					'position' 		=> '1',
					'is_variation' 	=> '1',
					'is_taxonomy' 	=> '0'
				),
			);

			update_post_meta( $tour_product_id, '_product_attributes', $product_attributes);

			$product_attributes = array(
				BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_ATT => array(
					'name'			=> BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_ATT,
					'value'			=> '',
					'is_visible' 	=> '1',
					'is_variation' 	=> '1',
					'position' 		=> '1',
					'is_taxonomy' 	=> '0'
				),
				BOOKYOURTRAVEL_WOOCOMMERCE_CABIN_TYPE_ATT => array(
					'name'			=> BOOKYOURTRAVEL_WOOCOMMERCE_CABIN_TYPE_ATT,
					'value'			=> '',
					'is_visible' 	=> '1',
					'is_variation' 	=> '1',
					'position' 		=> '1',
					'is_taxonomy' 	=> '0'
				),
			);

			update_post_meta( $cruise_product_id, '_product_attributes', $product_attributes);

			$product_attributes = array(
				BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_ATT => array(
					'name'			=> BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_ATT,
					'value'			=> '',
					'is_visible' 	=> '1',
					'is_variation' 	=> '1',
					'position' 		=> '1',
					'is_taxonomy' 	=> '0'
				),
			);

			update_post_meta( $car_rental_product_id, '_product_attributes', $product_attributes);

			update_option( '_bookyourtravel_woo_3_fix', true );
		}

	}

	function override_woocommerce_localisation_address_formats($formats) {

		global $bookyourtravel_theme_globals, $bookyourtravel_theme_of_custom;

		$booking_form_fields = $bookyourtravel_theme_globals->get_booking_form_fields();

		$extra_format = '';
		foreach ($booking_form_fields as $booking_field) {

			$field_type = $booking_field['type'];
			$field_hidden = isset($booking_field['hide']) && $booking_field['hide'] == 1 ? true : false;
			$field_id = $booking_field['id'];

			if ($field_id != 'agree_gdpr') {
				$woo_field_id = '';

				if ($field_id == 'first_name' || $field_id == 'last_name' || $field_id == 'phone' || $field_id == 'email' || $field_id == 'country' || $field_id == 'state' || $field_id == 'address_2' || $field_id == 'company') {
					$woo_field_id = 'billing_' . $field_id;
				} elseif ($field_id == 'zip') {
					$woo_field_id = 'billing_postcode';
				} elseif ($field_id == 'town') {
					$woo_field_id = 'billing_city';
				} elseif ($field_id == 'address') {
					$woo_field_id = 'billing_address_1';
				}

				if ($field_hidden) {
					if (array_key_exists($woo_field_id, $formats)) {
						unset($formats[$woo_field_id]);
					}
				} else {
					// field is not hidden
					if (empty($woo_field_id)) {
						$extra_format .= ", {" . $field_id . "}\n";
					}
				}
			}
		}

		foreach ($formats as $code => $format) {
			if (!empty($extra_format)) {
				$formats[$code] = $format . $extra_format;
			}
		}

		return $formats;
	}

	function override_woocommerce_formatted_address_replacements($fields, $args) {

		global $bookyourtravel_theme_globals, $bookyourtravel_theme_of_custom;

		$booking_form_fields = $bookyourtravel_theme_globals->get_booking_form_fields();

		foreach ($booking_form_fields as $booking_field) {

			$field_type = $booking_field['type'];
			$field_hidden = isset($booking_field['hide']) && $booking_field['hide'] == 1 ? true : false;
			$field_id = $booking_field['id'];

			$woo_field_id = '';

			if ($field_id == 'first_name' || $field_id == 'last_name' || $field_id == 'phone' || $field_id == 'email' || $field_id == 'country' || $field_id == 'state' || $field_id == 'address_2' || $field_id == 'company') {
				$woo_field_id = 'billing_' . $field_id;
			} elseif ($field_id == 'zip') {
				$woo_field_id = 'billing_postcode';
			} elseif ($field_id == 'town') {
				$woo_field_id = 'billing_city';
			} elseif ($field_id == 'address') {
				$woo_field_id = 'billing_address_1';
			}

			if ($field_hidden) {
				if (array_key_exists($woo_field_id, $fields)) {
					unset($fields[$woo_field_id]);
				}
			} else {
				// field is not hidden
				if (empty($woo_field_id) && !isset($address['{' . $field_id . '}'])) {
					if ( isset($args[$field_id])) {
						$fields['{' . $field_id . '}'] = $args[$field_id];
					} else {
						$fields['{' . $field_id . '}'] = '';
					}
				}
			}
		}

		return $fields;
	}

	function override_woocommerce_order_formatted_billing_address( $address, $wc_order ) {

		global $bookyourtravel_theme_of_custom, $bookyourtravel_theme_globals, $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper;

		if ($wc_order != null) {

			$booking_object = null;

			$items = $wc_order->get_items();

			if ($items != null) {

				foreach ($items as $item_id => $item) {

					$booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID, true);
					if ($booking_id > 0) {
						$booking_object = $bookyourtravel_accommodation_helper->get_accommodation_booking($booking_id);
					} else {
						$booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID, true);
						if ($booking_id > 0) {
							$booking_object = $bookyourtravel_tour_helper->get_tour_booking($booking_id);
						} else {
							$booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID, true);
							if ($booking_id > 0) {
								$booking_object = $bookyourtravel_cruise_helper->get_cruise_booking($booking_id);
							} else {
								$booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID, true);
								if ($booking_id > 0) {
									$booking_object = $bookyourtravel_car_rental_helper->get_car_rental_booking($booking_id);
								}
							}
						}
					}
				}
			}

			if ($booking_object != null && isset($booking_object->other_fields)) {

				$booking_object_other_fields = isset($booking_object->other_fields) ? unserialize($booking_object->other_fields) : array();
				$booking_form_fields = $bookyourtravel_theme_globals->get_booking_form_fields();

				foreach ($booking_form_fields as $booking_field) {

					$field_type = $booking_field['type'];
					$field_hidden = isset($booking_field['hide']) && $booking_field['hide'] == 1 ? true : false;
					$field_id = $booking_field['id'];

					if ($field_id != 'agree_gdpr') {
						$woo_field_id = '';

						if ($field_id == 'first_name' || $field_id == 'last_name' || $field_id == 'phone' || $field_id == 'email' || $field_id == 'country' || $field_id == 'state' || $field_id == 'address_2' || $field_id == 'company') {
							$woo_field_id = 'billing_' . $field_id;
						} elseif ($field_id == 'zip') {
							$woo_field_id = 'billing_postcode';
						} elseif ($field_id == 'town') {
							$woo_field_id = 'billing_city';
						} elseif ($field_id == 'address') {
							$woo_field_id = 'billing_address_1';
						}

						if ($field_hidden) {
							if (array_key_exists($woo_field_id, $address)) {
								unset($address[$woo_field_id]);
							}
						} else {

							// field is not hidden
							if (empty($woo_field_id) && !isset($address[$field_id])) {
								if ($field_id == 'special_requirements' && isset($booking_object->special_requirements)) {
									$address[$field_id] = $booking_object->special_requirements;
								} else if ( isset($booking_object_other_fields[$field_id])) {
									$address[$field_id] = $booking_object_other_fields[$field_id];
								}
							}
						}
					}
				}
			}
		}

		return $address;
	}

	function modify_email_headers_filter_function( $recipients, $order ) {

		global $bookyourtravel_theme_of_custom, $bookyourtravel_theme_globals, $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper;

		if ($order != null) {

			$items = $order->get_items();

			if ($items != null) {

				foreach ($items as $item_id => $item) {

					$contact_emails = '';

					$booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID, true);
					if ($booking_id > 0) {
						$booking_object = $bookyourtravel_accommodation_helper->get_accommodation_booking($booking_id);
						if ($booking_object != null) {
							$contact_emails = trim(get_post_meta($booking_object->accommodation_id, 'accommodation_contact_email', true ));
						}
					} else {
						$booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID, true);
						if ($booking_id > 0) {
							$booking_object = $bookyourtravel_tour_helper->get_tour_booking($booking_id);
							if ($booking_object != null) {
								$contact_emails = trim(get_post_meta($booking_object->tour_id, 'tour_contact_email', true ));
							}
						} else {
							$booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID, true);
							if ($booking_id > 0) {
								$booking_object = $bookyourtravel_cruise_helper->get_cruise_booking($booking_id);
								if ($booking_object != null) {
									$contact_emails = trim(get_post_meta($booking_object->cruise_id, 'cruise_contact_email', true ));
								}
							} else {
								$booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID, true);
								if ($booking_id > 0) {
									$booking_object = $bookyourtravel_car_rental_helper->get_car_rental_booking($booking_id);
									if ($booking_object != null) {
										$contact_emails = trim(get_post_meta($booking_object->car_rental_id, 'car_rental_contact_email', true ));
									}
								}
							}
						}
					}

					if (!empty($contact_emails)) {

						$emails_array = explode(';', $contact_emails);

						if (!empty($recipients)) {
							$recipients .= ',';
						}
						foreach ($emails_array as $email) {
							if (!empty($email)) {
								$recipients .= $email . ',';
							}
						}
						$recipients = rtrim($recipients, ',');
					}
				}
			}
		}

		return $recipients;
	}

	function override_checkout_fields($fields) {

		global $bookyourtravel_theme_globals, $bookyourtravel_theme_of_custom;

		$booking_form_fields = $bookyourtravel_theme_globals->get_booking_form_fields();

		foreach ($booking_form_fields as $booking_field) {

			$field_type = $booking_field['type'];
			$field_hidden = isset($booking_field['hide']) && $booking_field['hide'] == 1 ? true : false;
			$field_required = isset($booking_field['required']) && $booking_field['required'] == '1' ? true : false;
			$field_id = $booking_field['id'];
			$field_label = isset($booking_field['label']) ? $booking_field['label'] : '';
			$field_label = $bookyourtravel_theme_of_custom->get_translated_dynamic_string($bookyourtravel_theme_of_custom->get_option_id_context('booking_form_fields') . ' ' . $field_label, $field_label);
			$field_options_str = isset($booking_field['options']) ? $booking_field['options'] : '';

			if ($field_id != 'agree_gdpr') {
				$woo_field_id = '';

				if ($field_id == 'first_name' || $field_id == 'last_name' || $field_id == 'phone' || $field_id == 'email' || $field_id == 'country' || $field_id == 'state' || $field_id == 'address_2' || $field_id == 'company') {
					$woo_field_id = 'billing_' . $field_id;
				} elseif ($field_id == 'zip') {
					$woo_field_id = 'billing_postcode';
				} elseif ($field_id == 'town') {
					$woo_field_id = 'billing_city';
				} elseif ($field_id == 'address') {
					$woo_field_id = 'billing_address_1';
				}

				if ($field_hidden) {
					if (isset($fields['billing']) && array_key_exists($woo_field_id, $fields['billing'])) {
						unset($fields['billing'][$woo_field_id]);
					}
				} else {
					// field is not hidden
					if (empty($woo_field_id) && !isset($fields['billing'][$field_id])) {

						// and isn't a woo field ie is one we created dynamically.
						$woo_field_type = 'text';
						if ($field_type == 'textarea')
							$woo_field_type = 'textarea';
						else if ($field_type == 'email')
							$woo_field_type = 'email';
						else if ($field_type == 'select')
							$woo_field_type = 'select';
						else if ($field_type == 'checkbox')
							$woo_field_type = 'checkbox';							

						$fields['billing'][$field_id] = array(
							'type' => $woo_field_type,
							'label' => $field_label,
							'placeholder' => $field_label,
							'class' => array('form-row-wide'),
							'required' => $field_required
						);

						if ($field_type == 'select') {
							$fields['billing'][$field_id]['options'] = array();

							$field_options = explode(PHP_EOL, $field_options_str);

							foreach ($field_options as $field_option) {
								$option_array = explode(';', $field_option);

								$key = '';
								$val = '';
								if (count($option_array) > 1) {
									$key = $option_array[0];
									$val = $option_array[1];
								} else {
									$key = $option_array[0];
									$val = $option_array[0];
								}

								$fields['billing'][$field_id]['options'][$key] = $val;
							}
						}

					} else if (isset($woo_field_id) && isset($fields['billing'][$woo_field_id])) {
						$fields['billing'][$woo_field_id]['label'] = $field_label;
						$fields['billing'][$woo_field_id]['placeholder'] = $field_label;
						$fields['billing'][$woo_field_id]['required'] = $field_required;
					}
				}
			}
		}

		return $fields;
	}

	function before_delete_accommodation_booking($booking_id) {

		global $bookyourtravel_accommodation_helper;
		$booking_entry = $bookyourtravel_accommodation_helper->get_accommodation_booking($booking_id);

		if ($booking_entry != null) {

			$woo_order_id = $booking_entry->woo_order_id;

			if ($woo_order_id > 0) {

				$order = new WC_Order($woo_order_id);
                if ($order) {
                    $order->delete();
                }
			}
		}
	}

	function before_delete_tour_booking($booking_id) {

		global $bookyourtravel_tour_helper;
		$booking_entry = $bookyourtravel_tour_helper->get_tour_booking($booking_id);

		if ($booking_entry != null) {

			$woo_order_id = $booking_entry->woo_order_id;

			if ($woo_order_id > 0) {

				$order = new WC_Order($woo_order_id);
                if ($order) {
                    $order->delete();
                }
			}
		}
	}

	function before_delete_cruise_booking($booking_id) {

		global $bookyourtravel_cruise_helper;
		$booking_entry = $bookyourtravel_cruise_helper->get_cruise_booking($booking_id);

		if ($booking_entry != null) {

			$woo_order_id = $booking_entry->woo_order_id;

			if ($woo_order_id > 0) {

				$order = new WC_Order($woo_order_id);
                if ($order) {
                    $order->delete();
                }
			}
		}
	}

	function before_delete_car_rental_booking($booking_id) {

		global $bookyourtravel_car_rental_helper;
		$booking_entry = $bookyourtravel_car_rental_helper->get_car_rental_booking($booking_id);

		if ($booking_entry != null) {

			$woo_order_id = $booking_entry->woo_order_id;

			if ($woo_order_id > 0) {

				$order = new WC_Order($woo_order_id);
                if ($order) {
                    $order->delete();
                }
			}
		}
	}

	function cart_updated() {

		global $bookyourtravel_accommodation_helper, $bookyourtravel_cruise_helper, $bookyourtravel_tour_helper, $bookyourtravel_car_rental_helper, $woocommerce ;

		if ( isset( $_GET[ 'remove_item' ] ) ){

			$cart_item_key = $_GET[ 'remove_item' ];

			$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key);

			if ($cart_item_meta != null) {
				$accommodation_booking_id = 0;
				if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID])) {
					$accommodation_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID]);
					if ($accommodation_booking_id > 0) {
						$bookyourtravel_accommodation_helper->delete_accommodation_booking($accommodation_booking_id);
						unset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID]);
					}
				}

				$tour_booking_id = 0;
				if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID])) {
					$tour_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID]);
					if ($tour_booking_id > 0) {
						$bookyourtravel_tour_helper->delete_tour_booking($tour_booking_id);
						unset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID]);
					}
				}

				$cruise_booking_id = 0;
				if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID])) {
					$cruise_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID]);
					if ($cruise_booking_id > 0) {
						$bookyourtravel_cruise_helper->delete_cruise_booking($cruise_booking_id);
						unset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID]);
					}
				}

				$car_rental_booking_id = 0;
				if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID])) {
					$car_rental_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID]);
					if ($car_rental_booking_id > 0) {
						$bookyourtravel_car_rental_helper->delete_car_rental_booking($car_rental_booking_id);
						unset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID]);
					}
				}

				$woocommerce->session->set(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key, null);
				$woocommerce->cart->remove_cart_item($cart_item_key);
			}
		}
	}

	function delete_order_items( $order_id ) {

		global $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper;

		$order = new WC_Order( $order_id );

		if ($order != null) {

			$items = $order->get_items();

			foreach ($items as $item_id => $item) {

				$accommodation_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID, true);
				if ($accommodation_booking_id) {
					$bookyourtravel_accommodation_helper->delete_accommodation_booking(intval($accommodation_booking_id));
				}

				$tour_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID, true);
				if ($tour_booking_id) {
					$bookyourtravel_tour_helper->delete_tour_booking(intval($tour_booking_id));
				}

				$cruise_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID, true);
				if ($cruise_booking_id) {
					$bookyourtravel_cruise_helper->delete_cruise_booking(intval($cruise_booking_id));
				}

				$car_rental_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID, true);
				if ($car_rental_booking_id > 0) {
					$bookyourtravel_car_rental_helper->delete_car_rental_booking(intval($car_rental_booking_id));
				}
			}
		}
	}

	function order_status_changed( $order_id, $old_status, $new_status, $order ) {

		global $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper;

		$items = $order->get_items();

		if ($items != null) {

			foreach ($items as $item_id => $item) {

				$accommodation_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID, true);
				if ($accommodation_booking_id) {
					$bookyourtravel_accommodation_helper->update_booking_woocommerce_info($accommodation_booking_id, null, null, $new_status);
				}

				$tour_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID, true);
				if ($tour_booking_id) {
					$bookyourtravel_tour_helper->update_booking_woocommerce_info($tour_booking_id, null, null, $new_status);
				}

				$cruise_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID, true);
				if ($cruise_booking_id) {
					$bookyourtravel_cruise_helper->update_booking_woocommerce_info($cruise_booking_id, null, null, $new_status);
				}

				$car_rental_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID, true);
				if ($car_rental_booking_id) {
					$bookyourtravel_car_rental_helper->update_booking_woocommerce_info($car_rental_booking_id, null, null, $new_status);
				}
			}
		}
	}

	function checkout_order_processed( $order_id, $posted, $order ) {

		global $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper, $woocommerce, $bookyourtravel_theme_globals;
		global $current_user;

		if ($order != null) {

			$status = $order->get_status();

			if ($woocommerce->cart != null) {

				foreach ( $woocommerce->cart->cart_contents as $key => $value ) {

					$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $key);

					if ($cart_item_meta != null) {

						$booking_object = new stdClass();

						$booking_object->first_name = (isset($posted['billing_first_name']) ? sanitize_text_field($posted['billing_first_name']) : '');
						$booking_object->last_name = (isset($posted['billing_last_name']) ? sanitize_text_field($posted['billing_last_name']) : '');
						$booking_object->company = (isset($posted['billing_company']) ? sanitize_text_field($posted['billing_company']) : '');
						$booking_object->phone = (isset($posted['billing_phone']) ? sanitize_text_field($posted['billing_phone']) : '');
						$booking_object->email = (isset($posted['billing_email']) ? sanitize_text_field($posted['billing_email']) : '');
						$booking_object->address = (isset($posted['billing_address_1']) ? sanitize_text_field($posted['billing_address_1']) : '');
						$booking_object->address_2 = (isset($posted['billing_address_2']) ? sanitize_text_field($posted['billing_address_2']) : '');
						$booking_object->town = (isset($posted['billing_city']) ? sanitize_text_field($posted['billing_city']) : '');
						$booking_object->zip = (isset($posted['billing_postcode']) ? sanitize_text_field($posted['billing_postcode']) : '');
						$booking_object->state = (isset($posted['billing_state']) ? sanitize_text_field($posted['billing_state']) : '');
						$booking_object->country = (isset($posted['billing_country']) ? sanitize_text_field($posted['billing_country']) : '');
						$booking_object->special_requirements = (isset($posted['special_requirements']) ? sanitize_text_field($posted['special_requirements']) : '');
						$booking_object->other_fields = array();
						$booking_object->user_id = $current_user->ID;

						$booking_form_fields = $bookyourtravel_theme_globals->get_booking_form_fields();

						foreach ($booking_form_fields as $booking_field) {

							$field_hidden = isset($booking_field['hide']) && $booking_field['hide'] == 1 ? true : false;
							$field_id = $booking_field['id'];

							if (!$field_hidden && isset($posted[$field_id])) {

								if ($field_id != 'first_name' &&
									$field_id != 'last_name' &&
									$field_id != 'company' &&
									$field_id != 'email' &&
									$field_id != 'phone' &&
									$field_id != 'address' &&
									$field_id != 'address_2' &&
									$field_id != 'town' &&
									$field_id != 'zip' &&
									$field_id != 'state' &&
									$field_id != 'country' &&
									$field_id != 'special_requirements') {

									$booking_object->other_fields[$field_id] = sanitize_text_field($posted[$field_id]);
								}
							}
						}

						$accommodation_booking_id = 0;
						if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID])) {
							$accommodation_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID]);

							if ($accommodation_booking_id > 0) {

								$bookyourtravel_accommodation_helper->update_booking_woocommerce_info($accommodation_booking_id, $key, $order_id, $status);
								$bookyourtravel_accommodation_helper->update_accommodation_booking($accommodation_booking_id, $booking_object);
							}
						}

						$tour_booking_id = 0;
						if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID])) {
							$tour_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID]);

							if ($tour_booking_id > 0) {

								$bookyourtravel_tour_helper->update_booking_woocommerce_info($tour_booking_id, $key, $order_id, $status);
								$bookyourtravel_tour_helper->update_tour_booking($tour_booking_id, $booking_object);
							}
						}

						$cruise_booking_id = 0;
						if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID])) {
							$cruise_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID]);

							if ($cruise_booking_id > 0) {

								$bookyourtravel_cruise_helper->update_booking_woocommerce_info($cruise_booking_id, $key, $order_id, $status);
								$bookyourtravel_cruise_helper->update_cruise_booking($cruise_booking_id, $booking_object);
							}
						}

						$car_rental_booking_id = 0;
						if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID])) {
							$car_rental_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID]);

							if ($car_rental_booking_id > 0) {

								$bookyourtravel_car_rental_helper->update_booking_woocommerce_info($car_rental_booking_id, $key, $order_id, $status);
								$bookyourtravel_car_rental_helper->update_car_rental_booking($car_rental_booking_id, $booking_object);
							}
						}
					}
				}
			}
		}
	}

	function new_order_item( $item_id,  $item,  $order_id ) {

		global $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper, $woocommerce, $bookyourtravel_theme_globals;

		$order = new WC_Order( $order_id );

		if ($order != null) {

			if ($woocommerce->cart != null) {

				foreach ( $woocommerce->cart->cart_contents as $key => $cart_item ) {

					$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $key);

					$item_variation_id   = $item['variation_id'];
					$cart_variation_id   = $cart_item['variation_id'];

					if ($cart_item_meta != null && $item_variation_id != null && $cart_variation_id != null && $item_variation_id == $cart_variation_id) {

						$accommodation_booking_id = 0;
						if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID])) {
							$accommodation_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID]);
							if ($accommodation_booking_id) {
								wc_add_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID, $accommodation_booking_id, true);
							}
						};

						$tour_booking_id = 0;
						if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID])) {
							$tour_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID]);
							if ($tour_booking_id) {
								wc_add_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID, $tour_booking_id, true);
							}
						};

						$cruise_booking_id = 0;
						if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID])) {
							$cruise_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID]);
							if ($cruise_booking_id) {
								wc_add_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID, $cruise_booking_id, true);
							}
						};

						$car_rental_booking_id = 0;
						if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID])) {
							$car_rental_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID]);
							if ($car_rental_booking_id) {
								wc_add_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID, $car_rental_booking_id, true);
							}
						};

					}
				}
			}
		}
	}

	function add_order_item_meta($item_id, $values, $cart_item_key ) {

		global $woocommerce;
		$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key);

		if ($cart_item_meta != null) {

			$accommodation_booking_id = 0;
			if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID])) {
				$accommodation_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID]);
				if ($accommodation_booking_id) {
					wc_add_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID, $accommodation_booking_id, true);
				}
			};

			$tour_booking_id = 0;
			if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID])) {
				$tour_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID]);
				if ($tour_booking_id) {
					wc_add_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID, $tour_booking_id, true);
				}
			};

			$cruise_booking_id = 0;
			if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID])) {
				$cruise_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID]);
				if ($cruise_booking_id) {
					wc_add_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID, $cruise_booking_id, true);
				}
			};

			$car_rental_booking_id = 0;
			if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID])) {
				$car_rental_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID]);
				if ($car_rental_booking_id) {
					wc_add_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID, $car_rental_booking_id, true);
				}
			};
		}
	}

	function cart_item_thumbnail($image, $cart_item, $cart_item_key) {

		if (isset($cart_item['data'])) {

			$object_class = get_class($cart_item['data']);

			if ($object_class == 'WC_Product_Variation' && isset($cart_item['data'])) {

				global $woocommerce, $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper;

				$slug = $cart_item['data']->get_slug();

				if (strpos($slug, $this->accommodation_product_slug) !== false) {

					$product_id   	= $cart_item['product_id'];
					$variation_id   = $cart_item['variation_id'];

					$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key);

					if ($cart_item_meta != null) {

						$accommodation_booking_id = 0;
						$accommodation_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID]);

						if ($accommodation_booking_id > 0) {

							$booking_entry = $bookyourtravel_accommodation_helper->get_accommodation_booking($accommodation_booking_id);

							if ($booking_entry != null) {

								$accommodation_id = $booking_entry->accommodation_id;
								$room_type_id = $booking_entry->room_type_id;

								$accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
								$image_title = $accommodation_obj->get_title();
								$main_image_src = $accommodation_obj->get_main_image();
								if (empty($main_image_src)) {
									$main_image_src = BookYourTravel_Theme_Utils::get_file_uri('/images/uploads/img.jpg');
								}
								if ($room_type_id > 0) {
									$room_type_obj = new BookYourTravel_Room_Type(intval($room_type_id));
									$main_image_src = $room_type_obj->get_main_image('medium');
									$image_title = $room_type_obj->get_title();
								}

								if (empty($main_image_src)) {
									$main_image_src = $this->default_product_placeholder_image_src;
								}

								if (!empty($main_image_src)) {
									$image = "<img src='$main_image_src' alt='$image_title' />";
								}
							}
						}
					}
				}

				if (strpos($slug, $this->tour_product_slug) !== false) {

					$product_id   	= $cart_item['product_id'];
					$variation_id   = $cart_item['variation_id'];

					$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key);

					if ($cart_item_meta != null) {

						$tour_booking_id = 0;
						$tour_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID]);

						if ($tour_booking_id > 0) {

							$booking_entry = $bookyourtravel_tour_helper->get_tour_booking($tour_booking_id);

							if ($booking_entry != null) {

								$tour_id = $booking_entry->tour_id;

								$tour_obj = new BookYourTravel_Tour($tour_id);
								$image_title = $tour_obj->get_title();
								$main_image_src = $tour_obj->get_main_image();
								if (empty($main_image_src)) {
									$main_image_src = BookYourTravel_Theme_Utils::get_file_uri('/images/uploads/img.jpg');
								}

								if (empty($main_image_src)) {
									$main_image_src = $this->default_product_placeholder_image_src;
								}

								if (!empty($main_image_src)) {
									$image = "<img src='$main_image_src' alt='$image_title' />";
								}
							}
						}
					}
				}

				if (strpos($slug, $this->cruise_product_slug) !== false) {

					$product_id   	= $cart_item['product_id'];
					$variation_id   = $cart_item['variation_id'];

					$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key);

					if ($cart_item_meta != null) {

						$cruise_booking_id = 0;
						$cruise_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID]);

						if ($cruise_booking_id > 0) {

							$booking_entry = $bookyourtravel_cruise_helper->get_cruise_booking($cruise_booking_id);

							if ($booking_entry != null) {

								$cruise_id = $booking_entry->cruise_id;
								$cabin_type_id = $booking_entry->cabin_type_id;

								$cruise_obj = new BookYourTravel_Cruise($cruise_id);
								$image_title = $cruise_obj->get_title();
								$main_image_src = $cruise_obj->get_main_image();
								if (empty($main_image_src)) {
									$main_image_src = BookYourTravel_Theme_Utils::get_file_uri('/images/uploads/img.jpg');
								}
								if ($cabin_type_id > 0) {
									$cabin_type_obj = new BookYourTravel_Cabin_Type(intval($cabin_type_id));
									$main_image_src = $cabin_type_obj->get_main_image('medium');
									$image_title = $cabin_type_obj->get_title();
								}

								if (empty($main_image_src)) {
									$main_image_src = $this->default_product_placeholder_image_src;
								}

								if (!empty($main_image_src)) {
									$image = "<img src='$main_image_src' alt='$image_title' />";
								}
							}
						}
					}
				}

				if (strpos($slug, $this->car_rental_product_slug) !== false) {

					$product_id   	= $cart_item['product_id'];
					$variation_id   = $cart_item['variation_id'];

					$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key);

					if ($cart_item_meta != null) {

						$car_rental_booking_id = 0;
						$car_rental_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID]);

						if ($car_rental_booking_id > 0) {

							$booking_entry = $bookyourtravel_car_rental_helper->get_car_rental_booking($car_rental_booking_id);

							if ($booking_entry != null) {

								$car_rental_obj = new BookYourTravel_Car_Rental($booking_entry->car_rental_id);
								$image_title = $car_rental_obj->get_title();
								$main_image_src = $car_rental_obj->get_main_image();
								if (empty($main_image_src)) {
									$main_image_src = BookYourTravel_Theme_Utils::get_file_uri('/images/uploads/img.jpg');
								}

								if (empty($main_image_src)) {
									$main_image_src = $this->default_product_placeholder_image_src;
								}

								if (!empty($main_image_src)) {
									$image = "<img src='$main_image_src' alt='$image_title' />";
								}
							}
						}
					}
				}
			}
		}

		return $image;
	}

	// Show order details (from, to, transport type, dates etc) in order admin when viewing individual orders.
	function before_order_itemmeta($item_id, $item, $_product) {

		global $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper;

		$product_id   	= $item['product_id'];
		$variation_id   = $item['variation_id'];

		if (!isset($variation_id) || $variation_id == 0) {
			return;
		}
		$variation = new WC_Product_Variation($variation_id);

		$accommodation_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID, true);
		if ($accommodation_booking_id) {

			$booking_entry = $bookyourtravel_accommodation_helper->get_accommodation_booking($accommodation_booking_id);

			if ($booking_entry != null && $variation != null) {

				$accommodation_id = $booking_entry->accommodation_id;
				$accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
				$max_child_count = $accommodation_obj->get_max_child_count();

				$room_type_obj = null;
				$room_type_id = $booking_entry->room_type_id;
				if ($room_type_id > 0) {
					$room_type_obj = new BookYourTravel_Room_Type($room_type_id);
					$max_child_count = $room_type_obj->get_max_child_count();
				}
				$adults = $booking_entry->adults;
				$children = $booking_entry->children;
				$room_count = $booking_entry->room_count;
				$date_from = $booking_entry->date_from;
				$date_from = date_i18n($this->date_format, strtotime($date_from));
				$date_to = $booking_entry->date_to;
				$date_to = date_i18n($this->date_format, strtotime($date_to));

				$extra_items_string = '';
				$extra_items_array = array();
				if (!empty($booking_entry->extra_items)) {
					$extra_items_array = unserialize($booking_entry->extra_items);
				}

				if ($extra_items_array != null) {
					foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
						$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
						$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
					}
				}

				$extra_items_string = trim(rtrim($extra_items_string, ', '));

				$item_text = '<br />';
				$item_text .= sprintf(esc_html__('%s', 'bookyourtravel'), $accommodation_obj->get_title()) . '<br />';
				if ($room_type_obj) {
					$item_text .= sprintf(esc_html__('%s', 'bookyourtravel'), $room_type_obj->get_title()) . '<br />';
				}
				$item_text .= sprintf(esc_html__('Dates: %s to %s', 'bookyourtravel'), $date_from, $date_to) . '<br />';

				if ($max_child_count > 0) {
					$item_text .= sprintf(esc_html__('People: %d adults, %d children', 'bookyourtravel'), $adults, $children) . '<br />';
				} else {
					$item_text .= sprintf(esc_html__('People: %d', 'bookyourtravel'), $adults) . '<br />';
				}

				if (!$accommodation_obj->get_disabled_room_types()) {
					$item_text .= sprintf(esc_html__('Rooms: %d', 'bookyourtravel'), $room_count) . '<br />';
				};

				if (!empty($extra_items_string)) {
					$item_text .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string;
				}

				echo wp_kses($item_text, array('br' => array()));
			}
		}

		$tour_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID, true);
		if ($tour_booking_id) {

			$booking_entry = $bookyourtravel_tour_helper->get_tour_booking($tour_booking_id);

			if ($booking_entry != null && $variation != null) {

				$tour_id = $booking_entry->tour_id;
				$tour_obj = new BookYourTravel_Tour($tour_id);
				$adults = $booking_entry->adults;
				$children = $booking_entry->children;
				$tour_date = $booking_entry->tour_date;
				$tour_date = date_i18n($this->date_format, strtotime($tour_date));

				$extra_items_string = '';
				$extra_items_array = array();
				if (!empty($booking_entry->extra_items)) {
					$extra_items_array = unserialize($booking_entry->extra_items);
				}

				if ($extra_items_array != null) {
					foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
						$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
						$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
					}
				}

				$extra_items_string = trim(rtrim($extra_items_string, ', '));

				$item_text = '<br />';
				$item_text .= sprintf(esc_html__('%s', 'bookyourtravel'), $tour_obj->get_title()) . '<br />';
				$item_text .= sprintf(esc_html__('Tour date: %s', 'bookyourtravel'), $tour_date) . '<br />';

				$item_text .= sprintf(esc_html__('People: %d adults, %d children', 'bookyourtravel'), $adults, $children) . '<br />';

				if (!empty($extra_items_string)) {
					$item_text .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string;
				}

				echo wp_kses($item_text, array('br' => array()));
			}
		}

		$cruise_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID, true);
		if ($cruise_booking_id) {

			$booking_entry = $bookyourtravel_cruise_helper->get_cruise_booking($cruise_booking_id);

			if ($booking_entry != null && $variation != null) {

				$cruise_id = $booking_entry->cruise_id;
				$cruise_obj = new BookYourTravel_Cruise($cruise_id);
				$cabin_type_obj = null;
				$cabin_type_id = $booking_entry->cabin_type_id;
				if ($cabin_type_id > 0) {
					$cabin_type_obj = new BookYourTravel_Cabin_Type($cabin_type_id);
				}
				$adults = $booking_entry->adults;
				$children = $booking_entry->children;
				$cabin_count = $booking_entry->cabin_count;
				$cruise_date = $booking_entry->cruise_date;
				$cruise_date = date_i18n($this->date_format, strtotime($cruise_date));

				$extra_items_string = '';
				$extra_items_array = array();
				if (!empty($booking_entry->extra_items)) {
					$extra_items_array = unserialize($booking_entry->extra_items);
				}

				if ($extra_items_array != null) {
					foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
						$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
						$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
					}
				}

				$extra_items_string = trim(rtrim($extra_items_string, ', '));

				$item_text = '<br />';
				$item_text .= sprintf(esc_html__('%s', 'bookyourtravel'), $cruise_obj->get_title()) . '<br />';
				if ($cabin_type_obj) {
					$item_text .= sprintf(esc_html__('%s', 'bookyourtravel'), $cabin_type_obj->get_title()) . '<br />';
				}
				$item_text .= sprintf(esc_html__('Cruise date: %s', 'bookyourtravel'), $cruise_date) . '<br />';
				$item_text .= sprintf(esc_html__('People: %d adults, %d children', 'bookyourtravel'), $adults, $children) . '<br />';

				$item_text .= sprintf(esc_html__('Cabins: %d', 'bookyourtravel'), $cabin_count) . '<br />';

				if (!empty($extra_items_string)) {
					$item_text .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string;
				}

				echo wp_kses($item_text, array('br' => array()));
			}
		}

		$car_rental_booking_id = wc_get_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID, true);
		if ($car_rental_booking_id) {

			$booking_entry = $bookyourtravel_car_rental_helper->get_car_rental_booking($car_rental_booking_id);

			if ($booking_entry != null && $variation != null) {

				$car_rental_id = $booking_entry->car_rental_id;
				$car_rental_obj = new BookYourTravel_Car_Rental($car_rental_id);

				$start_date = $booking_entry->start_date;
				$start_date = date_i18n($this->date_format, strtotime($start_date));
				$end_date = $booking_entry->end_date;
				$end_date = date_i18n($this->date_format, strtotime($end_date));

				$extra_items_string = '';
				$extra_items_array = array();
				if (!empty($booking_entry->extra_items)) {
					$extra_items_array = unserialize($booking_entry->extra_items);
				}

				if ($extra_items_array != null) {
					foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
						$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
						$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
					}
				}

				$extra_items_string = trim(rtrim($extra_items_string, ', '));

				$item_text = '<br />';

				$item_text .= sprintf(esc_html__('%s', 'bookyourtravel'), $car_rental_obj->get_title()) . '<br />';
				$item_text .= sprintf(esc_html__('From date: %s', 'bookyourtravel'), $start_date) . '<br />';
				$item_text .= sprintf(esc_html__('To date: %s', 'bookyourtravel'), $end_date) . '<br />';
				$item_text .= sprintf(esc_html__('Pick up: %s', 'bookyourtravel'), $booking_entry->pick_up_title) . '<br />';
				$item_text .= sprintf(esc_html__('Drop off: %s', 'bookyourtravel'), $booking_entry->drop_off_title) . '<br />';

				if (!empty($extra_items_string)) {
					$item_text .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string;
				}

				echo wp_kses($item_text, array('br' => array()));
			}
		}

		if ($booking_entry) {
			global $bookyourtravel_theme_globals;
			if ($bookyourtravel_theme_globals->enable_deposit_payments()) {
				$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
				$default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
				$show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();			

				$total_at_property = $booking_entry->total_price - $booking_entry->cart_price;
				$total_at_property = (float)BookYourTravel_Theme_Utils::get_price_in_current_currency($total_at_property);

				$total_price_str = '';
				if (!$show_currency_symbol_after) {
					$total_price_str = $default_currency_symbol . ' ' . number_format_i18n($total_at_property, $price_decimal_places);
				} else {
					$total_price_str = number_format_i18n($total_at_property, $price_decimal_places) . ' ' . $default_currency_symbol;
				}

				echo esc_html__('Total due upon arrival: ', 'bookyourtravel') . $total_price_str;
			}
		}
	}

	function variation_is_purchasable($purchasable, $product_variation) {

		$object_class = get_class($product_variation);

		if ($object_class == 'WC_Product_Variation') {
			$slug = $product_variation->get_slug();

			if (strpos($slug, $this->accommodation_product_slug) !== false ||
				strpos($slug, $this->tour_product_slug) !== false ||
				strpos($slug, $this->cruise_product_slug) !== false ||
				strpos($slug, $this->car_rental_product_slug) !== false) {
				// mark purchasable as true even though we have not specified product price when creating product and variation, which allows us to set the price at the time product is added to cart.
				$purchasable = true;
			}
		}

		return $purchasable;
	}

	function set_custom_total_price($cart_object) {

		$booking_entry = null;
		
		// this is where we access our booking object, get price, and update cart with it to have things synced.
		global $woocommerce, $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper;

		foreach ( $cart_object->cart_contents as $key => $value ) {

			$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $key);

			if ($cart_item_meta != null) {

				if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID])) {

					$accommodation_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID]);
					if ($accommodation_booking_id > 0) {
						$booking_entry = $bookyourtravel_accommodation_helper->get_accommodation_booking($accommodation_booking_id);
					} else {
						unset( WC()->cart->cart_contents[$key] );
						$woocommerce->session->set(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $key, null);
						$woocommerce->cart->remove_cart_item($key);
					}
				} else if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID])) {

					$tour_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID]);
					if ($tour_booking_id > 0) {
						$booking_entry = $bookyourtravel_tour_helper->get_tour_booking($tour_booking_id);
					} else {
						unset( WC()->cart->cart_contents[$key] );
						$woocommerce->session->set(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $key, null);
						$woocommerce->cart->remove_cart_item($key);
					}
				} else if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID])) {

					$cruise_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID]);
					if ($cruise_booking_id > 0) {
						$booking_entry = $bookyourtravel_cruise_helper->get_cruise_booking($cruise_booking_id);
					} else {
						unset( WC()->cart->cart_contents[$key] );
						$woocommerce->session->set(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $key, null);
						$woocommerce->cart->remove_cart_item($key);
					}
				} else if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID])) {

					$car_rental_booking_id = intval($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID]);

					if ($car_rental_booking_id > 0) {
						$booking_entry = $bookyourtravel_car_rental_helper->get_car_rental_booking($car_rental_booking_id);
					} else {
						unset( WC()->cart->cart_contents[$key] );
						$woocommerce->session->set(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $key, null);
						$woocommerce->cart->remove_cart_item($key);
					}
				}

				if (isset($booking_entry) && isset($booking_entry->cart_price)) {
					if ($booking_entry->cart_price >= 0) {
						global $woocommerce_wpml;
						if ($woocommerce_wpml && isset($woocommerce_wpml->multi_currency)) {
							$cart_price = BookYourTravel_Theme_Utils::get_price_in_current_currency($booking_entry->cart_price);
							$value['data']->set_price($cart_price);
						} else {
							$value['data']->set_price($booking_entry->cart_price);
						}
					} else {
						unset(WC()->cart->cart_contents[$key] );
						$woocommerce->session->set(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $key, null);
						$woocommerce->cart->remove_cart_item($key);
					}
				}
			} else {
				if ($value['data']->get_price() == '') {
					// don't allow string price.
					$value['data']->set_price(0);

					$woocommerce->session->set(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $key, null);
				}
			}
		}
	}

	function cart_item_product($cart_item_data, $cart_item, $cart_item_key) {
		global $woocommerce, $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper;

		$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key);
		$booking_entry = null;

		if ($cart_item_meta) {
			if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID])) {

				$booking_id = $cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID];
				$booking_entry = $bookyourtravel_accommodation_helper->get_accommodation_booking($booking_id);

			} else if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID])) {

				$booking_id = $cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID];
				$booking_entry = $bookyourtravel_tour_helper->get_tour_booking($booking_id);

			} else if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID])) {

				$booking_id = $cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID];
				$booking_entry = $bookyourtravel_cruise_helper->get_cruise_booking($booking_id);

			} else if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID])) {

				$booking_id = $cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID];
				$booking_entry = $bookyourtravel_car_rental_helper->get_car_rental_booking($booking_id);

			}

			if (!$booking_entry) {
				$woocommerce->session->set(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key, null);
				$woocommerce->cart->remove_cart_item($cart_item_key);
				return null;
			}
		} else {
			$woocommerce->session->set(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key, null);
			$woocommerce->cart->remove_cart_item($cart_item_key);
			return null;
		}

		return $cart_item_data;
	}

	function order_item_name($product_title, $item) {

		global $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper;
		global $bookyourtravel_theme_globals;

		$product_id   	= $item['product_id'];
		$variation_id   = $item['variation_id'];

		if (!isset($variation_id) || $variation_id == 0) {
			return $product_title;
		}

		$variation = new WC_Product_Variation($variation_id);

		$booking_entry = null;

		if (isset($item[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID])) {

			$booking_id = (int)$item[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID];
			$booking_entry = $bookyourtravel_accommodation_helper->get_accommodation_booking($booking_id);

			if ($booking_entry != null && $variation != null) {

				$accommodation_id = $booking_entry->accommodation_id;
				$accommodation_id = BookYourTravel_Theme_Utils::get_current_language_post_id($accommodation_id, 'accommodation');
				$accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
				$max_child_count = $accommodation_obj->get_max_child_count();

				$room_type_obj = null;
				$room_type_id = $booking_entry->room_type_id;
				$room_type_id = BookYourTravel_Theme_Utils::get_current_language_post_id($room_type_id, 'room_type');
				if ($room_type_id > 0) {
					$room_type_obj = new BookYourTravel_Room_Type($room_type_id);
					$max_child_count = $room_type_obj->get_max_child_count();
				}

				$adults = $booking_entry->adults;
				$children = $booking_entry->children;
				$room_count = $booking_entry->room_count;
				$date_from = $booking_entry->date_from;
				$date_from = date_i18n($this->date_format, strtotime($date_from));
				$date_to = $booking_entry->date_to;
				$date_to = date_i18n($this->date_format, strtotime($date_to));

				$extra_items_string = '';
				$extra_items_array = array();
				if (!empty($booking_entry->extra_items)) {
					$extra_items_array = unserialize($booking_entry->extra_items);
				}

				if (is_array($extra_items_array)) {
					foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
						$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
						$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
					}
				}

				$extra_items_string = trim(rtrim($extra_items_string, ', '));

				$product_title = '';
				$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $accommodation_obj->get_title()) . '<br />';
				if ($room_type_obj) {
					$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $room_type_obj->get_title()) . '<br />';
				}
				$product_title .= sprintf(esc_html__('Dates: %s to %s', 'bookyourtravel'), $date_from, $date_to) . '<br />';

				if ($max_child_count > 0) {
					$product_title .= sprintf(esc_html__('People: %d adults, %d children', 'bookyourtravel'), $adults, $children) . '<br />';
				} else {
					$product_title .= sprintf(esc_html__('People: %d', 'bookyourtravel'), $adults) . '<br />';
				}

				if (!$accommodation_obj->get_disabled_room_types()) {
					$product_title .= sprintf(esc_html__('Rooms: %d', 'bookyourtravel'), $room_count) . '<br />';
				};

				if (!empty($extra_items_string)) {
					$product_title .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string . '<br />';
				}
			}
		}

		if (isset($item[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID])) {

			$booking_id = (int)$item[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID];
			$booking_entry = $bookyourtravel_tour_helper->get_tour_booking($booking_id);

			if ($booking_entry != null && $variation != null) {

				$tour_id = $booking_entry->tour_id;
				$tour_id = BookYourTravel_Theme_Utils::get_current_language_post_id($tour_id, 'tour');
				$tour_obj = new BookYourTravel_Tour($tour_id);
				$adults = $booking_entry->adults;
				$children = $booking_entry->children;
				$tour_date = $booking_entry->tour_date;
				$tour_date = date_i18n($this->date_format, strtotime($tour_date));

				$extra_items_string = '';
				$extra_items_array = array();
				if (!empty($booking_entry->extra_items)) {
					$extra_items_array = unserialize($booking_entry->extra_items);
				}

				if (is_array($extra_items_array)) {
					foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
						$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
						$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
					}
				}

				$extra_items_string = trim(rtrim($extra_items_string, ', '));

				$product_title = '';
				$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $tour_obj->get_title()) . '<br />';

				$product_title .= sprintf(esc_html__('Tour date: %s', 'bookyourtravel'), $tour_date) . '<br />';
				$product_title .= sprintf(esc_html__('People: %d adults, %d children', 'bookyourtravel'), $adults, $children) . '<br />';

				if (!empty($extra_items_string)) {
					$product_title .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string . '<br />';
				}
			}
		}

		if (isset($item[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID])) {

			$booking_id = (int)$item[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID];
			$booking_entry = $bookyourtravel_cruise_helper->get_cruise_booking($booking_id);

			if ($booking_entry != null && $variation != null) {

				$cruise_id = $booking_entry->cruise_id;
				$cruise_id = BookYourTravel_Theme_Utils::get_current_language_post_id($cruise_id, 'cruise');
				$cruise_obj = new BookYourTravel_Cruise($cruise_id);
				$cabin_type_obj = null;
				$cabin_type_id = $booking_entry->cabin_type_id;
				$cabin_type_id = BookYourTravel_Theme_Utils::get_current_language_post_id($cabin_type_id, 'cabin_type');
				if ($cabin_type_id > 0) {
					$cabin_type_obj = new BookYourTravel_Cabin_Type($cabin_type_id);
					$max_child_count = $cabin_type_obj->get_max_child_count();
				}

				$adults = $booking_entry->adults;
				$children = $booking_entry->children;
				$cabin_count = $booking_entry->cabin_count;
				$cruise_date = $booking_entry->cruise_date;
				$cruise_date = date_i18n($this->date_format, strtotime($cruise_date));

				$extra_items_string = '';
				$extra_items_array = array();
				if (!empty($booking_entry->extra_items)) {
					$extra_items_array = unserialize($booking_entry->extra_items);
				}

				if (is_array($extra_items_array)) {
					foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
						$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
						$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
					}
				}

				$extra_items_string = trim(rtrim($extra_items_string, ', '));

				$product_title = '';
				$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $cruise_obj->get_title()) . '<br />';
				if ($cabin_type_obj) {
					$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $cabin_type_obj->get_title()) . '<br />';
				}
				$product_title .= sprintf(esc_html__('Cruise date: %s', 'bookyourtravel'), $cruise_date) . '<br />';

				if ($max_child_count > 0) {
					$product_title .= sprintf(esc_html__('People: %d adults, %d children', 'bookyourtravel'), $adults, $children) . '<br />';
				} else {
					$product_title .= sprintf(esc_html__('People: %d', 'bookyourtravel'), $adults) . '<br />';
				}

				$product_title .= sprintf(esc_html__('Cabins: %d', 'bookyourtravel'), $cabin_count) . '<br />';

				if (!empty($extra_items_string)) {
					$product_title .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string . '<br />';
				}
			}
		}

		if (isset($item[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID])) {

			$booking_id = (int)$item[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID];
			$booking_entry = $bookyourtravel_car_rental_helper->get_car_rental_booking($booking_id);

			if ($booking_entry != null && $variation != null) {

				$car_rental_id = $booking_entry->car_rental_id;
				$car_rental_id = BookYourTravel_Theme_Utils::get_current_language_post_id($car_rental_id, 'car_rental');
				$car_rental_obj = new BookYourTravel_Car_Rental($car_rental_id);

				$start_date = $booking_entry->start_date;
				$start_date = date_i18n($this->date_format, strtotime($start_date));
				$end_date = $booking_entry->end_date;
				$end_date = date_i18n($this->date_format, strtotime($end_date));

				$extra_items_string = '';
				$extra_items_array = array();
				if (!empty($booking_entry->extra_items)) {
					$extra_items_array = unserialize($booking_entry->extra_items);
				}

				if (is_array($extra_items_array)) {
					foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
						$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
						$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
					}
				}

				$extra_items_string = trim(rtrim($extra_items_string, ', '));

				$product_title = '';
				$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $car_rental_obj->get_title()) . '<br />';
				$product_title .= sprintf(esc_html__('From date: %s', 'bookyourtravel'), $start_date) . '<br />';
				$product_title .= sprintf(esc_html__('To date: %s', 'bookyourtravel'), $end_date) . '<br />';
				$product_title .= sprintf(esc_html__('Pick up: %s', 'bookyourtravel'), $booking_entry->pick_up_title) . '<br />';
				$product_title .= sprintf(esc_html__('Drop off: %s', 'bookyourtravel'), $booking_entry->drop_off_title) . '<br />';

				if (!empty($extra_items_string)) {
					$product_title .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string . '<br />';
				}
			}
		}

		if ($booking_entry) {
			if ($bookyourtravel_theme_globals->enable_deposit_payments()) {
				$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
				$default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
				$show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();			

				$total_at_property = $booking_entry->total_price - $booking_entry->cart_price;
				$total_at_property = (float)BookYourTravel_Theme_Utils::get_price_in_current_currency($total_at_property);

				$total_price_str = '';
				if (!$show_currency_symbol_after) {
					$total_price_str = $default_currency_symbol . ' ' . number_format_i18n($total_at_property, $price_decimal_places);
				} else {
					$total_price_str = number_format_i18n($total_at_property, $price_decimal_places) . ' ' . $default_currency_symbol;
				}

				$product_title .= esc_html__('Total due upon arrival: ', 'bookyourtravel') . $total_price_str;
			}
		}	

		return $product_title;
    }

    function after_cart_item_name($cart_item, $cart_item_key) {
        global $woocommerce;

		if (isset($cart_item['data'])) {
			$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key);

            if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID]) ||
                isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID]) ||
                isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID]) ||
                isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID])) {

                $quantity_script = '';
				$quantity_script .= '<script>';
				$quantity_script .= '(function ($) {';
				$quantity_script .= '$(document).ready(function () {';
                $quantity_script .= '		$(".cart_item").each(function() {';

                if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID])) {
                    $quantity_script .= '			var removeLink = $("a[data-product_sku^=bookyourtravel_accommodation_booking_]");';
                } else if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID])) {
                    $quantity_script .= '			var removeLink = $("a[data-product_sku^=bookyourtravel_cruise_booking_]");';
                } else if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID])) {
                    $quantity_script .= '			var removeLink = $("a[data-product_sku^=bookyourtravel_car_rental_booking_]");';
                } else if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID])) {
                    $quantity_script .= '			var removeLink = $("a[data-product_sku^=bookyourtravel_tour_booking_]");';
                }

				$quantity_script .= '			var quantityTr = $(removeLink).parent().nextAll(".product-quantity");';
				$quantity_script .= '			var quantityInput = quantityTr.find(".qty");';
				$quantity_script .= '			if (quantityInput.length > 0) {';
				$quantity_script .= '				quantityInput.attr("disabled", "true");';
				$quantity_script .= '			}';
				$quantity_script .= '		})';
				$quantity_script .= '})';
				$quantity_script .= '}(jQuery));';
                $quantity_script .= '</script>';

                echo $quantity_script;
            }
        }
    }

	function cart_item_name($product_title, $cart_item, $cart_item_key){

		global $woocommerce, $bookyourtravel_theme_globals, $bookyourtravel_accommodation_helper, $bookyourtravel_tour_helper, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper;

		if (isset($cart_item['data'])) {
			$item_data = $cart_item['data'];

			$object_class = get_class($item_data);

			if ( !$item_data || $object_class != 'WC_Product_Variation') {
				var_dump($item_data);
				var_dump($object_class);

				return $product_title;
			}

			$slug = $item_data->get_slug();

			if (strpos($slug, $this->accommodation_product_slug) === false &&
				strpos($slug, $this->tour_product_slug) === false &&
				strpos($slug, $this->cruise_product_slug) === false &&
				strpos($slug, $this->car_rental_product_slug) === false) {
				return $product_title;
			}

			$variation_id = (int)$item_data->get_id();

			$variation = new WC_Product_Variation($variation_id);

			if (!$variation) {
				return $product_title;
			}

			$cart_item_meta = $woocommerce->session->get(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key);
			$booking_entry = null;

 			if ($cart_item_meta) {
				if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID])) {

					$booking_id = $cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID];
					$booking_entry = $bookyourtravel_accommodation_helper->get_accommodation_booking($booking_id);

					if ($booking_entry != null) {
						$accommodation_id = $booking_entry->accommodation_id;
						$accommodation_id = BookYourTravel_Theme_Utils::get_current_language_post_id($accommodation_id, 'accommodation');
						$accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);

						$max_child_count = $accommodation_obj->get_max_child_count();

						$room_type_obj = null;
						$room_type_id = $booking_entry->room_type_id;
						$room_type_id = BookYourTravel_Theme_Utils::get_current_language_post_id($room_type_id, 'room_type');
						if ($room_type_id > 0) {
							$room_type_obj = new BookYourTravel_Room_Type($room_type_id);
							$max_child_count = $room_type_obj->get_max_child_count();
						}

						$adults = $booking_entry->adults;
						$children = $booking_entry->children;
						$room_count = $booking_entry->room_count;
						$date_from = $booking_entry->date_from;
						$date_from = date_i18n($this->date_format, strtotime($date_from));
						$date_to = $booking_entry->date_to;
						$date_to = date_i18n($this->date_format, strtotime($date_to));

						$extra_items_string = '';
						$extra_items_array = array();
						if (!empty($booking_entry->extra_items)) {
							$extra_items_array = unserialize($booking_entry->extra_items);
						}

						if (is_array($extra_items_array)) {
							foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
								$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
								$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
							}
						}

						$extra_items_string = trim(rtrim($extra_items_string, ', '));

						$product_title = '';

						$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $accommodation_obj->get_title()) . '<br />';
						if ($room_type_obj) {
							$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $room_type_obj->get_title()) . '<br />';
						}
						$product_title .= sprintf(esc_html__('Dates: %s to %s', 'bookyourtravel'), $date_from, $date_to) . '<br />';

						if ($max_child_count > 0) {
							$product_title .= sprintf(esc_html__('People: %d adults, %d children', 'bookyourtravel'), $adults, $children) . '<br />';
						} else {
							$product_title .= sprintf(esc_html__('People: %d', 'bookyourtravel'), $adults) . '<br />';
						}

						if (!$accommodation_obj->get_disabled_room_types()) {
							$product_title .= sprintf(esc_html__('Rooms: %d', 'bookyourtravel'), $room_count) . '<br />';
						};

						if (!empty($extra_items_string)) {
							$product_title .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string . '<br />';
						}
					}
				} else if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID])) {

					$booking_id = $cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID];
					$booking_entry = $bookyourtravel_tour_helper->get_tour_booking($booking_id);

					if ($booking_entry != null) {

						$tour_id = $booking_entry->tour_id;
						$tour_id = BookYourTravel_Theme_Utils::get_current_language_post_id($tour_id, 'tour');
						$tour_obj = new BookYourTravel_Tour($tour_id);
						$adults = $booking_entry->adults;
						$children = $booking_entry->children;
						$tour_date = $booking_entry->tour_date;
						$tour_date = date_i18n($this->date_format, strtotime($tour_date));

						$extra_items_string = '';
						$extra_items_array = array();
						if (!empty($booking_entry->extra_items)) {
							$extra_items_array = unserialize($booking_entry->extra_items);
						}

						if (is_array($extra_items_array)) {
							foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
								$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
								$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
							}
						}

						$extra_items_string = trim(rtrim($extra_items_string, ', '));

						$product_title = '';

						$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $tour_obj->get_title()) . '<br />';
						$product_title .= sprintf(esc_html__('Tour date: %s', 'bookyourtravel'), $tour_date) . '<br />';
						$product_title .= sprintf(esc_html__('People: %d adults, %d children', 'bookyourtravel'), $adults, $children) . '<br />';

						if (!empty($extra_items_string)) {
							$product_title .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string . '<br />';
						}
					}
				} else if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID])) {

					$booking_id = $cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID];
					$booking_entry = $bookyourtravel_cruise_helper->get_cruise_booking($booking_id);

					if ($booking_entry != null) {

						$cruise_id = $booking_entry->cruise_id;
						$cruise_id = BookYourTravel_Theme_Utils::get_current_language_post_id($cruise_id, 'cruise');
						$cruise_obj = new BookYourTravel_Cruise($cruise_id);
						$cabin_type_obj = null;
						$cabin_type_id = $booking_entry->cabin_type_id;
						$cabin_type_id = BookYourTravel_Theme_Utils::get_current_language_post_id($cabin_type_id, 'cabin_type');
						if ($cabin_type_id > 0) {
							$cabin_type_obj = new BookYourTravel_Cabin_Type($cabin_type_id);
							$max_child_count = $cabin_type_obj->get_max_child_count();
						}

						$adults = $booking_entry->adults;
						$children = $booking_entry->children;
						$cabin_count = $booking_entry->cabin_count;
						$cruise_date = $booking_entry->cruise_date;
						$cruise_date = date_i18n($this->date_format, strtotime($cruise_date));

						$extra_items_string = '';
						$extra_items_array = array();
						if (!empty($booking_entry->extra_items)) {
							$extra_items_array = unserialize($booking_entry->extra_items);
						}

						if (is_array($extra_items_array)) {
							foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
								$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
								$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
							}
						}

						$extra_items_string = trim(rtrim($extra_items_string, ', '));

						$product_title = '';

						$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $cruise_obj->get_title()) . '<br />';
						if ($cabin_type_obj) {
							$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $cabin_type_obj->get_title()) . '<br />';
						}
						$product_title .= sprintf(esc_html__('Cruise date: %s', 'bookyourtravel'), $cruise_date) . '<br />';
						if ($max_child_count > 0) {
							$product_title .= sprintf(esc_html__('People: %d adults, %d children', 'bookyourtravel'), $adults, $children) . '<br />';
						} else {
							$product_title .= sprintf(esc_html__('People: %d', 'bookyourtravel'), $adults) . '<br />';
						}

						$product_title .= sprintf(esc_html__('Cabins: %d', 'bookyourtravel'), $cabin_count) . '<br />';

						if (!empty($extra_items_string)) {
							$product_title .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string . '<br />';
						}
					}
				} else if (isset($cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID])) {

					$booking_id = $cart_item_meta[BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID];
					$booking_entry = $bookyourtravel_car_rental_helper->get_car_rental_booking($booking_id);

					if ($booking_entry != null) {

						$car_rental_id = $booking_entry->car_rental_id;
						$car_rental_id = BookYourTravel_Theme_Utils::get_current_language_post_id($car_rental_id, 'car_rental');
						$car_rental_obj = new BookYourTravel_Car_Rental($car_rental_id);

						$start_date = $booking_entry->start_date;
						$start_date = date_i18n($this->date_format, strtotime($start_date));
						$end_date = $booking_entry->end_date;
						$end_date = date_i18n($this->date_format, strtotime($end_date));

						$extra_items_string = '';
						$extra_items_array = array();
						if (!empty($booking_entry->extra_items)) {
							$extra_items_array = unserialize($booking_entry->extra_items);
						}

						if (is_array($extra_items_array)) {
							foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
								$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
								$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
							}
						}

						$extra_items_string = trim(rtrim($extra_items_string, ', '));

						$product_title = '';

						$product_title .= sprintf(esc_html__('%s', 'bookyourtravel'), $car_rental_obj->get_title()) . '<br />';
						$product_title .= sprintf(esc_html__('From date: %s', 'bookyourtravel'), $start_date) . '<br />';
						$product_title .= sprintf(esc_html__('To date: %s', 'bookyourtravel'), $end_date) . '<br />';
						$product_title .= sprintf(esc_html__('Pick up: %s', 'bookyourtravel'), $booking_entry->pick_up_title) . '<br />';
						$product_title .= sprintf(esc_html__('Drop off: %s', 'bookyourtravel'), $booking_entry->drop_off_title) . '<br />';

						if (!empty($extra_items_string)) {
							$product_title .= esc_html__('Extras: ', 'bookyourtravel') . $extra_items_string . '<br />';
						}
					}
				}

				if ($booking_entry) {
					if ($bookyourtravel_theme_globals->enable_deposit_payments()) {
						$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
						$default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
						$show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();			
		
						$total_at_property = $booking_entry->total_price - $booking_entry->cart_price;
						$total_at_property = (float)BookYourTravel_Theme_Utils::get_price_in_current_currency($total_at_property);

						$total_price_str = '';
						if (!$show_currency_symbol_after) {
							$total_price_str = $default_currency_symbol . ' ' . number_format_i18n($total_at_property, $price_decimal_places);
						} else {
							$total_price_str = number_format_i18n($total_at_property, $price_decimal_places) . ' ' . $default_currency_symbol;
						}
		
						$product_title .= esc_html__('Total due upon arrival: ', 'bookyourtravel') . $total_price_str;
					}
				} else {
					$woocommerce->session->set(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key, null);
					$woocommerce->cart->remove_cart_item($cart_item_key);
				}
			}

			return $product_title;
		}
	}

	function is_variation_in_cart($product_id, $variation_id) {

		global $woocommerce;
		$pass = false;

		if ($woocommerce->cart != null) {

			foreach ( $woocommerce->cart->cart_contents as $cart_key => $cart_item ) {

				$added_product_id   	= (int)$cart_item['product_id'];
				$added_variation_id   = (int)$cart_item['variation_id'];

				if ($added_product_id == (int)$product_id && $added_variation_id == (int)$variation_id) {
					wc_add_notice( __( 'You cannot book two of the same item within one session.', 'bookyourtravel' ), 'error' );
					$pass = true;
				}
			}
		}

		return $pass;
	}

	function accommodation_booking_add_to_cart_ajax_request() {

		if ( isset($_REQUEST) ) {

			$nonce = $_REQUEST['nonce'];

			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {

				global $woocommerce, $bookyourtravel_accommodation_helper;

				if ($this->use_woocommerce_for_checkout) {

					$current_user = wp_get_current_user();

					$booking_request_object 	= $bookyourtravel_accommodation_helper->retrieve_booking_values_from_request();

                    if ($booking_request_object) {
                        $accommodation_booking_id 	= $bookyourtravel_accommodation_helper->create_accommodation_booking($current_user->ID, $booking_request_object);

                        $product_id 				= $this->get_product_id('accommodation');
                        $variation_id 				= $this->get_accommodations_product_variation_id($product_id, $booking_request_object->accommodation_id, $booking_request_object->room_type_id);

                        if ($product_id > 0 && $variation_id > 0) {
                            if (!$this->is_variation_in_cart($product_id, $variation_id)) {
                                $cart_item_key 			= $woocommerce->cart->add_to_cart($product_id, 1, $variation_id, null, null); // $cart_item_data);

                                if (!is_user_logged_in()) {
                                    $woocommerce->session->set_customer_session_cookie(true);
                                }
                                $woocommerce->session->set(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key, array(BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID => $accommodation_booking_id));

                                echo json_encode($accommodation_booking_id);
                            }
                        }
                    } else {
						echo -3;
					}

				} else  {
					echo -2;
				}
			} else  {
				echo -3;
			}
		}

		die();
	}

	function cruise_booking_add_to_cart_ajax_request() {

		global $woocommerce, $bookyourtravel_cruise_helper;

		if ( isset($_REQUEST) ) {

			$nonce = $_REQUEST['nonce'];

			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {

				if ($this->use_woocommerce_for_checkout) {

					$current_user = wp_get_current_user();

					$booking_request_object 	= $bookyourtravel_cruise_helper->retrieve_booking_values_from_request();

                    if ($booking_request_object) {
                        $cruise_booking_id 	= $bookyourtravel_cruise_helper->create_cruise_booking($current_user->ID, $booking_request_object);

                        $product_id 				= $this->get_product_id('cruise');
                        $variation_id 				= $this->get_cruises_product_variation_id($product_id, $booking_request_object->cruise_id, $booking_request_object->cabin_type_id);

                        if ($product_id > 0 && $variation_id > 0) {
                            if (!$this->is_variation_in_cart($product_id, $variation_id)) {
                                $cart_item_key 			= $woocommerce->cart->add_to_cart($product_id, 1, $variation_id, null, null); // $cart_item_data);

                                if (!is_user_logged_in()) {
                                    $woocommerce->session->set_customer_session_cookie(true);
                                }
                                $woocommerce->session->set(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key, array(BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID => $cruise_booking_id));

                                echo json_encode($cruise_booking_id);
                            }
                        }
                    } else {
						echo -3;
					}

				} else  {
					echo -2;
				}
			} else  {
				echo -3;
			}
		}

		die();
	}

	function tour_booking_add_to_cart_ajax_request() {

		if ( isset($_REQUEST) ) {

			$nonce = $_REQUEST['nonce'];

			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {

				global $woocommerce, $bookyourtravel_tour_helper;

				if ($this->use_woocommerce_for_checkout) {

					$current_user = wp_get_current_user();

					$booking_request_object 	= $bookyourtravel_tour_helper->retrieve_booking_values_from_request();

                    if ($booking_request_object) {
                        $tour_booking_id 	= $bookyourtravel_tour_helper->create_tour_booking($current_user->ID, $booking_request_object);

                        $product_id 				= $this->get_product_id('tour');
                        $variation_id 				= $this->get_tours_product_variation_id($product_id, $booking_request_object->tour_id);

                        if ($product_id > 0 && $variation_id > 0) {
                            if (!$this->is_variation_in_cart($product_id, $variation_id)) {
                                $cart_item_key 			= $woocommerce->cart->add_to_cart($product_id, 1, $variation_id, null, null); // $cart_item_data);

                                if (!is_user_logged_in()) {
                                    $woocommerce->session->set_customer_session_cookie(true);
                                }
                                $woocommerce->session->set(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key, array(BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID => $tour_booking_id));

								echo json_encode($tour_booking_id);
                            }
                        }
                    } else {
						echo -3;
					}

				} else  {
					echo -2;
				}
			} else  {
				echo -3;
			}
		}

		die();
	}

	function car_rental_booking_add_to_cart_ajax_request() {

		if ( isset($_REQUEST) ) {

			$nonce = $_REQUEST['nonce'];

			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {

				global $woocommerce, $bookyourtravel_car_rental_helper;

				if ($this->use_woocommerce_for_checkout) {

					$current_user = wp_get_current_user();

					$booking_request_object 	= $bookyourtravel_car_rental_helper->retrieve_booking_values_from_request();

                    if ($booking_request_object) {
                        $car_rental_booking_id 		= $bookyourtravel_car_rental_helper->create_car_rental_booking($current_user->ID, $booking_request_object);

                        $product_id 				= $this->get_product_id('car_rental');

                        $variation_id 				= $this->get_car_rentals_product_variation_id($product_id, $booking_request_object->car_rental_id);

                        if ($product_id > 0 && $variation_id > 0) {
                            if (!$this->is_variation_in_cart($product_id, $variation_id)) {
                                $cart_item_key 			= $woocommerce->cart->add_to_cart($product_id, 1, $variation_id, null, null); // $cart_item_data);

                                if (!is_user_logged_in()) {
                                    $woocommerce->session->set_customer_session_cookie(true);
                                }
                                $woocommerce->session->set(BOOKYOURTRAVEL_WOOCOMMERCE_BOOKING_SESSION_KEY . $cart_item_key, array(BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID => $car_rental_booking_id));

                                echo json_encode($car_rental_booking_id);
                            }
                        }
                    } else {
						echo -3;
					}

				} else  {
					echo -2;
				}
			} else  {
				echo -3;
			}
		}

		die();
	}

	function get_product_slug($post_type) {

		$slug = '';

		switch ($post_type) {
			case 'accommodation' : $slug = $this->accommodation_product_slug;break;
			case 'cruise' 		 : $slug = $this->cruise_product_slug;break;
			case 'tour' 		 : $slug = $this->tour_product_slug;break;
			case 'car_rental' 	 : $slug = $this->car_rental_product_slug;break;
			default 			 : $slug = $this->accommodation_product_slug;break;
		}

		return $slug;
	}

	function get_product_id($post_type) {

		$product_id = 0;

		if (BookYourTravel_Theme_Utils::is_woocommerce_active() && $this->use_woocommerce_for_checkout) {

			global $wpdb;

			$product_slug = '%' . $this->get_product_slug($post_type) . '%';

			$sql = "SELECT Id FROM $wpdb->posts WHERE post_type='product' AND post_name LIKE '%s' AND post_status='publish' LIMIT 1";

			$id = $wpdb->get_var($wpdb->prepare($sql, $product_slug));

			$product_id = intval($id);

			if (!isset($product_id) || empty($product_id)) {
				if ($post_type == 'accommodation') {
					$product_id 			= $this->create_accommodations_product();
				} else if ($post_type == 'tour') {
					$product_id 			= $this->create_tours_product();
				} else if ($post_type == 'cruise') {
					$product_id 			= $this->create_cruises_product();
				} else if ($post_type == 'car_rental') {
					$product_id 			= $this->create_car_rentals_product();
				}
			}

			if (BookYourTravel_Theme_Utils::is_wpml_active() ) {

				$translated_product_id = BookYourTravel_Theme_Utils::get_current_language_post_id($product_id, 'product', false);

				if (!isset($translated_product_id)) {
					// no translation exists yet... so create one
					icl_makes_duplicates_public($product_id);
					$translated_product_id = BookYourTravel_Theme_Utils::get_current_language_post_id($product_id, 'product');
				}

				$product_id = $translated_product_id;

				global $sitepress;
				if ($sitepress) {
					$active_languages = $sitepress->get_active_languages();
					foreach ($active_languages as $language => $details) {
						$p_id = BookYourTravel_Theme_Utils::get_language_post_id($product_id, 'product', $language, false);
						if (!isset($p_id)) {
							icl_makes_duplicates_public($product_id);
						}
					}
				}
			}
		}

		return $product_id;
	}

	function get_accommodations_product_variation_id($product_id, $accommodation_id, $room_type_id = 0) {

		$variation_id = 0;

		if (BookYourTravel_Theme_Utils::is_woocommerce_active() && $this->use_woocommerce_for_checkout) {

			global $wpdb;

			$product_variation_name = $this->build_accommodation_product_variation_slug($accommodation_id, $room_type_id);

			$sql = "SELECT ID FROM $wpdb->posts WHERE post_type='product_variation' AND post_parent = %d AND post_name = '%s' AND post_status='publish' LIMIT 1";

			$sql = $wpdb->prepare($sql, $product_id, $product_variation_name);

			$variation_id = $wpdb->get_var($sql);
			if (!isset($variation_id) || empty($variation_id)) {
				$variation_id 			= $this->create_accommodation_product_variation($product_id, $accommodation_id, $room_type_id);
			}

			if (BookYourTravel_Theme_Utils::is_wpml_active() ) {

				$translated_variation_id = BookYourTravel_Theme_Utils::get_current_language_post_id($variation_id, 'product_variation', false);

				if (!isset($translated_variation_id)) {
					// no translation exists yet... so create one
					icl_makes_duplicates_public($variation_id);
					$translated_variation_id = BookYourTravel_Theme_Utils::get_current_language_post_id($variation_id, 'product_variation');
				}

				$variation_id = $translated_variation_id;
			}
		}

		return $variation_id;
	}

	function get_tours_product_variation_id($product_id, $tour_id) {

		global $wpdb;

		$product_name = $this->build_tour_product_variation_slug($tour_id);

		$sql = "SELECT ID FROM $wpdb->posts WHERE post_type='product_variation' AND post_parent = %d AND post_name = '%s' AND post_status='publish' LIMIT 1";

		$sql = $wpdb->prepare($sql, $product_id, $product_name);

		$variation_id = $wpdb->get_var($sql);
		if (!isset($variation_id) || empty($variation_id)) {
			$variation_id 			= $this->create_tour_product_variation($product_id, $tour_id);
		}

		if (BookYourTravel_Theme_Utils::is_wpml_active() ) {

			$translated_variation_id = BookYourTravel_Theme_Utils::get_current_language_post_id($variation_id, 'product_variation', false);

			if (!isset($translated_variation_id)) {
				// no translation exists yet... so create one
				icl_makes_duplicates_public($variation_id);
				$translated_variation_id = BookYourTravel_Theme_Utils::get_current_language_post_id($variation_id, 'product_variation');
			}

			$variation_id = $translated_variation_id;
		}

		return $variation_id;
	}

	function get_cruises_product_variation_id($product_id, $cruise_id, $cabin_type_id = 0) {

		global $wpdb;

		$product_name = $this->build_cruise_product_variation_slug($cruise_id, $cabin_type_id);

		$sql = "SELECT ID FROM $wpdb->posts WHERE post_type='product_variation' AND post_parent = %d AND post_name = '%s' AND post_status='publish' LIMIT 1";

		$sql = $wpdb->prepare($sql, $product_id, $product_name);

		$variation_id = $wpdb->get_var($sql);
		if (!isset($variation_id) || empty($variation_id)) {
			$variation_id 			= $this->create_cruise_product_variation($product_id, $cruise_id, $cabin_type_id);
		}

		if (BookYourTravel_Theme_Utils::is_wpml_active() ) {

			$translated_variation_id = BookYourTravel_Theme_Utils::get_current_language_post_id($variation_id, 'product_variation', false);

			if (!isset($translated_variation_id)) {
				// no translation exists yet... so create one
				icl_makes_duplicates_public($variation_id);
				$translated_variation_id = BookYourTravel_Theme_Utils::get_current_language_post_id($variation_id, 'product_variation');
			}

			$variation_id = $translated_variation_id;
		}

		return $variation_id;
	}

	function get_car_rentals_product_variation_id($product_id, $car_rental_id) {

		global $wpdb;

		$product_name = $this->build_car_rental_product_variation_slug($car_rental_id);

		$sql = "SELECT ID FROM $wpdb->posts WHERE post_type='product_variation' AND post_parent = %d AND post_name = '%s' AND post_status='publish' LIMIT 1";

		$sql = $wpdb->prepare($sql, $product_id, $product_name);

		$variation_id = $wpdb->get_var($sql);

		if (!isset($variation_id) || empty($variation_id)) {
			$variation_id 			= $this->create_car_rental_product_variation($product_id, $car_rental_id);
		}

		if (BookYourTravel_Theme_Utils::is_wpml_active() ) {

			$translated_variation_id = BookYourTravel_Theme_Utils::get_current_language_post_id($variation_id, 'product_variation', false);

			if (!isset($translated_variation_id)) {
				// no translation exists yet... so create one
				icl_makes_duplicates_public($variation_id);
				$translated_variation_id = BookYourTravel_Theme_Utils::get_current_language_post_id($variation_id, 'product_variation');
			}

			$variation_id = $translated_variation_id;
		}

		return $variation_id;
	}

	function assign_product_category($product_id) {
		$term = get_term_by('slug', 'bookyourtravel-products', 'product_cat', ARRAY_A);

		if (!$term) {
			$term = wp_insert_term( __('BookYourTravel Products', 'bookyourtravel'), 'product_cat', [
				'description'=> __('BookYourTravel Products category', 'bookyourtravel'),
				'slug' => 'bookyourtravel-products' ]
			);
		}

		wp_set_object_terms($product_id, $term['term_id'], 'product_cat');
	}

	function create_accommodations_product() {

		$new_post = array(
			'post_title' 		=> esc_html__('BookYourTravel Accommodations Product', 'bookyourtravel'),
			'post_content' 		=> esc_html__('This is a variable product used for bookyourtravel theme accommodation bookings processed with WooCommerce', 'bookyourtravel'),
			'post_status' 		=> 'publish',
			'post_name' 		=> $this->accommodation_product_slug,
			'post_type' 		=> 'product',
			'comment_status' 	=> 'closed'
		);

		$product_id 			= wp_insert_post($new_post);

		$this->assign_product_category($product_id);

		$skuu 					= $this->random_sku('bookyourtravel_accommodation_booking_', 6);

		update_post_meta($product_id, '_sku', 				$skuu );

		wp_set_object_terms($product_id, 'variable', 		'product_type');

		$product_attributes = array(
			BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_ATT => array(
				'name'			=> BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_ATT,
				'value'			=> '',
				'is_visible' 	=> '1',
				'is_variation' 	=> '1',
				'position' 		=> '1',
				'is_taxonomy' 	=> '0'
			),
			BOOKYOURTRAVEL_WOOCOMMERCE_ROOM_TYPE_ATT => array(
				'name'			=> BOOKYOURTRAVEL_WOOCOMMERCE_ROOM_TYPE_ATT,
				'value'			=> '',
				'is_visible' 	=> '1',
				'is_variation' 	=> '1',
				'position' 		=> '1',
				'is_taxonomy' 	=> '0'
			),
		);

		update_post_meta( $product_id, '_product_attributes', $product_attributes);

		return $product_id;
	}

	function create_tours_product() {

		$new_post = array(
			'post_title' 		=> esc_html__('BookYourTravel Tours Product', 'bookyourtravel'),
			'post_content' 		=> esc_html__('This is a variable product used for bookyourtravel theme tour bookings processed with WooCommerce', 'bookyourtravel'),
			'post_status' 		=> 'publish',
			'post_name' 		=> $this->tour_product_slug,
			'post_type' 		=> 'product',
			'comment_status' 	=> 'closed'
		);

		$product_id 			= wp_insert_post($new_post);
		$this->assign_product_category($product_id);
		$skuu 					= $this->random_sku('bookyourtravel_tour_booking_', 6);

		update_post_meta($product_id, '_sku', 				$skuu );

		wp_set_object_terms($product_id, 'variable', 		'product_type');

		$product_attributes = array(
			BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_ATT => array(
				'name'			=> BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_ATT,
				'value'			=> '',
				'is_visible' 	=> '1',
				'is_variation' 	=> '1',
				'position' 		=> '1',
				'is_taxonomy' 	=> '0'
			),
		);

		update_post_meta( $product_id, '_product_attributes', $product_attributes);

		return $product_id;
	}

	function create_cruises_product() {

		$new_post = array(
			'post_title' 		=> esc_html__('BookYourTravel Cruises Product', 'bookyourtravel'),
			'post_content' 		=> esc_html__('This is a variable product used for bookyourtravel theme cruise bookings processed with WooCommerce', 'bookyourtravel'),
			'post_status' 		=> 'publish',
			'post_name' 		=> $this->cruise_product_slug,
			'post_type' 		=> 'product',
			'comment_status' 	=> 'closed'
		);

		$product_id 			= wp_insert_post($new_post);
		$this->assign_product_category($product_id);
		$skuu 					= $this->random_sku('bookyourtravel_cruise_booking_', 6);

		update_post_meta($product_id, '_sku', 				$skuu );

		wp_set_object_terms($product_id, 'variable', 		'product_type');

		$product_attributes = array(
			BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_ATT => array(
				'name'			=> BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_ATT,
				'value'			=> '',
				'is_visible' 	=> '1',
				'is_variation' 	=> '1',
				'position' 		=> '1',
				'is_taxonomy' 	=> '0'
			),
			BOOKYOURTRAVEL_WOOCOMMERCE_CABIN_TYPE_ATT => array(
				'name'			=> BOOKYOURTRAVEL_WOOCOMMERCE_CABIN_TYPE_ATT,
				'value'			=> '',
				'is_visible' 	=> '1',
				'is_variation' 	=> '1',
				'position' 		=> '1',
				'is_taxonomy' 	=> '0'
			),
		);

		update_post_meta( $product_id, '_product_attributes', $product_attributes);

		return $product_id;
	}

	function create_car_rentals_product() {

		$new_post = array(
			'post_title' 		=> esc_html__('BookYourTravel Car Rentals Product', 'bookyourtravel'),
			'post_content' 		=> esc_html__('This is a variable product used for bookyourtravel theme car rental bookings processed with WooCommerce', 'bookyourtravel'),
			'post_status' 		=> 'publish',
			'post_name' 		=> $this->car_rental_product_slug,
			'post_type' 		=> 'product',
			'comment_status' 	=> 'closed'
		);

		$product_id 			= wp_insert_post($new_post);
		$this->assign_product_category($product_id);
		$skuu 					= $this->random_sku('bookyourtravel_car_rental_booking_', 6);

		update_post_meta($product_id, '_sku', 				$skuu );

		wp_set_object_terms($product_id, 'variable', 		'product_type');

		$product_attributes = array(
			BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_ATT => array(
				'name'			=> BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_ATT,
				'value'			=> '',
				'is_visible' 	=> '1',
				'is_variation' 	=> '1',
				'position' 		=> '1',
				'is_taxonomy' 	=> '0'
			),
		);

		update_post_meta( $product_id, '_product_attributes', $product_attributes);

		return $product_id;
	}

	function build_accommodation_product_variation_title($accommodation_id, $room_type_id = 0) {

		$cl_accommodation_id = BookYourTravel_Theme_Utils::get_current_language_post_id($accommodation_id, 'accommodation');
		$cl_room_type_id = BookYourTravel_Theme_Utils::get_current_language_post_id($room_type_id, 'room_type');

		$accommodation_title = get_the_title($cl_accommodation_id);
		$room_type_title = get_the_title($cl_room_type_id);

		$variation_title = sprintf(__('Accommodation %s', 'bookyourtravel'), $accommodation_title);
		if (!empty($room_type_title)) {
			$variation_title .= sprintf(__(' (%s) ', 'bookyourtravel'), $room_type_title);
		}
		$variation_title .= __('booking', 'bookyourtravel');

		return $variation_title;
	}

	function build_tour_product_variation_title($tour_id) {

		$cl_tour_id = BookYourTravel_Theme_Utils::get_current_language_post_id($tour_id, 'tour');
		$tour_title = get_the_title($cl_tour_id);

		$variation_title = sprintf(__('Tour %s ', 'bookyourtravel'), $tour_title);
		$variation_title .= __('booking', 'bookyourtravel');

		return $variation_title;
	}

	function build_cruise_product_variation_title($cruise_id, $cabin_type_id = 0) {

		$cl_cruise_id = BookYourTravel_Theme_Utils::get_current_language_post_id($cruise_id, 'cruise');
		$cl_cabin_type_id = BookYourTravel_Theme_Utils::get_current_language_post_id($cabin_type_id, 'cabin_type');

		$cruise_title = get_the_title($cl_cruise_id);
		$cabin_type_title = get_the_title($cl_cabin_type_id);

		$variation_title = sprintf(__('Cruise %s', 'bookyourtravel'), $cruise_title);
		if (!empty($room_type_title)) {
			$variation_title .= sprintf(__(' (%s) ', 'bookyourtravel'), $cabin_type_title);
		}
		$variation_title .= __('booking', 'bookyourtravel');

		return $variation_title;
	}

	function build_car_rental_product_variation_title($car_rental_id) {

		$cl_car_rental_id = BookYourTravel_Theme_Utils::get_current_language_post_id($car_rental_id, 'car_rental');
		$car_rental_title = get_the_title($cl_car_rental_id);

		$variation_title = sprintf(__('Car rental %s ', 'bookyourtravel'), $car_rental_title);
		$variation_title .= __('booking', 'bookyourtravel');

		return $variation_title;
	}

	function create_accommodation_product_variation($product_id, $accommodation_id, $room_type_id = 0) {

		$variation_title = $this->build_accommodation_product_variation_title($accommodation_id, $room_type_id);

		$new_post = array(
			'post_title' 		=> $variation_title,
			'post_content' 		=> __('This is a bookyourtravel accommodation product variation', 'bookyourtravel'),
			'post_status' 		=> 'publish',
			'post_type' 		=> 'product_variation',
			'post_parent'		=> $product_id,
			'post_name' 		=> $this->build_accommodation_product_variation_slug($accommodation_id, $room_type_id),
			'comment_status' 	=> 'closed'
		);

		$variation_id 			= wp_insert_post($new_post);

		update_post_meta($variation_id, '_stock_status', 		'instock');
		// update_post_meta($variation_id, '_sold_individually', 	'yes');
		update_post_meta($variation_id, '_virtual', 			'yes');
		update_post_meta($variation_id, '_manage_stock', 'no' );
		update_post_meta($variation_id, '_downloadable', 'no' );
		update_post_meta($variation_id, 'attribute_' . BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_ATT, $accommodation_id);
		update_post_meta($variation_id, 'attribute_' . BOOKYOURTRAVEL_WOOCOMMERCE_ROOM_TYPE_ATT, $room_type_id);

		return $variation_id;
	}

	function create_tour_product_variation($product_id, $tour_id) {

		$variation_title = $this->build_tour_product_variation_title($tour_id);

		$new_post = array(
			'post_title' 		=> $variation_title,
			'post_content' 		=> __('This is a bookyourtravel tour product variation', 'bookyourtravel'),
			'post_status' 		=> 'publish',
			'post_type' 		=> 'product_variation',
			'post_parent'		=> $product_id,
			'post_name' 		=> $this->build_tour_product_variation_slug($tour_id),
			'comment_status' 	=> 'closed'
		);

		$variation_id 			= wp_insert_post($new_post);

		update_post_meta($variation_id, '_stock_status', 		'instock');
		// update_post_meta($variation_id, '_sold_individually', 	'yes');
		update_post_meta($variation_id, '_virtual', 			'yes');
		update_post_meta($variation_id, '_manage_stock', 'no' );
		update_post_meta($variation_id, '_downloadable', 'no' );
		update_post_meta($variation_id, 'attribute_' . BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_ATT, $tour_id);

		return $variation_id;
	}

	function create_cruise_product_variation($product_id, $cruise_id, $cabin_type_id = 0) {

		$variation_title = $this->build_cruise_product_variation_title($cruise_id, $cabin_type_id);

		$new_post = array(
			'post_title' 		=> $variation_title,
			'post_content' 		=> __('This is a bookyourtravel cruise product variation', 'bookyourtravel'),
			'post_status' 		=> 'publish',
			'post_type' 		=> 'product_variation',
			'post_parent'		=> $product_id,
			'post_name' 		=> $this->build_cruise_product_variation_slug($cruise_id, $cabin_type_id),
			'comment_status' 	=> 'closed'
		);

		$variation_id 			= wp_insert_post($new_post);

		update_post_meta($variation_id, '_stock_status', 		'instock');
		// update_post_meta($variation_id, '_sold_individually', 	'yes');
		update_post_meta($variation_id, '_virtual', 			'yes');
		update_post_meta($variation_id, '_manage_stock', 'no' );
		update_post_meta($variation_id, '_downloadable', 'no' );
		update_post_meta($variation_id, 'attribute_' . BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_ATT, $cruise_id);
		update_post_meta($variation_id, 'attribute_' . BOOKYOURTRAVEL_WOOCOMMERCE_CABIN_TYPE_ATT, $cabin_type_id);

		return $variation_id;
	}

	function create_car_rental_product_variation($product_id, $car_rental_id) {

		$variation_title = $this->build_car_rental_product_variation_title($car_rental_id);

		$new_post = array(
			'post_title' 		=> $variation_title,
			'post_content' 		=> __('This is a bookyourtravel car rental product variation', 'bookyourtravel'),
			'post_status' 		=> 'publish',
			'post_type' 		=> 'product_variation',
			'post_parent'		=> $product_id,
			'post_name' 		=> $this->build_car_rental_product_variation_slug($car_rental_id),
			'comment_status' 	=> 'closed'
		);

		$variation_id 			= wp_insert_post($new_post);

		update_post_meta($variation_id, '_stock_status', 		'instock');
		// update_post_meta($variation_id, '_sold_individually', 	'yes');
		update_post_meta($variation_id, '_virtual', 			'yes');
		update_post_meta($variation_id, '_manage_stock', 'no' );
		update_post_meta($variation_id, '_downloadable', 'no' );
		update_post_meta($variation_id, 'attribute_' . BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_ATT, $car_rental_id);

		return $variation_id;
	}

	function post_class($classes) {

		if (in_array('product', $classes) && !is_single()) {

			if ($this->page_sidebar_positioning == 'both')
				$classes[] = 'one-half';
			else if ($this->page_sidebar_positioning == 'left' || $this->page_sidebar_positioning == 'right')
				$classes[] = 'one-third';
			else
				$classes[] = 'one-fourth';
		}

		return $classes;
	}

	function loop_shop_columns() {

		if ($this->page_sidebar_positioning == 'both')
			return 2;
		else if ($this->page_sidebar_positioning == 'left' || $this->page_sidebar_positioning == 'right')
			return 3;
		return 4; // 4 products per row
	}

	function before_main_content() {

		$section_class = 'full-width';

		if ($this->page_sidebar_positioning == 'both')
			$section_class = 'one-half';
		else if ($this->page_sidebar_positioning == 'left' || $this->page_sidebar_positioning == 'right')
			$section_class = 'three-fourth';

		?>
		<!--row-->
		<div class="row">
		<?php
		if ($this->page_sidebar_positioning == 'both' || $this->page_sidebar_positioning == 'left') {
			get_sidebar('left');
		}
		?>
			<section class="content <?php echo esc_attr($section_class); ?>">
		<?php
	}

	function after_main_content() {

		$section_class = 'full-width';

		if ($this->page_sidebar_positioning == 'both')
			$section_class = 'one-half';
		else if ($this->page_sidebar_positioning == 'left' || $this->page_sidebar_positioning == 'right')
			$section_class = 'three-fourth';

		?>
			</section>
		<?php
		if ($this->page_sidebar_positioning == 'both' || $this->page_sidebar_positioning == 'right') {
			get_sidebar('right');
		}
		?>
		</div><!--wrap-->
		<?php
	}

	function customized_breadcrumbs() {
		global $bookyourtravel_theme_globals;
		$hide_breadcrumbs = $bookyourtravel_theme_globals->get_hide_breadcrumbs();
		if (!$hide_breadcrumbs) {
			if (function_exists('woocommerce_breadcrumb')) {

				$args = array(
						'delimiter' => '',
						'before' => '<li>',
						'after' => '</li>',
						'wrap_before' => '<nav role="navigation" class="breadcrumbs" itemprop="breadcrumb"><ul>',
						'wrap_after' => '</ul></nav>',
				);

				woocommerce_breadcrumb($args);
			}
		}
	}

	function random_sku($prefix, $len = 6) {

		$str = '';

		for ($i = 0; $i < $len; $i++) {
			$str .= substr('0123456789', mt_rand(0, strlen('0123456789') - 1), 1);
		}

		return $prefix . $str;
	}

	function build_accommodation_product_variation_slug($accommodation_id, $room_type_id = 0) {

		$slug = sprintf($this->accommodation_product_slug . "-v-%d", $accommodation_id);

		if ($room_type_id > 0) {
			$slug .= sprintf("-%d", $room_type_id);
		}

		return $slug;
	}

	function build_tour_product_variation_slug($tour_id) {

		$slug = sprintf($this->tour_product_slug . "-v-%d", $tour_id);

		return $slug;
	}

	function build_cruise_product_variation_slug($cruise_id, $cabin_type_id = 0) {

		$slug = sprintf($this->cruise_product_slug . "-v-%d", $cruise_id);

		if ($cabin_type_id > 0) {
			$slug .= sprintf("-%d", $cabin_type_id);
		}

		return $slug;
	}

	function build_car_rental_product_variation_slug($car_rental_id) {

		$slug = sprintf($this->car_rental_product_slug . "-v-%d", $car_rental_id);

		return $slug;
	}

	function dynamically_create_accommodation_woo_order($booking_id, $total_price, $address_array, $accommodation_id, $room_type_id = 0) {

		$product_id 				= $this->get_product_id('accommodation');
		$variation_id 				= $this->get_accommodations_product_variation_id($product_id, $accommodation_id, $room_type_id);

		return $this->create_accommodation_woo_order($variation_id, $booking_id, $total_price, $address_array);
	}

	function create_accommodation_woo_order($variation_id, $booking_id, $total_price, $address_array) {

		global $bookyourtravel_accommodation_helper, $woocommerce;

        $order = wc_create_order();

		$product_variation = new WC_Product_Variation($variation_id);

		$item_id = $order->add_product($product_variation, 1);

		if ($item_id > 0) {
			wc_add_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_ACCOMMODATION_BOOKING_ID, $booking_id, true);
		}

		$order->set_prices_include_tax( 'yes' === get_option( 'woocommerce_prices_include_tax' ) );
		
		$tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' ); // Uses standard tax class.
		$tax_rates = WC_Tax::get_rates( $tax_class );
		$price_includes_tax = wc_prices_include_tax();
		
		foreach($order->get_items() as $item_id => $item) {
			// Set the new price
			$item->set_subtotal( $total_price ); 
			$item->set_total( $total_price );
			
			$subtotal_taxes = WC_Tax::calc_tax( $item->get_subtotal(), $tax_rates, $price_includes_tax );
			$subtotal_tax = array_sum($subtotal_taxes);

			if ( $price_includes_tax ) {
				// Use unrounded taxes so we can re-calculate from the orders screen accurately later.
				$item->set_subtotal($total_price - $subtotal_tax);
				$item->set_total($total_price - $subtotal_tax);
			}
			
			$item->save(); // Save line item data
		}		
		
		$order->set_address( $address_array, 'billing' );
		$order->calculate_totals();
		$order->payment_complete();
		$order->update_status( 'completed' );
		$order->save();

		if ($woocommerce && $woocommerce->cart) {
			$woocommerce->cart->empty_cart();
		}

		$bookyourtravel_accommodation_helper->update_booking_woocommerce_info($booking_id, 'manual add', $order->get_id(), 'completed');

		return $order->get_id();
	}

	function dynamically_create_tour_woo_order($booking_id, $total_price, $address_array, $tour_id) {

		$product_id 				= $this->get_product_id('tour');
		$variation_id 				= $this->get_tours_product_variation_id($product_id, $tour_id);

		return $this->create_tour_woo_order($variation_id, $booking_id, $total_price, $address_array);
	}

	function create_tour_woo_order($variation_id, $booking_id, $total_price, $address_array) {

		global $bookyourtravel_tour_helper, $woocommerce;

        $order = wc_create_order();

		$product_variation = new WC_Product_Variation($variation_id);

		$item_id = $order->add_product($product_variation, 1);

		if ($item_id > 0) {
			wc_add_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_TOUR_BOOKING_ID, $booking_id, true);
		}

		$order->set_prices_include_tax( 'yes' === get_option( 'woocommerce_prices_include_tax' ) );
		
		$tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' ); // Uses standard tax class.
		$tax_rates = WC_Tax::get_rates( $tax_class );
		$price_includes_tax = wc_prices_include_tax();
		
		foreach($order->get_items() as $item_id => $item) {
			// Set the new price
			$item->set_subtotal( $total_price ); 
			$item->set_total( $total_price );
			
			$subtotal_taxes = WC_Tax::calc_tax( $item->get_subtotal(), $tax_rates, $price_includes_tax );
			$subtotal_tax = array_sum($subtotal_taxes);

			if ( $price_includes_tax ) {
				// Use unrounded taxes so we can re-calculate from the orders screen accurately later.
				$item->set_subtotal($total_price - $subtotal_tax);
				$item->set_total($total_price - $subtotal_tax);
			}
			
			$item->save(); // Save line item data
		}		
		
		$order->set_address( $address_array, 'billing' );
		$order->calculate_totals();
		$order->payment_complete();
		$order->update_status( 'completed' );
		$order->save();

		if ($woocommerce && $woocommerce->cart) {
			$woocommerce->cart->empty_cart();
		}

		$bookyourtravel_tour_helper->update_booking_woocommerce_info($booking_id, 'manual add', $order->get_id(), 'completed');

		return $order->get_id();
	}

	function dynamically_create_cruise_woo_order($booking_id, $total_price, $address_array, $cruise_id, $cabin_type_id = 0) {

		$product_id 				= $this->get_product_id('cruise');
		$variation_id 				= $this->get_cruises_product_variation_id($product_id, $cruise_id, $cabin_type_id);

		return $this->create_cruise_woo_order($variation_id, $booking_id, $total_price, $address_array);
	}

	function create_cruise_woo_order($variation_id, $booking_id, $total_price, $address_array) {

		global $bookyourtravel_cruise_helper, $woocommerce;

        $order = wc_create_order();

		$product_variation = new WC_Product_Variation($variation_id);

		$item_id = $order->add_product($product_variation, 1);

		if ($item_id > 0) {
			wc_add_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CRUISE_BOOKING_ID, $booking_id, true);
		}

		$order->set_prices_include_tax( 'yes' === get_option( 'woocommerce_prices_include_tax' ) );
		
		$tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' ); // Uses standard tax class.
		$tax_rates = WC_Tax::get_rates( $tax_class );
		$price_includes_tax = wc_prices_include_tax();
		
		foreach($order->get_items() as $item_id => $item) {
			// Set the new price
			$item->set_subtotal( $total_price ); 
			$item->set_total( $total_price );
			
			$subtotal_taxes = WC_Tax::calc_tax( $item->get_subtotal(), $tax_rates, $price_includes_tax );
			$subtotal_tax = array_sum($subtotal_taxes);

			if ( $price_includes_tax ) {
				// Use unrounded taxes so we can re-calculate from the orders screen accurately later.
				$item->set_subtotal($total_price - $subtotal_tax);
				$item->set_total($total_price - $subtotal_tax);
			}
			
			$item->save(); // Save line item data
		}		
		
		$order->set_address( $address_array, 'billing' );
		$order->calculate_totals();
		$order->payment_complete();
		$order->update_status( 'completed' );
		$order->save();

		if ($woocommerce && $woocommerce->cart) {
			$woocommerce->cart->empty_cart();
		}

		$bookyourtravel_cruise_helper->update_booking_woocommerce_info($booking_id, 'manual add', $order->get_id(), 'completed');

		return $order->get_id();
	}

	function dynamically_create_car_rental_woo_order($booking_id, $total_price, $address_array, $car_rental_id) {

		$product_id 				= $this->get_product_id('car_rental');
		$variation_id 				= $this->get_car_rentals_product_variation_id($product_id, $car_rental_id);

		return $this->create_car_rental_woo_order($variation_id, $booking_id, $total_price, $address_array);
	}

	function create_car_rental_woo_order($variation_id, $booking_id, $total_price, $address_array) {

		global $bookyourtravel_car_rental_helper, $woocommerce;

        $order = wc_create_order();

		$product_variation = new WC_Product_Variation($variation_id);

		$item_id = $order->add_product($product_variation, 1);

		if ($item_id > 0) {
			wc_add_order_item_meta($item_id, BOOKYOURTRAVEL_WOOCOMMERCE_CAR_RENTAL_BOOKING_ID, $booking_id, true);
		}

		$order->set_prices_include_tax( 'yes' === get_option( 'woocommerce_prices_include_tax' ) );
		
		$tax_class = apply_filters( 'woocommerce_price_filter_widget_tax_class', '' ); // Uses standard tax class.
		$tax_rates = WC_Tax::get_rates( $tax_class );
		$price_includes_tax = wc_prices_include_tax();
		
		foreach($order->get_items() as $item_id => $item) {
			// Set the new price
			$item->set_subtotal( $total_price ); 
			$item->set_total( $total_price );
			
			$subtotal_taxes = WC_Tax::calc_tax( $item->get_subtotal(), $tax_rates, $price_includes_tax );
			$subtotal_tax = array_sum($subtotal_taxes);

			if ( $price_includes_tax ) {
				// Use unrounded taxes so we can re-calculate from the orders screen accurately later.
				$item->set_subtotal($total_price - $subtotal_tax);
				$item->set_total($total_price - $subtotal_tax);
			}
			
			$item->save(); // Save line item data
		}		
		
		$order->set_address( $address_array, 'billing' );
		$order->calculate_totals();
		$order->payment_complete();
		$order->update_status( 'completed' );
		$order->save();

		if ($woocommerce && $woocommerce->cart) {
			$woocommerce->cart->empty_cart();
		}

		$bookyourtravel_car_rental_helper->update_booking_woocommerce_info($booking_id, 'manual add', $order->get_id(), 'completed');

		return $order->get_id();
	}
}

// store the instance in a variable to be retrieved later and call init
$bookyourtravel_theme_woocommerce = BookYourTravel_Theme_WooCommerce::get_instance();
