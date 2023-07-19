<?php

/**
 * BookYourTravel_Cruise_Helper class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-cruise.php');

class BookYourTravel_Cruise_Helper extends BookYourTravel_BaseSingleton
{

	private $enable_cruises;
	private $cruise_custom_meta_fields;
	private $cruise_list_custom_meta_fields;
	private $cruise_custom_meta_tabs;
	private $cruise_list_custom_meta_tabs;
	private $cruise_list_meta_box;

	// used by frontend submit {
	private $cruise_schedule_fields;
	private $cruise_booking_fields;
	// }

	protected function __construct()
	{

		global $bookyourtravel_theme_globals;

		$this->enable_cruises = $bookyourtravel_theme_globals->enable_cruises();

		// our parent class might
		// contain shared code in its constructor
		parent::__construct();
	}

	public function init()
	{

		add_action('bookyourtravel_initialize_post_types', array($this, 'initialize_post_type'), 0);

		if ($this->enable_cruises) {

			add_action('bookyourtravel_after_delete_cruise', array($this, 'after_delete_cruise'), 10, 1);
			add_action('bookyourtravel_save_cruise', array($this, 'save_cruise'), 10, 1);
			add_action('admin_init', array($this, 'remove_unnecessary_meta_boxes'));
			add_filter('manage_edit-cruise_columns', array($this, 'manage_edit_cruise_columns'), 10, 1);
			add_action('admin_init', array($this, 'cruise_admin_init'));
			add_action('edited_cruise_type', array($this, 'save_cruise_type_custom_meta'), 10, 2);
			add_action('create_cruise_type', array($this, 'save_cruise_type_custom_meta'), 10, 2);
			add_action('cruise_type_add_form_fields', array($this, 'cruise_type_add_new_meta_fields'), 10, 1);
			add_action('cruise_type_edit_form_fields', array($this, 'cruise_type_edit_meta_fields'), 10, 2);
			add_filter('bookyourtravel_custom_taxonomy_list', array($this, 'custom_taxonomy_list'), 10, 1);

			add_action('bookyourtravel_before_single_cruise_content', array($this, 'before_single_cruise_content'));

			add_action('booking_form_details_cruise_core_fields', array($this, 'booking_form_details_core_fields'));
			add_action('booking_form_confirmation_cruise_core_fields', array($this, 'booking_form_confirmation_core_fields'));
			add_action('booking_form_calendar_cruise_after_calendar_control', array($this, 'booking_form_calendar_after_calendar_control'));
			add_action('booking_form_calendar_cruise_start_summary_control', array($this, 'booking_form_calendar_start_summary_control'));
			add_action('booking_form_calendar_cruise_booking_terms', array($this, 'booking_form_calendar_booking_terms'));

			add_filter('manage_edit-cruise_type_columns', array($this, 'cruise_type_taxonomy_columns'));
			add_filter('manage_cruise_type_custom_column', array($this, 'cruise_type_columns_content'), 10, 3);

			add_action('bookyourtravel_initialize_ajax', array($this, 'initialize_ajax'), 0);

			add_action('booking_form_calendar_cruise_after_price_breakdown', array($this, 'booking_form_calendar_after_price_breakdown'));

			$this->initialize_meta_fields();
		}
	}

	function custom_taxonomy_list($taxonomies)
	{
		if ($this->enable_cruises) {
			$taxonomies[] = "cruise_type";
			$taxonomies[] = "cruise_tag";
		}

		return $taxonomies;
	}

	function get_custom_meta_fields()
	{
		$this->initialize_meta_fields();
		return $this->cruise_custom_meta_fields;
	}

	function get_custom_meta_tabs()
	{
		$this->initialize_meta_fields();
		return $this->cruise_custom_meta_tabs;
	}

	function get_cruise_schedule_fields()
	{
		$this->initialize_meta_fields();
		return $this->cruise_schedule_fields;
	}

	function get_cruise_booking_fields()
	{
		$this->initialize_meta_fields();
		return $this->cruise_booking_fields;
	}

	function initialize_meta_fields()
	{

		global $bookyourtravel_cabin_type_helper, $post, $bookyourtravel_theme_globals;

		$post_id = 0;
		if (isset($post))
			$post_id = $post->ID;
		else if (isset($_GET['post']))
			$post_id = (int)$_GET['post'];

		$cabin_types = array();

		$original_post = $post;

		if ($post_id > 0) {
			$cabin_type_query = $bookyourtravel_cabin_type_helper->list_cabin_types(null, true, $post_id);
			if ($cabin_type_query->have_posts()) {
				while ($cabin_type_query->have_posts()) {
					$cabin_type_query->the_post();
					global $post;
					$cabin_types[] = array('value' => $post->ID, 'label' => $post->post_title);
				}
			}
			wp_reset_postdata();
		}

		if (count($cabin_types) == 0) {
			// if cruise has no associated cabin types, list all possible cabin types (for backwards compatibility)
			$cabin_type_query = $bookyourtravel_cabin_type_helper->list_cabin_types(null, true, null);
			if ($cabin_type_query->have_posts()) {
				while ($cabin_type_query->have_posts()) {
					$cabin_type_query->the_post();
					global $post;
					$cabin_types[] = array('value' => $post->ID, 'label' => $post->post_title);
				}
			}
			wp_reset_postdata();
		}

		$post = $original_post;

		$this->cruise_custom_meta_tabs = array(
			array(
				'label' => esc_html__('General', 'bookyourtravel'),
				'id' => '_cruise_general_tab',
				'class' => 'general_tab'
			),
			array(
				'label' => esc_html__('Booking', 'bookyourtravel'),
				'id' => '_cruise_booking_tab',
				'class' => 'booking_tab'
			),
			array(
				'label' => esc_html__('Gallery', 'bookyourtravel'),
				'id' => '_cruise_gallery_tab',
				'class' => 'gallery_tab'
			),
			array(
				'label' => esc_html__('Content', 'bookyourtravel'),
				'id' => '_cruise_content_tab',
				'class' => 'content_tab'
			)
		);

		$this->cruise_custom_meta_tabs = apply_filters('bookyourtravel_cruise_custom_meta_tabs', $this->cruise_custom_meta_tabs);

		$cruise_feature_displays = array();
		$cruise_feature_displays[] = array('value' => 'gallery', 'label' => esc_html__('Image gallery', 'bookyourtravel'));
		$cruise_feature_displays[] = array('value' => 'image', 'label' => esc_html__('Featured image', 'bookyourtravel'));

		$this->cruise_custom_meta_fields = array(
			array(
				'label'	=> esc_html__('General description', 'bookyourtravel'),
				'desc'	=> esc_html__('General description', 'bookyourtravel'),
				'id'	=> 'cruise_general_description',
				'type'	=> 'editor',
				'admin_tab_id' => 'content_tab'
			),
			array(
				'label'	=> esc_html__('Short description', 'bookyourtravel'),
				'desc'	=> esc_html__('Short description is shown in the right sidebar of a single item and as a description of an item card when the item is displayed in lists', 'bookyourtravel'),
				'id'	=> 'cruise_short_description',
				'type'	=> 'editor',
				'admin_tab_id' => 'content_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Use referral url?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('List on list pages and widgets but link to an external website via referral url.', 'bookyourtravel'), // description
				'id'	=> 'cruise_use_referral_url', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array(
				'label'	=> esc_html__('Referral url', 'bookyourtravel'),
				'desc'	=> esc_html__('Referral url to take visitors to when item is clicked on on list pages and widgets (to use for example for affiliate links).', 'bookyourtravel'),
				'id'	=> 'cruise_referral_url',
				'type'	=> 'text',
				'admin_tab_id' => 'general_tab',
				'field_container_class' => 'referral_url'
			),
			array(
				'label'	=> esc_html__('Referral price', 'bookyourtravel'),
				'desc'	=> esc_html__('Referral price to display for item when item is listed on list pages and widgets.', 'bookyourtravel'),
				'id'	=> 'cruise_referral_price',
				'type'	=> 'text',
				'admin_tab_id' => 'general_tab',
				'field_container_class' => 'referral_url'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Is Featured', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Show in lists where only featured items are shown.', 'bookyourtravel'), // description
				'id'	=> 'cruise_is_featured', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array(
				'label'	=> esc_html__('Ribbon text', 'bookyourtravel'),
				'desc'	=> esc_html__('If specified, this text will appear in a ribbon placed on top of the item in lists when card display mode is used.', 'bookyourtravel'),
				'id'	=> 'cruise_ribbon_text',
				'type'	=> 'text',
				'admin_tab_id' => 'content_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide inquiry form', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Do you want to not show inquiry form for this cruise?', 'bookyourtravel'), // description
				'id'	=> 'cruise_hide_inquiry_form', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array(
				'label'	=> esc_html__('Contact email addresses', 'bookyourtravel'),
				'desc'	=> esc_html__('Override admin contact email address by specifying contact email addresses for this cruise. If specifying multiple email addresses, separate each address with a semi-colon ;', 'bookyourtravel'),
				'id'	=> 'cruise_contact_email',
				'type'	=> 'text',
				'admin_tab_id' => 'general_tab'
			),
			array( // Taxonomy Checkboxes
				'label'	=> esc_html__('Cruise tags', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'cruise_tag', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'tax_checkboxes', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array( // Taxonomy Select box
				'label'	=> esc_html__('Cruise type', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'cruise_type', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'tax_select', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array( // Taxonomy Select box
				'label'	=> esc_html__('Facilities', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'facility', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'tax_checkboxes', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array( // Taxonomy Select box
				'label'	=> esc_html__('Duration', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'cruise_duration', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'tax_checkboxes', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Locations', 'bookyourtravel'), // <label>
				'desc'	=> '', // description
				'id'	=> 'locations', // field id and name
				'type'	=> 'post_checkboxes', // type of field
				'post_type' => array('location'), // post types to display, options are prefixed with their post type
				'admin_tab_id' => 'general_tab'
			),
			array(
				'label'	=> esc_html__('Address', 'bookyourtravel'),
				'desc'	=> esc_html__('Address text is shown below the item title in list pages and widgets', 'bookyourtravel'),
				'id'	=> 'cruise_address',
				'type'	=> 'text',
				'admin_tab_id' => 'general_tab'
			),
			array(
				'label' => esc_html__('Force disable single view calendar?', 'bookyourtravel'), // <label>
				'desc' => esc_html__('If this option is checked, then this cruise will not display a calendar in the availability tab regardless of whether it has valid schedules or not.', 'bookyourtravel'), // description
				'id' => 'cruise_force_disable_calendar', // field id and name
				'type' => 'checkbox', // type of field
				'admin_tab_id' => 'booking_tab',
			),
			array(
				'label'	=> esc_html__('Is for reservation only?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If this option is checked, then this particular cruise will not be processed via WooCommerce even if WooCommerce is in use.', 'bookyourtravel'), // description
				'id'	=> 'cruise_is_reservation_only', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'booking_tab'
			),
			array(
				'label'	=> esc_html__('Cruise duration (days)', 'bookyourtravel'),
				'desc'	=> esc_html__('What is the number of days the cruise lasts for? (Note: field is used to determine cost of extra items if priced per day)', 'bookyourtravel'),
				'id'	=> 'cruise_duration_days',
				'type'	=> 'slider',
				'min'	=> apply_filters('bookyourtravel_cruise_duration_days_min', '1'),
				'max'	=> apply_filters('bookyourtravel_cruise_duration_days_max', '101'),
				'step'	=> '1',
				'admin_tab_id' => 'booking_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Cabin types', 'bookyourtravel'), // <label>
				'desc'	=> '', // description
				'id'	=> 'cabin_types', // field id and name
				'type'	=> 'post_checkboxes', // type of field
				'post_type' => array('cabin_type'), // post types to display, options are prefixed with their post type
				'admin_tab_id' => 'booking_tab'
			),
			array(
				'label'	=> esc_html__('Price per person?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Is price calculated per person (adult, child)? If not then calculations are done per cabin.', 'bookyourtravel'), // description
				'id'	=> 'cruise_is_price_per_person', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'booking_tab'
			),
			array(
				'label'	=> esc_html__('Count children stay free', 'bookyourtravel'),
				'desc'	=> esc_html__('How many kids stay free before we charge a fee?', 'bookyourtravel'),
				'id'	=> 'cruise_count_children_stay_free',
				'type'	=> 'slider',
				'min'	=> '0',
				'max'	=> '5',
				'step'	=> '1',
				'admin_tab_id' => 'booking_tab',
				'field_container_class' => 'per_person'
			),
			array(
				'label'	=> esc_html__('Availability extra text', 'bookyourtravel'),
				'desc'	=> esc_html__('Extra text shown on availability tab above the book now area.', 'bookyourtravel'),
				'id'	=> 'cruise_availability_text',
				'type'	=> 'textarea',
				'admin_tab_id' => 'booking_tab'
			),
			array( // Select box
				'label'	=> esc_html__('Displayed featured element', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'cruise_displayed_featured_element', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'select', // type of field
				'options' => $cruise_feature_displays,
				'std' => 'gallery',
				'admin_tab_id' => 'gallery_tab'
			),
			array( // Repeatable & Sortable Text inputs
				'label'	=> esc_html__('Gallery images', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('A collection of images to be used in slider/gallery on single page', 'bookyourtravel'), // description
				'id'	=> 'cruise_images', // field id and name
				'type'	=> 'repeatable', // type of field
				'sanitizer' => array( // array of sanitizers with matching kets to next array
					'featured' => 'meta_box_santitize_boolean',
					'title' => 'sanitize_text_field',
					'desc' => 'wp_kses_data'
				),
				'repeatable_fields' => array( // array of fields to be repeated
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
			array_unshift($this->cruise_custom_meta_fields, array( // Select box
				'label' => esc_html__('Static "From" price', 'bookyourtravel'), // <label>
				'desc' => esc_html__('This price is shown in grids when the "Show static from prices in grid displays?" in enabled in theme configuration settings', 'bookyourtravel'), // description
				'id' => 'cruise_static_from_price', // field id and name, needs to be the exact name of the taxonomy
				'type' => 'text', // type of field
				'std' => '0',
				'admin_tab_id' => 'booking_tab'
			));
		}

		if ($bookyourtravel_theme_globals->enable_deposit_payments()) {
			array_unshift($this->cruise_custom_meta_fields, array( // Select box
				'label' => esc_html__('Deposit percentage', 'bookyourtravel'), // <label>
				'desc' => esc_html__('% deposit charge', 'bookyourtravel'), // description
				'id' => 'cruise_deposit_percentage', // field id and name, needs to be the exact name of the taxonomy
				'type' => 'number', // type of field
				'std' => '100',
				'min' => '0',
				'max' => '100',
				'step' => '1',
				'admin_tab_id' => 'booking_tab',
				'field_container_class' => 'deposit_percentage'
			));
		}

		global $default_cruise_extra_fields;

		$cruise_extra_fields = of_get_option('cruise_extra_fields');
		if (!is_array($cruise_extra_fields) || count($cruise_extra_fields) == 0)
			$cruise_extra_fields = $default_cruise_extra_fields;
		else
			$cruise_extra_fields = BookYourTravel_Theme_Of_Default_Fields::merge_fields_and_defaults($cruise_extra_fields, $default_cruise_extra_fields);

		foreach ($cruise_extra_fields as $cruise_extra_field) {
			$field_is_hidden = isset($cruise_extra_field['hide']) ? intval($cruise_extra_field['hide']) : 0;

			if (!$field_is_hidden) {
				$extra_field = null;
				$field_label = isset($cruise_extra_field['label']) ? $cruise_extra_field['label'] : '';
				$field_id = isset($cruise_extra_field['id']) ? $cruise_extra_field['id'] : '';
				$field_type = isset($cruise_extra_field['type']) ? $cruise_extra_field['type'] :  '';
				$field_desc = isset($cruise_extra_field['desc']) ? $cruise_extra_field['desc'] :  '';

				$field_options_array = null;
				if (isset($cruise_extra_field['options'])) {
					if (is_array($cruise_extra_field['options'])) {
						$field_options_array = $cruise_extra_field['options'];
					} else {
						$field_options = isset($cruise_extra_field['options']) ? trim($cruise_extra_field['options']) :  '';
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
					$min = isset($cruise_extra_field['min']) && strlen($cruise_extra_field['min']) > 0 ? intval($cruise_extra_field['min']) :  1;
					$max = isset($cruise_extra_field['max']) && strlen($cruise_extra_field['max']) > 0 ? intval($cruise_extra_field['max']) :  10;
					$step = isset($cruise_extra_field['step']) && strlen($cruise_extra_field['step']) > 0 ? intval($cruise_extra_field['step']) :  1;
				}

				if (!empty($field_label) && !empty($field_id) && !empty($field_type)) {
					$extra_field = array(
						'label'	=> $field_label,
						'desc'	=> $field_desc,
						'id'	=> 'cruise_' . $field_id,
						'type'	=> $field_type,
						'admin_tab_id' => 'content_tab',
						'options' => $field_options_array,
						'min' => $min,
						'max' => $max,
						'step' => $step,
					);
				}

				if ($extra_field)
					$this->cruise_custom_meta_fields[] = $extra_field;
			}
		}

		$this->cruise_custom_meta_fields = apply_filters('bookyourtravel_cruise_custom_meta_fields', $this->cruise_custom_meta_fields);


		$this->cruise_schedule_fields = array(
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
				'field_container_class' => 'datepicker-wrap is_repeated',
				'field_override_class' => '',
				'show_in_fs_list' => true
			),
			array(
				'label' => esc_html__('Cruise', 'bookyourtravel'),
				'id' => 'cruise_id',
				'type' => 'post_select',
				'post_type' => 'cruise',
				'field_override_class' => 'required',
				'show_in_fs_list' => true
			),
			array(
				'label' => esc_html__('Cabin type', 'bookyourtravel'),
				'id' => 'cabin_type_id',
				'type' => 'post_select',
				'post_type' => 'cabin_type',
				'field_override_class' => 'required',
				'show_in_fs_list' => true
			),
			array(
				'label' => esc_html__('Cabin count', 'bookyourtravel'),
				'id' => 'cabin_count',
				'type' => 'slider',
				'min' => '1',
				'max' => '100',
				'step' => '1',
				'field_override_class' => 'required',
				'show_in_fs_list' => true
			),
			array(
				'label' => esc_html__('Price', 'bookyourtravel'),
				'id' => 'price',
				'type' => 'number',
				'field_override_class' => 'required',
				'show_in_fs_list' => true
			),
			array(
				'label' => esc_html__('Price (child)', 'bookyourtravel'),
				'desc' => esc_html__('Price per child (leave blank if not different to regular price)', 'bookyourtravel'),
				'id' => 'price_child',
				'type' => 'number',
				'field_container_class' => 'per_person',
			)
		);

		$this->cruise_schedule_fields = apply_filters('bookyourtravel_cruise_schedule_fields', $this->cruise_schedule_fields);

		$this->cruise_booking_fields = array(
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
				'label' => esc_html__('Cruise date', 'bookyourtravel'),
				'id' => 'cruise_date',
				'type' => 'datepicker',
				'field_container_class' => 'datepicker-wrap',
				'field_override_class' => 'required',
				'show_in_fs_list' => true
			),
			array(
				'label' => esc_html__('Cruise', 'bookyourtravel'),
				'id' => 'cruise_id',
				'type' => 'post_select',
				'post_type' => 'cruise',
				'field_override_class' => 'required',
				'show_in_fs_list' => true
			),
			array(
				'label' => esc_html__('Cabin type', 'bookyourtravel'),
				'id' => 'cabin_type_id',
				'type' => 'post_select',
				'post_type' => 'cabin_type',
				'field_override_class' => 'required',
				'show_in_fs_list' => true
			),
			array(
				'label' => esc_html__('Adults', 'bookyourtravel'),
				'id' => 'adults',
				'type' => 'slider',
				'min' => '1',
				'max' => '100',
				'step' => '1',
				'field_override_class' => 'required'
			),
			array(
				'label' => esc_html__('Children', 'bookyourtravel'),
				'id' => 'children',
				'type' => 'slider',
				'min' => '0',
				'max' => '100',
				'step' => '1',
				'field_override_class' => ''
			),
			array(
				'label' => esc_html__('Total cruise price', 'bookyourtravel'),
				'id' => 'total_cruise_price',
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
			$this->cruise_booking_fields[] = array(
				'label' => esc_html__('Deposit amount', 'bookyourtravel'),
				'id' => 'cart_price',
				'type' => 'number',
				'field_override_class' => 'required',
				'show_in_fs_list' => true
			);
		}

		$this->cruise_booking_fields = apply_filters('bookyourtravel_cruise_booking_fields', $this->cruise_booking_fields);


		$sort_by_columns = array();
		$sort_by_columns[] = array('value' => 'title', 'label' => esc_html__('Cruise title', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'ID', 'label' => esc_html__('Cruise ID', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'date', 'label' => esc_html__('Publish date', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'rand', 'label' => esc_html__('Random', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'comment_count', 'label' => esc_html__('Comment count', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'price', 'label' => esc_html__('Price', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'menu_order', 'label' => esc_html__('Order attribute', 'bookyourtravel'));

		$sort_by_columns = apply_filters('bookyourtravel_cruise_list_sort_by_columns', $sort_by_columns);

		$this->cruise_list_custom_meta_tabs = array(
			array(
				'label' => esc_html__('Content', 'bookyourtravel'),
				'id' => '_cruise_list_filter_tab',
				'class' => 'filter_tab'
			),
			array(
				'label' => esc_html__('Display settings', 'bookyourtravel'),
				'id' => '_cruise_list_item_settings_tab',
				'class' => 'item_settings_tab'
			)
		);

		$this->cruise_list_custom_meta_fields = array(
			array( // Taxonomy Select box
				'label'	=> esc_html__('Cruise type', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'cruise_type', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'tax_checkboxes', // type of field
				'admin_tab_id' => 'filter_tab'
			),
			array( // Taxonomy Select box
				'label'	=> esc_html__('Cruise duration', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'cruise_duration', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'tax_checkboxes', // type of field
				'admin_tab_id' => 'filter_tab'
			),
			array( // Taxonomy Select box
				'label'	=> esc_html__('Cruise tags', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'cruise_tag', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'tax_checkboxes', // type of field
				'admin_tab_id' => 'filter_tab'
			),
			array( // Taxonomy Select box
				'label'	=> esc_html__('Location', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'cruise_list_location_post_id', // field id and name
				'type'	=> 'post_select', // type of field
				'post_type' => array('location'), // post types to display, options are prefixed with their post type
				'admin_tab_id' => 'filter_tab'
			),
			array( // Select box
				'label'	=> esc_html__('Sort by field', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'cruise_list_sort_by', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'select', // type of field
				'options' => $sort_by_columns,
				'admin_tab_id' => 'filter_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Sort descending?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will sort cruises in descending order', 'bookyourtravel'), // description
				'id'	=> 'cruise_list_sort_descending', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'filter_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Show featured only?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will list featured cruises only', 'bookyourtravel'), // description
				'id'	=> 'cruise_list_show_featured_only', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'filter_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Items per page', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('How many items do you want to show per page?', 'bookyourtravel'), // description
				'id'	=> 'cruise_list_posts_per_page', // field id and name
				'std'	=> '12',
				'type'	=> 'slider',
				'min'	=> apply_filters('bookyourtravel_cruise_list_posts_per_page_min', '1'),
				'max'	=> apply_filters('bookyourtravel_cruise_list_posts_per_page_max', '50'),
				'step'	=> '1',
				'admin_tab_id' => 'filter_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Items per row', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('How many items do you want to show per row?', 'bookyourtravel'), // description
				'id'	=> 'cruise_list_posts_per_row', // field id and name
				'std'	=> '4',
				'type'	=> 'slider',
				'min'	=> apply_filters('bookyourtravel_cruise_list_posts_per_row_min', '1'),
				'max'	=> apply_filters('bookyourtravel_cruise_list_posts_per_row_max', '5'),
				'step'	=> '1',
				'admin_tab_id' => 'filter_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide item titles?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide titles of listed cruises', 'bookyourtravel'), // description
				'id'	=> 'cruise_list_hide_item_titles', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'item_settings_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide item images?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide images of listed cruises', 'bookyourtravel'), // description
				'id'	=> 'cruise_list_hide_item_images', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'item_settings_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide item descriptions?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide descriptions of listed cruises', 'bookyourtravel'), // description
				'id'	=> 'cruise_list_hide_item_descriptions', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'item_settings_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide buttons?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide buttons of listed cruises', 'bookyourtravel'), // description
				'id'	=> 'cruise_list_hide_item_actions', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'item_settings_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide price?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide price of listed cruises', 'bookyourtravel'), // description
				'id'	=> 'cruise_list_hide_item_prices', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'item_settings_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide item address?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide address of listed cruises', 'bookyourtravel'), // description
				'id'	=> 'cruise_list_hide_item_address', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'item_settings_tab'
			)
		);

		global $bookyourtravel_theme_globals;
		if ($bookyourtravel_theme_globals->enable_reviews()) {
			$this->cruise_list_custom_meta_fields[] = array( // Post ID select box
				'label'	=> esc_html__('Hide item rating?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide rating of listed cruises', 'bookyourtravel'), // description
				'id'	=> 'cruise_list_hide_item_rating', // field id and name
				'type'	=> 'checkbox', // type of field
			);
		}
	}

	function cruise_type_columns_content($content, $column_name, $term_id)
	{
		if ('is_repeated' == $column_name) {
			$term_meta = get_option("taxonomy_$term_id");
			$content = is_array($term_meta) && isset($term_meta['cruise_type_is_repeated']) ? intval($term_meta['cruise_type_is_repeated']) : 0;

			switch ($content) {
				case 0:
					$content = __('One off', 'bookyourtravel');
					break;
				case 1:
					$content = __('Daily', 'bookyourtravel');
					break;
				case 2:
					$content = __('Weekdays', 'bookyourtravel');
					break;
				case 3:
					$content = __('Weekly', 'bookyourtravel');
					break;
				case 4:
					$content = __('Weekly, multidays', 'bookyourtravel');
					break;
				default:
					$content = __('One off', 'bookyourtravel');
					break;
			}
		}
		return $content;
	}

	function cruise_type_taxonomy_columns($columns)
	{
		$columns['is_repeated'] = __('Is Repeated', 'bookyourtravel');

		return $columns;
	}

	function before_single_cruise_content()
	{
		global $post, $entity_obj, $bookyourtravel_theme_globals;

		if ($post && $post->post_type == 'cruise') {
			$cruise_obj = new BookYourTravel_Cruise($post);
			$entity_obj = $cruise_obj;

			$cruise_is_reservation_only = (int)$cruise_obj->get_is_reservation_only();
			$use_woocommerce_for_checkout = $bookyourtravel_theme_globals->use_woocommerce_for_checkout();

			if ($bookyourtravel_theme_globals->enable_reviews()) {
				get_template_part('includes/parts/review/review', 'form');
			}

			get_template_part('includes/parts/inquiry/inquiry', 'form');

			if ($cruise_is_reservation_only || !BookYourTravel_Theme_Utils::is_woocommerce_active() || !$use_woocommerce_for_checkout) {
				get_template_part('includes/parts/booking/form', 'details');
				get_template_part('includes/parts/booking/form', 'confirmation');
			}
		}
	}

	function initialize_ajax()
	{

		add_action('wp_ajax_cruise_process_booking_ajax_request', array($this, 'process_booking_ajax_request'));
		add_action('wp_ajax_nopriv_cruise_process_booking_ajax_request', array($this, 'process_booking_ajax_request'));

		add_action('wp_ajax_frontend_delete_cruise_schedule_ajax_request', array($this, 'frontend_delete_cruise_schedule_ajax_request'));

		add_action('wp_ajax_nopriv_cruise_get_fields_ajax_request', array($this, 'cruise_get_fields_ajax_request'));
		add_action('wp_ajax_cruise_get_fields_ajax_request', array($this, 'cruise_get_fields_ajax_request'));

		add_action('byt_ajax_handler_cruise_available_dates_ajax_request', array($this, 'get_available_dates_json'));
		add_action('byt_ajax_handler_nopriv_cruise_available_dates_ajax_request', array($this, 'get_available_dates_json'));
		add_action('byt_ajax_handler_cruise_get_day_price_ajax_request', array($this, 'get_prices_json'));
		add_action('byt_ajax_handler_nopriv_cruise_get_day_price_ajax_request', array($this, 'get_prices_json'));
		add_action('byt_ajax_handler_nopriv_cruise_load_min_price_ajax_request', array($this, 'get_min_price_json'));
		add_action('byt_ajax_handler_cruise_load_min_price_ajax_request', array($this, 'get_min_price_json'));

		add_action('wp_ajax_cruise_available_dates_ajax_request', array($this, 'get_available_dates_json'));
		add_action('wp_ajax_nopriv_cruise_available_dates_ajax_request', array($this, 'get_available_dates_json'));
		add_action('wp_ajax_cruise_get_day_price_ajax_request', array($this, 'get_prices_json'));
		add_action('wp_ajax_nopriv_cruise_get_day_price_ajax_request', array($this, 'get_prices_json'));
		add_action('wp_ajax_nopriv_cruise_load_min_price_ajax_request', array($this, 'get_min_price_json'));
		add_action('wp_ajax_cruise_load_min_price_ajax_request', array($this, 'get_min_price_json'));
	}

	function booking_form_calendar_booking_terms()
	{
		get_template_part('includes/parts/cruise/single/booking-form-calendar', 'booking-terms');
	}

	function booking_form_calendar_start_summary_control()
	{
		get_template_part('includes/parts/cruise/single/booking-form-calendar', 'summary-fields');
	}

	function booking_form_calendar_after_calendar_control()
	{
		get_template_part('includes/parts/cruise/single/booking-form-calendar', 'fields');
	}

	function booking_form_confirmation_core_fields()
	{
		get_template_part('includes/parts/cruise/single/booking-form-confirmation', 'core-fields');
	}

	function booking_form_details_core_fields()
	{
		get_template_part('includes/parts/cruise/single/booking-form-details', 'core-fields');
	}

	function get_min_price_json()
	{
		if (isset($_REQUEST)) {

			$nonce = wp_kses($_REQUEST['nonce'], array());

			if (wp_verify_nonce($nonce, 'bookyourtravel_nonce')) {
				$cruise_id = isset($_REQUEST['cruise_id']) ? intval(wp_kses($_REQUEST['cruise_id'], array())) : 0;
				$cabin_type_id = isset($_REQUEST['cabin_type_id']) ? intval(wp_kses($_REQUEST['cabin_type_id'], array())) : 0;
				$start_date = isset($_REQUEST['start_date']) ? wp_kses($_REQUEST['start_date'], array()) : null;
				$end_date = isset($_REQUEST['end_date']) ? wp_kses($_REQUEST['end_date'], array()) : null;

				$price = $this->get_min_future_price($cruise_id, $cabin_type_id, $start_date, $end_date, true);

				$price = BookYourTravel_Theme_Utils::get_price_in_current_currency($price);

				if ($price > 0) {
					echo json_encode($price);
				}
			}
		}

		die();
	}

	function get_min_future_price($_cruise_id, $_cabin_type_id, $start_date = null, $end_date = null, $skip_cache = false)
	{
		global $wpdb, $bookyourtravel_theme_globals;

		$cruise_id = BookYourTravel_Theme_Utils::get_default_language_post_id($_cruise_id, 'cruise');
		if ($cabin_type_id > 0) {
			$cabin_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($_cabin_type_id, 'cabin_type');
		}

		$cruise_obj = new BookYourTravel_Cruise($cruise_id);

		// 0 - one off
		// 1 - daily
		// 2 - weekday
		// 3 - weekly
		// 4 - weekly, multidays
		$cruise_type_is_repeated = $cruise_obj->get_type_is_repeated();

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

		$min_price_meta_key = BookYourTravel_Theme_Utils::build_min_price_meta_key("cruise", $start_date, $end_date);
		$min_price_check_meta_key = BookYourTravel_Theme_Utils::build_min_price_check_meta_key("cruise", $start_date, $end_date);

		$cruise_obj = new BookYourTravel_Cruise($cruise_id);
		$min_price = $cruise_obj->_get_cached_price($min_price_meta_key, $min_price_check_meta_key);

		if ($cruise_type_is_repeated > 0) {
			$date_range_match = ' possible_dates.the_date >= DATE(availabilities.start_date) AND possible_dates.the_date <= DATE(availabilities.end_date) ';
		} else {
			$date_range_match = ' possible_dates.the_date = DATE(availabilities.start_date) ';
		}

		$is_reservation_only = $cruise_obj->get_is_reservation_only();

		if ($min_price == 0 || $skip_cache) {

			$use_referral_url = $cruise_obj->use_referral_url();
			$referral_url = $cruise_obj->get_referral_url();
			if ($use_referral_url && !empty($referral_url)) {
				$min_price = $cruise_obj->get_referral_price();
			} else {

				$sql = "
				SELECT IFNULL(MIN(price), 0) min_price
				FROM
				(
					SELECT DISTINCT (avc.number_of_available_cabins - IFNULL(bc.number_of_booked_cabins, 0)) available_cabins, avc.the_date, avc.price
					FROM
					(
						SELECT SUM(cabin_count) number_of_available_cabins, possible_dates.the_date, price, cabin_type_id
						FROM " . BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE . " availabilities
						INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON " . $date_range_match . "
						WHERE availabilities.cruise_id = %d AND price > 0 ";

				if ($cabin_type_id > 0) {
					$sql .= $wpdb->prepare(" AND availabilities.cabin_type_id = %d ", $cabin_type_id);
				}

				$sql .= " GROUP BY possible_dates.the_date, availabilities.price
					) as avc
					LEFT JOIN
					(
						SELECT SUM(bookings.cabin_count) number_of_booked_cabins, possible_dates.the_date booking_date, cabin_type_id
						FROM " . BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE . " bookings
						INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date = DATE(bookings.cruise_date)
						WHERE bookings.cruise_id = %d ";

				if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$is_reservation_only) {
					$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
					if (!empty($completed_statuses)) {
						$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ") ";
					}
				}

				if ($cabin_type_id > 0) {
					$sql .= $wpdb->prepare(" AND bookings.cabin_type_id = %d ", $cabin_type_id);
				}

				$sql .= " GROUP BY possible_dates.the_date, bookings.cabin_type_id
					) as bc
					ON bc.booking_date = avc.the_date AND bc.cabin_type_id=avc.cabin_type_id
					HAVING available_cabins > 0
				) as pr ";

				$sql = $wpdb->prepare($sql, $start_date, $start_date, $end_date, $cruise_id, $start_date, $start_date, $end_date, $cruise_id);

				$min_price = $wpdb->get_var($sql);
			}
		}

		update_post_meta($cruise_id, $min_price_meta_key, $min_price);
		update_post_meta($cruise_id, $min_price_check_meta_key, time());
		update_post_meta($_cruise_id, $min_price_meta_key, $min_price);
		update_post_meta($_cruise_id, $min_price_check_meta_key, time());

		return $min_price;
	}


	function get_min_future_date($cruise_id)
	{
		global $wpdb, $bookyourtravel_theme_globals;

		$cruise_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cruise_id, 'cruise');

		$cruise_obj = new BookYourTravel_Cruise($cruise_id);

		// 0 - one off
		// 1 - daily
		// 2 - weekday
		// 3 - weekly
		// 4 - weekly, multidays
		$cruise_type_is_repeated = $cruise_obj->get_type_is_repeated();

		$is_reservation_only = (int)$cruise_obj->get_is_reservation_only();

		$start_date = date("Y-m-d", time());
		$end_date = date('Y-m-d', strtotime($start_date . " +50 months"));

		if ($cruise_type_is_repeated > 0) {
			$date_range_match = ' possible_dates.the_date >= DATE(availabilities.start_date) AND possible_dates.the_date <= DATE(availabilities.end_date) ';
		} else {
			$date_range_match = ' possible_dates.the_date = DATE(availabilities.start_date) ';
		}

		$sql = "
		SELECT IFNULL(MIN(the_date), 0) min_date
		FROM
		(
			SELECT DISTINCT (avc.number_of_available_cabins - IFNULL(bc.number_of_booked_cabins, 0)) available_cabins, avc.the_date, avc.price FROM
			(
				SELECT SUM(cabin_count) number_of_available_cabins, possible_dates.the_date, price, cabin_type_id
				FROM " . BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE . " availabilities
				INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON " . $date_range_match . "
				WHERE availabilities.cruise_id = %d ";

		$sql .= " GROUP BY possible_dates.the_date, availabilities.price
			) as avc
			LEFT JOIN
			(
				SELECT SUM(bookings.cabin_count) number_of_booked_cabins, possible_dates.the_date booking_date, cabin_type_id
				FROM " . BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE . " bookings
				INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date = DATE(bookings.cruise_date)
				WHERE bookings.cruise_id = %d ";

		if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$is_reservation_only) {
			$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
			if (!empty($completed_statuses)) {
				$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ") ";
			}
		}

		$sql .= " GROUP BY possible_dates.the_date
			) as bc
			ON bc.booking_date = avc.the_date AND bc.cabin_type_id=avc.cabin_type_id
			HAVING available_cabins > 0
		) as pr ";

		$sql = $wpdb->prepare($sql, $start_date, $start_date, $end_date, $cruise_id, $start_date, $start_date, $end_date, $cruise_id);

		$min_date = $wpdb->get_var($sql);

		return $min_date;
	}

	function get_min_static_from_price_by_location($_location_id)
	{
		$min_price = 0;

		global $wpdb, $bookyourtravel_theme_globals;

		$location_id = BookYourTravel_Theme_Utils::get_default_language_post_id($_location_id, 'location');
		$location_obj = new BookYourTravel_Location($location_id);

		$min_price_meta_key = BookYourTravel_Theme_Utils::build_min_price_meta_key("cruises");
		$min_price_check_meta_key = BookYourTravel_Theme_Utils::build_min_price_check_meta_key("cruises");
		$min_price = $location_obj->_get_cached_price($min_price_meta_key, $min_price_check_meta_key);

		if ($min_price == 0 || $skip_cache) {
			$post_ids = $location_obj->get_cruise_ids();

			if (count($post_ids) > 0) {
				delete_post_meta($location_id, $min_price_meta_key);

				$post_ids = array_map(function ($v) {
					return "'" . esc_sql($v) . "'";
				}, $post_ids);
				$post_ids_str = implode(',', $post_ids);

				$sql = "SELECT IFNULL(MIN(meta_value), 0) min_price 
                    FROM $wpdb->postmeta as meta
                    WHERE meta_key='cruise_static_from_price' AND post_id IN ($post_ids_str) ";

				$min_price = $wpdb->get_var($sql);

				update_post_meta($location_id, $min_price_meta_key, $min_price);
				update_post_meta($location_id, $min_price_check_meta_key, time());
				update_post_meta($_location_id, $min_price_meta_key, $min_price);
				update_post_meta($_location_id, $min_price_check_meta_key, time());
			}
		}

		return $min_price;
	}

	function get_min_future_price_by_location($_location_id, $start_date = null, $end_date = null, $skip_cache = false)
	{
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

		$min_price_meta_key = BookYourTravel_Theme_Utils::build_min_price_meta_key("cruises", $start_date, $end_date);
		$min_price_check_meta_key = BookYourTravel_Theme_Utils::build_min_price_check_meta_key("cruises", $start_date, $end_date);
		$min_price = $location_obj->_get_cached_price($min_price_meta_key, $min_price_check_meta_key);

		$date_range_match = ' ((possible_dates.the_date >= DATE(availabilities.start_date) AND possible_dates.the_date <= DATE(availabilities.end_date)) OR ';
		$date_range_match .= ' (possible_dates.the_date = DATE(availabilities.start_date) AND availabilities.end_date IS NULL)) ';

		if (!$min_price || $skip_cache) {

			$cruise_ids = $location_obj->get_cruise_ids();

			if (count($cruise_ids) > 0) {
				$cruise_ids = array_map(function ($v) {
					return "'" . esc_sql($v) . "'";
				}, $cruise_ids);
				$cruise_ids_str = implode(',', $cruise_ids);

				$sql = "
                SELECT IFNULL(MIN(price), 0) min_price
                FROM
                (
                    SELECT DISTINCT (avc.number_of_available_cabins - IFNULL(bc.number_of_booked_cabins, 0)) available_cabins, avc.the_date, avc.price
                    FROM
                    (
                        SELECT SUM(cabin_count) number_of_available_cabins, possible_dates.the_date, price, cruise_id
                        FROM " . BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE . " availabilities
                        INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON " . $date_range_match . "
                        WHERE availabilities.cruise_id IN ($cruise_ids_str) ";

				$sql .= " GROUP BY possible_dates.the_date, availabilities.price
                    ) as avc
                    LEFT JOIN
                    (
                        SELECT SUM(bookings.cabin_count) number_of_booked_cabins, possible_dates.the_date booking_date, cruise_id
                        FROM " . BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE . " bookings
                        INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date = DATE(bookings.cruise_date)
                        WHERE bookings.cruise_id IN ($cruise_ids_str)
				";

				if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout()) {
					$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
					if (!empty($completed_statuses)) {
						$sql .= " AND IFNULL(woo_status, '') IN (" . $completed_statuses . ")";
					}
				}

				$sql .= " GROUP BY possible_dates.the_date
                    ) as bc
                    ON bc.booking_date = avc.the_date AND bc.cruise_id = avc.cruise_id
                    HAVING available_cabins > 0
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

	function get_prices_json()
	{
		global $bookyourtravel_theme_globals;

		if (isset($_REQUEST)) {

			$nonce = wp_kses($_REQUEST['nonce'], array());

			if (wp_verify_nonce($nonce, 'bookyourtravel_nonce')) {
				$cruise_id = isset($_REQUEST['cruise_id']) ? intval(wp_kses($_REQUEST['cruise_id'], array())) : 0;
				$cabin_type_id = isset($_REQUEST['cabin_type_id']) ? intval(wp_kses($_REQUEST['cabin_type_id'], array())) : 0;
				$search_date = isset($_REQUEST['the_date']) ? wp_kses($_REQUEST['the_date'], array()) : 0;
				$prices = $this->get_prices($search_date, $cruise_id, $cabin_type_id);

				if (isset($prices->regular_price)) {
					$prices->regular_price = BookYourTravel_Theme_Utils::get_price_in_current_currency($prices->regular_price);
					$prices->regular_price_child = BookYourTravel_Theme_Utils::get_price_in_current_currency($prices->regular_price_child);
				}

				echo json_encode($prices);
			}
		}

		die();
	}

	function get_prices($search_date, $cruise_id, $cabin_type_id = 0, $current_booking_id = 0)
	{

		global $wpdb, $bookyourtravel_theme_globals;
		$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();

		$cruise_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cruise_id, 'cruise');
		$cabin_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cabin_type_id, 'cabin_type');

		$cruise_obj = new BookYourTravel_Cruise($cruise_id);
		$is_reservation_only = $cruise_obj->get_is_reservation_only();

		// 0 - one off
		// 1 - daily
		// 2 - weekday
		// 3 - weekly
		// 4 - weekly, multidays
		$cruise_type_is_repeated = $cruise_obj->get_type_is_repeated();

		$search_date = date('Y-m-d', strtotime($search_date));

		$sql = "SELECT a.availability_id, a.price regular_price, a.price_child regular_price_child, a.cabin_count, a.booked_cabins,
				(@runtot := @runtot + a.cabin_count) AS running_available_total
				FROM
				(
					SELECT availables.*, IFNULL(SUM(bookings.cabin_count), 0) booked_cabins
					FROM
					(
					SELECT availables_inner.*, date_format(DATE(availables_inner.single_date), '%Y-%m-%d 12:00:01') as bookable_single_date ";

		$sql .= $wpdb->prepare("FROM
						(
							SELECT schedules.Id availability_id, %s single_date, schedules.price, schedules.price_child, schedules.cabin_count
							FROM " . BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE . " schedules
							WHERE 1=1 ", $search_date);

		if ($cruise_type_is_repeated == 0) {
			$sql .= $wpdb->prepare(" AND %s = schedules.start_date AND schedules.end_date IS NULL ", $search_date);
		} else {
			$sql .= $wpdb->prepare(" AND %s >= schedules.start_date AND %s <= schedules.end_date ", $search_date, $search_date);
		}

		$sql .= $wpdb->prepare(" AND schedules.cruise_id = %d ", $cruise_id);

		if ($cabin_type_id > 0)
			$sql .= $wpdb->prepare(" AND schedules.cabin_type_id = %d ", $cabin_type_id);

		$sql .= $wpdb->prepare("
							GROUP BY availability_id
						) availables_inner
					) availables
					LEFT JOIN " . BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE . " bookings ON DATE(availables.bookable_single_date) = DATE(bookings.cruise_date)
					AND bookings.cruise_id = %d ", $cruise_id);

		if ($cabin_type_id > 0) {
			$sql .= $wpdb->prepare(" AND bookings.cabin_type_id = %d ", $cabin_type_id);
		}

		if ($current_booking_id > 0) {
			$sql .= $wpdb->prepare(" AND bookings.Id <> %d ", $current_booking_id);
		}

		if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$is_reservation_only) {

			$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
			if (!empty($completed_statuses)) {
				$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")";
			}
		}

		$sql .=		" GROUP BY availables.availability_id
					ORDER BY availables.price ASC
				) a, (SELECT @runtot:=0) AS n
				GROUP BY a.availability_id
				HAVING running_available_total > booked_cabins
				ORDER BY price ASC
                LIMIT 1 ";

		$result = $wpdb->get_row($sql);

		return $result;
	}

	function get_available_dates_json()
	{
		global $bookyourtravel_theme_globals;

		$available_dates = [];

		if (isset($_REQUEST)) {

			$nonce = wp_kses($_REQUEST['nonce'], array());

			if (wp_verify_nonce($nonce, 'bookyourtravel_nonce')) {
				$cruise_id = isset($_REQUEST['cruise_id']) ? intval(wp_kses($_REQUEST['cruise_id'], array())) : 0;
				$cabin_type_id = isset($_REQUEST['cabin_type_id']) ? intval(wp_kses($_REQUEST['cabin_type_id'], array())) : 0;
				$month = isset($_REQUEST['month']) ? intval(wp_kses($_REQUEST['month'], array())) : 0;
				$year = isset($_REQUEST['year']) ? intval(wp_kses($_REQUEST['year'], array())) : 0;
				$start_date = isset($_REQUEST['start_date']) ? wp_kses($_REQUEST['start_date'], array()) : null;
				$month_range = isset($_REQUEST['month_range']) ? intval(wp_kses($_REQUEST['month_range'], array())) : 4;
				$cabins = isset($_REQUEST['cabins']) ? intval(wp_kses($_REQUEST['cabins'], array())) : 1;

				if ($cruise_id > 0 && $cabin_type_id > 0) {
					$available_dates = $this->list_available_dates($cruise_id, $cabin_type_id, $start_date, $month, $year, $month_range, $cabins);
				}
			}
		}

		echo json_encode($available_dates);

		die();
	}

	function list_available_dates($cruise_id, $cabin_type_id, $start_date, $month, $year, $month_range, $cabins)
	{

		global $wpdb, $bookyourtravel_theme_globals;

		$cruise_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cruise_id, 'cruise');
		$cabin_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cabin_type_id, 'cabin_type');

		$cruise_obj = new BookYourTravel_Cruise($cruise_id);
		$cruise_is_reservation_only = $cruise_obj->get_is_reservation_only();

		// 0 - one off
		// 1 - daily
		// 2 - weekday
		// 3 - weekly
		// 4 - weekly, multidays
		$cruise_type_is_repeated = $cruise_obj->get_type_is_repeated();

		$available_dates = array();

		$start_date = date("Y-m-d", strtotime($start_date));
		$start_date_ym = sprintf("%d-%d-%d", $year, $month, 1);
		$start_date_ym = date("Y-m-d", strtotime($start_date_ym));

		if ($start_date < $start_date_ym) {
			$start_date = $start_date_ym;
		}

		$date_range_query = DISTICT_DATE_RANGE_QUERY;

		$end_date = date("Y-m-t", strtotime($start_date)); // last day of end date month
		$end_date = date('Y-m-d', strtotime(sprintf("+%d months", $month_range + 12), strtotime($end_date)));

		$date_range_match = ' possible_dates.the_date >= DATE(availabilities.start_date) AND possible_dates.the_date <= DATE(availabilities.end_date) ';

		if ($cruise_type_is_repeated == 2) {
			$date_range_query = str_replace("order by d.the_date", "HAVING WEEKDAY(d.the_date) BETWEEN 0 AND 4 order by d.the_date ", $date_range_query);
		} else if ($cruise_type_is_repeated == 3) {
			$cruise_type_days_of_week = $cruise_obj->get_type_day_of_week_indexes();

			$cruise_type_day_of_week = 0;
			if (is_array($cruise_type_days_of_week) && count($cruise_type_days_of_week) > 0) {
				$cruise_type_day_of_week = $cruise_type_days_of_week[0];
			}

			$date_range_query = str_replace("order by d.the_date", sprintf("HAVING WEEKDAY(d.the_date) = %d order by d.the_date ", $cruise_type_day_of_week), $date_range_query);
		} else if ($cruise_type_is_repeated == 4) {
			$cruise_type_days_of_week = $cruise_obj->get_type_day_of_week_indexes();

			$having_query = "HAVING 1=1 ";
			if (count($cruise_type_days_of_week) > 0) {
				$having_query .= " AND (1=0 ";

				foreach ($cruise_type_days_of_week as $day) {
					$having_query .= $wpdb->prepare(" OR WEEKDAY(d.the_date) = %d ", $day);
				}
				$having_query .= ") ";
			}

			$date_range_query = str_replace("order by d.the_date", $having_query . " order by d.the_date ", $date_range_query);
		} else if ($cruise_type_is_repeated == 1) {
			// this was already set right with
			// $date_range_match = ' possible_dates.the_date >= DATE(availabilities.start_date) AND possible_dates.the_date <= DATE(availabilities.end_date) ';
		} else {
			$date_range_match = ' possible_dates.the_date = DATE(availabilities.start_date)  ';
		}

		$sql = "
			SELECT DISTINCT (avc.number_of_available_cabins - IFNULL(bc.number_of_booked_cabins, 0)) available_cabins, avc.the_date FROM
			(
				SELECT SUM(cabin_count) number_of_available_cabins, possible_dates.the_date
				FROM " . BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE . " availabilities
				INNER JOIN (" . $date_range_query . ") possible_dates ON " . $date_range_match . "
				WHERE availabilities.cruise_id = %d AND availabilities.cabin_type_id = %d
				";

		$sql .= "
				GROUP BY possible_dates.the_date
			) as avc
			LEFT JOIN
			(
				SELECT SUM(bookings.cabin_count) number_of_booked_cabins, possible_dates.the_date booking_date
				FROM " . BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE . " bookings
				INNER JOIN (" . $date_range_query . ") possible_dates ON possible_dates.the_date = DATE(bookings.cruise_date)
				WHERE bookings.cruise_id = %d AND bookings.cabin_type_id = %d  ";

		if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$cruise_is_reservation_only) {
			$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
			if (!empty($completed_statuses)) {
				$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")";
			}
		}

		$sql .= "
				GROUP BY possible_dates.the_date
			) as bc
			 ON bc.booking_date = avc.the_date
			HAVING available_cabins >= %d
		";

		$sql = $wpdb->prepare($sql, $start_date, $start_date, $end_date, $cruise_id, $cabin_type_id, $start_date, $start_date, $end_date, $cruise_id, $cabin_type_id, $cabins);

		return $wpdb->get_results($sql);
	}

	function cruise_get_fields_ajax_request()
	{

		global $wpdb;

		if (isset($_REQUEST)) {
			$nonce = wp_kses($_REQUEST['nonce'], array());
			$cruise_id = intval(wp_kses($_REQUEST['cruiseId'], array()));

			if (wp_verify_nonce($nonce, 'bookyourtravel_nonce')) {

				$cruise_obj = new BookYourTravel_Cruise(intval($cruise_id));

				if ($cruise_obj) {

					$is_reservation_only = $cruise_obj->get_is_reservation_only();
					$is_price_per_person = $cruise_obj->get_is_price_per_person();
					$type_is_repeated = $cruise_obj->get_type_is_repeated();
					$duration_days = $cruise_obj->get_duration_days();

					$fields = new stdClass();
					$fields->type_is_repeated = intval($type_is_repeated);
					$fields->is_price_per_person = intval($is_price_per_person);
					$fields->is_reservation_only = intval($is_reservation_only);
					$fields->duration_days = $duration_days;

					$cabin_types = array();

					$cabin_type_ids = $cruise_obj->get_cabin_types();
					if ($cruise_obj && $cabin_type_ids && count($cabin_type_ids) > 0) {
						for ($i = 0; $i < count($cabin_type_ids); $i++) {
							$temp_id = $cabin_type_ids[$i];
							$cabin_type_obj = new BookYourTravel_Cabin_Type(intval($temp_id));
							$cabin_type_temp = new stdClass();
							$cabin_type_temp->name = $cabin_type_obj->get_title();
							$cabin_type_temp->id = $temp_id;
							$cabin_types[] = $cabin_type_temp;
						}
					}

					$fields->cruise_cabin_types = $cabin_types;

					echo json_encode($fields);
				}
			}
		}

		// Always die in functions echoing ajax content
		die();
	}

	function save_cruise($post_id)
	{
		delete_post_meta_by_key('_location_cruise_ids');

		$cruise_obj = new BookYourTravel_Cruise($post_id);
		if ($cruise_obj) {
			$locations = $cruise_obj->get_locations();
			if ($locations && count($locations) > 0) {
				foreach ($locations as $location_id) {
					delete_post_meta($location_id, '_location_cruise_count');
				}
			}
		}
	}

	function after_delete_cruise($post_id)
	{
		delete_post_meta_by_key('_location_cruise_ids');

		$cruise_obj = new BookYourTravel_Cruise($post_id);
		if ($cruise_obj) {
			$locations = $cruise_obj->get_locations();
			if ($locations && count($locations) > 0) {
				foreach ($locations as $location_id) {
					delete_post_meta($location_id, '_location_cruise_count');
				}
			}
		}
	}

	function frontend_delete_cruise_schedule_ajax_request()
	{

		global $bookyourtravel_theme_globals, $bookyourtravel_cruise_helper;

		if (isset($_REQUEST)) {

			$nonce = wp_kses($_REQUEST['nonce'], array());

			if (wp_verify_nonce($nonce, 'bookyourtravel_nonce')) {

				$schedule_id = isset($_REQUEST['schedule_id']) ? intval(wp_kses($_REQUEST['schedule_id'], array())) : 0;

				if ($schedule_id > 0) {

					$bookyourtravel_cruise_helper->delete_cruise_schedule($schedule_id);

					echo '1';
				}
			}
		}

		die();
	}

	function retrieve_booking_values_from_request($dont_calculate_totals = false)
	{

		global $bookyourtravel_theme_globals, $bookyourtravel_extra_item_helper;

		$enable_extra_items = $bookyourtravel_theme_globals->enable_extra_items();

		$booking_object = null;

		if (isset($_REQUEST)) {

			$booking_object = new stdClass();

			$booking_object->Id = isset($_REQUEST['booking_id']) ? intval(wp_kses($_REQUEST['booking_id'], array())) : 0;

			$booking_object->total_price = 0;
			$booking_object->total_cruise_price = 0;
			$booking_object->total_extra_items_price = 0;

			$booking_object->cruise_id = isset($_REQUEST['cruise_id']) ? intval(wp_kses($_REQUEST['cruise_id'], array())) : 0;
			$booking_object->cabin_type_id = isset($_REQUEST['cabin_type_id']) ? intval(wp_kses($_REQUEST['cabin_type_id'], array())) : 0;
			if ($booking_object->cabin_type_id == 0) {
				$booking_object->cabin_type_id = isset($_REQUEST['cabin_types_select']) ? intval(wp_kses($_REQUEST['cabin_types_select'], array())) : 0;
			}

			$booking_object->cruise_id = BookYourTravel_Theme_Utils::get_default_language_post_id($booking_object->cruise_id, 'cruise');
			$booking_object->cabin_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($booking_object->cabin_type_id, 'cabin_type');

			$cruise_obj = new BookYourTravel_Cruise($booking_object->cruise_id);

			$booking_object->cabin_count = isset($_REQUEST['cabin_count']) ? intval(wp_kses($_REQUEST['cabin_count'], array())) : 1;

			$booking_object->adults = isset($_REQUEST['adults']) ? intval(wp_kses($_REQUEST['adults'], array())) : 1;
			$booking_object->children = isset($_REQUEST['children']) ? intval(wp_kses($_REQUEST['children'], array())) : 0;
			$booking_object->cruise_date = isset($_REQUEST['cruise_date']) ? date('Y-m-d', strtotime(sanitize_text_field($_REQUEST['cruise_date']))) : null;

			$cruise_count_children_stay_free = get_post_meta($booking_object->cruise_id, 'cruise_count_children_stay_free', true);
			$cruise_count_children_stay_free = isset($cruise_count_children_stay_free) ? intval($cruise_count_children_stay_free) : 0;

			$booking_object->billable_children = $booking_object->children - $cruise_count_children_stay_free;
			$booking_object->billable_children = $booking_object->billable_children > 0 ? $booking_object->billable_children : 0;

			if ($dont_calculate_totals) {
				$booking_object->total_cruise_price = isset($_REQUEST['total_cruise_price']) ? $_REQUEST['total_cruise_price'] : 0;
				$booking_object->total_price = isset($_REQUEST['total_price']) ? $_REQUEST['total_price'] : 0;
			} else {
				$booking_object->total_cruise_price = $this->calculate_total_cruise_price($booking_object->cruise_id, $booking_object->cabin_type_id, $booking_object->cruise_date, $booking_object->adults, $booking_object->billable_children, $booking_object->Id, $booking_object->cabin_count);
				if ($booking_object->total_cruise_price == -1) {
					return null;
				}
				$booking_object->total_price += $booking_object->total_cruise_price;
			}

			$booking_object->extra_items = null;

			if ($dont_calculate_totals) {
				$booking_object->total_extra_items_price = isset($_REQUEST['total_extra_items_price']) ? $_REQUEST['total_extra_items_price'] : 0;
			} else if ($enable_extra_items && isset($_REQUEST['extra_items'])) {

				$booking_object->submitted_extra_items_array = (array)$_REQUEST['extra_items'];

				$booking_object->extra_items = array();

				$total_days = (int)$cruise_obj->get_duration_days();
				$total_days = $total_days > 0 ? $total_days : 1;

				foreach ($booking_object->submitted_extra_items_array as $submitted_extra_item) {
					if (isset($submitted_extra_item['id']) && $submitted_extra_item['quantity']) {
						$extra_item_id = intval(sanitize_text_field($submitted_extra_item['id']));
						$quantity = intval(sanitize_text_field($submitted_extra_item['quantity']));
						$booking_object->extra_items[$extra_item_id] = $quantity;
						$booking_object->total_extra_items_price += $bookyourtravel_extra_item_helper->calculate_extra_item_total($extra_item_id, $quantity, $booking_object->adults, $booking_object->billable_children, $total_days, 1);
					}
				}

				$booking_object->total_price += $booking_object->total_extra_items_price;
			}

			$booking_object->cart_price = isset($_REQUEST['cart_price']) ? floatval(wp_kses($_REQUEST['cart_price'], array())) : 0;
			if (!isset($_REQUEST['cart_price'])) {
				$booking_object->cart_price = $booking_object->total_price;
			}
			if ($bookyourtravel_theme_globals->enable_deposit_payments()) {
				$cruise_deposit_percentage = get_post_meta($booking_object->cruise_id, 'cruise_deposit_percentage', true);
				$cruise_deposit_percentage = isset($cruise_deposit_percentage) && $cruise_deposit_percentage !== "" ? intval($cruise_deposit_percentage) : 100;

				if (!$dont_calculate_totals) {
					$booking_object->cart_price = $booking_object->total_price * ($cruise_deposit_percentage / 100);
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

						case 'first_name': {
								$booking_object->first_name = $field_value;
								break;
							}
						case 'last_name': {
								$booking_object->last_name = $field_value;
								break;
							}
						case 'company': {
								$booking_object->company = $field_value;
								break;
							}
						case 'email': {
								$booking_object->email = $field_value;
								break;
							}
						case 'phone': {
								$booking_object->phone = $field_value;
								break;
							}
						case 'address': {
								$booking_object->address = $field_value;
								break;
							}
						case 'address_2': {
								$booking_object->address_2 = $field_value;
								break;
							}
						case 'town': {
								$booking_object->town = $field_value;
								break;
							}
						case 'zip': {
								$booking_object->zip = $field_value;
								break;
							}
						case 'state': {
								$booking_object->state = $field_value;
								break;
							}
						case 'country': {
								$booking_object->country = $field_value;
								break;
							}
						case 'special_requirements': {
								$booking_object->special_requirements = $field_value;
								break;
							}
						default: {
								$booking_object->other_fields[$field_id] = $field_value;
								break;
							}
					}
				}
			}
		}

		return $booking_object;
	}

	function booking_form_calendar_after_price_breakdown()
	{
		get_template_part('includes/parts/booking/booking-form', 'after-price-breakdown');
	}

	function process_booking_ajax_request()
	{

		global $bookyourtravel_theme_globals, $bookyourtravel_theme_woocommerce, $bookyourtravel_extra_item_helper;

		$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
		$default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
		$show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();
		$current_user = wp_get_current_user();

		if (isset($_REQUEST)) {

			$nonce = wp_kses($_REQUEST['nonce'], array());

			if (wp_verify_nonce($nonce, 'bookyourtravel_nonce')) {

				$booking_object = $this->retrieve_booking_values_from_request();

				if ($booking_object != null) {

					$cruise_id = BookYourTravel_Theme_Utils::get_current_language_post_id($booking_object->cruise_id, "cruise");
					$cabin_type_id = BookYourTravel_Theme_Utils::get_current_language_post_id($booking_object->cabin_type_id, "cabin_type");

					$cruise_obj = new BookYourTravel_Cruise($cruise_id);
					$cabin_type_obj = new BookYourTravel_Cabin_Type($cabin_type_id);

					if ($cruise_obj != null && $cabin_type_obj) {

						$booking_object->Id = $this->create_cruise_booking($current_user->ID, $booking_object);

						echo json_encode($booking_object->Id);

						$use_woocommerce_for_checkout = $bookyourtravel_theme_globals->use_woocommerce_for_checkout();
						$is_reservation_only = get_post_meta($cruise_id, 'cruise_is_reservation_only', true);

						if (!$use_woocommerce_for_checkout || !BookYourTravel_Theme_Utils::is_woocommerce_active() || $is_reservation_only) {

							// only send email if we are not proceeding to WooCommerce checkout or if woocommerce is not active at all.
							$admin_email = get_bloginfo('admin_email');
							$admin_name = get_bloginfo('name');

							$subject = esc_html__('New cruise booking', 'bookyourtravel');

							$message = esc_html__('New cruise booking: ', 'bookyourtravel');
							$message .= "\n\n";
							$message .= sprintf(esc_html__("Cruise: %s", 'bookyourtravel'), $cruise_obj->get_title()) . "\n\n";

							if ($cabin_type_obj) {
								$message .= sprintf(esc_html__("Cabin type: %s", 'bookyourtravel'), $cabin_type_obj->get_title()) . "\n\n";
							}

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
							$message .= sprintf(esc_html__("Cruise date: %s", 'bookyourtravel'), date_i18n($date_format, strtotime($booking_object->cruise_date))) . "\n\n";
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
									$total_extra_items_price_string = $default_currency_symbol . ' ' . number_format_i18n($total_extra_items_price, $price_decimal_places);
								} else {
									$total_extra_items_price_string = number_format_i18n($total_extra_items_price, $price_decimal_places) . ' ' . $default_currency_symbol;
								}

								$total_extra_items_price_string = preg_replace("/&nbsp;/", ' ', $total_extra_items_price_string);

								$message .= sprintf(esc_html__("Extra items total: %s", 'bookyourtravel'), $total_extra_items_price_string) . "\n\n";
							}

							if ($booking_object->total_cruise_price > 0) {

								$total_cruise_price = BookYourTravel_Theme_Utils::get_price_in_current_currency($booking_object->total_cruise_price);

								$total_cruise_price_string = '';
								if (!$show_currency_symbol_after) {
									$total_cruise_price_string = $default_currency_symbol . ' ' . number_format_i18n($total_cruise_price, $price_decimal_places);
								} else {
									$total_cruise_price_string = number_format_i18n($total_cruise_price, $price_decimal_places) . ' ' . $default_currency_symbol;
								}

								$total_cruise_price_string = preg_replace("/&nbsp;/", ' ', $total_cruise_price_string);

								$message .= sprintf(esc_html__("Reservation total: %s", 'bookyourtravel'), $total_cruise_price_string) . "\n\n";
							}

							$total_price = BookYourTravel_Theme_Utils::get_price_in_current_currency($booking_object->total_price);

							$total_price_string = '';
							if (!$show_currency_symbol_after) {
								$total_price_string = $default_currency_symbol . ' ' . number_format_i18n($total_price, $price_decimal_places);
							} else {
								$total_price_string = number_format_i18n($total_price, $price_decimal_places) . ' ' . $default_currency_symbol;
							}

							$total_price_string = preg_replace("/&nbsp;/", ' ', $total_price_string);
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

							$contact_emails = trim(get_post_meta($cruise_id, 'cruise_contact_email', true));

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

	function calculate_total_cruise_price($cruise_id, $cabin_type_id, $cruise_date, $adults, $children, $booking_id, $cabin_count)
	{

		$cruise_obj = new BookYourTravel_Cruise($cruise_id);
		$cruise_is_price_per_person = $cruise_obj->get_is_price_per_person();

		$prices = $this->get_prices($cruise_date, $cruise_id, $cabin_type_id, $booking_id);

		if (!isset($prices) && !isset($prices->regular_price)) {
			return -1;
		}

		$total_price_adults = 0;

		$total_price_children = 0;
		if ($cruise_is_price_per_person) {
			$total_price_children = $prices->regular_price_child * $children;
			$total_price_adults = $prices->regular_price * $adults;
		} else {
			$total_price_adults = $prices->regular_price;
		}

		$total_price = $total_price_adults + $total_price_children;

		$total_price = $total_price * $cabin_count;

		return $total_price;
	}

	function update_booking_woocommerce_info($booking_id, $cart_key = null, $woo_order_id = null, $woo_status = null)
	{

		global $wpdb;

		if (isset($cart_key) || isset($woo_order_id) || isset($woo_status)) {
			$sql = "UPDATE " . BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE . "
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

	function create_cruise_booking($user_id, $booking_object)
	{

		global $wpdb;

		$errors = array();

		$sql = "INSERT INTO " . BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE . "
				(user_id, cruise_id, cabin_type_id, cabin_count, adults, children, cruise_date, first_name, last_name, company, email, phone, address, address_2, town, zip, state, country, special_requirements, other_fields, extra_items, total_cruise_price, total_extra_items_price, total_price, cart_price)
				VALUES
				(%d, %d, %d, %d, %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %f, %f, %f, %f);";

		$sql = $wpdb->prepare($sql, $user_id, $booking_object->cruise_id, $booking_object->cabin_type_id, $booking_object->cabin_count, $booking_object->adults, $booking_object->children, $booking_object->cruise_date, $booking_object->first_name, $booking_object->last_name, $booking_object->company, $booking_object->email, $booking_object->phone, $booking_object->address, $booking_object->address_2, $booking_object->town, $booking_object->zip, $booking_object->state, $booking_object->country, $booking_object->special_requirements, serialize($booking_object->other_fields), serialize($booking_object->extra_items), $booking_object->total_cruise_price, $booking_object->total_extra_items_price, $booking_object->total_price, $booking_object->cart_price);

		$result = $wpdb->query($sql);

		if (is_wp_error($result))
			$errors[] = $result;

		$booking_object->Id = $wpdb->insert_id;

		$this->clear_price_meta_cache($booking_object->cruise_id);

		return $booking_object->Id;
	}

	function update_cruise_booking($booking_id, $booking_object)
	{

		global $wpdb;

		$result = 0;

		$sql = "UPDATE " . BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE . " SET ";

		$field_sql = '';

		foreach ($booking_object as $field_key => $field_value) {

			switch ($field_key) {

				case 'cruise_id':
					$field_sql .= $wpdb->prepare("cruise_id = %d, ", $field_value);
					break;
				case 'cabin_type_id':
					$field_sql .= $wpdb->prepare("cabin_type_id = %d, ", $field_value);
					break;
				case 'cruise_date':
					$field_sql .= $wpdb->prepare("cruise_date = %s, ", $field_value);
					break;
				case 'cabin_count':
					$field_sql .= $wpdb->prepare("cabin_count = %d, ", $field_value);
					break;
				case 'adults':
					$field_sql .= $wpdb->prepare("adults = %d, ", $field_value);
					break;
				case 'children':
					$field_sql .= $wpdb->prepare("children = %d, ", $field_value);
					break;
				case 'user_id':
					$field_sql .= $wpdb->prepare("user_id = %d, ", $field_value);
					break;
				case 'first_name':
					$field_sql .= $wpdb->prepare("first_name = %s, ", $field_value);
					break;
				case 'last_name':
					$field_sql .= $wpdb->prepare("last_name = %s, ", $field_value);
					break;
				case 'company':
					$field_sql .= $wpdb->prepare("company = %s, ", $field_value);
					break;
				case 'email':
					$field_sql .= $wpdb->prepare("email = %s, ", $field_value);
					break;
				case 'phone':
					$field_sql .= $wpdb->prepare("phone = %s, ", $field_value);
					break;
				case 'address':
					$field_sql .= $wpdb->prepare("address = %s, ", $field_value);
					break;
				case 'address_2':
					$field_sql .= $wpdb->prepare("address_2 = %s, ", $field_value);
					break;
				case 'town':
					$field_sql .= $wpdb->prepare("town = %s, ", $field_value);
					break;
				case 'zip':
					$field_sql .= $wpdb->prepare("zip = %s, ", $field_value);
					break;
				case 'state':
					$field_sql .= $wpdb->prepare("state = %s, ", $field_value);
					break;
				case 'country':
					$field_sql .= $wpdb->prepare("country = %s, ", $field_value);
					break;
				case 'special_requirements':
					$field_sql .= $wpdb->prepare("special_requirements = %s, ", $field_value);
					break;
				case 'other_fields':
					$field_sql .= $wpdb->prepare("other_fields = %s, ", serialize($field_value));
					break;
				case 'extra_items':
					$field_sql .= $wpdb->prepare("extra_items = %s, ", serialize($field_value));
					break;
				case 'total_cruise_price':
					$field_sql .= $wpdb->prepare("total_cruise_price = %f, ", $field_value);
					break;
				case 'cart_price':
					$field_sql .= $wpdb->prepare("cart_price = %f, ", $field_value);
					break;
				case 'total_extra_items_price':
					$field_sql .= $wpdb->prepare("total_extra_items_price = %f, ", $field_value);
					break;
				case 'total_price':
					$field_sql .= $wpdb->prepare("total_price = %f, ", $field_value);
					break;
				case 'woo_order_id':
					$field_sql .= $wpdb->prepare("woo_order_id = %d, ", $field_value);
					break;
				case 'cart_key':
					$field_sql .= $wpdb->prepare("cart_key = %s, ", $field_value);
					break;
				case 'woo_status':
					$field_sql .= $wpdb->prepare("woo_status = %s, ", $field_value);
					break;
				default:
					break;
			}
		}

		if (!empty($field_sql)) {

			$field_sql = rtrim($field_sql, ", ");

			$sql .= $field_sql;

			$sql .= $wpdb->prepare(" WHERE Id = %d;", $booking_id);

			$result = $wpdb->query($sql);
		}

		$this->clear_price_meta_cache($booking_object->cruise_id);

		return $result;
	}

	function cruise_admin_init()
	{
		if ($this->enable_cruises) {
			$this->initialize_meta_fields();
			new Custom_Add_Meta_Box('cruise_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->cruise_custom_meta_fields, $this->cruise_custom_meta_tabs, 'cruise');

			$this->cruise_list_meta_box = new Custom_Add_Meta_Box('cruise_list_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->cruise_list_custom_meta_fields, $this->cruise_list_custom_meta_tabs, 'page');
			remove_action('add_meta_boxes', array($this->cruise_list_meta_box, 'add_box'));
			add_action('add_meta_boxes', array($this, 'cruise_list_add_meta_boxes'));
		}
	}

	function cruise_list_add_meta_boxes()
	{
		global $post;
		$template_file = get_post_meta($post->ID, '_wp_page_template', true);
		if ($template_file == 'page-cruise-list.php') {
			add_meta_box(
				$this->cruise_list_meta_box->id,
				$this->cruise_list_meta_box->title,
				array($this->cruise_list_meta_box, 'meta_box_callback'),
				'page',
				'normal',
				'high'
			);
		}
	}

	function initialize_post_type()
	{

		global $bookyourtravel_theme_globals;
		$this->enable_cruises = $bookyourtravel_theme_globals->enable_cruises();

		if ($this->enable_cruises) {
			$this->register_cruise_post_type();
			$this->register_cruise_tag_taxonomy();
			$this->register_cruise_type_taxonomy();
			$this->register_cruise_duration_taxonomy();
		}

		// have to make sure extra tables are created regardless of whether the
		// post type is enabled or not in order for tables to exist if post type
		// is enabled at a later stage.
		$this->create_cruise_extra_tables();
	}

	function manage_edit_cruise_columns($columns)
	{

		//unset($columns['taxonomy-cruise_type']);
		return $columns;
	}

	function remove_unnecessary_meta_boxes()
	{
		remove_meta_box('tagsdiv-cruise_tag', 'cruise', 'side');
		remove_meta_box('tagsdiv-cruise_type', 'cruise', 'side');
		remove_meta_box('tagsdiv-facility', 'cruise', 'side');
	}

	function register_cruise_tag_taxonomy()
	{

		$labels = array(
			'name'              => esc_html__('Cruise tags', 'bookyourtravel'),
			'singular_name'     => esc_html__('Cruise tag', 'bookyourtravel'),
			'search_items'      => esc_html__('Search Cruise tags', 'bookyourtravel'),
			'all_items'         => esc_html__('All Cruise tags', 'bookyourtravel'),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'         => esc_html__('Edit Cruise tag', 'bookyourtravel'),
			'update_item'       => esc_html__('Update Cruise tag', 'bookyourtravel'),
			'add_new_item'      => esc_html__('Add New Cruise tag', 'bookyourtravel'),
			'new_item_name'     => esc_html__('New Cruise tag Name', 'bookyourtravel'),
			'separate_items_with_commas' => esc_html__('Separate cruise tags with commas', 'bookyourtravel'),
			'add_or_remove_items'        => esc_html__('Add or remove cruise tags', 'bookyourtravel'),
			'choose_from_most_used'      => esc_html__('Choose from the most used cruise tags', 'bookyourtravel'),
			'not_found'                  => esc_html__('No cruise tags found.', 'bookyourtravel'),
			'menu_name'         => esc_html__('Cruise tags', 'bookyourtravel'),
		);

		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'update_count_callback' => '_update_post_term_count',
			'rewrite' => array('slug' => 'cruise-tag'),
		);

		register_taxonomy('cruise_tag', array('cruise'), $args);
	}

	function register_cruise_duration_taxonomy()
	{
		$labels = array(
			'name'              => esc_html__('Cruise durations', 'bookyourtravel'),
			'singular_name'     => esc_html__('Cruise duration', 'bookyourtravel'),
			'search_items'      => esc_html__('Search Cruise durations', 'bookyourtravel'),
			'all_items'         => esc_html__('All Cruise durations', 'bookyourtravel'),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'         => esc_html__('Edit Cruise duration', 'bookyourtravel'),
			'update_item'       => esc_html__('Update Cruise duration', 'bookyourtravel'),
			'add_new_item'      => esc_html__('Add New Cruise duration', 'bookyourtravel'),
			'new_item_name'     => esc_html__('New Cruise Duration Name', 'bookyourtravel'),
			'separate_items_with_commas' => esc_html__('Separate Cruise durations with commas', 'bookyourtravel'),
			'add_or_remove_items'        => esc_html__('Add or remove Cruise durations', 'bookyourtravel'),
			'choose_from_most_used'      => esc_html__('Choose from the most used Cruise durations', 'bookyourtravel'),
			'not_found'                  => esc_html__('No Cruise durations found.', 'bookyourtravel'),
			'menu_name'         => esc_html__('Cruise durations', 'bookyourtravel'),
		);

		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => false,
			'update_count_callback' => '_update_post_term_count',
			'rewrite' => array('slug' => 'cruise-duration'),
		);

		register_taxonomy('cruise_duration', 'cruise', $args);
	}

	function register_cruise_type_taxonomy()
	{
		$labels = array(
			'name'              => esc_html__('Cruise types', 'bookyourtravel'),
			'singular_name'     => esc_html__('Cruise type', 'bookyourtravel'),
			'search_items'      => esc_html__('Search Cruise types', 'bookyourtravel'),
			'all_items'         => esc_html__('All Cruise types', 'bookyourtravel'),
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'         => esc_html__('Edit Cruise type', 'bookyourtravel'),
			'update_item'       => esc_html__('Update Cruise type', 'bookyourtravel'),
			'add_new_item'      => esc_html__('Add New Cruise type', 'bookyourtravel'),
			// custom
			'parent'      => esc_html__('Add New Cruise type', 'bookyourtravel'),
			'new_item_name'     => esc_html__('New Cruise Type Name', 'bookyourtravel'),
			'separate_items_with_commas' => esc_html__('Separate Cruise types with commas', 'bookyourtravel'),
			'add_or_remove_items'        => esc_html__('Add or remove Cruise types', 'bookyourtravel'),
			'choose_from_most_used'      => esc_html__('Choose from the most used Cruise types', 'bookyourtravel'),
			'not_found'                  => esc_html__('No Cruise types found.', 'bookyourtravel'),
			'menu_name'         => esc_html__('Cruise types', 'bookyourtravel'),
		);

		$args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => false,
			'update_count_callback' => '_update_post_term_count',
			'rewrite' => array('slug' => 'cruise-type'),
		);

		register_taxonomy('cruise_type', 'cruise', $args);
	}

	function register_cruise_post_type()
	{

		global $bookyourtravel_theme_globals;

		$cruises_permalink_slug = $bookyourtravel_theme_globals->get_cruises_permalink_slug();

		$cruise_list_page_id = $bookyourtravel_theme_globals->get_cruise_list_page_id();

		if ($cruise_list_page_id > 0) {

			add_rewrite_rule(
				"{$cruises_permalink_slug}$",
				"index.php?post_type=page&page_id={$cruise_list_page_id}",
				'top'
			);

			add_rewrite_rule(
				"{$cruises_permalink_slug}/page/?([1-9][0-9]*)",
				"index.php?post_type=page&page_id={$cruise_list_page_id}&paged=\$matches[1]",
				'top'
			);
		}

		add_rewrite_rule(
			"{$cruises_permalink_slug}/([^/]+)/page/?([1-9][0-9]*)",
			"index.php?post_type=cruise&name=\$matches[1]&paged-byt=\$matches[2]",
			'top'
		);

		add_rewrite_tag('%paged-byt%', '([1-9][0-9]*)');

		$labels = array(
			'name'                => esc_html__('Cruises', 'bookyourtravel'),
			'singular_name'       => esc_html__('Cruise', 'bookyourtravel'),
			'menu_name'           => esc_html__('Cruises', 'bookyourtravel'),
			'all_items'           => esc_html__('All Cruises', 'bookyourtravel'),
			'view_item'           => esc_html__('View Cruise', 'bookyourtravel'),
			'add_new_item'        => esc_html__('Add New Cruise', 'bookyourtravel'),
			'add_new'             => esc_html__('New Cruise', 'bookyourtravel'),
			'edit_item'           => esc_html__('Edit Cruise', 'bookyourtravel'),
			'update_item'         => esc_html__('Update Cruise', 'bookyourtravel'),
			'search_items'        => esc_html__('Search Cruises', 'bookyourtravel'),
			'not_found'           => esc_html__('No Cruises found', 'bookyourtravel'),
			'not_found_in_trash'  => esc_html__('No Cruises found in Trash', 'bookyourtravel'),
		);
		$args = array(
			'label'               => esc_html__('Cruise', 'bookyourtravel'),
			'description'         => esc_html__('Cruise information pages', 'bookyourtravel'),
			'labels'              => $labels,
			'supports'            => array('title', 'editor', 'thumbnail', 'author', 'page-attributes'),
			'taxonomies'          => array(),
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
			'rewrite' => array('slug' => $cruises_permalink_slug),
		);

		register_post_type('cruise', $args);
	}

	function create_cruise_extra_tables()
	{

		global $bookyourtravel_installed_version, $force_recreate_tables;

		if ($bookyourtravel_installed_version != BOOKYOURTRAVEL_VERSION || $force_recreate_tables) {

			global $wpdb;

			$table_name = BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE;
			$sql = "CREATE TABLE " . $table_name . " (
						Id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						season_name varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
						start_date datetime NOT NULL,
						end_date datetime NULL,
						cruise_id bigint(20) unsigned NOT NULL,
						cabin_type_id bigint(20) unsigned NOT NULL DEFAULT '0',
						cabin_count int(11) NOT NULL,
						price decimal(16,2) NOT NULL,
						price_child decimal(16,2) NOT NULL,
						created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
						PRIMARY KEY  (Id)
					);";

			// we do not execute sql directly
			// we are calling dbDelta which cant migrate database
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);

			global $EZSQL_ERROR;
			$EZSQL_ERROR = array();

			$table_name = BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE;
			$sql = "CREATE TABLE " . $table_name . " (
						Id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
						special_requirements text CHARACTER SET utf8 COLLATE utf8_bin,
						other_fields text CHARACTER SET utf8 COLLATE utf8_bin NULL,
						extra_items text CHARACTER SET utf8 COLLATE utf8_bin NULL,
						adults int(11) NOT NULL DEFAULT '0',
						children int(11) NOT NULL DEFAULT '0',
						cruise_id bigint(20) NOT NULL DEFAULT 0,
						cabin_type_id bigint(20) NOT NULL DEFAULT 0,
						cabin_count bigint(20) NOT NULL DEFAULT 1,
						cruise_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
						user_id bigint(20) unsigned DEFAULT NULL,
						total_price_adults decimal(16, 2) NOT NULL,
						total_price_children decimal(16, 2) NOT NULL,
						total_cruise_price decimal(16,2) NOT NULL DEFAULT '0.00',
						cart_price decimal(16,2) NOT NULL DEFAULT '0.00',
						total_extra_items_price decimal(16,2) NOT NULL DEFAULT '0.00',
						total_price decimal(16, 2) NOT NULL,
						created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
						woo_order_id bigint(20) NULL,
						woo_status varchar(255) NULL,
						cart_key VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT '' NOT NULL,
						PRIMARY KEY  (Id)
					);";
			dbDelta($sql);

			$EZSQL_ERROR = array();
		}
	}

	function cruise_type_add_new_meta_fields($taxonomy)
	{
		// this will add the custom meta fields to the add new term page
		$days_of_week = BookYourTravel_Theme_Utils::get_days_of_week();

		?>

		<div class="form-field">
			<label for="term_meta[cruise_type_is_repeated]"><?php esc_html_e('Is cruise repeated?', 'bookyourtravel'); ?></label>
			<select class="cruise_type_repeat_type display_block" id="term_meta[cruise_type_is_repeated]" name="term_meta[cruise_type_is_repeated]" >
				<option value="0"><?php esc_html_e('No', 'bookyourtravel') ?></option>
				<option value="1"><?php esc_html_e('Daily', 'bookyourtravel') ?></option>
				<option value="2"><?php esc_html_e('Weekdays', 'bookyourtravel') ?></option>
				<option value="3"><?php esc_html_e('Weekly', 'bookyourtravel') ?></option>
				<option value="4"><?php esc_html_e('Weekly (multi-days)', 'bookyourtravel') ?></option>
			</select>
			<p class="description"><?php esc_html_e('Do cruises belonging to this cruise type repeat on a daily, weekly, weekday or monthly basis?', 'bookyourtravel'); ?></p>
		</div>
		<div id="tr_cruise_type_day_of_week" class="form-field" style="display:none">
			<label for="term_meta[cruise_type_day_of_week]"><?php esc_html_e('Start day (if weekly)', 'bookyourtravel'); ?></label>
			<select id="term_meta[cruise_type_day_of_week]" name="term_meta[cruise_type_day_of_week]">
				<?php
				for ($i = 0; $i < count($days_of_week); $i++) {
					$day_of_week = $days_of_week[$i]; ?>
					<option value="<?php echo esc_attr($i); ?>"><?php echo esc_html($day_of_week); ?></option>
				<?php } ?>
			</select>
			<p class="description"><?php esc_html_e('Select a start day of the week for weekly cruise', 'bookyourtravel'); ?></p>
		</div>
		<div id="tr_cruise_type_days_of_week" class="form-field" style="display:none">
			<label><?php esc_html_e('Start day (if weekly multi-days)', 'bookyourtravel'); ?></label>
			<?php
			for ($i = 0; $i < count($days_of_week); $i++) {
				$day_of_week = $days_of_week[$i]; ?>
				<input type="checkbox" id="term_meta[cruise_type_days_of_week_<?php echo esc_attr($i); ?>]" name="term_meta[cruise_type_days_of_week][]" value="<?php echo esc_attr($i); ?>"><?php echo esc_html($day_of_week); ?>
			<?php } ?>
			<p class="description"><?php esc_html_e('Select multiple start days of the week for weekly cruise', 'bookyourtravel'); ?></p>
		</div>
	<?php
	}

	function cruise_type_edit_meta_fields($term, $taxonomy)
	{

		$days_of_week = BookYourTravel_Theme_Utils::get_days_of_week();

		// put the term ID into a variable
		$t_id = $term->term_id;

		// retrieve the existing value(s) for this meta field. This returns an array
	
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="term_meta[cruise_type_is_repeated]"><?php esc_html_e('Is cruise repeated?', 'bookyourtravel'); ?></label></th>
			<td>
				<select class="cruise_type_repeat_type display_table_row" id="term_meta[cruise_type_is_repeated]" name="term_meta[cruise_type_is_repeated]">
					<option <?php echo isset($term_meta['cruise_type_is_repeated']) && (int) $term_meta['cruise_type_is_repeated'] == 0 ? 'selected' : '' ?> value="0"><?php esc_html_e('No', 'bookyourtravel') ?></option>
					<option <?php echo isset($term_meta['cruise_type_is_repeated']) && (int) $term_meta['cruise_type_is_repeated'] == 1 ? 'selected' : '' ?> value="1"><?php esc_html_e('Daily', 'bookyourtravel') ?></option>
					<option <?php echo isset($term_meta['cruise_type_is_repeated']) && (int) $term_meta['cruise_type_is_repeated'] == 2 ? 'selected' : '' ?> value="2"><?php esc_html_e('Weekdays', 'bookyourtravel') ?></option>
					<option <?php echo isset($term_meta['cruise_type_is_repeated']) && (int) $term_meta['cruise_type_is_repeated'] == 3 ? 'selected' : '' ?> value="3"><?php esc_html_e('Weekly', 'bookyourtravel') ?></option>
					<option <?php echo isset($term_meta['cruise_type_is_repeated']) && (int) $term_meta['cruise_type_is_repeated'] == 4 ? 'selected' : '' ?> value="4"><?php esc_html_e('Weekly (multi-days)', 'bookyourtravel') ?></option>
				</select>
				<p class="description"><?php esc_html_e('Do cruises belonging to this cruise type repeat on a set basis?', 'bookyourtravel'); ?></p>
			</td>
		</tr>
		<tr id="tr_cruise_type_day_of_week" class="form-field" style="<?php echo !isset($term_meta['cruise_type_is_repeated']) || (int)$term_meta['cruise_type_is_repeated'] != 3 ? 'display:none' : ''; ?>">
			<th scope="row" valign="top"><label for="term_meta[cruise_type_day_of_week]"><?php esc_html_e('Start day (if weekly)', 'bookyourtravel'); ?></label></th>
			<td>
				<select id="term_meta[cruise_type_day_of_week]" name="term_meta[cruise_type_day_of_week]">
					<?php
					for ($i = 0; $i < count($days_of_week); $i++) {
						$day_of_week = $days_of_week[$i]; ?>
						<option <?php echo isset($term_meta['cruise_type_day_of_week']) && (int)$term_meta['cruise_type_day_of_week'] == $i ? 'selected' : '' ?> value="<?php echo esc_attr($i); ?>"><?php echo esc_html($day_of_week); ?></option>
					<?php } ?>
				</select>
				<p class="description"><?php esc_html_e('Select a start day of the week for weekly cruise', 'bookyourtravel'); ?></p>
			</td>
		</tr>
		<tr id="tr_cruise_type_days_of_week" class="form-field" style="<?php echo !isset($term_meta['cruise_type_is_repeated']) || (int)$term_meta['cruise_type_is_repeated'] != 4 ? 'display:none' : ''; ?>">
			<th scope="row" valign="top"><label><?php esc_html_e('Start day (if weekly multi-days)', 'bookyourtravel'); ?></label></th>
			<td>
				<?php
				for ($i = 0; $i < count($days_of_week); $i++) {
					$day_of_week = $days_of_week[$i]; ?>
					<input <?php echo isset($term_meta['cruise_type_days_of_week']) && in_array($i, (array)$term_meta['cruise_type_days_of_week']) ? 'checked' : '' ?> type="checkbox" id="term_meta[cruise_type_days_of_week_<?php echo esc_attr($i); ?>]" name="term_meta[cruise_type_days_of_week][]" value="<?php echo esc_attr($i); ?>"><?php echo esc_html($day_of_week); ?>
				<?php } ?>
				<p class="description"><?php esc_html_e('Select multiple start days of the week for weekly cruise', 'bookyourtravel'); ?></p>
			</td>
		</tr>
<?php
	}

	function save_cruise_type_custom_meta($term_id, $tt_id)
	{

		if (isset($_POST['term_meta'])) {
			$t_id = $term_id;
			$term_meta = get_option("taxonomy_{$t_id}");

			if (!is_array($term_meta)) {
				$term_meta = array();
			}

			$cat_keys = array_keys($_POST['term_meta']);
			foreach ($cat_keys as $key) {
				if (isset($_POST['term_meta'][$key])) {
					$term_meta[$key] = $_POST['term_meta'][$key];
				}
			}
			// Save the option array.
			update_option("taxonomy_$t_id", $term_meta);
		}
	}

	function cruises_search_fields($fields, $wp_query)
	{

		global $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;

		if (isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'cruise') {

			$search_only_available = false;
			if (isset($wp_query->query_vars['search_only_available']))
				$search_only_available = $wp_query->get('search_only_available');

			$date_today = date('Y-m-d', time());
			$date_from = null;
			if (isset($wp_query->query_vars['byt_date_from']))
				$date_from = date('Y-m-d', strtotime($wp_query->get('byt_date_from')));
			else
				$date_from = $date_today;

			$date_to = null;
			if (isset($wp_query->query_vars['byt_date_to']))
				$date_to = date('Y-m-d', strtotime($wp_query->get('byt_date_to') . ' -1 day'));
			else
				$date_to = date('Y-m-d', strtotime($date_from . ' +24 months'));

			if (isset($date_from) && $date_from == $date_to)
				$date_to = date('Y-m-d', strtotime($date_from . ' +7 day'));

			if ($search_only_available || isset($wp_query->query_vars['byt_date_from']) || isset($wp_query->query_vars['byt_date_from'])) {

				if ((isset($date_from) || isset($date_to))) {

					$fields .= ", (";

					$temp_fields_sql = "SELECT IFNULL(SUM(cabin_count), 0) cabins_available
						FROM " . BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE . " schedule
						WHERE 1=1 AND ";

					if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
						$temp_fields_sql .= " (cruise_id = wpml_translations_default.element_id OR cruise_id = wpml_translations.element_id) ";
					} else {
						$temp_fields_sql .= " cruise_id = {$wpdb->posts}.ID ";
					}

					$temp_fields_sql .= " AND
					(
						((schedule.end_date IS NULL OR schedule.end_date = '0000-00-00 00:00:00') AND (DATE(schedule.start_date) BETWEEN DATE(%s) AND DATE(%s)))
						OR
						(
							((schedule.end_date IS NOT NULL OR schedule.end_date != '0000-00-00 00:00:00') AND DATE(%s) < DATE(schedule.end_date) AND DATE(%s) > DATE(schedule.start_date))
					";

					$temp_fields_sql .= "

						)
					)";

					$fields .= $wpdb->prepare($temp_fields_sql, $date_from, $date_to, $date_from, $date_to);

					$fields .= " ) cabins_available ";

					$fields .= ", (
									SELECT COUNT(*) places_booked
									FROM " . BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE . " bookings
									INNER JOIN " . BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE . " schedule ON bookings.cruise_id = schedule.cruise_id ";

					if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
						$fields .= " WHERE (schedule.cruise_id = wpml_translations_default.element_id OR schedule.cruise_id = wpml_translations.element_id) ";
					} else {
						$fields .= " WHERE schedule.cruise_id = {$wpdb->posts}.ID ";
					}

					if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout()) {

						$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
						if (!empty($completed_statuses)) {
							$fields .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")";
						}
					}

					if ($date_from != null) {
						$fields .= $wpdb->prepare(" AND DATE(%s) = DATE(bookings.cruise_date) ", $date_from);
					}

					$fields .= " ) cabins_booked ";
				}
			}

			if (isset($wp_query->query_vars['byt_date_to']))
				$date_to = date('Y-m-d', strtotime($wp_query->get('byt_date_to')));
			else
				$date_to = date('Y-m-d', strtotime($date_from . ' +24 months'));
			$min_price_meta_key = BookYourTravel_Theme_Utils::build_min_price_meta_key("cruise", $date_from, $date_to);
			$min_price_check_meta_key = BookYourTravel_Theme_Utils::build_min_price_check_meta_key("cruise", $date_from, $date_to);

			$fields_sql = ", IFNULL((SELECT price_meta2.meta_value + 0 FROM {$wpdb->postmeta} price_meta2 ";
			if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
				$fields_sql .= " WHERE price_meta2.post_id={$wpdb->posts}.ID ";
			} else {
				$fields_sql .= " WHERE price_meta2.post_id={$wpdb->posts}.ID ";
			}

			$fields_sql .= " AND price_meta2.meta_key=%s LIMIT 1), 0) cruise_price ";

			$fields .= $wpdb->prepare($fields_sql, $min_price_meta_key);

			$fields_sql = ", IFNULL((SELECT price_meta3.meta_value + 0 FROM {$wpdb->postmeta} price_meta3 ";
			if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
				$fields_sql .= " WHERE price_meta3.post_id={$wpdb->posts}.ID ";
			} else {
				$fields_sql .= " WHERE price_meta3.post_id={$wpdb->posts}.ID ";
			}

			$fields_sql .= " AND price_meta3.meta_key='cruise_static_from_price' LIMIT 1), 0) cruise_static_price ";

			$fields .= $fields_sql;
		}

		return $fields;
	}

	function cruises_search_where($where, $wp_query)
	{

		global $wpdb;

		if (isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'cruise') {
			$where = str_replace('DECIMAL', 'DECIMAL(10,2)', $where);
		}

		return $where;
	}

	function cruises_search_groupby($groupby, $wp_query)
	{

		global $wpdb;

		if (empty($groupby))
			$groupby = " {$wpdb->posts}.ID ";

		if (!is_admin()) {
			if (isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'cruise') {

				$date_today = date('Y-m-d', time());
				$date_from = null;
				if (isset($wp_query->query_vars['byt_date_from']))
					$date_from = date('Y-m-d', strtotime($wp_query->get('byt_date_from')));
				else
					$date_from = $date_today;

				$date_to = null;
				if (isset($wp_query->query_vars['byt_date_to']))
					$date_to = date('Y-m-d', strtotime($wp_query->get('byt_date_to') . ' -1 day'));
				else
					$date_to = date('Y-m-d', strtotime($date_from . ' +24 months'));

				if (isset($date_from) && $date_from == $date_to)
					$date_to = date('Y-m-d', strtotime($date_from . ' +7 day'));

				$search_only_available = false;
				if (isset($wp_query->query_vars['search_only_available']))
					$search_only_available = $wp_query->get('search_only_available');

				$groupby .= " HAVING 1=1 ";

				if ($search_only_available && isset($date_from)) {
					$groupby .= ' AND cabins_available > cabins_booked ';
					if (isset($wp_query->query_vars['byt_cabins'])) {
						$groupby .= $wpdb->prepare(" AND cabins_available >= %d ", $wp_query->query_vars['byt_cabins']);
					}
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
						for ($i = 0; $i < $price_range_count; $i++) {
							$bottom = ($i * $price_range_increment) + $price_range_bottom;
							if ($bottom == 0) {
								$bottom = 0.1;
							}
							$top = (($i + 1) * $price_range_increment) + $price_range_bottom - 1;

							if (in_array($i + 1, $prices)) {
								if ($i < (($price_range_count - 1))) {
									$groupby .= $wpdb->prepare(" OR (cruise_price >= %f AND cruise_price <= %f ) ", $bottom, $top);
									$groupby .= $wpdb->prepare(" OR (cruise_static_price >= %f AND cruise_static_price <= %f ) ", $bottom, $top);
								} else {
									$groupby .= $wpdb->prepare(" OR (cruise_price >= %f ) ", $bottom);
									$groupby .= $wpdb->prepare(" OR (cruise_static_price >= %f ) ", $bottom);
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

	function cruises_search_join($join)
	{

		global $wp_query, $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;

		if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$join .= " LEFT JOIN {$wpdb->prefix}icl_translations wpml_translations_default ON wpml_translations_default.trid = wpml_translations.trid AND (wpml_translations_default.source_language_code IS NULL OR wpml_translations.source_language_code IS NULL) ";
		}

		return $join;
	}

	function build_cruises_search_orderby($orderby, $wp_query)
	{

		global $wpdb;

		if (isset($wp_query->query_vars['byt_orderby']) && isset($wp_query->query_vars['byt_order'])) {

			$order = 'ASC';
			if ($wp_query->get('byt_order') == 'DESC') {
				$order = 'DESC';
			}

			$column = 'cruise_price';
			if ($wp_query->get('byt_orderby') == $column) {
				$orderby = $column . ' ' . $order;
			}
			$column = 'cruise_static_price';
			if ($wp_query->get('byt_orderby') == $column) {
				$orderby = $column . ' ' . $order;
			}
		}

		return $orderby;
	}

	function list_cruises_count($paged = 0, $per_page = 0, $orderby = '', $order = '', $location_ids = array(), $exclusive_locations = false, $cruise_types_array = array(), $cruise_durations_array = array(), $cruise_tags_array = array(), $cruise_facilities_array = array(), $search_args = array(), $featured_only = false, $author_id = null, $include_private = false)
	{
		$results = $this->list_cruises($paged, $per_page, $orderby, $order, $location_ids, $exclusive_locations, $cruise_types_array, $cruise_durations_array, $cruise_tags_array, $cruise_facilities_array, $search_args, $featured_only, $author_id, $include_private, true);
		return $results['total'];
	}

	function list_cruises($paged = 0, $per_page = -1, $orderby = '', $order = '', $param_location_ids = array(), $exclusive_locations = false, $cruise_types_array = array(), $cruise_durations_array = array(), $cruise_tags_array = array(), $cruise_facilities_array = array(), $search_args = array(), $featured_only = false, $author_id = null, $include_private = false, $count_only = false)
	{

		global $bookyourtravel_theme_globals;

		$location_ids = array();

		if (count($param_location_ids) > 0 && is_array($param_location_ids)) {
			foreach ($param_location_ids as $location_id) {
				if ($location_id > 0) {
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
				'posts_per_page' => -1,
				'post_status' => 'publish',
				'suppress_filters' => false
			);

			$location_posts = get_posts($args);
			foreach ($location_posts as $location) {
				$location_ids[] = $location->ID; // BookYourTravel_Theme_Utils::get_default_language_post_id($location->ID, 'location');
			}

			$descendant_location_ids = array();
			foreach ($location_ids as $temp_location_id) {
				$location_descendants = BookYourTravel_Theme_Utils::get_post_descendants($temp_location_id, 'location');
				foreach ($location_descendants as $location) {
					$descendant_location_ids[] = $location->ID; // BookYourTravel_Theme_Utils::get_default_language_post_id($location->ID, 'location');
				}
			}
			$location_ids = array_merge($descendant_location_ids, $location_ids);
		}

		$args = array(
			'post_type'         => 'cruise',
			'post_status'       => array('publish'),
			'posts_per_page'    => $per_page,
			'paged' 			=> $paged,
			'orderby'           => $orderby,
			'suppress_filters' 	=> false,
			'order'				=> $order,
			'meta_query'        => array('relation' => 'AND')
		);

		if ($orderby == 'review_score') {
			$args['meta_key'] = 'review_score';
			$args['orderby'] = 'meta_value_num';
		} else if ($orderby == 'price' || $orderby == 'min_price') {
			if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
				$args['byt_orderby'] = 'cruise_static_price';
			} else {
				$args['byt_orderby'] = 'cruise_price';
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

		if (isset($search_args['rating']) && strlen($search_args['rating']) > 0) {
			$rating = floatval(intval($search_args['rating']) / 10);
			if ($rating > 0 & $rating <= 10) {
				$args['meta_query'][] = array(
					'relation' => 'AND',
					array(
						'key' => 'review_score',
						'value' => $rating,
						'type' => 'DECIMAL',
						'compare'   => '>=',
					),
					array(
						'key' => 'review_score',
						'compare' => 'EXISTS'
					)
				);
			}
		}

		if (isset($featured_only) && $featured_only) {
			$args['meta_query'][] = array(
				'key'       => 'cruise_is_featured',
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

		$args['tax_query'] = array();

		if (!empty($cruise_types_array)) {
			$args['tax_query'][] = 	array(
				'taxonomy' => 'cruise_type',
				'field' => 'term_id',
				'terms' => $cruise_types_array,
				'operator' => 'IN'
			);
		}

		if (!empty($cruise_durations_array)) {
			$args['tax_query'][] = 	array(
				'taxonomy' => 'cruise_duration',
				'field' => 'term_id',
				'terms' => $cruise_durations_array,
				'operator' => 'IN'
			);
		}

		if (!empty($cruise_tags_array)) {
			$args['tax_query'][] = 	array(
				'taxonomy' => 'cruise_tag',
				'field' => 'term_id',
				'terms' => $cruise_tags_array,
				'operator' => 'IN'
			);
		}

		if (!empty($cruise_facilities_array)) {
			$args['tax_query'][] = 	array(
				'taxonomy' => 'facility',
				'field' => 'id',
				'terms' => $cruise_facilities_array,
				'operator' => 'IN'
			);
		}

		$search_only_available = false;
		if (isset($search_args['search_only_available'])) {
			$search_only_available = $search_args['search_only_available'];
		}

		if (isset($search_args['date_from']))
			$args['byt_date_from'] = $search_args['date_from'];

		if (isset($search_args['date_to']))
			$args['byt_date_to'] = $search_args['date_to'];

		if (isset($search_args['cabins']))
			$args['byt_cabins'] = $search_args['cabins'];

		$args['search_only_available'] = $search_only_available;

		if (isset($search_args['prices'])) {
			$args['prices'] = $search_args['prices'];
			$args['price_range_bottom'] = $bookyourtravel_theme_globals->get_price_range_bottom();
			$args['price_range_increment'] = $bookyourtravel_theme_globals->get_price_range_increment();
			$args['price_range_count'] = $bookyourtravel_theme_globals->get_price_range_count();
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

		add_filter('posts_where', array($this, 'cruises_search_where'), 10, 2);
		add_filter('posts_fields', array($this, 'cruises_search_fields'), 10, 2);
		add_filter('posts_groupby', array($this, 'cruises_search_groupby'), 10, 2);
		add_filter('posts_join', array($this, 'cruises_search_join'), 10, 2);
		add_filter('posts_orderby', array($this, 'build_cruises_search_orderby'), 10, 2);

		$posts_query = new WP_Query($args);

		// echo $posts_query->request;

		if ($count_only) {
			$results = array(
				'total' => $posts_query->found_posts,
				'results' => null
			);
		} else {
			$results = array();

			if ($posts_query->have_posts()) {
				while ($posts_query->have_posts()) {
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

		remove_filter('posts_where', array($this, 'cruises_search_where'));
		remove_filter('posts_fields', array($this, 'cruises_search_fields'));
		remove_filter('posts_groupby', array($this, 'cruises_search_groupby'));
		remove_filter('posts_join', array($this, 'cruises_search_join'));
		remove_filter('posts_orderby', array($this, 'build_cruises_search_orderby'));

		return $results;
	}

	function get_cruise_booking($booking_id)
	{

		global $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;

		$table_name_schedule = BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE;
		$table_name_bookings = BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE;

		$sql = "SELECT 	DISTINCT bookings.*,
						cruises.post_title cruise_name,
						cabin_types.post_title cabin_type,
						bookings.total_price,
						bookings.cruise_id,
						bookings.cabin_type_id,
						'cruise_booking' entry_type
				FROM $table_name_bookings bookings ";

		if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations cruise_translations_default ON cruise_translations_default.element_type = 'post_cruise' AND cruise_translations_default.language_code='" . BookYourTravel_Theme_Utils::get_default_language() . "' AND cruise_translations_default.element_id = bookings.cruise_id ";
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations cruise_translations ON cruise_translations.element_type = 'post_cruise' AND cruise_translations.language_code='" . ICL_LANGUAGE_CODE . "' AND cruise_translations.trid = cruise_translations_default.trid ";
		}

		$sql .= " INNER JOIN $wpdb->posts cruises ON ";
		if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " cruises.ID = cruise_translations.element_id ";
		} else {
			$sql .= " cruises.ID = bookings.cruise_id ";
		}

		if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations cabin_translations_default ON cabin_translations_default.element_type = 'post_cabin_type' AND cabin_translations_default.language_code='" . BookYourTravel_Theme_Utils::get_default_language() . "' AND cabin_translations_default.element_id = bookings.cabin_type_id ";
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations cabin_translations ON cabin_translations.element_type = 'post_cabin_type' AND cabin_translations.language_code='" . ICL_LANGUAGE_CODE . "' AND cabin_translations.trid = cabin_translations_default.trid ";
		}

		$sql .= " INNER JOIN $wpdb->posts cabin_types ON ";
		if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " cabin_types.ID = cabin_translations.element_id ";
		} else {
			$sql .= " cabin_types.ID = bookings.cabin_type_id ";
		}

		$sql .= " WHERE cruises.post_status = 'publish' AND cabin_types.post_status = 'publish' AND bookings.Id = %d ";

		return $wpdb->get_row($wpdb->prepare($sql, $booking_id));
	}

	function delete_cruise_booking($booking_id)
	{

		global $wpdb;

		do_action('bookyourtravel_before_delete_cruise_booking', $booking_id);

		$booking = $this->get_cruise_booking($booking_id);
		if ($booking) {
			$cruise_id = BookYourTravel_Theme_Utils::get_default_language_post_id($booking->cruise_id, 'cruise');
			$this->clear_price_meta_cache($cruise_id);
		}

		$table_name_bookings = BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE;

		$sql = "DELETE FROM $table_name_bookings
				WHERE Id = %d";

		$wpdb->query($wpdb->prepare($sql, $booking_id));
	}

	function list_cruise_bookings($paged = null, $per_page = 0, $orderby = 'Id', $order = 'ASC', $search_term = null, $user_id = 0, $author_id = null, $cruise_id = null, $cabin_type_id = null)
	{

		global $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;

		$table_name_bookings = BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE;

		$sql = "SELECT 	DISTINCT bookings.*,
						cruises.post_title cruise_name,
						cabin_types.post_title cabin_type,
						bookings.total_price,
						bookings.cruise_id,
						bookings.cabin_type_id,
						'cruise_booking' entry_type
				FROM $table_name_bookings bookings  ";

		if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations cruise_translations_default ON cruise_translations_default.element_type = 'post_cruise' AND cruise_translations_default.language_code='" . BookYourTravel_Theme_Utils::get_default_language() . "' AND cruise_translations_default.element_id = bookings.cruise_id ";
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations cruise_translations ON cruise_translations.element_type = 'post_cruise' AND cruise_translations.language_code='" . ICL_LANGUAGE_CODE . "' AND cruise_translations.trid = cruise_translations_default.trid ";
		}

		$sql .= " INNER JOIN $wpdb->posts cruises ON ";
		if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " cruises.ID = cruise_translations.element_id ";
		} else {
			$sql .= " cruises.ID = bookings.cruise_id ";
		}

		if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations cabin_translations_default ON cabin_translations_default.element_type = 'post_cabin_type' AND cabin_translations_default.language_code='" . BookYourTravel_Theme_Utils::get_default_language() . "' AND cabin_translations_default.element_id = bookings.cabin_type_id ";
			$sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations cabin_translations ON cabin_translations.element_type = 'post_cabin_type' AND cabin_translations.language_code='" . ICL_LANGUAGE_CODE . "' AND cabin_translations.trid = cabin_translations_default.trid ";
		}

		$sql .= " INNER JOIN $wpdb->posts cabin_types ON ";
		if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('cruise') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$sql .= " cabin_types.ID = cabin_translations.element_id ";
		} else {
			$sql .= " cabin_types.ID = bookings.cabin_type_id ";
		}

		$sql .= " WHERE cruises.post_status = 'publish' AND cabin_types.post_status = 'publish' ";

		if ($search_term != null && !empty($search_term)) {
			$search_term_esc = "%" . $wpdb->esc_like($search_term) . "%";
			$sql .= $wpdb->prepare(" AND (LCASE(bookings.first_name) LIKE '%s' OR LCASE(bookings.last_name) LIKE '%s' OR cruises.post_title LIKE '%s') ", $search_term, $search_term, $search_term_esc);
		}

		if (isset($cruise_id) && $cruise_id > 0) {
			$sql .= $wpdb->prepare(" AND bookings.cruise_id = %d ", $cruise_id);
		}

		if (isset($cabin_type_id) && $cabin_type_id > 0) {
			$sql .= $wpdb->prepare(" AND bookings.cabin_type_id = %d ", $cabin_type_id);
		}

		if (isset($user_id) && $user_id > 0) {
			$sql .= $wpdb->prepare(" AND bookings.user_id=%d ", $user_id);
		}

		if (isset($author_id) && $author_id > 0) {
			$sql .= $wpdb->prepare(" AND cruises.post_author=%d ", $author_id);
		}

		if (!empty($orderby) && !empty($order)) {
			$sql .= "ORDER BY $orderby $order";
		}

		$sql_count = $sql;

		if (!empty($paged) && !empty($per_page)) {
			$offset = ($paged - 1) * $per_page;
			$sql .= $wpdb->prepare(" LIMIT %d, %d ", $offset, $per_page);
		}

		$results = array(
			'total' => $wpdb->query($sql_count),
			'results' => $wpdb->get_results($sql)
		);

		return $results;
	}

	function get_cruise_schedule_max_people($schedule_id, $cruise_id, $cabin_type_id, $date)
	{

		global $wpdb, $bookyourtravel_theme_globals;

		$table_name_schedule = BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE;
		$table_name_bookings = BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE;

		$cruise_obj = new BookYourTravel_Cruise(intval($cruise_id));
		$cruise_is_reservation_only = $cruise_obj->get_is_reservation_only();

		$sql = "SELECT 	schedule.max_people,
						(
							SELECT SUM(adults) + SUM(children) ct
							FROM $table_name_bookings bookings
							WHERE bookings.cruise_id = schedule.cruise_id AND bookings.cruise_date = %s ";

		if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$cruise_is_reservation_only) {

			$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
			if (!empty($completed_statuses)) {
				$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")";
			}
		}

		$sql .= "		) booking_count,
				IFNULL(cruise_duration_meta.meta_value, 1) duration_days
				FROM $table_name_schedule schedule
				LEFT JOIN $wpdb->postmeta cruise_duration_meta ON schedule.cruise_id = cruise_duration_meta.post_id AND cruise_duration_meta.meta_key = 'cruise_duration_days'
				WHERE schedule.Id=%d ";

		if ($cruise_obj->get_type_is_repeated() == 0) {
			$sql .= " AND schedule.start_date = %s ";
		} else {
			$sql .= " AND %s >= start_date AND (%s < end_date OR end_date IS NULL OR end_date = '0000-00-00 00:00:00') ";
		}

		$sql = $wpdb->prepare($sql, $date, $schedule_id, $date, $date);

		return $wpdb->get_row($sql);
	}

	function create_cruise_schedule($season_name, $cruise_id, $cabin_type_id, $cabin_count, $start_date, $price, $price_child, $end_date)
	{

		global $wpdb;

		$table_name_schedule = BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE;
		$table_name_bookings = BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE;

		$cruise_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cruise_id, 'cruise');

		$cruise_obj = new BookYourTravel_Cruise($cruise_id);

		// 0 - one off
		// 1 - daily
		// 2 - weekday
		// 3 - weekly
		// 4 - weekly, multidays
		$cruise_type_is_repeated = $cruise_obj->get_type_is_repeated();

		$this->clear_price_meta_cache($cruise_id);

		$cabin_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cabin_type_id, 'cabin_type');

		if ($cruise_type_is_repeated == 0) {
			$sql = "INSERT INTO $table_name_schedule
					(season_name, cruise_id, cabin_type_id, cabin_count, start_date, price, price_child, end_date)
					VALUES
					(%s, %d, %d, %d, %s, %f, %f, null);";
			$sql = $wpdb->prepare($sql, $season_name, $cruise_id, $cabin_type_id, $cabin_count, $start_date, $price, $price_child);
		} else {
			$sql = "INSERT INTO $table_name_schedule
					(season_name, cruise_id, cabin_type_id, cabin_count, start_date, price, price_child, end_date)
					VALUES
					(%s, %d, %d, %d, %s, %f, %f, %s);";
			$sql = $wpdb->prepare($sql, $season_name, $cruise_id, $cabin_type_id, $cabin_count, $start_date, $price, $price_child, $end_date);
		}

		$wpdb->query($sql);

		$schedule_id = $wpdb->insert_id;

		return $schedule_id;
	}

	function update_cruise_schedule($schedule_id, $season_name, $cruise_id, $cabin_type_id, $cabin_count, $start_date, $price, $price_child, $end_date)
	{

		global $wpdb;

		$table_name_schedule = BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE;
		$table_name_bookings = BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE;

		$cruise_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cruise_id, 'cruise');

		$cruise_obj = new BookYourTravel_Cruise($cruise_id);

		// 0 - one off
		// 1 - daily
		// 2 - weekday
		// 3 - weekly
		// 4 - weekly, multidays
		$cruise_type_is_repeated = $cruise_obj->get_type_is_repeated();

		$this->clear_price_meta_cache($cruise_id);

		$cabin_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cabin_type_id, 'cabin_type');

		if ($cruise_type_is_repeated == 0) {
			$sql = "UPDATE " . BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE . "
					SET season_name=%s, cruise_id=%d, cabin_type_id=%d, cabin_count=%d, start_date=%s, price=%f, price_child=%f, end_date=null
					WHERE Id=%d";
			$sql = $wpdb->prepare($sql, $season_name, $cruise_id, $cabin_type_id, $cabin_count, $start_date, $price, $price_child, $schedule_id);
		} else {
			$sql = "UPDATE " . BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE . "
					SET season_name=%s, cruise_id=%d, cabin_type_id=%d, cabin_count=%d, start_date=%s, price=%f, price_child=%f, end_date=%s
					WHERE Id=%d";
			$sql = $wpdb->prepare($sql, $season_name, $cruise_id, $cabin_type_id, $cabin_count, $start_date, $price, $price_child, $end_date, $schedule_id);
		}

		$wpdb->query($sql);
	}

	function delete_cruise_schedule($schedule_id)
	{

		global $wpdb;

		$table_name_schedule = BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE;

		$schedule = $this->get_cruise_schedule($schedule_id);

		$this->clear_price_meta_cache($schedule->cruise_id);

		$sql = "DELETE FROM $table_name_schedule
				WHERE Id = %d";

		$wpdb->query($wpdb->prepare($sql, $schedule_id));
	}

	function get_cruise_schedule($schedule_id)
	{

		global $wpdb, $bookyourtravel_theme_globals;

		$table_name_schedule = BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE;
		$table_name_bookings = BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE;

		$sql = "SELECT cruise_id FROM $table_name_schedule WHERE Id=%d";
		$cruise_id = $wpdb->get_var($wpdb->prepare($sql, $schedule_id));

		$cruise_obj = new BookYourTravel_Cruise(intval($cruise_id));
		$cruise_is_reservation_only = $cruise_obj->get_is_reservation_only();

		$sql = "SELECT 	schedule.*,
						cruises.post_title cruise_name,
						cabin_types.post_title cabin_type,
						(
							SELECT COUNT(*) ct
							FROM $table_name_bookings bookings
							WHERE bookings.cruise_id = schedule.cruise_id ";

		if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$cruise_is_reservation_only) {

			$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
			if (!empty($completed_statuses)) {
				$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")";
			}
		}

		$sql .= "		) has_bookings,
						IFNULL(cruise_price_meta.meta_value, 0) cruise_is_price_per_person,
						'cruise_schedule' entry_type
				FROM $table_name_schedule schedule
				INNER JOIN $wpdb->posts cruises ON cruises.ID = schedule.cruise_id
				INNER JOIN $wpdb->posts cabin_types ON cabin_types.ID = schedule.cabin_type_id
				LEFT JOIN $wpdb->postmeta cruise_price_meta ON cruises.ID = cruise_price_meta.post_id AND cruise_price_meta.meta_key = 'cruise_is_price_per_person'
				WHERE schedule.Id=%d AND cruises.post_status = 'publish' AND cabin_types.post_status = 'publish'  ";

		$sql = $wpdb->prepare($sql, $schedule_id);
		return $wpdb->get_row($sql);
	}

	function list_cruise_schedules($paged = null, $per_page = 0, $orderby = 'Id', $order = 'ASC', $day = 0, $month = 0, $year = 0, $cruise_id = 0, $cabin_type_id = 0, $search_term = '', $author_id = null)
	{

		global $wpdb, $bookyourtravel_theme_globals;

		$cruise_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cruise_id, 'cruise');
		$cabin_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($cabin_type_id, 'cabin_type');

		$cruise_obj = new BookYourTravel_Cruise(intval($cruise_id));
		$cruise_is_reservation_only = $cruise_obj->get_is_reservation_only();

		$filter_date = '';
		if ($day > 0 || $month > 0 || $year) {
			$filter_date .= ' AND ( 1=1 ';
			if ($day > 0)
				$filter_date .= $wpdb->prepare(" AND DAY(start_date) = %d ", $day);
			if ($month > 0)
				$filter_date .= $wpdb->prepare(" AND MONTH(start_date) = %d ", $month);
			if ($year > 0)
				$filter_date .= $wpdb->prepare(" AND YEAR(start_date) = %d ", $year);
			$filter_date .= ')';
		}

		$table_name_schedule = BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE;
		$table_name_bookings = BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE;

		$sql = "SELECT 	schedule.*,
						cruises.post_title cruise_name,
						cabin_types.post_title cabin_type,
						(
							SELECT COUNT(*) ct
							FROM $table_name_bookings bookings
							WHERE bookings.cruise_id = schedule.cruise_id ";

		if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$cruise_is_reservation_only) {

			$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
			if (!empty($completed_statuses)) {
				$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")";
			}
		}

		$sql .= "		) has_bookings,
						IFNULL(cruise_price_meta.meta_value, 0) cruise_is_price_per_person,
						'cruise_schedule' entry_type
				FROM $table_name_schedule schedule
				INNER JOIN $wpdb->posts cruises ON cruises.ID = schedule.cruise_id
				INNER JOIN $wpdb->posts cabin_types ON cabin_types.ID = schedule.cabin_type_id
				LEFT JOIN $wpdb->postmeta cruise_price_meta ON cruises.ID = cruise_price_meta.post_id AND cruise_price_meta.meta_key = 'cruise_is_price_per_person'
				WHERE cruises.post_status = 'publish' AND cabin_types.post_status = 'publish' ";

		if ($cruise_id > 0) {
			$sql .= $wpdb->prepare(" AND schedule.cruise_id=%d ", $cruise_id);
		}

		if ($cabin_type_id > 0) {
			$sql .= $wpdb->prepare(" AND schedule.cabin_type_id=%d ", $cabin_type_id);
		}

		if (isset($author_id)) {
			$sql .= $wpdb->prepare(" AND cruises.post_author=%d ", $author_id);
		}

		if ($filter_date != null && !empty($filter_date)) {
			$sql .= $filter_date;
		}

		if (!empty($orderby) & !empty($order)) {
			$sql .= " ORDER BY $orderby $order ";
		}

		$sql_count = $sql;

		if (!empty($paged) && !empty($per_page)) {
			$offset = ($paged - 1) * $per_page;
			$sql .= $wpdb->prepare(" LIMIT %d, %d ", $offset, $per_page);
		}

		$results = array(
			'total' => $wpdb->query($sql_count),
			'results' => $wpdb->get_results($sql)
		);

		return $results;
	}

	function get_cruise_schedule_price($schedule_id, $is_child_price)
	{

		global $wpdb;

		$table_name_schedule = BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE;

		$sql = "SELECT " . ($is_child_price ? "schedule.price_child" : "schedule.price") . "
				FROM $table_name_schedule schedule
				WHERE id=%d ";

		$price = $wpdb->get_var($wpdb->prepare($sql, $schedule_id));

		return $price;
	}


	function get_cruise_min_price($cruise_id, $cabin_type_id = 0, $date = null)
	{

		global $wpdb;

		$cruise_obj = new BookYourTravel_Cruise(intval($cruise_id));
		$cruise_id = $cruise_obj->get_base_id();

		if ($cabin_type_id > 0) {
			$cabin_type_obj = new BookYourTravel_Cabin_Type(intval($cabin_type_id));
			$cabin_type_id = $cabin_type_obj->get_base_id();
		}

		$min_price = -1;

		if ($cabin_type_id == 0) {

			$last_cache_minutes = 0;
			if ($cruise_obj->is_custom_field_set('_cruise_price_cache_time', false)) {
				$last_cache_seconds = intval($cruise_obj->get_custom_field('_cruise_price_cache_time', false));
				$current_seconds = time();
				if ($last_cache_seconds > 0) {
					$last_cache_minutes = ($current_seconds - $last_cache_seconds) / (60);
				}
			}

			if ($last_cache_minutes > 0 && $last_cache_minutes <= 10) {
				$min_price = floatval($cruise_obj->get_custom_field('_cruise_price_cache', false));
			}
		}

		if ($min_price == -1) {

			$cruise_type_is_repeated = $cruise_obj->get_type_is_repeated();

			if (!isset($date))
				$date = date('Y-m-d', time());

			$sql = "SELECT MIN(schedule.price) min_price
					FROM " . BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE . " schedule
					WHERE 1=1 ";

			$sql .= $wpdb->prepare(" AND schedule.cruise_id = %d ", $cruise_id);

			if ($cabin_type_id > 0) {
				$sql .= $wpdb->prepare(" AND schedule.cabin_type_id=%d ", $cabin_type_id);
			}

			if (isset($date)) {
				$sql .= $wpdb->prepare("  AND DATE(schedule.start_date) >= %s ", $date);
				if ($cruise_type_is_repeated > 0) {
					$sql .= $wpdb->prepare(" AND DATE(%s) < schedule.end_date ", $date);
				}
			}

			$sql .= " GROUP BY schedule.Id
					  ORDER BY min_price ASC
					  LIMIT 1	";

			$min_price = $wpdb->get_var($sql);

			if ($cabin_type_id == 0) {
				update_post_meta($cruise_id, '_cruise_price_cache', $min_price);
				update_post_meta($cruise_id, '_cruise_price_cache_time', time());
			}
		}

		return $min_price;
	}

	function clear_price_meta_cache($cruise_id)
	{
		global $wpdb;
		$search_term = "%cruise_min_price%";
		$sql = $wpdb->prepare("DELETE FROM $wpdb->postmeta WHERE post_id=%d AND meta_key LIKE '%s'", $cruise_id, $search_term);
		$wpdb->query($sql);

		$cruise_obj = new BookYourTravel_Cruise($cruise_id);
		$location_ids = $cruise_obj->get_locations();

		if ($location_ids && count($location_ids) > 0) {
			for ($i = 0; $i < count($location_ids); $i++) {
				$location_id = $location_ids[$i];

				$search_term = "%cruises_min_price%";
				$sql = $wpdb->prepare("DELETE FROM $wpdb->postmeta WHERE post_id=%d AND meta_key LIKE '%s'", $location_id, $search_term);
				$wpdb->query($sql);
			}
		}
	}
}

global $bookyourtravel_cruise_helper;
// store the instance in a variable to be retrieved later and call init
$bookyourtravel_cruise_helper = BookYourTravel_Cruise_Helper::get_instance();
$bookyourtravel_cruise_helper->init();

add_shortcode('byt_cruise_card', 'byt_cruise_card_shortcode');
function byt_cruise_card_shortcode($atts)
{

	global $cruise_item_args;

	extract(shortcode_atts(array(
		'cruise_id' => 0,
		'show_fields' => 'title,image,actions',
		'css' => ''
	), $atts));

	$show_fields = explode(',', $show_fields);

	$cruise_item_args = array();
	$cruise_item_args['cruise_id'] = $cruise_id;
	if ($cruise_id > 0) {
		$cruise_item_args['post']	= get_post($cruise_id);
	}
	$cruise_item_args['hide_title'] = !in_array('title', $show_fields);
	$cruise_item_args['hide_image'] = !in_array('image', $show_fields);
	$cruise_item_args['hide_actions'] = !in_array('actions', $show_fields);
	$cruise_item_args['hide_description'] = !in_array('description', $show_fields);
	$cruise_item_args['hide_rating'] = !in_array('rating', $show_fields);
	$cruise_item_args['hide_price'] = !in_array('price', $show_fields);
	$cruise_item_args['hide_address'] = !in_array('address', $show_fields);
	$cruise_item_args['item_class'] = 'single-card';

	$output = '';

	ob_start();
	get_template_part('includes/parts/cruise/cruise', 'item');

	$css_class = $css;
	if (function_exists('vc_shortcode_custom_css_class')) {
		$css_class = vc_shortcode_custom_css_class($css, ' ');
	}

	$output = sprintf('<div class="widget widget-sidebar %s">', $css_class);
	$output .= ob_get_clean();
	$output .= "</div>";

	wp_reset_postdata();
	return $output;
}
