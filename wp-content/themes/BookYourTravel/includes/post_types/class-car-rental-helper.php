<?php
/**
 * BookYourTravel_Car_Rental_Helper class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-car-rental.php');

class BookYourTravel_Car_Rental_Helper extends BookYourTravel_BaseSingleton {

	private $enable_car_rentals;
	private $car_rental_custom_meta_fields;
	private $car_rental_custom_meta_tabs;
	private $car_rental_list_custom_meta_fields;
	private $car_rental_list_custom_meta_tabs;
    private $car_rental_list_meta_box;

	// used by frontend submit {
        private $car_rental_availability_fields;
        private $car_rental_booking_fields;
    // }

	protected function __construct() {

		global $post, $bookyourtravel_theme_globals;

		$this->enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();

        // our parent class might
        // contain shared code in its constructor
        parent::__construct();
	}

    public function init() {

		add_action( 'bookyourtravel_initialize_post_types', array( $this, 'initialize_post_type' ), 0);

		if ($this->enable_car_rentals) {
			add_action( 'bookyourtravel_after_delete_car_rental', array( $this, 'after_delete_car_rental' ), 10, 1);
			add_action( 'bookyourtravel_save_car_rental', array( $this, 'save_car_rental' ), 10, 1);
			add_action( 'admin_init', array($this, 'remove_unnecessary_meta_boxes') );
			add_filter( 'manage_edit-car_rental_columns', array( $this, 'manage_edit_car_rental_columns'), 10, 1);
			add_action( 'admin_init', array( $this, 'car_rental_admin_init' ) );
            add_action( 'bookyourtravel_initialize_ajax', array( $this, 'initialize_ajax' ), 0);
            add_filter('bookyourtravel_custom_taxonomy_list', array($this, 'custom_taxonomy_list'), 10, 1);

			add_action('bookyourtravel_before_single_car_rental_content', array($this, 'before_single_car_rental_content'));
			add_action('booking_form_details_car_rental_core_fields', array($this, 'booking_form_details_core_fields'));
			add_action('booking_form_confirmation_car_rental_core_fields', array($this, 'booking_form_confirmation_core_fields'));
			add_action('booking_form_calendar_car_rental_start_summary_control', array($this, 'booking_form_calendar_start_summary_control'));
			add_action('booking_form_calendar_car_rental_booking_terms', array($this, 'booking_form_calendar_booking_terms'));
			add_action('booking_form_calendar_car_rental_after_price_breakdown', array($this, 'booking_form_calendar_after_price_breakdown'));

			$this->initialize_meta_fields();
		}
    }

    function custom_taxonomy_list($taxonomies) {
        if ($this->enable_car_rentals) {
            $taxonomies[] = "car_type";
            $taxonomies[] = "car_rental_tag";
        }

        return $taxonomies;
    }

	function initialize_ajax() {
		if ($this->enable_car_rentals) {
			add_action( 'wp_ajax_book_car_rental_ajax_request', array($this, 'book_car_rental_ajax_request' ) );
			add_action( 'wp_ajax_nopriv_book_car_rental_ajax_request', array($this, 'book_car_rental_ajax_request' ) );

			add_action( 'wp_ajax_car_rental_process_booking_ajax_request', array( $this, 'process_booking_ajax_request' ) );
			add_action( 'wp_ajax_nopriv_car_rental_process_booking_ajax_request', array( $this, 'process_booking_ajax_request' ) );
			add_action( 'wp_ajax_car_rental_get_fields_ajax_request', array( $this, 'get_fields_ajax_request') );

			add_action( 'byt_ajax_handler_nopriv_car_rental_available_start_dates_ajax_request', array( $this, 'get_available_start_dates_json' ) );
			add_action( 'byt_ajax_handler_car_rental_available_start_dates_ajax_request', array( $this, 'get_available_start_dates_json') );
			add_action( 'byt_ajax_handler_nopriv_car_rental_available_end_dates_ajax_request', array( $this, 'get_available_end_dates_json' ) );
			add_action( 'byt_ajax_handler_car_rental_available_end_dates_ajax_request', array( $this, 'get_available_end_dates_json') );
			add_action( 'byt_ajax_handler_nopriv_car_rental_load_min_price_ajax_request', array( $this, 'get_min_price_json' ) );
			add_action( 'byt_ajax_handler_car_rental_load_min_price_ajax_request', array( $this, 'get_min_price_json') );
			add_action( 'byt_ajax_handler_car_rental_get_day_price_ajax_request', array( $this, 'get_prices_json') );
            add_action( 'byt_ajax_handler_nopriv_car_rental_get_day_price_ajax_request', array( $this, 'get_prices_json') );

			add_action( 'wp_ajax_nopriv_car_rental_available_start_dates_ajax_request', array( $this, 'get_available_start_dates_json' ) );
			add_action( 'wp_ajax_car_rental_available_start_dates_ajax_request', array( $this, 'get_available_start_dates_json') );
			add_action( 'wp_ajax_nopriv_car_rental_available_end_dates_ajax_request', array( $this, 'get_available_end_dates_json' ) );
			add_action( 'wp_ajax_car_rental_available_end_dates_ajax_request', array( $this, 'get_available_end_dates_json') );
			add_action( 'wp_ajax_nopriv_car_rental_load_min_price_ajax_request', array( $this, 'get_min_price_json' ) );
			add_action( 'wp_ajax_car_rental_load_min_price_ajax_request', array( $this, 'get_min_price_json') );
			add_action( 'wp_ajax_car_rental_get_day_price_ajax_request', array( $this, 'get_prices_json') );
            add_action( 'wp_ajax_nopriv_car_rental_get_day_price_ajax_request', array( $this, 'get_prices_json') );
		}
	}

	function get_custom_meta_fields() {
		$this->initialize_meta_fields();
		return $this->car_rental_custom_meta_fields;
	}

	function get_custom_meta_tabs() {
		$this->initialize_meta_fields();
		return $this->car_rental_custom_meta_tabs;
    }

	function get_car_rental_availability_fields() {
		$this->initialize_meta_fields();
		return $this->car_rental_availability_fields;
    }

	function get_car_rental_booking_fields() {
		$this->initialize_meta_fields();
		return $this->car_rental_booking_fields;
	}

	function initialize_meta_fields() {

		global $bookyourtravel_theme_globals;

		// $transmission_types = array();
		// $transmission_types[] = array('value' => 'manual', 'label' => esc_html__('Manual transmission', 'bookyourtravel'));
		// $transmission_types[] = array('value' => 'auto', 'label' => esc_html__('Auto transmission', 'bookyourtravel'));

		$car_rental_feature_displays = array();
		$car_rental_feature_displays[] = array('value' => 'gallery', 'label' => esc_html__('Image gallery', 'bookyourtravel'));
		$car_rental_feature_displays[] = array('value' => 'image', 'label' => esc_html__('Featured image', 'bookyourtravel'));

		$this->car_rental_custom_meta_tabs = array(
			array(
				'label' => esc_html__('General', 'bookyourtravel'),
				'id' => '_car_rental_general_tab',
				'class' => 'general_tab'
			),
			array(
				'label' => esc_html__('Booking', 'bookyourtravel'),
				'id' => '_car_rental_booking_tab',
				'class' => 'booking_tab'
			),
			array(
				'label' => esc_html__('Gallery', 'bookyourtravel'),
				'id' => '_car_rental_gallery_tab',
				'class' => 'gallery_tab'
			),
			array(
				'label' => esc_html__('Content', 'bookyourtravel'),
				'id' => '_car_rental_content_tab',
				'class' => 'content_tab'
			)
        );

        $this->car_rental_custom_meta_tabs = apply_filters('bookyourtravel_car_rental_custom_meta_tabs', $this->car_rental_custom_meta_tabs);

		$this->car_rental_custom_meta_fields = array(
			array(
				'label'	=> esc_html__('General description', 'bookyourtravel'),
				'desc'	=> esc_html__('General description', 'bookyourtravel'),
				'id'	=> 'car_rental_general_description',
				'type'	=> 'editor',
				'admin_tab_id' => 'content_tab'
			),
			array(
				'label'	=> esc_html__('Short description', 'bookyourtravel'),
				'desc'	=> esc_html__('Short description is shown in the right sidebar of a single item and as a description of an item card when the item is displayed in lists', 'bookyourtravel'),
				'id'	=> 'car_rental_short_description',
				'type'	=> 'editor',
				'admin_tab_id' => 'content_tab'
            ),
			array( // Post ID select box
				'label'	=> esc_html__('Use referral url?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('List on list pages and widgets but link to an external website via referral url.', 'bookyourtravel'), // description
				'id'	=> 'car_rental_use_referral_url', // field id and name
				'type'	=> 'checkbox', // type of field
                'admin_tab_id' => 'general_tab'
            ),
			array(
				'label'	=> esc_html__('Referral url', 'bookyourtravel'),
				'desc'	=> esc_html__('Referral url to take visitors to when item is clicked on on list pages and widgets (to use for example for affiliate links).', 'bookyourtravel'),
				'id'	=> 'car_rental_referral_url',
				'type'	=> 'text',
				'admin_tab_id' => 'general_tab',
				'field_container_class' => 'referral_url'
            ),
			array(
				'label'	=> esc_html__('Referral price', 'bookyourtravel'),
				'desc'	=> esc_html__('Referral price to display for item when item is listed on list pages and widgets.', 'bookyourtravel'),
				'id'	=> 'car_rental_referral_price',
				'type'	=> 'text',
				'admin_tab_id' => 'general_tab',
				'field_container_class' => 'referral_url'
            ),
			array( // Post ID select box
				'label'	=> esc_html__('Is Featured', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Show in lists where only featured items are shown.', 'bookyourtravel'), // description
				'id'	=> 'car_rental_is_featured', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array(
				'label'	=> esc_html__('Ribbon text', 'bookyourtravel'),
				'desc'	=> esc_html__('If specified, this text will appear in a ribbon placed on top of the item in lists when card display mode is used.', 'bookyourtravel'),
				'id'	=> 'car_rental_ribbon_text',
				'type'	=> 'text',
				'admin_tab_id' => 'content_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide inquiry form', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Do you want to not show inquiry form for this car rental?', 'bookyourtravel'), // description
				'id'	=> 'car_rental_hide_inquiry_form', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array(
				'label'	=> esc_html__('Contact email addresses', 'bookyourtravel'),
				'desc'	=> esc_html__('Override admin contact email address by specifying contact email addresses for this car rental. If specifying multiple email addresses, separate each address with a semi-colon ;', 'bookyourtravel'),
				'id'	=> 'car_rental_contact_email',
				'type'	=> 'text',
				'admin_tab_id' => 'general_tab'
			),
			array( // Taxonomy Select box
				'label'	=> esc_html__('Car rental tags', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'car_rental_tag', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'tax_checkboxes', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array( // Taxonomy Select box
				'label'	=> esc_html__('Car type', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'car_type', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'tax_select', // type of field
				'admin_tab_id' => 'general_tab'
			),
            array( // Post ID select box
                'label'	=> esc_html__('Locations', 'bookyourtravel'), // <label>
                'desc'	=> '', // description
                'id'	=> 'locations', // field id and name
                'type'	=> 'post_checkboxes', // type of field
                'post_type' => array('location'), // post types to display, options are prefixed with their post type
                'admin_tab_id'=> 'general_tab'
            ),
			array(
				'label'	=> esc_html__('Address', 'bookyourtravel'),
				'desc'	=> esc_html__('Address text is shown below the item title in list pages and widgets', 'bookyourtravel'),
				'id'	=> 'car_rental_address',
				'type'	=> 'text',
				'admin_tab_id' => 'general_tab'
			),
            array(
                'label' => esc_html__('Force disable single view calendar?', 'bookyourtravel'), // <label>
                'desc' => esc_html__('If this option is checked, then this car rental will not display a calendar in the availability tab regardless of whether it has valid availability or not.', 'bookyourtravel'), // description
                'id' => 'car_rental_force_disable_calendar', // field id and name
                'type' => 'checkbox', // type of field
                'admin_tab_id' => 'booking_tab',
            ),	
			array(
				'label'	=> esc_html__('Is for reservation only?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If this option is checked, then this particular car rental will not be processed via WooCommerce even if WooCommerce is in use.', 'bookyourtravel'), // description
				'id'	=> 'car_rental_is_reservation_only', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'booking_tab'
			),
            array(
                'label' => esc_html__('Minimum booking days', 'bookyourtravel'),
                'desc' => esc_html__('What is the minimum number of days car rental can be booked for?', 'bookyourtravel'),
                'id' => 'car_rental_min_booking_days',
                'type' => 'slider',
                'min' => apply_filters('bookyourtravel_car_rental_min_booking_days_min', '1'),
                'max' => apply_filters('bookyourtravel_car_rental_min_booking_days_max', '30'),
                'step' => '1',
                'admin_tab_id' => 'booking_tab',
            ),
            array(
                'label' => esc_html__('Maximum booking days', 'bookyourtravel'),
                'desc' => esc_html__('What is the maximum number of days car rental can be booked for? Leave as 0 to ignore.', 'bookyourtravel'),
                'id' => 'car_rental_max_booking_days',
                'type' => 'slider',
                'min' => apply_filters('bookyourtravel_car_rental_max_booking_days_min', '0'),
                'max' => apply_filters('bookyourtravel_car_rental_max_booking_days_max', '30'),
                'step' => '1',
                'admin_tab_id' => 'booking_tab',
            ),
			array( // Select box
				'label'	=> esc_html__('Displayed featured element', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'car_rental_displayed_featured_element', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'select', // type of field
				'options' => $car_rental_feature_displays,
				'std' => 'gallery',
				'admin_tab_id' => 'gallery_tab'
			),
			array( // Repeatable & Sortable Text inputs
				'label'	=> esc_html__('Gallery images', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('A collection of images to be used in slider/gallery on single page', 'bookyourtravel'), // description
				'id'	=> 'car_rental_images', // field id and name
				'type'	=> 'repeatable', // type of field
				'sanitizer' => array( // array of sanitizers with matching kets to next array
					'featured' => 'meta_box_santitize_boolean',
					'title' => 'sanitize_text_field',
					'desc' => 'wp_kses_data'
				),
				'repeatable_fields' => array ( // array of fields to be repeated
					array( // Image ID field
						'label'	=> esc_html__('Image', 'bookyourtravel'), // <label>
						'id'	=> 'image', // field id and name
						'type'	=> 'image' // type of field
					)
				),
				'admin_tab_id' => 'gallery_tab'
			),
		);

        if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
            array_unshift($this->car_rental_custom_meta_fields, array( // Select box
                'label' => esc_html__('Static "From" price', 'bookyourtravel'), // <label>
                'desc' => esc_html__('This price is shown in grids when the "Show static from prices in grid displays?" in enabled in theme configuration settings', 'bookyourtravel'), // description
                'id' => 'car_rental_static_from_price', // field id and name, needs to be the exact name of the taxonomy
                'type' => 'text', // type of field
                'std' => '0',
                'admin_tab_id' => 'booking_tab'
            ));
        }		

		if ($bookyourtravel_theme_globals->enable_deposit_payments()) {
			array_unshift($this->car_rental_custom_meta_fields, array( // Select box
                'label' => esc_html__('Deposit percentage', 'bookyourtravel'), // <label>
                'desc' => esc_html__('% deposit charge', 'bookyourtravel'), // description
                'id' => 'car_rental_deposit_percentage', // field id and name, needs to be the exact name of the taxonomy
                'type' => 'number', // type of field
				'std' => '100',
                'min' => '0',
                'max' => '100',
                'step' => '1',
				'admin_tab_id' => 'booking_tab',
				'field_container_class' => 'deposit_percentage'
            ));
		}

		global $default_car_rental_extra_fields;

		$car_rental_extra_fields = of_get_option('car_rental_extra_fields');

		if (!is_array($car_rental_extra_fields) || count($car_rental_extra_fields) == 0)
			$car_rental_extra_fields = $default_car_rental_extra_fields;
		else
			$car_rental_extra_fields = BookYourTravel_Theme_Of_Default_Fields::merge_fields_and_defaults($car_rental_extra_fields, $default_car_rental_extra_fields);

		foreach ($car_rental_extra_fields as $car_rental_extra_field) {
			$field_is_hidden = isset($car_rental_extra_field['hide']) ? intval($car_rental_extra_field['hide']) : 0;

			if (!$field_is_hidden) {
				$extra_field = null;
				$field_label = isset($car_rental_extra_field['label']) ? $car_rental_extra_field['label'] : '';
				$field_id = isset($car_rental_extra_field['id']) ? $car_rental_extra_field['id'] : '';
				$field_type = isset($car_rental_extra_field['type']) ? $car_rental_extra_field['type'] :  '';
				$field_desc = isset($car_rental_extra_field['desc']) ? $car_rental_extra_field['desc'] :  '';

				$field_options_array = null;
				if (isset($car_rental_extra_field['options'])) {
					if (is_array($car_rental_extra_field['options'])) {
						$field_options_array = $car_rental_extra_field['options'];
					} else {
						$field_options = isset($car_rental_extra_field['options']) ? trim($car_rental_extra_field['options']) :  '';
						$field_options_array = array();
						$options_array = preg_split("/(\r\n|\n|\r)/", $field_options);
						foreach ($options_array as $option) {
							$option_array = preg_split("/:/", $option);
							if (count($option_array) > 0) {
								$option_value = $option_array[0];
								$option_text = trim($option_array[0]);
								if (count($option_array) > 1) {
									$option_text = trim($option_array[1]);
								}
								$field_options_array[] = array(
									'value' => $option_value,
									'label' => $option_text
								);
							}
						}
					}
				}


				$min = 0;
				$max = 10;
				$step = 1;

				if ($field_type == 'textarea') {
					$field_type = 'editor';
				} else if ($field_type == 'select' && !empty($field_options)) {

				} else if ($field_type == 'slider') {
					$min = isset($car_rental_extra_field['min']) && strlen($car_rental_extra_field['min']) > 0 ? intval($car_rental_extra_field['min']) :  1;
					$max = isset($car_rental_extra_field['max']) && strlen($car_rental_extra_field['max']) > 0 ? intval($car_rental_extra_field['max']) :  10;
					$step = isset($car_rental_extra_field['step']) && strlen($car_rental_extra_field['step']) > 0 ? intval($car_rental_extra_field['step']) :  1;
				}

				if (!empty($field_label) && !empty($field_id) && !empty($field_type)) {
					$extra_field = array(
						'label'	=> $field_label,
						'desc'	=> $field_desc,
						'id'	=> 'car_rental_' . $field_id,
						'type'	=> $field_type,
						'admin_tab_id' => 'content_tab',
						'options' => $field_options_array,
						'min' => $min,
						'max' => $max,
						'step' => $step,
					);
				}

				if ($extra_field)
					$this->car_rental_custom_meta_fields[] = $extra_field;
			}
        }

        $this->car_rental_custom_meta_fields = apply_filters('bookyourtravel_car_rental_custom_meta_fields', $this->car_rental_custom_meta_fields);

		$this->car_rental_availability_fields = array(
			array(
				'label' => esc_html__('Season name', 'bookyourtravel'),
				'id' => 'season_name',
				'type' => 'text',
				'field_override_class' => 'required',
				'show_in_fs_list' => true
			),
			array(
				'label' => esc_html__('Start date', 'bookyourtravel'),
				'id' => 'start_date',
				'type' => 'datepicker',
				'field_container_class' => 'datepicker-wrap',
				'field_override_class' => 'required',
				'show_in_fs_list' => true
			),
			array(
				'label' => esc_html__('End date', 'bookyourtravel'),
				'id' => 'end_date',
				'type' => 'datepicker',
				'field_container_class' => 'datepicker-wrap',
				'field_override_class' => 'required',
				'show_in_fs_list' => true
			),
			array(
				'label' => esc_html__('Car rental', 'bookyourtravel'),
				'id' => 'car_rental_id',
				'type' => 'post_select',
				'post_type' => 'car_rental',
				'field_override_class' => 'required',
				'show_in_fs_list' => true
			),
			array(
				'label' => esc_html__('Number of cars', 'bookyourtravel'),
				'id' => 'number_of_cars',
				'type' => 'slider',
				'min' => '1',
				'max' => '100',
				'step' => '1',
				'field_override_class' => 'required',
				'show_in_fs_list' => true
            ),
			array(
				'label' => esc_html__('Price per day', 'bookyourtravel'),
				'id' => 'price_per_day',
				'type' => 'number',
				'field_override_class' => 'required',
				'show_in_fs_list' => true
			)
        );

        $this->car_rental_availability_fields = apply_filters('bookyourtravel_car_rental_availability_fields', $this->car_rental_availability_fields);

		$this->car_rental_booking_fields = array(
			array(
				'label' => esc_html__('First name', 'bookyourtravel'),
				'id' => 'first_name',
				'type' => 'text',
				'field_override_class' => 'required',
				'show_in_fs_list' => true
			),
			array(
				'label' => esc_html__('Last name', 'bookyourtravel'),
				'id' => 'last_name',
				'type' => 'text',
				'field_override_class' => 'required',
				'show_in_fs_list' => true
			),
			array(
				'label' => esc_html__('Company', 'bookyourtravel'),
				'id' => 'company',
				'type' => 'text',
				'field_override_class' => ''
			),
			array(
				'label' => esc_html__('Email', 'bookyourtravel'),
				'id' => 'email',
				'type' => 'text',
				'field_override_class' => 'required email',
				'show_in_fs_list' => true
			),
			array(
				'label' => esc_html__('Phone', 'bookyourtravel'),
				'id' => 'phone',
				'type' => 'text',
				'field_override_class' => ''
			),
			array(
				'label' => esc_html__('Address', 'bookyourtravel'),
				'id' => 'address',
				'type' => 'text',
				'field_override_class' => ''
			),
			array(
				'label' => esc_html__('Address 2', 'bookyourtravel'),
				'id' => 'address_2',
				'type' => 'text',
				'field_override_class' => ''
			),
			array(
				'label' => esc_html__('Town', 'bookyourtravel'),
				'id' => 'town',
				'type' => 'text',
				'field_override_class' => ''
			),
			array(
				'label' => esc_html__('Zip', 'bookyourtravel'),
				'id' => 'zip',
				'type' => 'text',
				'field_override_class' => ''
			),
			array(
				'label' => esc_html__('State', 'bookyourtravel'),
				'id' => 'state',
				'type' => 'text',
				'field_override_class' => ''
			),
			array(
				'label' => esc_html__('Country', 'bookyourtravel'),
				'id' => 'country',
				'type' => 'text',
				'field_override_class' => ''
			),
			array(
				'label' => esc_html__('Special requirements', 'bookyourtravel'),
				'id' => 'special_requirements',
				'type' => 'textarea',
				'field_override_class' => ''
			),
			array(
				'label' => esc_html__('Start date', 'bookyourtravel'),
				'id' => 'start_date',
				'type' => 'datepicker',
				'field_container_class' => 'datepicker-wrap',
				'field_override_class' => 'required',
				'show_in_fs_list' => true
			),
			array(
				'label' => esc_html__('End date', 'bookyourtravel'),
				'id' => 'end_date',
				'type' => 'datepicker',
				'field_container_class' => 'datepicker-wrap',
				'field_override_class' => 'required',
				'show_in_fs_list' => true
			),
			array(
				'label' => esc_html__('Car rental', 'bookyourtravel'),
				'id' => 'car_rental_id',
				'type' => 'post_select',
                'post_type' => 'car_rental',
				'field_override_class' => 'required',
				'show_in_fs_list' => true
			),
			array(
				'label' => esc_html__('Total car rental price', 'bookyourtravel'),
				'id' => 'total_car_rental_price',
				'type' => 'number',
				'field_override_class' => 'required'
			),
			array(
				'label' => esc_html__('Total extra items price', 'bookyourtravel'),
				'id' => 'total_extra_items_price',
				'type' => 'number',
				'field_override_class' => 'required'
			),
			array(
				'label' => esc_html__('Total price', 'bookyourtravel'),
				'id' => 'total_price',
				'type' => 'number',
				'field_override_class' => 'required',
				'show_in_fs_list' => true
			)
		);
		
		if ($bookyourtravel_theme_globals->enable_deposit_payments()) {
			$this->car_rental_booking_fields[] = array(
				'label' => esc_html__('Deposit amount', 'bookyourtravel'),
				'id' => 'cart_price',
				'type' => 'number',
				'field_override_class' => 'required',
				'show_in_fs_list' => true
			);
		}

		$this->car_rental_booking_fields = apply_filters('bookyourtravel_car_rental_booking_fields', $this->car_rental_booking_fields);

		$sort_by_columns = array();
		$sort_by_columns[] = array('value' => 'title', 'label' => esc_html__('Car rental title', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'ID', 'label' => esc_html__('Car rental ID', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'date', 'label' => esc_html__('Publish date', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'rand', 'label' => esc_html__('Random', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'comment_count', 'label' => esc_html__('Comment count', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'price', 'label' => esc_html__('Price', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'menu_order', 'label' => esc_html__('Order attribute', 'bookyourtravel'));

		$sort_by_columns = apply_filters('bookyourtravel_car_rental_list_sort_by_columns', $sort_by_columns);

		$this->car_rental_list_custom_meta_tabs = array(
			array(
				'label' => esc_html__('Content', 'bookyourtravel'),
				'id' => '_car_rental_list_filter_tab',
				'class' => 'filter_tab'
			),
			array(
				'label' => esc_html__('Display settings', 'bookyourtravel'),
				'id' => '_car_rental_list_item_settings_tab',
				'class' => 'item_settings_tab'
			)
		);

		$this->car_rental_list_custom_meta_fields = array(
			array( // Taxonomy Select box
				'label'	=> esc_html__('Car type', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'car_type', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'tax_checkboxes', // type of field
				'admin_tab_id' => 'filter_tab'
			),
			array( // Taxonomy Select box
				'label'	=> esc_html__('Location', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'car_rental_list_location_post_id', // field id and name
				'type'	=> 'post_select', // type of field
				'post_type' => array('location'), // post types to display, options are prefixed with their post type
				'admin_tab_id' => 'filter_tab'
			),
			array( // Taxonomy Select box
				'label'	=> esc_html__('Car rental tags', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'car_rental_tag', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'tax_checkboxes', // type of field
				'admin_tab_id' => 'filter_tab'
			),
			array( // Select box
				'label'	=> esc_html__('Sort by field', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'car_rental_list_sort_by', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'select', // type of field
				'options' => $sort_by_columns,
				'admin_tab_id' => 'filter_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Sort descending?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will sort car rentals in descending order', 'bookyourtravel'), // description
				'id'	=> 'car_rental_list_sort_descending', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'filter_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Show featured only?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will list featured car rentals only', 'bookyourtravel'), // description
				'id'	=> 'car_rental_list_show_featured_only', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'filter_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Items per page', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('How many items do you want to show per page?', 'bookyourtravel'), // description
				'id'	=> 'car_rental_list_posts_per_page', // field id and name
				'std'	=> '12',
				'type'	=> 'slider',
				'min'	=> apply_filters('bookyourtravel_car_rental_list_posts_per_page_min', '1'),
				'max'	=> apply_filters('bookyourtravel_car_rental_list_posts_per_page_max', '50'),
				'step'	=> '1',
				'admin_tab_id' => 'filter_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Items per row', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('How many items do you want to show per row?', 'bookyourtravel'), // description
				'id'	=> 'car_rental_list_posts_per_row', // field id and name
				'std'	=> '4',
				'type'	=> 'slider',
				'min'	=> apply_filters('bookyourtravel_car_rental_list_posts_per_row_min', '1'),
				'max'	=> apply_filters('bookyourtravel_car_rental_list_posts_per_row_max', '5'),
				'step'	=> '1',
				'admin_tab_id' => 'filter_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide item titles?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide titles of listed car rentals', 'bookyourtravel'), // description
				'id'	=> 'car_rental_list_hide_item_titles', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'item_settings_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide item images?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide images of listed car rentals', 'bookyourtravel'), // description
				'id'	=> 'car_rental_list_hide_item_images', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'item_settings_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide item descriptions?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide descriptions of listed car rentals', 'bookyourtravel'), // description
				'id'	=> 'car_rental_list_hide_item_descriptions', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'item_settings_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide buttons?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide buttons of listed car rentals', 'bookyourtravel'), // description
				'id'	=> 'car_rental_list_hide_item_actions', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'item_settings_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide price?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide price of listed car rentals', 'bookyourtravel'), // description
				'id'	=> 'car_rental_list_hide_item_prices', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'item_settings_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide item address?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide address of listed car rentals', 'bookyourtravel'), // description
				'id'	=> 'car_rental_list_hide_item_locations', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'item_settings_tab'
			)
		);

		global $bookyourtravel_theme_globals;
		if ($bookyourtravel_theme_globals->enable_reviews()) {
			$this->car_rental_list_custom_meta_fields[] = array( // Post ID select box
				'label'	=> esc_html__('Hide item rating?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide rating of listed car rentals', 'bookyourtravel'), // description
				'id'	=> 'car_rental_list_hide_item_rating', // field id and name
				'type'	=> 'checkbox', // type of field
			);
		}
	}

	function booking_form_calendar_booking_terms() {
		get_template_part('includes/parts/car_rental/single/booking-form-calendar', 'booking-terms');
	}

	function booking_form_calendar_start_summary_control() {
		get_template_part('includes/parts/car_rental/single/booking-form-calendar', 'summary-fields');
	}

	function booking_form_confirmation_core_fields() {
		get_template_part('includes/parts/car_rental/single/booking-form-confirmation', 'core-fields');
	}

	function booking_form_details_core_fields() {
		get_template_part('includes/parts/car_rental/single/booking-form-details', 'core-fields');
	}

	function before_single_car_rental_content() {
		global $post, $entity_obj, $bookyourtravel_theme_globals;

		if ($post && $post->post_type == 'car_rental') {
			$use_woocommerce_for_checkout = $bookyourtravel_theme_globals->use_woocommerce_for_checkout();
			$car_rental_obj = new BookYourTravel_Car_Rental($post);
			$entity_obj = $car_rental_obj;
			$car_rental_is_reservation_only = (int)$car_rental_obj->get_is_reservation_only();

			if ($bookyourtravel_theme_globals->enable_reviews()) {
				get_template_part('includes/parts/review/review', 'form');
			}

			get_template_part('includes/parts/inquiry/inquiry', 'form');
			if ($car_rental_is_reservation_only || !BookYourTravel_Theme_Utils::is_woocommerce_active() || !$use_woocommerce_for_checkout) {
				get_template_part('includes/parts/booking/form', 'details');
				get_template_part('includes/parts/booking/form', 'confirmation');
			}
		}
	}

	function get_prices_json() {
		global $bookyourtravel_theme_globals;

		if ( isset($_REQUEST) ) {

			$nonce = wp_kses($_REQUEST['nonce'], array());

			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
                $car_rental_id = isset($_REQUEST['car_rental_id']) ? intval(wp_kses($_REQUEST['car_rental_id'], array())) : 0;
				$location_id = isset($_REQUEST['car_rental_pick_up_id']) ? intval(wp_kses($_REQUEST['car_rental_pick_up_id'], array())) : 0;
				$location_id = BookYourTravel_Theme_Utils::get_default_language_post_id($location_id, 'location');

				$search_date = isset($_REQUEST['the_date']) ? wp_kses($_REQUEST['the_date'], array()) : 0;
				$prices = $this->get_prices($search_date, $car_rental_id, 0, $location_id);


				if (isset($prices->price_per_day)) {
					$prices->price_per_day = BookYourTravel_Theme_Utils::get_price_in_current_currency($prices->price_per_day);
				}

				echo json_encode($prices);
			}
		}

		die();
	}

	function get_prices($search_date, $car_rental_id, $current_booking_id = 0, $pick_up_location_id = 0) {
		global $bookyourtravel_theme_globals, $wpdb;

		$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();        

		if ($car_rental_id > 0 && isset($search_date)) {

			$car_rental_id = BookYourTravel_Theme_Utils::get_default_language_post_id($car_rental_id, 'car_rental');

			$car_rental_obj = new BookYourTravel_Car_Rental($car_rental_id);
			$car_rental_is_reservation_only = $car_rental_obj->get_is_reservation_only();

			$search_date = date('Y-m-d', strtotime($search_date));

			$sql = "SELECT a.availability_id, a.price_per_day, a.number_of_cars, a.booked_cars,
					(@runtot := @runtot + a.number_of_cars) AS running_available_total
					FROM
					(
						SELECT availables.*, IFNULL(COUNT(bookings.Id), 0) booked_cars
						FROM
						(
						SELECT availables_inner.*, date_format(DATE(availables_inner.single_date), '%Y-%m-%d') as bookable_single_date ";

			$sql .= $wpdb->prepare("FROM
							(
								SELECT availabilities.Id availability_id, %s single_date, availabilities.price_per_day, availabilities.number_of_cars
								FROM " . BOOKYOURTRAVEL_CAR_RENTAL_AVAILABILITIES_TABLE . " availabilities
								WHERE %s >= availabilities.start_date AND %s < availabilities.end_date AND availabilities.car_rental_id = %d ", $search_date, $search_date, $search_date, $car_rental_id );

				$sql .= $wpdb->prepare ("
								GROUP BY availability_id
							) availables_inner
						) availables
						LEFT JOIN " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE . " bookings ON availables.bookable_single_date >= DATE(bookings.start_date) AND availables.bookable_single_date < DATE(bookings.end_date)
						AND bookings.car_rental_id = %d ", $car_rental_id);

            if ($pick_up_location_id > 0) {
                $sql .= $wpdb->prepare(" AND bookings.car_rental_pick_up_id = %d ", $pick_up_location_id);
            }

			if ($current_booking_id > 0) {
				$sql .= $wpdb->prepare(" AND bookings.Id <> %d ", $current_booking_id);
			}

			if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$car_rental_is_reservation_only) {
				$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
				$completed_statuses_contains_initiated = $bookyourtravel_theme_globals->completed_order_woocommerce_statuses_contains('initiated');

				if (!empty($completed_statuses)) {
					if ($completed_statuses_contains_initiated) {
						$sql .= " AND (bookings.woo_status IS NULL OR IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")) ";
					} else {
						$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ") ";
					}
				}
			}

			$sql .=		" GROUP BY availables.availability_id
						ORDER BY availables.price_per_day ASC
					) a, (SELECT @runtot:=0) AS n
					GROUP BY a.availability_id
					HAVING running_available_total > booked_cars
					ORDER BY price_per_day ASC
                    LIMIT 1 ";

			$result = $wpdb->get_row($sql);

			return $result;
		}

		return null;
	}

	function get_available_start_dates_json() {

		global $bookyourtravel_theme_globals;
		$available_dates = [];

		if ( isset($_REQUEST) ) {

			$nonce = wp_kses($_REQUEST['nonce'], array());

			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {

				$car_rental_id = isset($_REQUEST['car_rental_id']) ? intval(wp_kses($_REQUEST['car_rental_id'], array())) : 0;
				$location_id = isset($_REQUEST['car_rental_pick_up_id']) ? intval(wp_kses($_REQUEST['car_rental_pick_up_id'], array())) : 0;
				$month = isset($_REQUEST['month']) ? intval(wp_kses($_REQUEST['month'], array())) : 0;
				$year = isset($_REQUEST['year']) ? intval(wp_kses($_REQUEST['year'], array())) : 0;
				$month_range = isset($_REQUEST['month_range']) ? intval(wp_kses($_REQUEST['month_range'], array())) : 4;

				if ($car_rental_id > 0) {
					$car_rental_id = BookYourTravel_Theme_Utils::get_default_language_post_id($car_rental_id, 'car_rental');
					$location_id = BookYourTravel_Theme_Utils::get_default_language_post_id($location_id, 'location');
					$available_dates = $this->list_available_start_dates($car_rental_id, $location_id, $month, $year, $month_range);
				}
			}
		}

		echo json_encode($available_dates);

		die();
	}

	function get_available_end_dates_json() {

		global $bookyourtravel_theme_globals;
		$available_dates = [];

		if ( isset($_REQUEST) ) {

			$nonce = wp_kses($_REQUEST['nonce'], array());

			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
				$car_rental_id = isset($_REQUEST['car_rental_id']) ? intval(wp_kses($_REQUEST['car_rental_id'], array())) : 0;
				$location_id = isset($_REQUEST['car_rental_pick_up_id']) ? intval(wp_kses($_REQUEST['car_rental_pick_up_id'], array())) : 0;
				$month = isset($_REQUEST['month']) ? intval(wp_kses($_REQUEST['month'], array())) : 0;
				$year = isset($_REQUEST['year']) ? intval(wp_kses($_REQUEST['year'], array())) : 0;
				$start_date = isset($_REQUEST['start_date']) ? wp_kses($_REQUEST['start_date'], array()) : null;
				$month_range = isset($_REQUEST['month_range']) ? intval(wp_kses($_REQUEST['month_range'], array())) : 4;

				if ($car_rental_id > 0) {
					$car_rental_id = BookYourTravel_Theme_Utils::get_default_language_post_id($car_rental_id, 'car_rental');
					$location_id = BookYourTravel_Theme_Utils::get_default_language_post_id($location_id, 'location');
					$available_dates = $this->list_available_end_dates($car_rental_id, $location_id, $start_date, $month, $year, $month_range);
				}
			}
		}

		echo json_encode($available_dates);

		die();
	}

	function get_min_price_json() {
		if ( isset($_REQUEST) ) {

			$nonce = wp_kses($_REQUEST['nonce'], array());

			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
				$car_rental_id = isset($_REQUEST['car_rental_id']) ? intval(wp_kses($_REQUEST['car_rental_id'], array())) : 0;
				$start_date = isset($_REQUEST['start_date']) ? wp_kses($_REQUEST['start_date'], array()) : null;
				$end_date = isset($_REQUEST['end_date']) ? wp_kses($_REQUEST['end_date'], array()) : null;

				$price = $this->get_min_future_price($car_rental_id, $start_date, $end_date, true);

				$price = BookYourTravel_Theme_Utils::get_price_in_current_currency($price);

				if ($price > 0) {
					echo json_encode($price);
                }					
			}
		}

		die();
	}

	function get_min_future_price($_car_rental_id, $start_date = null, $end_date = null, $skip_cache = false) {
		global $wpdb, $bookyourtravel_theme_globals;

		$car_rental_id = BookYourTravel_Theme_Utils::get_default_language_post_id($_car_rental_id, 'car_rental');

		if (isset($start_date) && strlen($start_date) > 0) {
			$start_date = date("Y-m-d", strtotime($start_date));
		} else {
			$start_date = date("Y-m-d", time());
		}

		if (isset($end_date) && strlen($end_date) > 0) {
			$end_date = date("Y-m-d", strtotime($end_date));
		} else {
			$end_date = date('Y-m-d', strtotime($start_date . " +24 months"));
		}

		$min_price_meta_key = BookYourTravel_Theme_Utils::build_min_price_meta_key("car_rental", $start_date, $end_date);
		$min_price_check_meta_key = BookYourTravel_Theme_Utils::build_min_price_check_meta_key("car_rental", $start_date, $end_date);

        $car_rental_obj = new BookYourTravel_Car_Rental($car_rental_id);
		$min_price = $car_rental_obj->_get_cached_price($min_price_meta_key, $min_price_check_meta_key);
		$is_reservation_only = $car_rental_obj->get_is_reservation_only();

		if ($min_price == 0 || $skip_cache) {
			$use_referral_url = $car_rental_obj->use_referral_url();
			$referral_url = $car_rental_obj->get_referral_url();
			if ($use_referral_url && !empty($referral_url)) {
				$min_price = $car_rental_obj->get_referral_price();
			} else {
				$sql = "
				SELECT IFNULL(MIN(price_per_day), 0) min_price
				FROM
				(
					SELECT DISTINCT (avc.number_of_available_cars - IFNULL(bc.number_of_booked_cars, 0)) available_cars, avc.the_date, avc.price_per_day FROM
					(
						SELECT SUM(number_of_cars) number_of_available_cars, possible_dates.the_date, price_per_day
						FROM " . BOOKYOURTRAVEL_CAR_RENTAL_AVAILABILITIES_TABLE . " availabilities
						INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date > DATE(availabilities.start_date) AND possible_dates.the_date <= DATE(availabilities.end_date)
						WHERE availabilities.car_rental_id = %d
						GROUP BY possible_dates.the_date, availabilities.price_per_day
					) as avc
					LEFT JOIN
					(
						SELECT COUNT(DISTINCT bookings.id) number_of_booked_cars, possible_dates.the_date booking_date
						FROM " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE . " bookings
						INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date >= DATE(bookings.start_date) AND possible_dates.the_date <= DATE(bookings.end_date)
						WHERE bookings.car_rental_id = %d ";

						if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$is_reservation_only) {
							$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
							if (!empty($completed_statuses)) {
								$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ") ";
							}
						}

					$sql .= "
						GROUP BY possible_dates.the_date
					) as bc
					ON bc.booking_date = avc.the_date
					HAVING available_cars > 0
				) as pr ";

				$sql = $wpdb->prepare($sql, $start_date, $start_date, $end_date, $car_rental_id, $start_date, $start_date, $end_date, $car_rental_id);

				$min_price = $wpdb->get_var($sql);
			}
		}

		update_post_meta($car_rental_id, $min_price_meta_key, $min_price);
		update_post_meta($car_rental_id, $min_price_check_meta_key, time());
		update_post_meta($_car_rental_id, $min_price_meta_key, $min_price);
		update_post_meta($_car_rental_id, $min_price_check_meta_key, time());

		return $min_price;
	}
	
	function get_min_future_date($car_rental_id) {
		global $wpdb, $bookyourtravel_theme_globals;

		$car_rental_id = BookYourTravel_Theme_Utils::get_default_language_post_id($car_rental_id, 'car_rental');

        $car_rental_obj = new BookYourTravel_Car_Rental($car_rental_id);
		$is_reservation_only = (int)$car_rental_obj->get_is_reservation_only();	

		$start_date = date("Y-m-d", time());
		$end_date = date('Y-m-d', strtotime($start_date . " +50 months"));

		$sql = "
		SELECT IFNULL(MIN(the_date), 0) min_date
		FROM
		(
			SELECT DISTINCT (avc.number_of_available_cars - IFNULL(bc.number_of_booked_cars, 0)) available_cars, avc.the_date, avc.price_per_day FROM
			(
				SELECT SUM(number_of_cars) number_of_available_cars, possible_dates.the_date, price_per_day
				FROM " . BOOKYOURTRAVEL_CAR_RENTAL_AVAILABILITIES_TABLE . " availabilities
				INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date > DATE(availabilities.start_date) AND possible_dates.the_date <= DATE(availabilities.end_date)
				WHERE availabilities.car_rental_id = %d
				GROUP BY possible_dates.the_date, availabilities.price_per_day
			) as avc
			LEFT JOIN
			(
				SELECT COUNT(DISTINCT bookings.id) number_of_booked_cars, possible_dates.the_date booking_date
				FROM " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE . " bookings
				INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date >= DATE(bookings.start_date) AND possible_dates.the_date <= DATE(bookings.end_date)
				WHERE bookings.car_rental_id = %d ";

		if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$is_reservation_only) {
			$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
			if (!empty($completed_statuses)) {
				$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ") ";
			}
		}				

		$sql .= "GROUP BY possible_dates.the_date
			) as bc
			ON bc.booking_date = avc.the_date
			HAVING available_cars > 0
		) as pr ";

		$sql = $wpdb->prepare($sql, $start_date, $start_date, $end_date, $car_rental_id, $start_date, $start_date, $end_date, $car_rental_id);

		$min_date = $wpdb->get_var($sql);

		return $min_date;
	}	
	
	function get_min_static_from_price_by_location($_location_id) {
        $min_price = 0;

        global $wpdb, $bookyourtravel_theme_globals;

        $location_id = BookYourTravel_Theme_Utils::get_default_language_post_id($_location_id, 'location');
        $location_obj = new BookYourTravel_Location($location_id);

        $min_price_meta_key = BookYourTravel_Theme_Utils::build_min_price_meta_key("car_rentals");
        $min_price_check_meta_key = BookYourTravel_Theme_Utils::build_min_price_check_meta_key("car_rentals");
        $min_price = $location_obj->_get_cached_price($min_price_meta_key, $min_price_check_meta_key);

        if ($min_price == 0 || $skip_cache) {
            $post_ids = $location_obj->get_car_rental_ids();

            if (count($post_ids) > 0) {
                delete_post_meta($location_id, $min_price_meta_key);

                $post_ids = array_map(function ($v) {
                    return "'" . esc_sql($v) . "'";
                }, $post_ids);
                $post_ids_str = implode(',', $post_ids);                

                $sql = "SELECT IFNULL(MIN(meta_value), 0) min_price 
                    FROM $wpdb->postmeta as meta
                    WHERE meta_key='car_rental_static_from_price' AND post_id IN ($post_ids_str) ";

                $min_price = $wpdb->get_var($sql);

                update_post_meta($location_id, $min_price_meta_key, $min_price);
                update_post_meta($location_id, $min_price_check_meta_key, time());
                update_post_meta($_location_id, $min_price_meta_key, $min_price);
                update_post_meta($_location_id, $min_price_check_meta_key, time());				
            }
        }

        return $min_price;
    }

	function get_min_future_price_by_location($_location_id, $start_date = null, $end_date = null, $skip_cache = false) {
        global $wpdb, $bookyourtravel_theme_globals;

		$location_id = BookYourTravel_Theme_Utils::get_default_language_post_id($_location_id, 'location');
		$location_obj = new BookYourTravel_Location($location_id);

		if (isset($start_date) && strlen($start_date) > 0) {
			$start_date = date("Y-m-d", strtotime($start_date));
		} else {
			$start_date = date("Y-m-d", time());
		}

		if (isset($end_date) && strlen($end_date) > 0) {
			$end_date = date("Y-m-d", strtotime($end_date));
		} else {
			$end_date = date('Y-m-d', strtotime($start_date . " +24 months"));
		}

		$min_price_meta_key = BookYourTravel_Theme_Utils::build_min_price_meta_key("car_rentals", $start_date, $end_date);
		$min_price_check_meta_key = BookYourTravel_Theme_Utils::build_min_price_check_meta_key("car_rentals", $start_date, $end_date);
		$min_price = $location_obj->_get_cached_price($min_price_meta_key, $min_price_check_meta_key);

        delete_post_meta($location_id, $min_price_meta_key);

		if ($min_price == 0 || $skip_cache) {

            $car_rental_ids = $location_obj->get_car_rental_ids();

            if (count($car_rental_ids) > 0) {
				$car_rental_ids = array_map(function($v) {
					return "'" . esc_sql($v) . "'";
				}, $car_rental_ids);
				$car_rental_ids_str = implode(',', $car_rental_ids);

                $sql = "
                SELECT IFNULL(MIN(price_per_day), 0) min_price
                FROM
                (
                    SELECT DISTINCT (avc.number_of_available_cars - IFNULL(bc.number_of_booked_cars, 0)) available_cars, avc.the_date, avc.price_per_day FROM
                    (
                        SELECT SUM(number_of_cars) number_of_available_cars, possible_dates.the_date, price_per_day, car_rental_id
                        FROM " . BOOKYOURTRAVEL_CAR_RENTAL_AVAILABILITIES_TABLE . " availabilities
                        INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date > DATE(availabilities.start_date) AND possible_dates.the_date <= DATE(availabilities.end_date)
                        WHERE availabilities.car_rental_id IN ($car_rental_ids_str)
                        GROUP BY possible_dates.the_date, availabilities.price_per_day
                    ) as avc
                    LEFT JOIN
                    (
                        SELECT COUNT(DISTINCT bookings.id) number_of_booked_cars, possible_dates.the_date booking_date, car_rental_id
                        FROM " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE . " bookings
                        INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date >= DATE(bookings.start_date) AND possible_dates.the_date <= DATE(bookings.end_date)
						WHERE bookings.car_rental_id IN ($car_rental_ids_str) ";
						
				if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout()) {
					$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
					if (!empty($completed_statuses)) {
						$sql .= " AND IFNULL(woo_status, '') IN (" . $completed_statuses . ")";
					}
				}

				$sql .= "
                        GROUP BY possible_dates.the_date
                    ) as bc
                    ON bc.booking_date = avc.the_date
                    HAVING available_cars > 0
                ) as pr ";

                $sql = $wpdb->prepare($sql, $start_date, $start_date, $end_date, $start_date, $start_date, $end_date);

                $min_price = $wpdb->get_var($sql);

                update_post_meta($location_id, $min_price_meta_key, $min_price);
                update_post_meta($location_id, $min_price_check_meta_key, time());
                update_post_meta($_location_id, $min_price_meta_key, $min_price);
                update_post_meta($_location_id, $min_price_check_meta_key, time());				
            }
		}

		return $min_price;
	}

	function list_available_end_dates($car_rental_id, $location_id, $start_date, $month, $year, $month_range) {

		global $wpdb, $bookyourtravel_theme_globals;

		$available_dates = array();

		$car_rental_id = BookYourTravel_Theme_Utils::get_default_language_post_id($car_rental_id, 'car_rental');
		$car_rental_obj = new BookYourTravel_Car_Rental($car_rental_id);
		$car_rental_is_reservation_only = $car_rental_obj->get_is_reservation_only();

		$start_date = date("Y-m-d", strtotime($start_date));
		$end_date = date("Y-m-t", strtotime($start_date)); // last day of end date month
		$end_date = date('Y-m-d', strtotime(sprintf("+%d months", $month_range), strtotime($end_date)));

		$sql = "
			SELECT DISTINCT (avc.number_of_available_cars - IFNULL(bc.number_of_booked_cars, 0)) available_cars, avc.the_date FROM
			(
				SELECT SUM(number_of_cars) number_of_available_cars, possible_dates.the_date
				FROM " . BOOKYOURTRAVEL_CAR_RENTAL_AVAILABILITIES_TABLE . " availabilities
				INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date > DATE(availabilities.start_date) AND possible_dates.the_date <= DATE(availabilities.end_date)
				WHERE availabilities.car_rental_id = %d
				GROUP BY possible_dates.the_date
			) as avc
			LEFT JOIN
			(
				SELECT COUNT(DISTINCT bookings.id) number_of_booked_cars, possible_dates.the_date booking_date
				FROM " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE . " bookings
				INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date > DATE(bookings.start_date) AND possible_dates.the_date <= DATE(bookings.end_date)
				WHERE bookings.car_rental_id = %d ";

			if (!$bookyourtravel_theme_globals->are_car_rentals_available_per_location_only()) {
				$sql .= $wpdb->prepare(" AND bookings.car_rental_pick_up_id = %d ", $location_id);
			}

			if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$car_rental_is_reservation_only) {
				$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
				if (!empty($completed_statuses)) {
					$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")";
				}
			}

			$sql .= " GROUP BY possible_dates.the_date
			) as bc
			 ON bc.booking_date = avc.the_date
			HAVING available_cars > 0";

		$sql = $wpdb->prepare($sql, $start_date, $start_date, $end_date, $car_rental_id, $start_date, $start_date, $end_date, $car_rental_id);

		$available_results = $wpdb->get_results($sql);

		$prev_date = null;

		foreach ($available_results as $available_result) {

			$new_date = date('Y-m-d', strtotime($available_result->the_date));

			if (isset($prev_date)) {
				$next_date = date('Y-m-d', strtotime($prev_date . ' +1 days'));

				if ($next_date != $new_date) {
					// there was a break in days so days after this one are not bookable
					break;
				}
			}

			$available_dates[] = date('Y-m-d', strtotime($available_result->the_date));

			$prev_date = $new_date;
		}

		return $available_dates;
	}

	function list_available_start_dates($car_rental_id, $location_id, $month, $year, $month_range) {

		global $wpdb, $bookyourtravel_theme_globals;

		$available_dates = array();

		$car_rental_id = BookYourTravel_Theme_Utils::get_default_language_post_id($car_rental_id, 'car_rental');
		$car_rental_obj = new BookYourTravel_Car_Rental($car_rental_id);
		$car_rental_is_reservation_only = $car_rental_obj->get_is_reservation_only();

		$start_date = sprintf("%d-%d-%d", $year, $month, 1);
		$start_date = date("Y-m-d", strtotime($start_date));
		$end_date = date("Y-m-t", strtotime($start_date)); // last day of end date month
		$end_date = date('Y-m-d', strtotime(sprintf("+%d months", $month_range + 8), strtotime($end_date)));

		$sql = "
			SELECT DISTINCT (avc.number_of_available_cars - IFNULL(bc.number_of_booked_cars, 0)) available_cars, avc.the_date FROM
			(
				SELECT SUM(number_of_cars) number_of_available_cars, possible_dates.the_date
				FROM " . BOOKYOURTRAVEL_CAR_RENTAL_AVAILABILITIES_TABLE . " availabilities
				INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date >= DATE(availabilities.start_date) AND possible_dates.the_date < DATE(availabilities.end_date)
				WHERE availabilities.car_rental_id = %d
				GROUP BY possible_dates.the_date
			) as avc
			LEFT JOIN
			(
				SELECT COUNT(DISTINCT bookings.id) number_of_booked_cars, possible_dates.the_date booking_date
				FROM " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE . " bookings
				INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date >= DATE(bookings.start_date) AND possible_dates.the_date < DATE(bookings.end_date)
				WHERE bookings.car_rental_id = %d ";
				
			if (!$bookyourtravel_theme_globals->are_car_rentals_available_per_location_only()) {
				$sql .= $wpdb->prepare(" AND bookings.car_rental_pick_up_id = %d ", $location_id);
			}				

			if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$car_rental_is_reservation_only) {
				$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
				if (!empty($completed_statuses)) {
					$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")";
				}
			}

			$sql .= " GROUP BY possible_dates.the_date
			) as bc
			 ON bc.booking_date = avc.the_date
			HAVING available_cars > 0
		";

		$sql = $wpdb->prepare($sql, $start_date, $start_date, $end_date, $car_rental_id, $start_date, $start_date, $end_date, $car_rental_id);


		$available_results = $wpdb->get_results($sql);

		foreach ($available_results as $available_result) {
			$available_dates[] = date('Y-m-d', strtotime($available_result->the_date));
		}

		return $available_dates;
	}

	function get_fields_ajax_request() {

		if ( isset($_REQUEST) ) {

			$nonce = wp_kses($_REQUEST['nonce'], array());
			$car_rental_id = intval(wp_kses($_REQUEST['carRentalId'], array()));

			if (wp_verify_nonce( $nonce, 'bookyourtravel_nonce' )) {

				$car_rental_obj = new BookYourTravel_Car_Rental((int)$car_rental_id);

				$fields = new stdClass();
				$fields->locations = array();

				$location_ids = $car_rental_obj->get_locations();

				if ($location_ids && count($location_ids) > 0) {

					for ( $i = 0; $i < count($location_ids); $i++ ) {

						$temp_id = $location_ids[$i];
						$location_obj = new BookYourTravel_Location(intval($temp_id));
						$location_temp = new stdClass();
						$location_temp->name = $location_obj->get_title();
						$location_temp->id = $location_obj->get_id();
						$fields->locations[] = $location_temp;
					}
				}

				$fields->min_booking_days = $car_rental_obj->get_min_booking_days();
                $fields->max_booking_days = $car_rental_obj->get_max_booking_days();

				echo json_encode($fields);
			}
		}

		// Always die in functions echoing ajax content
		die();
	}

	function save_car_rental($post_id) {
        delete_post_meta_by_key('_location_car_rental_ids');

		$car_rental_obj = new BookYourTravel_Car_Rental($post_id);
		if ($car_rental_obj) {
			$locations = $car_rental_obj->get_locations();
			if ($locations && count($locations) > 0) {
				foreach ($locations as $location_id) {
					delete_post_meta($location_id, '_location_car_rental_count');
				}
			}
		}
	}

	function after_delete_car_rental($post_id) {
		delete_post_meta_by_key('_location_car_rental_ids');

		$car_rental_obj = new BookYourTravel_Car_Rental($post_id);
		if ($car_rental_obj) {
			$locations = $car_rental_obj->get_locations();
			if ($locations && count($locations) > 0) {
				foreach ($locations as $location_id) {
					delete_post_meta($location_id, '_location_car_rental_count');
				}
			}
		}
	}

	function booking_form_calendar_after_price_breakdown() {
        get_template_part('includes/parts/booking/booking-form', 'after-price-breakdown');
    }

	function process_booking_ajax_request() {

		global $bookyourtravel_theme_globals, $bookyourtravel_theme_woocommerce, $bookyourtravel_extra_item_helper;

		$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
		$default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
		$show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();
		$current_user = wp_get_current_user();

		if ( isset($_REQUEST) ) {

			$nonce = wp_kses($_REQUEST['nonce'], array());

			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {

				$booking_object = $this->retrieve_booking_values_from_request();

				if ($booking_object != null) {

					$car_rental_obj = new BookYourTravel_Car_Rental($booking_object->car_rental_id);

					if ($car_rental_obj != null) {

						$booking_object->Id = $this->create_car_rental_booking($current_user->ID, $booking_object);

						echo json_encode($booking_object->Id);

						$use_woocommerce_for_checkout = $bookyourtravel_theme_globals->use_woocommerce_for_checkout();
						$is_reservation_only = get_post_meta( $booking_object->car_rental_id, 'car_rental_is_reservation_only', true );

						if (!$use_woocommerce_for_checkout || !BookYourTravel_Theme_Utils::is_woocommerce_active() || $is_reservation_only) {

							// only send email if we are not proceeding to WooCommerce checkout or if woocommerce is not active at all.
							$admin_email = get_bloginfo('admin_email');
							$admin_name = get_bloginfo('name');

							$subject = esc_html__('New car rental booking', 'bookyourtravel');

							$message = esc_html__('New car rental booking: ', 'bookyourtravel');
							$message .= "\n\n";
							$message .= sprintf(esc_html__("Car Rental: %s", 'bookyourtravel'), $car_rental_obj->get_title()) . "\n\n";

							$booking_form_fields = $bookyourtravel_theme_globals->get_booking_form_fields();
							$customer_email = '';
							foreach ($booking_form_fields as $form_field) {

								$field_id = $form_field['id'];

								if (isset($_REQUEST[$field_id]) && (!isset($form_field['hide']) || $form_field['hide'] !== '1')) {

									$field_value = sanitize_text_field($_REQUEST[$field_id]);
									if ($field_id == 'email') {
										$customer_email = $field_value;
									}
									$field_label = $form_field['label'];

									$message .= $field_label . ': ' . $field_value . "\n\n";
								}
							}

                            $date_format = get_option('date_format');
                            $message .= sprintf(esc_html__("Date from: %s", 'bookyourtravel'), date_i18n($date_format, strtotime($booking_object->date_from))) . "\n\n";
                            $message .= sprintf(esc_html__("Date to: %s", 'bookyourtravel'), date_i18n($date_format, strtotime($booking_object->date_to))) . "\n\n";
							$message .= sprintf(esc_html__("Adults: %s", 'bookyourtravel'), $booking_object->adults) . "\n\n";
							$message .= sprintf(esc_html__("Children: %s", 'bookyourtravel'), $booking_object->children) . "\n\n";

							if ($booking_object->total_extra_items_price > 0) {

								$extra_items_string = '';
                                $extra_items_array = array();

								if (!empty($booking_object->extra_items) && is_array($booking_object->extra_items)) {
									$extra_items_array = $booking_object->extra_items;
								}

								if ($extra_items_array != null) {
									foreach ($extra_items_array as $extra_item_id => $extra_item_quantity) {
										$extra_item_obj = new BookYourTravel_Extra_Item($extra_item_id);
										$extra_items_string .= $extra_item_quantity . ' x ' . $extra_item_obj->get_title() . ', ';
									}
								}

								$extra_items_string = trim(rtrim($extra_items_string, ', '));

								$message .= esc_html__("Extra items:", 'bookyourtravel') . "\n\n";
								$message .= $extra_items_string . "\n\n";

								$total_extra_items_price = BookYourTravel_Theme_Utils::get_price_in_current_currency($booking_object->total_extra_items_price);

								$total_extra_items_price_string = '';
								if (!$show_currency_symbol_after) {
									$total_extra_items_price_string = $default_currency_symbol . ' ' . number_format_i18n( $total_extra_items_price, $price_decimal_places );
								} else {
									$total_extra_items_price_string = number_format_i18n( $total_extra_items_price, $price_decimal_places ) . ' ' . $default_currency_symbol;
								}

								$total_extra_items_price_string = preg_replace("/&nbsp;/",' ',$total_extra_items_price_string);

								$message .= sprintf(esc_html__("Extra items total: %s", 'bookyourtravel'), $total_extra_items_price_string) . "\n\n";
							}

							if ($booking_object->total_car_rental_price > 0) {

								$total_car_rental_price = BookYourTravel_Theme_Utils::get_price_in_current_currency($booking_object->total_car_rental_price);

								$total_car_rental_price_string = '';
								if (!$show_currency_symbol_after) {
									$total_car_rental_price_string = $default_currency_symbol . ' ' . number_format_i18n( $total_car_rental_price, $price_decimal_places );
								} else {
									$total_car_rental_price_string = number_format_i18n( $total_car_rental_price, $price_decimal_places ) . ' ' . $default_currency_symbol;
								}

								$total_car_rental_price_string = preg_replace("/&nbsp;/",' ',$total_car_rental_price_string);

								$message .= sprintf(esc_html__("Reservation total: %s", 'bookyourtravel'), $total_car_rental_price_string) . "\n\n";
							}

							$total_price = BookYourTravel_Theme_Utils::get_price_in_current_currency($booking_object->total_price);

							$total_price_string = '';
							if (!$show_currency_symbol_after) {
								$total_price_string = $default_currency_symbol . ' ' . number_format_i18n( $total_price, $price_decimal_places );
							} else {
								$total_price_string = number_format_i18n( $total_price, $price_decimal_places ) . ' ' . $default_currency_symbol;
							}

							$total_price_string = preg_replace("/&nbsp;/",' ',$total_price_string);
							$message .= sprintf(esc_html__("Total price: %s", 'bookyourtravel'), $total_price_string) . "\n\n";

							$headers = "Content-Type: text/plain; charset=utf-8\r\n";
							$headers .= "From: " . $admin_name . " <" . $admin_email . ">\r\n";
							$headers .= "Reply-To: " . $admin_name . " <" . $admin_email . ">\r\n";

							if (!empty($customer_email)) {
								$ret = wp_mail($customer_email, $subject, $message, $headers, "");
								if (!$ret) {
									global $phpmailer;
									if (isset($phpmailer) && WP_DEBUG) {
										var_dump($phpmailer->ErrorInfo);
									}
								}
							}

							$contact_emails = trim(get_post_meta($booking_object->car_rental_id, 'car_rental_contact_email', true ));

							$emails_array = array();
							if (empty($contact_emails))
								$emails_array = array($admin_email);
							else
								$emails_array = explode(';', $contact_emails);

							foreach ($emails_array as $email) {
								if (!empty($email)) {
									$ret = wp_mail($email, $subject, $message, $headers, "");
									if (!$ret) {
										global $phpmailer;
										if (isset($phpmailer) && WP_DEBUG) {
											var_dump($phpmailer->ErrorInfo);
										}
									}
								}
							}
						}
					}
				}
			}
		}

		// Always die in functions echoing ajax content
		die();
	}

	function retrieve_booking_values_from_request($dont_calculate_totals = false) {

		global $bookyourtravel_theme_globals, $bookyourtravel_extra_item_helper;

		$enable_extra_items = $bookyourtravel_theme_globals->enable_extra_items();

		$booking_object = null;

		if ( isset($_REQUEST) ) {

			$booking_object = new stdClass();

			$booking_object->Id = isset($_REQUEST['booking_id']) ? intval(wp_kses($_REQUEST['booking_id'], array())) : 0;

			$booking_object->total_price = 0;
			$booking_object->total_car_rental_price = 0;
			$booking_object->total_extra_items_price = 0;

			$booking_object->car_rental_id = isset($_REQUEST['car_rental_id']) ? intval(wp_kses($_REQUEST['car_rental_id'], array())) : 0;
			$booking_object->car_rental_id = BookYourTravel_Theme_Utils::get_default_language_post_id($booking_object->car_rental_id, 'car_rental');

			$booking_object->car_rental_pick_up_id = isset($_REQUEST['car_rental_pick_up_id']) ? intval(wp_kses($_REQUEST['car_rental_pick_up_id'], array())) : 0;
			$booking_object->car_rental_pick_up_id = BookYourTravel_Theme_Utils::get_default_language_post_id($booking_object->car_rental_pick_up_id, 'location');

			$booking_object->car_rental_drop_off_id = isset($_REQUEST['car_rental_drop_off_id']) ? intval(wp_kses($_REQUEST['car_rental_drop_off_id'], array())) : 0;
			$booking_object->car_rental_drop_off_id = BookYourTravel_Theme_Utils::get_default_language_post_id($booking_object->car_rental_drop_off_id, 'location');

			$booking_object->date_from = isset($_REQUEST['date_from']) ? date('Y-m-d', strtotime(sanitize_text_field($_REQUEST['date_from']))) : null;
			$booking_object->date_to = isset($_REQUEST['date_to']) ? date('Y-m-d', strtotime(sanitize_text_field($_REQUEST['date_to']))) : null;

			if ($dont_calculate_totals) {
				$booking_object->total_car_rental_price = isset($_REQUEST['total_car_rental_price']) ? $_REQUEST['total_car_rental_price'] : 0;
				$booking_object->total_price = isset($_REQUEST['total_price']) ? $_REQUEST['total_price'] : 0;
			} else {
				$booking_object->total_car_rental_price = $this->calculate_total_car_rental_price($booking_object->car_rental_id, $booking_object->date_from, $booking_object->date_to, $booking_object->Id);
				if ($booking_object->total_car_rental_price == -1) {
					return null;
				}
				$booking_object->total_price += $booking_object->total_car_rental_price;
			}

			$booking_object->date_from = date('Y-m-d',strtotime($booking_object->date_from));
			$booking_object->date_to = date('Y-m-d',strtotime($booking_object->date_to));

			$booking_object->extra_items = null;

			if ($dont_calculate_totals) {
				$booking_object->total_extra_items_price = isset($_REQUEST['total_extra_items_price']) ? $_REQUEST['total_extra_items_price'] : 0;
			} else if ($enable_extra_items && isset($_REQUEST['extra_items'])) {

				$booking_object->submitted_extra_items_array = (array)$_REQUEST['extra_items'];

				$booking_object->extra_items = array();

				$from_time = strtotime($booking_object->date_from);
				$to_time = strtotime($booking_object->date_to);
				$time_diff = $to_time - $from_time;
				$total_days = floor($time_diff/(60*60*24));
				$total_days = $total_days > 0 ? $total_days : 1;

				foreach ($booking_object->submitted_extra_items_array as $submitted_extra_item) {
					if (isset($submitted_extra_item['id']) && $submitted_extra_item['quantity']) {
						$extra_item_id = intval(sanitize_text_field($submitted_extra_item['id']));
						$quantity = intval(sanitize_text_field($submitted_extra_item['quantity']));
						$booking_object->extra_items[$extra_item_id] = $quantity;
						$booking_object->total_extra_items_price += $bookyourtravel_extra_item_helper->calculate_extra_item_total($extra_item_id, $quantity, 1, 0, $total_days, 1);
					}
				}

				$booking_object->total_price += $booking_object->total_extra_items_price;
			}

			$booking_object->cart_price = isset($_REQUEST['cart_price']) ? floatval(wp_kses($_REQUEST['cart_price'], array())) : 0;
            if (!isset($_REQUEST['cart_price'])) {
                $booking_object->cart_price = $booking_object->total_price;
            }
            if ($bookyourtravel_theme_globals->enable_deposit_payments()) {
				$car_rental_deposit_percentage = get_post_meta($booking_object->car_rental_id, 'car_rental_deposit_percentage', true);
				$car_rental_deposit_percentage = isset($car_rental_deposit_percentage) && $car_rental_deposit_percentage !== "" ? intval($car_rental_deposit_percentage) : 100;
	
                if (!$dont_calculate_totals) {
                    $booking_object->cart_price = $booking_object->total_price * ($car_rental_deposit_percentage/100);
                }
            }

			$booking_form_fields = $bookyourtravel_theme_globals->get_booking_form_fields();

			$booking_object->first_name = '';
			$booking_object->last_name = '';
			$booking_object->company = '';
			$booking_object->email = '';
			$booking_object->phone = '';
			$booking_object->address = '';
			$booking_object->address_2 = '';
			$booking_object->town = '';
			$booking_object->zip = '';
			$booking_object->state = '';
			$booking_object->country = '';
			$booking_object->special_requirements = '';
			$booking_object->other_fields = array();

			foreach ($booking_form_fields as $form_field) {

				$field_id = $form_field['id'];

				if (isset($_REQUEST[$field_id]) && (!isset($form_field['hide']) || $form_field['hide'] !== '1')) {

					$field_value = sanitize_text_field($_REQUEST[$field_id]);

					switch ($field_id) {

						case 'first_name' 			: { $booking_object->first_name = $field_value; break; }
						case 'last_name' 			: { $booking_object->last_name = $field_value; break; }
						case 'company' 				: { $booking_object->company = $field_value; break; }
						case 'email' 				: { $booking_object->email = $field_value; break; }
						case 'phone' 				: { $booking_object->phone = $field_value; break; }
						case 'address' 				: { $booking_object->address = $field_value; break; }
						case 'address_2' 			: { $booking_object->address_2 = $field_value; break; }
						case 'town' 				: { $booking_object->town = $field_value; break; }
						case 'zip' 					: { $booking_object->zip = $field_value; break; }
						case 'state' 				: { $booking_object->state = $field_value; break; }
						case 'country' 				: { $booking_object->country = $field_value; break; }
						case 'special_requirements' : { $booking_object->special_requirements = $field_value; break; }
						default : {
							$booking_object->other_fields[$field_id] = $field_value;
							break;
						}
					}
				}
			}
		}

		return $booking_object;
	}

	function calculate_total_car_rental_price($car_rental_id, $date_from, $date_to, $current_booking_id = 0) {

		global $wpdb;

		// we are actually (in terms of db data) looking for date 1 day before the to date
		// e.g. when you look to book a car from 19.12. to 20.12 you will be staying 1 night, not 2
		$date_to = date('Y-m-d', strtotime($date_to.' -1 day'));

		$dates = BookYourTravel_Theme_Utils::get_dates_from_range($date_from, $date_to);

		$total_price = 0;

		foreach ($dates as $search_date) {
			$prices = $this->get_prices($search_date, $car_rental_id, $current_booking_id);
			if (isset($prices) && isset($prices->price_per_day)) {
				$total_price += (float)$prices->price_per_day;
			} else {
				return -1;
			}
		}

		return $total_price;
	}

	function remove_unnecessary_meta_boxes() {

		remove_meta_box('tagsdiv-car_rental_tag', 'car_rental', 'side');
		remove_meta_box('tagsdiv-car_type', 'car_rental', 'side');
	}

	function manage_edit_car_rental_columns($columns) {

		//unset($columns['taxonomy-car_type']);
		return $columns;
	}

	function car_rental_admin_init() {
        if ($this->enable_car_rentals) {
            $this->initialize_meta_fields();
            new Custom_Add_Meta_Box('car_rental_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->car_rental_custom_meta_fields, $this->car_rental_custom_meta_tabs, 'car_rental');

            $this->car_rental_list_meta_box = new Custom_Add_Meta_Box('car_rental_list_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->car_rental_list_custom_meta_fields, $this->car_rental_list_custom_meta_tabs, 'page');
            remove_action('add_meta_boxes', array( $this->car_rental_list_meta_box, 'add_box' ));
            add_action('add_meta_boxes', array($this, 'car_rental_list_add_meta_boxes'));
        }
	}

	function car_rental_list_add_meta_boxes() {
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file == 'page-car_rental-list.php') {
			add_meta_box(
				$this->car_rental_list_meta_box->id,
				$this->car_rental_list_meta_box->title,
				array( $this->car_rental_list_meta_box,
				'meta_box_callback' ),
				'page', 'normal', 'high'
			);
		}
	}

	function initialize_post_type() {

		global $bookyourtravel_theme_globals;
		$this->enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();

		if ($this->enable_car_rentals) {
			$this->register_car_rental_post_type();
			$this->register_car_rental_tag_taxonomy();
			$this->register_car_type_taxonomy();
		}

		// have to make sure extra tables are created regardless of whether the post type is enabled or not in order for tables to exist if post type
		// is enabled at a later stage.
		$this->create_car_rental_extra_tables();
	}

	function register_car_rental_tag_taxonomy() {

		$labels = array(
				'name'              => esc_html__( 'Car rental tags', 'bookyourtravel' ),
				'singular_name'     => esc_html__( 'Car rental tag', 'bookyourtravel' ),
				'search_items'      => esc_html__( 'Search Car rental tags', 'bookyourtravel' ),
				'all_items'         => esc_html__( 'All Car rental tags', 'bookyourtravel' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'         => esc_html__( 'Edit Car rental tag', 'bookyourtravel' ),
				'update_item'       => esc_html__( 'Update Car rental tag', 'bookyourtravel' ),
				'add_new_item'      => esc_html__( 'Add New Car rental tag', 'bookyourtravel' ),
				'new_item_name'     => esc_html__( 'New Car rental tag Name', 'bookyourtravel' ),
				'separate_items_with_commas' => esc_html__( 'Separate car rental tags with commas', 'bookyourtravel' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove car rental tags', 'bookyourtravel' ),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used car rental tags', 'bookyourtravel' ),
				'not_found'                  => esc_html__( 'No car rental tags found.', 'bookyourtravel' ),
				'menu_name'         => esc_html__( 'Car rental tags', 'bookyourtravel' ),
			);

		$args = array(
				'hierarchical'      => false,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'update_count_callback' => '_update_post_term_count',
				'rewrite' => array('slug' => 'car-rental-tag'),
			);

		register_taxonomy( 'car_rental_tag', array( 'car_rental' ), $args );
	}

	function register_car_rental_post_type() {

		global $bookyourtravel_theme_globals;
		$car_rentals_permalink_slug = $bookyourtravel_theme_globals->get_car_rentals_permalink_slug();

		$car_rental_list_page_id = $bookyourtravel_theme_globals->get_car_rental_list_page_id();

		if ($car_rental_list_page_id > 0) {

			add_rewrite_rule(
				"{$car_rentals_permalink_slug}$",
				"index.php?post_type=page&page_id={$car_rental_list_page_id}", 'top');

			add_rewrite_rule(
				"{$car_rentals_permalink_slug}/page/?([1-9][0-9]*)",
				"index.php?post_type=page&page_id={$car_rental_list_page_id}&paged=\$matches[1]", 'top');

		}

		add_rewrite_rule(
			"{$car_rentals_permalink_slug}/([^/]+)/page/?([1-9][0-9]*)",
			"index.php?post_type=car_rental&name=\$matches[1]&paged-byt=\$matches[2]", 'top');

		add_rewrite_tag('%paged-byt%', '([1-9][0-9]*)');

		$labels = array(
			'name'                => esc_html__( 'Car rentals', 'bookyourtravel' ),
			'singular_name'       => esc_html__( 'Car rental', 'bookyourtravel' ),
			'menu_name'           => esc_html__( 'Car rentals', 'bookyourtravel' ),
			'all_items'           => esc_html__( 'All Car rentals', 'bookyourtravel' ),
			'view_item'           => esc_html__( 'View Car rental', 'bookyourtravel' ),
			'add_new_item'        => esc_html__( 'Add New Car rental', 'bookyourtravel' ),
			'add_new'             => esc_html__( 'New Car rental', 'bookyourtravel' ),
			'edit_item'           => esc_html__( 'Edit Car rental', 'bookyourtravel' ),
			'update_item'         => esc_html__( 'Update Car rental', 'bookyourtravel' ),
			'search_items'        => esc_html__( 'Search Car rentals', 'bookyourtravel' ),
			'not_found'           => esc_html__( 'No Car rentals found', 'bookyourtravel' ),
			'not_found_in_trash'  => esc_html__( 'No Car rentals found in Trash', 'bookyourtravel' ),
		);
		$args = array(
			'label'               => esc_html__( 'Car rental', 'bookyourtravel' ),
			'description'         => esc_html__( 'Car rental information pages', 'bookyourtravel' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'author', 'page-attributes' ),
			'taxonomies'          => array( ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'show_in_rest'		  => true,
			'rewrite' =>  array('slug' => $car_rentals_permalink_slug),
		);

		register_post_type( 'car_rental', $args );
	}

	function register_car_type_taxonomy() {

		$labels = array(
				'name'              => esc_html__( 'Car types', 'bookyourtravel' ),
				'singular_name'     => esc_html__( 'Car type', 'bookyourtravel' ),
				'search_items'      => esc_html__( 'Search Car types', 'bookyourtravel' ),
				'all_items'         => esc_html__( 'All Car types', 'bookyourtravel' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'         => esc_html__( 'Edit Car type', 'bookyourtravel' ),
				'update_item'       => esc_html__( 'Update Car type', 'bookyourtravel' ),
				'add_new_item'      => esc_html__( 'Add New Car type', 'bookyourtravel' ),
				'new_item_name'     => esc_html__( 'New Car type Name', 'bookyourtravel' ),
				'separate_items_with_commas' => esc_html__( 'Separate car types with commas', 'bookyourtravel' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove car types', 'bookyourtravel' ),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used car types', 'bookyourtravel' ),
				'not_found'                  => esc_html__( 'No car types found.', 'bookyourtravel' ),
				'menu_name'         => esc_html__( 'Car types', 'bookyourtravel' ),
			);

		$args = array(
				'hierarchical'      => false,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => false,
				'update_count_callback' => '_update_post_term_count',
				'rewrite' => array('slug' => 'car-rental-type'),
			);

		register_taxonomy( 'car_type', 'car_rental', $args );
	}

	function create_car_rental_extra_tables() {

		global $wpdb, $bookyourtravel_installed_version, $force_recreate_tables;

		if ($bookyourtravel_installed_version != BOOKYOURTRAVEL_VERSION || $force_recreate_tables) {

			// we do not execute sql directly
			// we are calling dbDelta which cant migrate database
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

			$table_name = BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE;
			$sql = "CREATE TABLE " . $table_name . " (
						Id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						car_rental_id bigint(20) NOT NULL,
						car_rental_pick_up_id bigint(20) NOT NULL,
						car_rental_drop_off_id bigint(20) NOT NULL,
						start_date datetime NOT NULL,
						end_date datetime NOT NULL,
						first_name varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
						last_name varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
						company varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						email varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
						phone varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						address varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						address_2 varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						town varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						zip varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						state varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						country varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NULL,
						special_requirements text CHARACTER SET utf8 COLLATE utf8_bin NULL,
						other_fields text CHARACTER SET utf8 COLLATE utf8_bin NULL,
						extra_items text CHARACTER SET utf8 COLLATE utf8_bin NULL,
						total_car_rental_price decimal(16,2) NOT NULL DEFAULT '0.00',
						total_extra_items_price decimal(16,2) NOT NULL DEFAULT '0.00',
						cart_price decimal(16,2) NOT NULL DEFAULT '0.00',
						total_price decimal(16, 2) NOT NULL,
						user_id bigint(10) NOT NULL DEFAULT 0,
						created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
						woo_order_id bigint(20) NULL,
						cart_key VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' NOT NULL,
						woo_status varchar(255) NULL,
						PRIMARY KEY  (Id)
					);";

			dbDelta($sql);

			$table_name = BOOKYOURTRAVEL_CAR_RENTAL_AVAILABILITIES_TABLE;
			$sql = "CREATE TABLE " . $table_name . " (
						Id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						season_name varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
						start_date datetime NOT NULL,
						end_date datetime NOT NULL,
						car_rental_id bigint(20) unsigned NOT NULL,
						price_per_day decimal(16,2) NOT NULL,
						number_of_cars bigint(10) NOT NULL DEFAULT 1,
						created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
						PRIMARY KEY  (Id)
					);";

			dbDelta($sql);
			global $EZSQL_ERROR;
			$EZSQL_ERROR = array();

		}
	}

	function car_rentals_search_fields( $fields, $wp_query ) {

		global $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;

		if ( isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'car_rental' ) {

			$search_only_available = false;
			if (isset($wp_query->query_vars['search_only_available']))
				$search_only_available = $wp_query->get('search_only_available');

			$date_today = date('Y-m-d', time());

			$date_from = null;
			if ( isset($wp_query->query_vars['byt_date_from']) )
				$date_from = date('Y-m-d', strtotime($wp_query->get('byt_date_from')));
			else
				$date_from = $date_today;

			$date_to = null;
            if (isset($wp_query->query_vars['byt_date_to'])) {
                $date_to = date('Y-m-d', strtotime($wp_query->get('byt_date_to')));
            } else {
				$date_to = date('Y-m-d', strtotime($date_from . ' +24 months'));
			}

 			if (isset($date_from) && $date_from == $date_to)
				$date_to = date('Y-m-d', strtotime($date_from . ' +7 day'));

			if ($search_only_available || isset($wp_query->query_vars['byt_date_from']) || isset($wp_query->query_vars['byt_date_from'])) {

				if (isset($date_from) || isset($date_to)) {

					$fields .= ", (SELECT SUM(cars_available) cars_available FROM ( ";
					$fields .= "SELECT IFNULL(SUM(number_of_cars), 0) cars_available, availabilities.car_rental_id
								FROM " . BOOKYOURTRAVEL_CAR_RENTAL_AVAILABILITIES_TABLE . " availabilities
								INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date > DATE(availabilities.start_date) AND possible_dates.the_date <= DATE(availabilities.end_date)
								WHERE 1=1 ";

					if ($date_from != null && $date_to != null) {
						$fields .= $wpdb->prepare(" AND the_date >= %s AND the_date < %s ", $date_from, $date_to);
					} else if ($date_from != null) {
						$fields .= $wpdb->prepare(" AND the_date >= %s ", $date_from);
					} else if ($date_to != null) {
						$fields .= $wpdb->prepare(" AND the_date < %s ", $date_to);
					}

					$date_today = date('Y-m-d', time());
					$fields .= $wpdb->prepare(" AND the_date >= %s ", $date_today);

					$fields = $wpdb->prepare($fields, $date_from, $date_from, $date_to);
					$fields .= "GROUP BY possible_dates.the_date, availabilities.car_rental_id ) as ca ";

					if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('car_rental') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
						$fields .= " WHERE (ca.car_rental_id = wpml_translations_default.element_id OR ca.car_rental_id = wpml_translations.element_id)";
					} else {
						$fields .= " WHERE ca.car_rental_id = {$wpdb->posts}.ID ";
					}

					$fields .= " ) cars_available ";

                    $date_range_match = ' (possible_dates.the_date > DATE(bookings.start_date) AND possible_dates.the_date < DATE(bookings.end_date)) ';

					$fields .= ", (SELECT IFNULL(SUM(cars_booked), 0) cars_booked FROM (SELECT IFNULL(COUNT(the_date), 0) cars_booked, bookings.car_rental_id 
								FROM " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE . " bookings ";

					$fields .= $wpdb->prepare("INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON " . $date_range_match, $date_from, $date_from, $date_to);

					if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout()) {
						$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
						if (!empty($completed_statuses)) {
							$fields .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")";
						}
					}

					$fields .= " GROUP BY possible_dates.the_date, bookings.car_rental_id) as cb";

					if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('car_rental') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
						$fields .= " WHERE (cb.car_rental_id = wpml_translations_default.element_id OR cb.car_rental_id = wpml_translations.element_id)";
					} else {
						$fields .= " WHERE cb.car_rental_id = {$wpdb->posts}.ID ";
					}

					$fields .= " ) cars_booked ";
				}
			}

			$min_price_meta_key = BookYourTravel_Theme_Utils::build_min_price_meta_key("car_rental", $date_from, $date_to);
			$min_price_check_meta_key = BookYourTravel_Theme_Utils::build_min_price_check_meta_key("car_rental", $date_from, $date_to);

			$fields_sql = ", IFNULL((SELECT price_meta2.meta_value + 0 FROM {$wpdb->postmeta} price_meta2 ";
			if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('car_rental') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
				$fields_sql .= " WHERE price_meta2.post_id={$wpdb->posts}.ID ";
			} else {
				$fields_sql .= " WHERE price_meta2.post_id={$wpdb->posts}.ID ";
			}
			
			$fields_sql .= " AND price_meta2.meta_key=%s LIMIT 1), 0) car_rental_price ";

			$fields .= $wpdb->prepare($fields_sql, $min_price_meta_key);
			
			$fields_sql = ", IFNULL((SELECT price_meta3.meta_value + 0 FROM {$wpdb->postmeta} price_meta3 ";
			if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('car_rental') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
				$fields_sql .= " WHERE price_meta3.post_id={$wpdb->posts}.ID ";
			} else {
				$fields_sql .= " WHERE price_meta3.post_id={$wpdb->posts}.ID ";
			}
			
			$fields_sql .= " AND price_meta3.meta_key='car_rental_static_from_price' LIMIT 1), 0) car_rental_static_price ";

            $fields .= $fields_sql;
		}

		return $fields;
	}

	function car_rentals_search_where( $where, $wp_query ) {

		global $wpdb;

		if ( isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'car_rental' ) {
			$where = str_replace('DECIMAL', 'DECIMAL(10,2)', $where);
		}

		return $where;
	}

	function car_rentals_search_orderby($orderby, $wp_query) {

		global $wpdb;

        if (isset($wp_query->query_vars['byt_orderby']) && isset($wp_query->query_vars['byt_order'])) {
            $order = 'ASC';
            if ($wp_query->get('byt_order') == 'DESC') {
                $order = 'DESC';
            }

            $column = 'car_rental_price';
            if ($wp_query->get('byt_orderby') == $column) {
                $orderby = $column . ' ' . $order;
            }
            $column = 'car_rental_static_price';
            if ($wp_query->get('byt_orderby') == $column) {
                $orderby = $column . ' ' . $order;
            }
        }

		return $orderby;
	}

	function car_rentals_search_groupby( $groupby, $wp_query ) {

		global $wpdb;

		if (empty($groupby))
			$groupby = " {$wpdb->posts}.ID ";

		if (!is_admin()) {

			if ( isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'car_rental' ) {

				$date_today = date('Y-m-d', time());
				$date_from = null;
				if ( isset($wp_query->query_vars['byt_date_from']) )
					$date_from = date('Y-m-d', strtotime($wp_query->get('byt_date_from')));
				else
					$date_from = $date_today;

				$date_to = null;
				if ( isset($wp_query->query_vars['byt_date_to']) ) {
					$date_to = date('Y-m-d', strtotime($wp_query->get('byt_date_to')));
				} else {
					$date_to = date('Y-m-d', strtotime($date_from . ' +24 months'));
				}

				if (isset($date_from) && $date_from == $date_to) {
					$date_to = date('Y-m-d', strtotime($date_from . ' +7 day'));
				}

				$search_only_available = false;
				if (isset($wp_query->query_vars['search_only_available']))
					$search_only_available = $wp_query->get('search_only_available');

				$groupby .= ' HAVING 1=1 ';

				if ($search_only_available && (isset($date_from) || isset($date_to))) {
					$groupby .= ' AND cars_available > cars_booked ';
				}

				$from_time = strtotime($date_from);
				$to_time = strtotime($date_to);
				$total_days = floor(($to_time - $from_time)/(60*60*24));
				$total_days = $total_days > 0 ? $total_days : 1;

				if ($search_only_available && $total_days > 1 && isset($wp_query->query_vars['byt_date_from']) && isset($wp_query->query_vars['byt_date_to'])) {
					$groupby .= $wpdb->prepare(' AND (cars_available - cars_booked) >= %d', $total_days);
				}

				if (isset($wp_query->query_vars['prices'])) {

					$prices = (array)$wp_query->query_vars['prices'];
					if (count($prices) > 0) {

						$price_range_bottom = $wp_query->query_vars['price_range_bottom'];
						$price_range_increment = $wp_query->query_vars['price_range_increment'];
						$price_range_count = $wp_query->query_vars['price_range_count'];

						$bottom = 0;
						$top = 0;

						$groupby .= ' AND ( 1!=1 ';
						for ( $i = 0; $i < $price_range_count; $i++ ) {
							$bottom = ($i * $price_range_increment) + $price_range_bottom;
							if ($bottom == 0) {
								$bottom = 0.1;
							}							
							$top = ( ( $i+1 ) * $price_range_increment ) + $price_range_bottom - 1;

							if ( in_array( $i + 1, $prices ) ) {
								if ( $i < ( ($price_range_count - 1) ) ) {
									$groupby .= $wpdb->prepare(" OR (car_rental_price >= %f AND car_rental_price <= %f ) ", $bottom, $top);
									$groupby .= $wpdb->prepare(" OR (car_rental_static_price >= %f AND car_rental_static_price <= %f ) ", $bottom, $top);
								} else {
									$groupby .= $wpdb->prepare(" OR (car_rental_price >= %f ) ", $bottom);
									$groupby .= $wpdb->prepare(" OR (car_rental_static_price >= %f ) ", $bottom);
								}
							}
						}

						$groupby .= ")";
					}
				}

			}
		}

		return $groupby;
	}

	function car_rentals_search_join($join) {

		global $wp_query, $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;

		if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('car_rental') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$join .= " LEFT JOIN {$wpdb->prefix}icl_translations wpml_translations_default ON wpml_translations_default.trid = wpml_translations.trid AND (wpml_translations_default.source_language_code IS NULL OR wpml_translations.source_language_code IS NULL) ";
		}

		return $join;
	}

	function list_car_rentals_count ( $paged = 0, $per_page = -1, $orderby = '', $order = '', $location_ids = array(), $exclusive_locations = false, $car_types_array = array(), $car_rental_tags_array = array(), $search_args = array(), $featured_only = false, $author_id = null, $include_private = false ) {
		$results = $this->list_car_rentals($paged, $per_page, $orderby, $order, $location_ids, $exclusive_locations, $car_types_array, $car_rental_tags_array, $search_args, $featured_only, $author_id, $include_private, true);
		return $results['total'];
	}

	function list_car_rentals( $paged = 0, $per_page = -1, $orderby = '', $order = '', $param_location_ids = array(), $exclusive_locations = false, $car_types_array = array(), $car_rental_tags_array = array(), $search_args = array(), $featured_only = false, $author_id = null, $include_private = false, $count_only = false ) {

		global $bookyourtravel_theme_globals;
		$location_ids = array();

		if (count($param_location_ids) > 0 && is_array($param_location_ids)) {
			foreach ($param_location_ids as $location_id) {
				if ($location_id > 0) {
					$location_id = BookYourTravel_Theme_Utils::get_current_language_post_id(intval($location_id), 'location');
					$location_ids[] = $location_id;
					if (!$exclusive_locations) {
						$location_descendants = BookYourTravel_Theme_Utils::get_post_descendants($location_id, 'location');
						foreach ($location_descendants as $location) {
							$location_ids[] = $location->ID; // BookYourTravel_Theme_Utils::get_default_language_post_id($location->ID, 'location');
						}
					}
				}
			}
		}

		if (isset($search_args['keyword']) && strlen($search_args['keyword']) > 0 && !$exclusive_locations) {
			$args = array(
						's' => $search_args['keyword'],
						'post_type' => 'location',
						'posts_per_page'=> -1, 
						'post_status' => 'publish',
						'suppress_filters' => false
					);

			$location_posts = get_posts($args);
			foreach ($location_posts as $location) {
				$location_id = BookYourTravel_Theme_Utils::get_current_language_post_id(intval($location->ID), 'location');
				if (!in_array($location_id, $location_ids)) {
					$location_ids[] = $location_id; // BookYourTravel_Theme_Utils::get_default_language_post_id($location->ID, 'location');
				}
			}

			$descendant_location_ids = array();
			foreach ($location_ids as $temp_location_id) {
				$location_descendants = BookYourTravel_Theme_Utils::get_post_descendants($temp_location_id, 'location');
				foreach ($location_descendants as $location) {
					if (!in_array($location->ID, $descendant_location_ids)) {
						$descendant_location_ids[] = $location->ID; // BookYourTravel_Theme_Utils::get_default_language_post_id($location->ID, 'location');
					}
				}
			}
			$location_ids = array_merge($descendant_location_ids, $location_ids);
		}

		$args = array(
			'post_type'         => 'car_rental',
			'post_status'       => array('publish'),
			'posts_per_page'    => $per_page,
			'paged' 			=> $paged,
			'orderby'           => $orderby,
			'suppress_filters' 	=> false,
			'order'				=> $order,
		);

		if ($orderby == 'review_score') {
			$args['meta_key'] = 'review_score';
			$args['orderby'] = 'meta_value_num';
		} else if ($orderby == 'price' || $orderby == 'min_price') {
            if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
                $args['byt_orderby'] = 'car_rental_static_price';
            } else {
                $args['byt_orderby'] = 'car_rental_price';
            }
			$args['byt_order'] = $order;			
		}

		if (isset($search_args['keyword']) && strlen($search_args['keyword']) > 0) {
			$args['s'] = $search_args['keyword'];
		}

		if ($include_private) {
			$args['post_status'][] = 'draft';
			$args['post_status'][] = 'private';
		}

		if (isset($featured_only) && $featured_only) {
			$args['meta_query'][] = array(
				'key'       => 'car_rental_is_featured',
				'value'     => 1,
				'compare'   => '=',
				'type' => 'numeric'
			);
		}

		if (isset($author_id)) {
			$author_id = intval($author_id);
			if ($author_id > 0) {
				$args['author'] = $author_id;
			}
		}

		if (count($location_ids) > 0) {
			$meta_query_array = null;
			if ($exclusive_locations) {
				$meta_query_array = array(
					'relation' => 'AND'
				);
			} else {
				$meta_query_array = array(
					'relation' => 'OR'
				);
			}

			foreach ($location_ids as $location_id) {
				$meta_query_array[] = array(
					'key' => 'locations',
					'value' => serialize(strval($location_id)),
					'compare' => 'LIKE'
				);
			}

			$args['meta_query'][] = $meta_query_array;
		}

		$args['tax_query'] = array();

		if (!empty($car_types_array)) {
			$args['tax_query'][] = 	array(
					'taxonomy' => 'car_type',
					'field' => 'term_id',
					'terms' => $car_types_array,
					'operator'=> 'IN'
			);
		}

		if (!empty($car_rental_tags_array)) {
			$args['tax_query'][] = 	array(
					'taxonomy' => 'car_rental_tag',
					'field' => 'term_id',
					'terms' => $car_rental_tags_array,
					'operator'=> 'IN'
			);
		}

		$search_only_available = false;
		if ( isset($search_args['search_only_available'])) {
			$search_only_available = $search_args['search_only_available'];
		}

		if ( isset($search_args['prices']) ) {
			$args['prices'] = $search_args['prices'];
			$args['price_range_bottom'] = $bookyourtravel_theme_globals->get_price_range_bottom();
			$args['price_range_increment'] = $bookyourtravel_theme_globals->get_price_range_increment();
			$args['price_range_count'] = $bookyourtravel_theme_globals->get_price_range_count();
		}

		if ( isset($search_args['date_from']) )
			$args['byt_date_from'] = $search_args['date_from'];
		if ( isset($search_args['date_to']) )
			$args['byt_date_to'] =  $search_args['date_to'];

		$args['search_only_available'] = $search_only_available;

		add_filter('posts_where', array($this, 'car_rentals_search_where'), 10, 2);
		add_filter('posts_fields', array($this, 'car_rentals_search_fields'), 10, 2 );
		add_filter('posts_groupby', array($this, 'car_rentals_search_groupby'), 10, 2 );
		add_filter('posts_orderby', array($this, 'car_rentals_search_orderby'), 10, 2 );
		add_filter('posts_join', array($this, 'car_rentals_search_join'), 10, 2 );

		$posts_query = new WP_Query($args);

		// echo $posts_query->request;

		if ($count_only) {
			$results = array(
				'total' => $posts_query->found_posts,
				'results' => null
			);
		} else {
			$results = array();

			if ($posts_query->have_posts() ) {
				while ( $posts_query->have_posts() ) {
					global $post;
					$posts_query->the_post();
					$results[] = $post;
				}
			}

			$results = array(
				'total' => $posts_query->found_posts,
				'results' => $results
			);
		}

		wp_reset_postdata();

		remove_filter('posts_where', array($this, 'car_rentals_search_where'));
		remove_filter('posts_fields', array($this, 'car_rentals_search_fields' ));
		remove_filter('posts_groupby', array($this, 'car_rentals_search_groupby' ));
		remove_filter('posts_orderby', array($this, 'car_rentals_search_orderby' ));
		remove_filter('posts_join', array($this, 'car_rentals_search_join') );

		return $results;
	}

	function create_car_rental_booking($user_id, $booking_object) {

		global $wpdb;

		$errors = array();

		$sql = "INSERT INTO " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE . "
				(user_id, car_rental_id, car_rental_pick_up_id, car_rental_drop_off_id, start_date, end_date, first_name, last_name, company, email, phone, address, address_2, town, zip, state, country, special_requirements, other_fields, extra_items, total_car_rental_price, total_extra_items_price, total_price, cart_price)
				VALUES
				(%d, %d, %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %f, %f, %f, %f);";

		$result = $wpdb->query($wpdb->prepare($sql, $user_id, $booking_object->car_rental_id, $booking_object->car_rental_pick_up_id, $booking_object->car_rental_drop_off_id, $booking_object->date_from, $booking_object->date_to, $booking_object->first_name, $booking_object->last_name, $booking_object->company, $booking_object->email, $booking_object->phone, $booking_object->address, $booking_object->address_2, $booking_object->town, $booking_object->zip,  $booking_object->state, $booking_object->country, $booking_object->special_requirements, serialize($booking_object->other_fields), serialize($booking_object->extra_items), $booking_object->total_car_rental_price, $booking_object->total_extra_items_price, $booking_object->total_price, $booking_object->cart_price));

		if (is_wp_error($result))
			$errors[] = $result;

		$booking_object->Id = $wpdb->insert_id;

		$this->clear_price_meta_cache($booking_object->car_rental_id);

		return $booking_object->Id;
	}

	function update_car_rental_booking($booking_id, $booking_object) {

		global $wpdb;

		$result = 0;

		$sql = "UPDATE " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE . " SET ";

		$field_sql = '';

		foreach ($booking_object as $field_key => $field_value) {

			switch ($field_key) {
				case 'car_rental_id'				: $field_sql .= $wpdb->prepare("car_rental_id = %d, ", $field_value); break;
				case 'car_rental_pick_up_id'		: $field_sql .= $wpdb->prepare("car_rental_pick_up_id = %d, ", $field_value); break;
				case 'car_rental_drop_off_id'		: $field_sql .= $wpdb->prepare("car_rental_drop_off_id = %d, ", $field_value); break;
				case 'user_id' 						: $field_sql .= $wpdb->prepare("user_id = %d, ", $field_value); break;
				case 'date_from' 					: $field_sql .= $wpdb->prepare("start_date = %s, ", $field_value); break;
				case 'date_to' 						: $field_sql .= $wpdb->prepare("end_date = %s, ", $field_value); break;
				case 'first_name' 					: $field_sql .= $wpdb->prepare("first_name = %s, ", $field_value); break;
				case 'last_name' 					: $field_sql .= $wpdb->prepare("last_name = %s, ", $field_value); break;
				case 'company' 						: $field_sql .= $wpdb->prepare("company = %s, ", $field_value); break;
				case 'email' 						: $field_sql .= $wpdb->prepare("email = %s, ", $field_value); break;
				case 'phone' 						: $field_sql .= $wpdb->prepare("phone = %s, ", $field_value); break;
				case 'address' 						: $field_sql .= $wpdb->prepare("address = %s, ", $field_value); break;
				case 'address_2' 					: $field_sql .= $wpdb->prepare("address_2 = %s, ", $field_value); break;
				case 'town' 						: $field_sql .= $wpdb->prepare("town = %s, ", $field_value); break;
				case 'zip' 							: $field_sql .= $wpdb->prepare("zip = %s, ", $field_value); break;
				case 'state' 						: $field_sql .= $wpdb->prepare("state = %s, ", $field_value); break;
				case 'country' 						: $field_sql .= $wpdb->prepare("country = %s, ", $field_value); break;
				case 'special_requirements' 		: $field_sql .= $wpdb->prepare("special_requirements = %s, ", $field_value); break;
				case 'other_fields' 				: $field_sql .= $wpdb->prepare("other_fields = %s, ", serialize($field_value)); break;
				case 'extra_items' 					: $field_sql .= $wpdb->prepare("extra_items = %s, ", serialize($field_value)); break;
				case 'total_car_rental_price' 		: $field_sql .= $wpdb->prepare("total_car_rental_price = %f, ", $field_value); break;
				case 'cart_price' 					: $field_sql .= $wpdb->prepare("cart_price = %f, ", $field_value); break;
				case 'total_extra_items_price' 		: $field_sql .= $wpdb->prepare("total_extra_items_price = %f, ", $field_value); break;
				case 'total_price' 					: $field_sql .= $wpdb->prepare("total_price = %f, ", $field_value); break;
				case 'woo_order_id' 				: $field_sql .= $wpdb->prepare("woo_order_id = %d, ", $field_value); break;
				case 'cart_key' 					: $field_sql .= $wpdb->prepare("cart_key = %s, ", $field_value); break;
				case 'woo_status' 					: $field_sql .= $wpdb->prepare("woo_status = %s, ", $field_value); break;
				default : break;
			}
		}

		if (!empty($field_sql)) {

			$field_sql = rtrim($field_sql, ", ");

			$sql .= $field_sql;

			$sql .= $wpdb->prepare(" WHERE Id = %d;", $booking_id);

			$result = $wpdb->query($sql);

		}

		return $result;
	}

	function list_car_rental_bookings($search_term = null, $orderby = 'Id', $order = 'ASC', $paged = null, $per_page = 0, $user_id = 0, $author_id = null, $car_rental_id = null ) {

		global $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;

		$table_name = BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE;
		$sql = "SELECT DISTINCT bookings.*,
				car_rentals.post_title car_rental_name,
				locations.post_title pick_up_title,
				locations2.post_title drop_off_title,
				'car_rental_booking' entry_type
				FROM " . $table_name . " bookings ";

		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('car_rental') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations translations_default ON translations_default.element_type = 'post_car_rental' AND translations_default.language_code='" . BookYourTravel_Theme_Utils::get_default_language() . "' AND translations_default.element_id = bookings.car_rental_id ";
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations translations ON translations.element_type = 'post_car_rental' AND translations.language_code='" . ICL_LANGUAGE_CODE . "' AND translations.trid = translations_default.trid ";
		}

		$sql .= " INNER JOIN $wpdb->posts car_rentals ON ";
		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('car_rental') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " car_rentals.ID = translations.element_id ";
		} else {
			$sql .= " car_rentals.ID = bookings.car_rental_id ";
		}

		$sql .= "LEFT JOIN $wpdb->posts locations ON locations.ID = bookings.car_rental_pick_up_id AND locations.post_status = 'publish'
				LEFT JOIN $wpdb->posts locations2 ON locations2.ID = bookings.car_rental_drop_off_id AND locations2.post_status = 'publish'
				WHERE car_rentals.post_status = 'publish' ";

		if ($search_term != null && !empty($search_term)) {
			$search_term_esc = "%" . $wpdb->esc_like($search_term) . "%";
			$sql .= $wpdb->prepare(" AND (LCASE(bookings.first_name) LIKE '%s' OR LCASE(bookings.last_name) LIKE '%s' OR car_rentals.post_title LIKE '%s') ", $search_term, $search_term, $search_term_esc);
		}

		if (isset($car_rental_id) && $car_rental_id > 0) {
			$sql .= $wpdb->prepare(" AND bookings.car_rental_id = %d ", $car_rental_id) ;
		}

		if ($user_id > 0) {
			$sql .= $wpdb->prepare(" AND bookings.user_id = %d ", $user_id) ;
		}

		if (isset($author_id)) {
			$sql .= $wpdb->prepare(" AND car_rentals.post_author=%d ", $author_id);
		}

		if(!empty($orderby) & !empty($order)) {
			$sql.= ' ORDER BY '.$orderby.' '.$order;
		}

		$sql_count = $sql;

		if(!empty($paged) && !empty($per_page)) {
			$offset=($paged-1)*$per_page;
			$sql .= $wpdb->prepare(" LIMIT %d, %d ", $offset, $per_page);
		}

		$results = array(
			'total' => $wpdb->query($sql_count),
			'results' => $wpdb->get_results($sql)
		);

		return $results;
	}

	function get_car_rental_booking($booking_id) {

		global $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;

		$sql = "SELECT 	DISTINCT bookings.*,
						car_rentals.post_title car_rental_name,
						locations.ID pick_up_location_id,
						locations.post_title pick_up_title,
						locations2.post_title drop_off_title,
						'car_rental_booking' entry_type
				FROM " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE . " bookings ";

		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('car_rental') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations translations_default ON translations_default.element_type = 'post_car_rental' AND translations_default.language_code='" . BookYourTravel_Theme_Utils::get_default_language() . "' AND translations_default.element_id = bookings.car_rental_id ";
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations translations ON translations.element_type = 'post_car_rental' AND translations.language_code='" . ICL_LANGUAGE_CODE . "' AND translations.trid = translations_default.trid ";
		}

		$sql .= " INNER JOIN $wpdb->posts car_rentals ON ";
		if(defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('car_rental') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " car_rentals.ID = translations.element_id ";
		} else {
			$sql .= " car_rentals.ID = bookings.car_rental_id ";
		}

		$sql .= $wpdb->prepare("
				LEFT JOIN $wpdb->posts locations ON locations.ID = bookings.car_rental_pick_up_id AND locations.post_status = 'publish'
				LEFT JOIN $wpdb->posts locations2 ON locations2.ID = bookings.car_rental_drop_off_id AND locations2.post_status = 'publish'
				WHERE car_rentals.post_status = 'publish' AND bookings.Id = %d ", $booking_id);

		return $wpdb->get_row($sql);
	}

	function update_booking_woocommerce_info($booking_id, $cart_key = null, $woo_order_id = null, $woo_status = null) {

		global $wpdb;

		if (isset($cart_key) || isset($woo_order_id) || isset($woo_status)) {
			$sql = "UPDATE " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE . "
					SET ";

			if (isset($cart_key))
				$sql .= $wpdb->prepare("cart_key = %s, ", $cart_key);
			if (isset($woo_order_id))
				$sql .= $wpdb->prepare("woo_order_id = %d, ", $woo_order_id);
			if (isset($woo_status))
				$sql .= $wpdb->prepare("woo_status = %s, ", $woo_status);

			$sql = rtrim($sql, ", ");
			$sql .= $wpdb->prepare(" WHERE Id = %d", $booking_id);

			return $wpdb->query($sql);
		}

		return null;
	}

	function delete_car_rental_booking($booking_id) {

		global $wpdb;

		do_action('bookyourtravel_before_delete_car_rental_booking', $booking_id);

		$booking = $this->get_car_rental_booking($booking_id);
		if ($booking) {
			$car_rental_id = BookYourTravel_Theme_Utils::get_default_language_post_id($booking->car_rental_id, 'car_rental');
			$this->clear_price_meta_cache($car_rental_id);
		}

		$sql = "DELETE FROM " . BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE . "
		WHERE Id = %d";

		$wpdb->query($wpdb->prepare($sql, $booking_id));
	}

	function list_car_rental_availabilities($car_rental_id = 0, $paged = null, $per_page = 0, $orderby = 'Id', $order = 'ASC', $author_id = null) {

		global $wpdb;

		$car_rental_id = BookYourTravel_Theme_Utils::get_default_language_post_id($car_rental_id, 'car_rental');

		$sql = "SELECT DISTINCT availabilities.*, car_rentals.post_title car_rental_name, 'car_rental_availability' entry_type
				FROM " . BOOKYOURTRAVEL_CAR_RENTAL_AVAILABILITIES_TABLE . " availabilities
				INNER JOIN $wpdb->posts car_rentals ON car_rentals.ID = availabilities.car_rental_id
				WHERE 1=1 ";

		if ($car_rental_id > 0) {
			$sql .= $wpdb->prepare(" AND availabilities.car_rental_id=%d ", $car_rental_id);
		}

		if (isset($author_id)) {
			$sql .= $wpdb->prepare(" AND car_rentals.post_author=%d ", $author_id);
		}

		if(!empty($orderby) & !empty($order)) {
			$sql.=' ORDER BY ' . $orderby . ' ' . $order;
		}

		$sql_count = $sql;

		if(!empty($paged) && !empty($per_page)) {
			$offset=($paged-1)*$per_page;
			$sql .= $wpdb->prepare(" LIMIT %d, %d ", $offset, $per_page);
		}

		$results = array(
			'total' => $wpdb->query($sql_count),
			'results' => $wpdb->get_results($sql)
		);

		return $results;
	}

	function create_car_rental_availability($season_name, $car_rental_id, $start_date, $end_date, $number_of_cars, $price_per_day) {
		global $wpdb;

		$car_rental_id = BookYourTravel_Theme_Utils::get_default_language_post_id($car_rental_id, 'car_rental');

		$this->clear_price_meta_cache($car_rental_id);

		$sql = "INSERT INTO " . BOOKYOURTRAVEL_CAR_RENTAL_AVAILABILITIES_TABLE . "
				(season_name, start_date, end_date, car_rental_id, number_of_cars, price_per_day)
				VALUES
				(%s, %s, %s, %d, %d, %f);";

		$wpdb->query($wpdb->prepare($sql, $season_name, $start_date, $end_date, $car_rental_id, $number_of_cars, $price_per_day));

		$availability_id = $wpdb->insert_id;

		return $availability_id;
	}

	function update_car_rental_availability($availability_id, $car_rental_id, $season_name, $start_date, $end_date, $number_of_cars, $price_per_day) {

		global $wpdb;

		$car_rental_id = BookYourTravel_Theme_Utils::get_default_language_post_id($car_rental_id, 'car_rental');

		$this->clear_price_meta_cache($car_rental_id);

		$start_date = date('Y-m-d', strtotime($start_date));
		$end_date = date('Y-m-d', strtotime($end_date));

		$sql = "UPDATE " . BOOKYOURTRAVEL_CAR_RENTAL_AVAILABILITIES_TABLE . "
				SET season_name=%s, start_date=%s, end_date=%s, car_rental_id=%d, number_of_cars=%d, price_per_day=%f
				WHERE Id=%d";

		$wpdb->query($wpdb->prepare($sql, $season_name, $start_date, $end_date, $car_rental_id, $number_of_cars, $price_per_day, $availability_id));

		return $availability_id;
	}

	function delete_car_rental_availability($availability_id) {

		global $wpdb;

		$availability = $this->get_car_rental_availability($availability_id);

		if (isset($availability) && isset($availability->car_rental_id)) {

			$this->clear_price_meta_cache($availability->car_rental_id);

			$sql = "DELETE FROM " . BOOKYOURTRAVEL_CAR_RENTAL_AVAILABILITIES_TABLE . "
					WHERE Id = %d";

			$wpdb->query($wpdb->prepare($sql, $availability_id));
		}
	}

	function get_car_rental_availability($availability_id) {

		global $wpdb;

		$sql = "SELECT availabilities.*, car_rentals.post_title car_rental_name, 'car_rental_availability' entry_type
				FROM " . BOOKYOURTRAVEL_CAR_RENTAL_AVAILABILITIES_TABLE . " availabilities
				INNER JOIN $wpdb->posts car_rentals ON car_rentals.ID = availabilities.car_rental_id
				WHERE availabilities.Id=%d ";

		return $wpdb->get_row($wpdb->prepare($sql, $availability_id));
	}

	function clear_price_meta_cache($car_rental_id) {
		global $wpdb;
		$search_term = "%car_rental_min_price%";
		$sql = $wpdb->prepare("DELETE FROM $wpdb->postmeta WHERE post_id=%d AND meta_key LIKE '%s'", $car_rental_id, $search_term);
		$wpdb->query($sql);

		$car_rental_obj = new BookYourTravel_Car_Rental($car_rental_id);
		$location_ids = $car_rental_obj->get_locations();

		if ($location_ids && count($location_ids) > 0) {
			for ( $i = 0; $i < count($location_ids); $i++ ) {
				$location_id = $location_ids[$i];

				$search_term = "%car_rentals_min_price%";
				$sql = $wpdb->prepare("DELETE FROM $wpdb->postmeta WHERE post_id=%d AND meta_key LIKE '%s'", $location_id, $search_term);
				$wpdb->query($sql);				
			}
		}
	}
}

global $bookyourtravel_car_rental_helper;
// store the instance in a variable to be retrieved later and call init
$bookyourtravel_car_rental_helper = BookYourTravel_Car_Rental_Helper::get_instance();
$bookyourtravel_car_rental_helper->init();

add_shortcode( 'byt_car_rental_card', 'byt_car_rental_card_shortcode');
function byt_car_rental_card_shortcode($atts) {

	global $car_rental_item_args;

	extract(shortcode_atts(array(
	  'car_rental_id' => 0,
	  'show_fields' => 'title,image,actions',
      'css' => ''
	), $atts));

	$show_fields = explode(',', $show_fields);

	$car_rental_item_args = array();
	$car_rental_item_args['car_rental_id'] = $car_rental_id;
	if ($car_rental_id > 0) {
		$car_rental_item_args['post']	= get_post($car_rental_id);
	}
	$car_rental_item_args['hide_title'] = !in_array('title', $show_fields);
	$car_rental_item_args['hide_image'] = !in_array('image', $show_fields);
	$car_rental_item_args['hide_actions'] = !in_array('actions', $show_fields);
	$car_rental_item_args['hide_description'] = !in_array('description', $show_fields);
	$car_rental_item_args['hide_rating'] = !in_array('rating', $show_fields);
	$car_rental_item_args['hide_price'] = !in_array('price', $show_fields);
	$car_rental_item_args['hide_address'] = !in_array('address', $show_fields);
	$car_rental_item_args['item_class'] = 'single-card';

	$output = '';

	ob_start();
	get_template_part('includes/parts/car_rental/car_rental', 'item');

	$css_class = $css;
	if (function_exists('vc_shortcode_custom_css_class')) {
		$css_class = vc_shortcode_custom_css_class( $css, ' ' );
	}

    $output = sprintf('<div class="widget widget-sidebar %s">', $css_class);
    $output .= ob_get_clean();
    $output .= "</div>";

	wp_reset_postdata();
	return $output;
}
