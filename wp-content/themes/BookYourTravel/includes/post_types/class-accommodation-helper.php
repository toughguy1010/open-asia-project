<?php
/**
 * BookYourTravel_Post_Helper class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-accommodation.php');

class BookYourTravel_Accommodation_Helper extends BookYourTravel_BaseSingleton {

    /**
     * Member variables
     */
    private $enable_accommodations;

    private $accommodation_custom_meta_fields;
    private $accommodation_custom_meta_tabs;

    // used by frontend submit {
    private $accommodation_vacancy_fields;
    private $accommodation_booking_fields;
    // }

    private $accommodation_list_custom_meta_fields;
    private $accommodation_list_custom_meta_tabs;
    private $accommodation_list_meta_box;

    /**
     * Constructor
     */
    protected function __construct() {

        global $bookyourtravel_theme_globals;
        $this->enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();

        // our parent class might contain shared code in its constructor
        parent::__construct();
    }

    /**
     * Initialize class
     */
    public function init() {

        add_action('bookyourtravel_initialize_post_types', array($this, 'initialize_post_type'), 0);

        if ($this->enable_accommodations) {
            add_action('bookyourtravel_after_delete_accommodation', array($this, 'after_delete_accommodation'), 10, 1);
            add_action('bookyourtravel_save_accommodation', array($this, 'save_accommodation'), 10, 1);
            add_action('admin_init', array($this, 'remove_unnecessary_meta_boxes'));
            add_action('admin_init', array($this, 'accommodation_admin_init'));
            add_action('manage_accommodation_posts_custom_column', array($this, 'columns_content'), 10, 2);

            add_filter('manage_edit-accommodation_columns', array($this, 'manage_edit_accommodation_columns'), 10, 1);
            add_filter('manage_accommodation_posts_columns', array($this, 'columns_head'));
            add_filter('bookyourtravel_custom_taxonomy_list', array($this, 'custom_taxonomy_list'), 10, 1);

            add_action('bookyourtravel_before_single_accommodation_content', array($this, 'before_single_accommodation_content'));

            add_action('booking_form_details_accommodation_core_fields', array($this, 'booking_form_details_core_fields'));
            add_action('booking_form_confirmation_accommodation_core_fields', array($this, 'booking_form_confirmation_core_fields'));
            add_action('booking_form_calendar_accommodation_after_calendar_control', array($this, 'booking_form_calendar_after_calendar_control'));
            add_action('booking_form_calendar_accommodation_start_summary_control', array($this, 'booking_form_calendar_start_summary_control'));
            add_action('booking_form_calendar_accommodation_booking_terms', array($this, 'booking_form_calendar_booking_terms'));
            add_action('booking_form_calendar_accommodation_after_price_breakdown', array($this, 'booking_form_calendar_after_price_breakdown'));

            /* ajax requests */
            add_action('bookyourtravel_initialize_ajax', array($this, 'initialize_ajax'), 0);

            $this->initialize_meta_fields();
        }
    }

    function custom_taxonomy_list($taxonomies) {
        if ($this->enable_accommodations) {
            $taxonomies[] = "accommodation_type";
            $taxonomies[] = "acc_tag";
        }

        return $taxonomies;
    }

    function initialize_meta_fields() {

        global $bookyourtravel_room_type_helper, $bookyourtravel_theme_globals;

        wp_reset_postdata();

        $days_of_week = BookYourTravel_Theme_Utils::get_php_days_of_week();

        $stay_start_days = array();
        $stay_start_days[] = array('value' => -1, 'label' => __('Any day', 'bookyourtravel'));

        foreach ($days_of_week as $key => $label) {
            $stay_start_days[] = array('value' => $key, 'label' => $label);
        }

        $rent_types = array();
        $rent_types[] = array('value' => 0, 'label' => __('Daily', 'bookyourtravel'));
        $rent_types[] = array('value' => 1, 'label' => __('Weekly', 'bookyourtravel'));
        $rent_types[] = array('value' => 2, 'label' => __('Monthly', 'bookyourtravel'));

        $feature_displays = array();
        $feature_displays[] = array('value' => 'gallery', 'label' => esc_html__('Image gallery', 'bookyourtravel'));
        $feature_displays[] = array('value' => 'image', 'label' => esc_html__('Featured image', 'bookyourtravel'));

        $this->accommodation_custom_meta_tabs = array(
            array(
                'label' => esc_html__('General', 'bookyourtravel'),
                'id' => '_accommodation_general_tab',
                'class' => 'general_tab',
            ),
            array(
                'label' => esc_html__('Booking', 'bookyourtravel'),
                'id' => '_accommodation_booking_tab',
                'class' => 'booking_tab',
            ),
            array(
                'label' => esc_html__('Gallery', 'bookyourtravel'),
                'id' => '_accommodation_gallery_tab',
                'class' => 'gallery_tab',
            ),
            array(
                'label' => esc_html__('Content', 'bookyourtravel'),
                'id' => '_accommodation_content_tab',
                'class' => 'content_tab',
            ),
        );

        $this->accommodation_custom_meta_tabs = apply_filters('bookyourtravel_accommodation_custom_meta_tabs', $this->accommodation_custom_meta_tabs);

        $this->accommodation_custom_meta_fields = array(
            array(
                'label' => esc_html__('General description', 'bookyourtravel'),
                'desc' => esc_html__('General description', 'bookyourtravel'),
                'id' => 'accommodation_general_description',
                'type' => 'editor',
                'admin_tab_id' => 'content_tab',
            ),
            array(
                'label' => esc_html__('Short description', 'bookyourtravel'),
                'desc' => esc_html__('Short description is shown in the right sidebar of a single item and as a description of an item card when the item is displayed in lists', 'bookyourtravel'),
                'id' => 'accommodation_short_description',
                'type' => 'editor',
                'admin_tab_id' => 'content_tab',
            ),
            array( // Post ID select box
                'label' => esc_html__('Use referral url?', 'bookyourtravel'), // <label>
                'desc' => esc_html__('List on list pages and widgets but link to an external website via referral url.', 'bookyourtravel'), // description
                'id' => 'accommodation_use_referral_url', // field id and name
                'type' => 'checkbox', // type of field
                'admin_tab_id' => 'general_tab',
            ),
            array(
                'label' => esc_html__('Referral url', 'bookyourtravel'),
                'desc' => esc_html__('Referral url to take visitors to when item is clicked on on list pages and widgets (to use for example for affiliate links).', 'bookyourtravel'),
                'id' => 'accommodation_referral_url',
                'type' => 'text',
                'admin_tab_id' => 'general_tab',
                'field_container_class' => 'referral_url',
            ),
            array(
                'label' => esc_html__('Referral price', 'bookyourtravel'),
                'desc' => esc_html__('Referral price to display for item when item is listed on list pages and widgets.', 'bookyourtravel'),
                'id' => 'accommodation_referral_price',
                'type' => 'text',
                'admin_tab_id' => 'general_tab',
                'field_container_class' => 'referral_url',
            ),
            array( // Post ID select box
                'label' => esc_html__('Is featured', 'bookyourtravel'), // <label>
                'desc' => esc_html__('Show in lists where only featured items are shown.', 'bookyourtravel'), // description
                'id' => 'accommodation_is_featured', // field id and name
                'type' => 'checkbox', // type of field
                'admin_tab_id' => 'general_tab',
            ),
            array(
                'label' => esc_html__('Ribbon text', 'bookyourtravel'),
                'desc' => esc_html__('If specified, this text will appear in a ribbon placed on top of the item in lists when card display mode is used.', 'bookyourtravel'),
                'id' => 'accommodation_ribbon_text',
                'type' => 'text',
                'admin_tab_id' => 'content_tab',
            ),
            array( // Post ID select box
                'label' => esc_html__('Hide inquiry form?', 'bookyourtravel'), // <label>
                'desc' => esc_html__('Do you want to not show the inquiry form and inquiry button in right hand sidebar for this accommodation?', 'bookyourtravel'), // description
                'id' => 'accommodation_hide_inquiry_form', // field id and name
                'type' => 'checkbox', // type of field
                'admin_tab_id' => 'general_tab',
            ),
            array(
                'label' => esc_html__('Contact email addresses', 'bookyourtravel'),
                'desc' => esc_html__('Override admin contact email address by specifying contact email addresses for this accommodation. If specifying multiple email addresses, separate each address with a semi-colon ;', 'bookyourtravel'),
                'id' => 'accommodation_contact_email',
                'type' => 'text',
                'admin_tab_id' => 'general_tab',
            ),
            array(
                'label' => esc_html__('Website address', 'bookyourtravel'),
                'desc' => '',
                'id' => 'accommodation_website_address',
                'type' => 'text',
                'admin_tab_id' => 'general_tab',
            ),
            array( // Taxonomy Select box
                'label' => esc_html__('Accommodation tags', 'bookyourtravel'), // <label>
                // the description is created in the callback function with a link to Manage the taxonomy terms
                'id' => 'acc_tag', // field id and name, needs to be the exact name of the taxonomy
                'type' => 'tax_checkboxes', // type of field
                'admin_tab_id' => 'general_tab',
            ),
            array( // Taxonomy Select box
                'label' => esc_html__('Accommodation type', 'bookyourtravel'), // <label>
                // the description is created in the callback function with a link to Manage the taxonomy terms
                'id' => 'accommodation_type', // field id and name, needs to be the exact name of the taxonomy
                'type' => 'tax_select', // type of field
                'admin_tab_id' => 'general_tab',
            ),
            array( // Taxonomy Select box
                'label' => esc_html__('Facilities', 'bookyourtravel'), // <label>
                // the description is created in the callback function with a link to Manage the taxonomy terms
                'id' => 'facility', // field id and name, needs to be the exact name of the taxonomy
                'type' => 'tax_checkboxes', // type of field
                'admin_tab_id' => 'general_tab',
            ),
            array(
                'label' => esc_html__('Address', 'bookyourtravel'),
                'desc' => '',
                'id' => 'accommodation_address',
                'type' => 'text',
                'admin_tab_id' => 'general_tab',
            ),
            array( // Post ID select box
                'label' => esc_html__('Location', 'bookyourtravel'), // <label>
                'desc' => '', // description
                'id' => 'accommodation_location_post_id', // field id and name
                'type' => 'post_select', // type of field
                'post_type' => array('location'), // post types to display, options are prefixed with their post type
                'admin_tab_id' => 'general_tab',
            ),
            array(
                'label' => esc_html__('Latitude coordinates', 'bookyourtravel'),
                'desc' => esc_html__('Latitude coordinates for use with google map (leave blank to not use)', 'bookyourtravel'),
                'id' => 'accommodation_latitude',
                'type' => 'text',
                'admin_tab_id' => 'general_tab',
            ),
            array(
                'label' => esc_html__('Longitude coordinates', 'bookyourtravel'),
                'desc' => esc_html__('Longitude coordinates for use with google map (leave blank to not use)', 'bookyourtravel'),
                'id' => 'accommodation_longitude',
                'type' => 'text',
                'admin_tab_id' => 'general_tab',
            ),                 
            array(
                'label' => esc_html__('Force disable single view calendar?', 'bookyourtravel'), // <label>
                'desc' => esc_html__('If this option is checked, then this accommodation will not display a calendar in the availability tab regardless of whether it has valid vacancies or not.', 'bookyourtravel'), // description
                'id' => 'accommodation_force_disable_calendar', // field id and name
                'type' => 'checkbox', // type of field
                'admin_tab_id' => 'booking_tab',
            ),                  
            array(
                'label' => esc_html__('Is for reservation only?', 'bookyourtravel'), // <label>
                'desc' => esc_html__('If this option is checked, then this particular accommodation will not be processed via WooCommerce even if WooCommerce is in use.', 'bookyourtravel'), // description
                'id' => 'accommodation_is_reservation_only', // field id and name
                'type' => 'checkbox', // type of field
                'admin_tab_id' => 'booking_tab',
            ),
            array( // Post ID select box
                'label' => esc_html__('Disable room types?', 'bookyourtravel'), // <label>
                'desc' => esc_html__('Is the accommodation bookable as one entity (lodges, houses etc) or does it provide individual room booking (hotel/motel style)?', 'bookyourtravel'), // description
                'id' => 'accommodation_disabled_room_types', // field id and name
                'type' => 'checkbox', // type of field
                'admin_tab_id' => 'booking_tab',
            ),
            array( // Post ID select box
                'label' => esc_html__('Associated room types?', 'bookyourtravel'), // <label>
                'desc' => '', // description
                'id' => 'room_types', // field id and name
                'type' => 'post_checkboxes', // type of field
                'post_type' => array('room_type'), // post types to display, options are prefixed with their post type
                'admin_tab_id' => 'booking_tab',
                'field_container_class' => 'room_types',
            ),
            array(
                'label' => esc_html__('Rent type', 'bookyourtravel'),
                'desc' => esc_html__('Are you renting this accommodation on a daily (default), weekly or monthly basis?', 'bookyourtravel'),
                'id' => 'accommodation_rent_type',
                'type' => 'select',
                'options' => $rent_types,
                'admin_tab_id' => 'booking_tab',
            ),
            array(
                'label' => esc_html__('Priced per person?', 'bookyourtravel'), // <label>
                'desc' => esc_html__('Is price calculated per person (adult, child)? If not then calculations are done per room or per entity (if room types are disabled).', 'bookyourtravel'), // description
                'id' => 'accommodation_is_price_per_person', // field id and name
                'type' => 'checkbox', // type of field
                'admin_tab_id' => 'booking_tab',
            ),
            array(
                'label' => esc_html__('Minimum days stay', 'bookyourtravel'),
                'desc' => esc_html__('What is the minimum number of days accommodation can be booked for?', 'bookyourtravel'),
                'id' => 'accommodation_min_days_stay',
                'type' => 'slider',
                'min' => apply_filters('bookyourtravel_accommodation_min_days_stay_min', '1'),
                'max' => apply_filters('bookyourtravel_accommodation_min_days_stay_max', '30'),
                'step' => '1',
                'admin_tab_id' => 'booking_tab',
            ),
            array(
                'label' => esc_html__('Maximum days stay', 'bookyourtravel'),
                'desc' => esc_html__('What is the maximum number of days accommodation can be booked for? Leave as 0 to ignore.', 'bookyourtravel'),
                'id' => 'accommodation_max_days_stay',
                'type' => 'slider',
                'min' => apply_filters('bookyourtravel_accommodation_max_days_stay_min', '0'),
                'max' => apply_filters('bookyourtravel_accommodation_max_days_stay_max', '30'),
                'step' => '1',
                'admin_tab_id' => 'booking_tab',
            ),
            array(
                'label' => esc_html__('Allowed check-in day of the week for stay', 'bookyourtravel'),
                'desc' => esc_html__('What is the day of the week that visitors can check-in to the accommodation on? Do not select to ignore.', 'bookyourtravel'),
                'id' => 'accommodation_checkin_week_day',
                'type' => 'select',
                'options' => $stay_start_days,
                'admin_tab_id' => 'booking_tab',
            ),
            array(
                'label' => esc_html__('Allowed check-out day of the week for stay', 'bookyourtravel'),
                'desc' => esc_html__('What is the day of the week that visitors can check-out from the accommodation on? Do not select to ignore.', 'bookyourtravel'),
                'id' => 'accommodation_checkout_week_day',
                'type' => 'select',
                'options' => $stay_start_days,
                'admin_tab_id' => 'booking_tab',
            ),
            array(
                'label' => esc_html__('Minimum adult count', 'bookyourtravel'),
                'desc' => esc_html__('What is the fewest number of adults required in the accommodation?', 'bookyourtravel'),
                'id' => 'accommodation_min_count',
                'type' => 'slider',
                'min' => apply_filters('bookyourtravel_accommodation_min_count_min', '1'),
                'max' => apply_filters('bookyourtravel_accommodation_min_count_max', '30'),
                'step' => '1',
                'admin_tab_id' => 'booking_tab',
                'field_container_class' => 'not_room_types',
            ),
            array(
                'label' => esc_html__('Maximum adult count', 'bookyourtravel'),
                'desc' => esc_html__('How many adults are allowed in the accommodation?', 'bookyourtravel'),
                'id' => 'accommodation_max_count',
                'type' => 'slider',
                'min' => apply_filters('bookyourtravel_accommodation_max_count_max', '1'),
                'max' => apply_filters('bookyourtravel_accommodation_max_count_max', '30'),
                'step' => '1',
                'std' => '10',
                'admin_tab_id' => 'booking_tab',
                'field_container_class' => 'not_room_types',
            ),
            array(
                'label' => esc_html__('Minimum child count', 'bookyourtravel'),
                'desc' => esc_html__('What is the fewest number of children required in the accommodation?', 'bookyourtravel'),
                'id' => 'accommodation_min_child_count',
                'type' => 'slider',
                'min' => apply_filters('bookyourtravel_accommodation_min_child_count_min', '0'),
                'max' => apply_filters('bookyourtravel_accommodation_min_child_count_max', '30'),
                'step' => '1',
                'admin_tab_id' => 'booking_tab',
                'field_container_class' => 'not_room_types',
            ),
            array(
                'label' => esc_html__('Maximum child count', 'bookyourtravel'),
                'desc' => esc_html__('How many children are allowed in the accommodation?', 'bookyourtravel'),
                'id' => 'accommodation_max_child_count',
                'type' => 'slider',
                'min' => apply_filters('bookyourtravel_accommodation_max_child_count_min', '1'),
                'max' => apply_filters('bookyourtravel_accommodation_max_child_count_max', '30'),
                'step' => '1',
                'std' => '10',
                'admin_tab_id' => 'booking_tab',
                'field_container_class' => 'not_room_types',
            ),
            array(
                'label' => esc_html__('Count children stay free', 'bookyourtravel'),
                'desc' => esc_html__('How many kids stay free before we charge a fee?', 'bookyourtravel'),
                'id' => 'accommodation_count_children_stay_free',
                'type' => 'slider',
                'min' => apply_filters('bookyourtravel_accommodation_count_children_stay_free_min', '0'),
                'max' => apply_filters('bookyourtravel_accommodation_count_children_stay_free_max', '5'),
                'step' => '1',
                'admin_tab_id' => 'booking_tab',
                'field_container_class' => 'per_person',
            ),
            array(
                'label' => esc_html__('Availability extra text', 'bookyourtravel'),
                'desc' => esc_html__('Extra text shown on availability tab above the book now area.', 'bookyourtravel'),
                'id' => 'accommodation_availability_text',
                'type' => 'textarea',
                'admin_tab_id' => 'booking_tab',
            ),
            array( // Select box
                'label' => esc_html__('Displayed featured element', 'bookyourtravel'), // <label>
                // the description is created in the callback function with a link to Manage the taxonomy terms
                'id' => 'accommodation_displayed_featured_element', // field id and name, needs to be the exact name of the taxonomy
                'type' => 'select', // type of field
                'options' => $feature_displays,
                'std' => 'gallery',
                'admin_tab_id' => 'gallery_tab',
            ),
            array( // Repeatable & Sortable Text inputs
                'label' => esc_html__('Gallery images', 'bookyourtravel'), // <label>
                'desc' => esc_html__('A collection of images to be used in slider/gallery on single page', 'bookyourtravel'), // description
                'id' => 'accommodation_images', // field id and name
                'type' => 'repeatable', // type of field
                'sanitizer' => array( // array of sanitizers with matching kets to next array
                    'featured' => 'meta_box_santitize_boolean',
                    'title' => 'sanitize_text_field',
                    'desc' => 'wp_kses_data',
                ),
                'repeatable_fields' => array( // array of fields to be repeated
                    array( // Image ID field
                        'label' => esc_html__('Image', 'bookyourtravel'), // <label>
                        'id' => 'image', // field id and name
                        'type' => 'image', // type of field
                    ),
                ),
                'admin_tab_id' => 'gallery_tab',
            ),
        );

        if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
            array_unshift($this->accommodation_custom_meta_fields, array( // Select box
                'label' => esc_html__('Static "From" price', 'bookyourtravel'), // <label>
                'desc' => esc_html__('This price is shown in grids when the "Show static from prices in grid displays?" in enabled in theme configuration settings', 'bookyourtravel'), // description
                'id' => 'accommodation_static_from_price', // field id and name, needs to be the exact name of the taxonomy
                'type' => 'text', // type of field
                'std' => '0',
                'admin_tab_id' => 'booking_tab'
            ));
        }

        if ($bookyourtravel_theme_globals->enable_deposit_payments()) {
            array_unshift($this->accommodation_custom_meta_fields, array( // Select box
                'label' => esc_html__('Deposit percentage', 'bookyourtravel'), // <label>
                'desc' => esc_html__('% deposit charge', 'bookyourtravel'), // description
                'id' => 'accommodation_deposit_percentage', // field id and name, needs to be the exact name of the taxonomy
                'type' => 'number', // type of field
                'std' => '100',
                'min' => '0',
                'max' => '100',
                'step' => '1',
                'admin_tab_id' => 'booking_tab',
                'field_container_class' => 'deposit_percentage'
            ));
        }

        if (!$bookyourtravel_theme_globals->disable_star_count('accommodation')) {
            $this->accommodation_custom_meta_fields[] = array(
                'label' => esc_html__('Star count', 'bookyourtravel'),
                'desc' => '',
                'id' => 'accommodation_star_count',
                'type' => 'slider',
                'min' => '0',
                'max' => apply_filters('bookyourtravel_accommodation_star_count_max', '5'),
                'step' => '1',
                'admin_tab_id' => 'general_tab',
            );
        }

        global $default_accommodation_extra_fields;

        $accommodation_extra_fields = of_get_option('accommodation_extra_fields');
        if (!is_array($accommodation_extra_fields) || count($accommodation_extra_fields) == 0) {
            $accommodation_extra_fields = $default_accommodation_extra_fields;
        } else {
            $accommodation_extra_fields = BookYourTravel_Theme_Of_Default_Fields::merge_fields_and_defaults($accommodation_extra_fields, $default_accommodation_extra_fields);
        }

        foreach ($accommodation_extra_fields as $accommodation_extra_field) {
            $field_is_hidden = isset($accommodation_extra_field['hide']) ? intval($accommodation_extra_field['hide']) : 0;

            if (!$field_is_hidden) {
                $extra_field = null;
                $field_label = isset($accommodation_extra_field['label']) ? $accommodation_extra_field['label'] : '';
                $field_id = isset($accommodation_extra_field['id']) ? $accommodation_extra_field['id'] : '';
                $field_type = isset($accommodation_extra_field['type']) ? $accommodation_extra_field['type'] : '';
                $field_desc = isset($accommodation_extra_field['desc']) ? $accommodation_extra_field['desc'] : '';

                $field_options_array = null;
                if (isset($accommodation_extra_field['options'])) {
                    if (is_array($accommodation_extra_field['options'])) {
                        $field_options_array = $accommodation_extra_field['options'];
                    } else {
                        $field_options = isset($accommodation_extra_field['options']) ? trim($accommodation_extra_field['options']) : '';
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
                                    'label' => $option_text,
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
                } else if ($field_type == 'slider') {
                    $min = isset($accommodation_extra_field['min']) && strlen($accommodation_extra_field['min']) > 0 ? intval($accommodation_extra_field['min']) : 1;
                    $max = isset($accommodation_extra_field['max']) && strlen($accommodation_extra_field['max']) > 0 ? intval($accommodation_extra_field['max']) : 10;
                    $step = isset($accommodation_extra_field['step']) && strlen($accommodation_extra_field['step']) > 0 ? intval($accommodation_extra_field['step']) : 1;
                }

                if (!empty($field_label) && !empty($field_id) && !empty($field_type)) {
                    $extra_field = array(
                        'label' => $field_label,
                        'desc' => $field_desc,
                        'id' => 'accommodation_' . $field_id,
                        'type' => $field_type,
                        'admin_tab_id' => 'content_tab',
                        'options' => $field_options_array,
                        'min' => $min,
                        'max' => $max,
                        'step' => $step,
                    );
                }

                if ($extra_field) {
                    $this->accommodation_custom_meta_fields[] = $extra_field;
                }

            }
        }

        $this->accommodation_custom_meta_fields = apply_filters('bookyourtravel_accommodation_custom_meta_fields', $this->accommodation_custom_meta_fields);

        $sort_by_columns = array();
        $sort_by_columns[] = array('value' => 'title', 'label' => esc_html__('Accommodation title', 'bookyourtravel'));
        $sort_by_columns[] = array('value' => 'ID', 'label' => esc_html__('Accommodation ID', 'bookyourtravel'));
        $sort_by_columns[] = array('value' => 'date', 'label' => esc_html__('Publish date', 'bookyourtravel'));
        $sort_by_columns[] = array('value' => 'rand', 'label' => esc_html__('Random', 'bookyourtravel'));
        $sort_by_columns[] = array('value' => 'comment_count', 'label' => esc_html__('Comment count', 'bookyourtravel'));
        $sort_by_columns[] = array('value' => 'price', 'label' => esc_html__('Price', 'bookyourtravel'));
        $sort_by_columns[] = array('value' => 'menu_order', 'label' => esc_html__('Order attribute', 'bookyourtravel'));
        
        $sort_by_columns = apply_filters('bookyourtravel_accommodation_list_sort_by_columns', $sort_by_columns);

        $this->accommodation_list_custom_meta_tabs = array(
            array(
                'label' => esc_html__('Content', 'bookyourtravel'),
                'id' => '_accommodation_list_filter_tab',
                'class' => 'filter_tab',
            ),
            array(
                'label' => esc_html__('Display settings', 'bookyourtravel'),
                'id' => '_accommodation_list_item_settings_tab',
                'class' => 'item_settings_tab',
            ),
        );

        $this->accommodation_list_custom_meta_fields = array(
            array( // Taxonomy Select box
                'label' => esc_html__('Accomodation type', 'bookyourtravel'), // <label>
                // the description is created in the callback function with a link to Manage the taxonomy terms
                'id' => 'accommodation_type', // field id and name, needs to be the exact name of the taxonomy
                'type' => 'tax_checkboxes', // type of field
                'admin_tab_id' => 'filter_tab',
            ),
            array( // Taxonomy Select box
                'label' => esc_html__('Accommodation tags', 'bookyourtravel'), // <label>
                // the description is created in the callback function with a link to Manage the taxonomy terms
                'id' => 'acc_tag', // field id and name, needs to be the exact name of the taxonomy
                'type' => 'tax_checkboxes', // type of field
                'admin_tab_id' => 'filter_tab',
            ),
            array( // Taxonomy Select box
                'label' => esc_html__('Location', 'bookyourtravel'), // <label>
                // the description is created in the callback function with a link to Manage the taxonomy terms
                'id' => 'accommodation_list_location_post_id', // field id and name
                'type' => 'post_select', // type of field
                'post_type' => array('location'), // post types to display, options are prefixed with their post type
                'admin_tab_id' => 'filter_tab',
            ),
            array( // Select box
                'label' => esc_html__('Sort by field', 'bookyourtravel'), // <label>
                // the description is created in the callback function with a link to Manage the taxonomy terms
                'id' => 'accommodation_list_sort_by', // field id and name, needs to be the exact name of the taxonomy
                'type' => 'select', // type of field
                'options' => $sort_by_columns,
                'admin_tab_id' => 'filter_tab',
            ),
            array( // Post ID select box
                'label' => esc_html__('Sort descending?', 'bookyourtravel'), // <label>
                'desc' => esc_html__('If checked, will sort accommodations in descending order', 'bookyourtravel'), // description
                'id' => 'accommodation_list_sort_descending', // field id and name
                'type' => 'checkbox', // type of field
                'admin_tab_id' => 'filter_tab',
            ),
            array( // Post ID select box
                'label' => esc_html__('Show featured only?', 'bookyourtravel'), // <label>
                'desc' => esc_html__('If checked, will list featured accommodations only', 'bookyourtravel'), // description
                'id' => 'accommodation_list_show_featured_only', // field id and name
                'type' => 'checkbox', // type of field
                'admin_tab_id' => 'filter_tab',
            ),
            array( // Post ID select box
                'label' => esc_html__('Items per page', 'bookyourtravel'), // <label>
                'desc' => esc_html__('How many items do you want to show per page?', 'bookyourtravel'), // description
                'id' => 'accommodation_list_posts_per_page', // field id and name
                'std' => '12',
                'type' => 'slider',
                'min' => apply_filters('bookyourtravel_accommodation_list_posts_per_page_min', '1'),
                'max' => apply_filters('bookyourtravel_accommodation_list_posts_per_page_max', '50'),
                'step' => '1',
                'admin_tab_id' => 'filter_tab',
            ),
            array( // Post ID select box
                'label' => esc_html__('Items per row', 'bookyourtravel'), // <label>
                'desc' => esc_html__('How many items do you want to show per row?', 'bookyourtravel'), // description
                'id' => 'accommodation_list_posts_per_row', // field id and name
                'std' => '4',
                'type' => 'slider',
                'min' => apply_filters('bookyourtravel_accommodation_list_posts_per_row_min', '1'),
                'max' => apply_filters('bookyourtravel_accommodation_list_posts_per_row_max', '5'),
                'step' => '1',
                'admin_tab_id' => 'filter_tab',
            ),
            array( // Post ID select box
                'label' => esc_html__('Hide item titles?', 'bookyourtravel'), // <label>
                'desc' => esc_html__('If checked, will hide titles of listed accommodations', 'bookyourtravel'), // description
                'id' => 'accommodation_list_hide_item_titles', // field id and name
                'type' => 'checkbox', // type of field
                'admin_tab_id' => 'item_settings_tab',
            ),
            array( // Post ID select box
                'label' => esc_html__('Hide item images?', 'bookyourtravel'), // <label>
                'desc' => esc_html__('If checked, will hide images of listed accommodations', 'bookyourtravel'), // description
                'id' => 'accommodation_list_hide_item_images', // field id and name
                'type' => 'checkbox', // type of field
                'admin_tab_id' => 'item_settings_tab',
            ),
            array( // Post ID select box
                'label' => esc_html__('Hide item descriptions?', 'bookyourtravel'), // <label>
                'desc' => esc_html__('If checked, will hide descriptions of listed accommodations', 'bookyourtravel'), // description
                'id' => 'accommodation_list_hide_item_descriptions', // field id and name
                'type' => 'checkbox', // type of field
                'admin_tab_id' => 'item_settings_tab',
            ),
            array( // Post ID select box
                'label' => esc_html__('Hide buttons?', 'bookyourtravel'), // <label>
                'desc' => esc_html__('If checked, will hide buttons of listed accommodations', 'bookyourtravel'), // description
                'id' => 'accommodation_list_hide_item_actions', // field id and name
                'type' => 'checkbox', // type of field
                'admin_tab_id' => 'item_settings_tab',
            ),
            array( // Post ID select box
                'label' => esc_html__('Hide price?', 'bookyourtravel'), // <label>
                'desc' => esc_html__('If checked, will hide price of listed accommodations', 'bookyourtravel'), // description
                'id' => 'accommodation_list_hide_item_prices', // field id and name
                'type' => 'checkbox', // type of field
                'admin_tab_id' => 'item_settings_tab',
            ),
            array( // Post ID select box
                'label' => esc_html__('Hide item address?', 'bookyourtravel'), // <label>
                'desc' => esc_html__('If checked, will hide address of listed accommodations', 'bookyourtravel'), // description
                'id' => 'accommodation_list_hide_item_address', // field id and name
                'type' => 'checkbox', // type of field
                'admin_tab_id' => 'item_settings_tab',
            ),
        );

        if (!$bookyourtravel_theme_globals->disable_star_count('accommodation')) {
            $this->accommodation_list_custom_meta_fields[] = array( // Post ID select box
                'label' => esc_html__('Hide item stars?', 'bookyourtravel'), // <label>
                'desc' => esc_html__('If checked, will hide stars of listed accommodations', 'bookyourtravel'), // description
                'id' => 'accommodation_list_hide_item_stars', // field id and name
                'type' => 'checkbox', // type of field
            );
        }

        if ($bookyourtravel_theme_globals->enable_reviews()) {
            $this->accommodation_list_custom_meta_fields[] = array( // Post ID select box
                'label' => esc_html__('Hide item rating?', 'bookyourtravel'), // <label>
                'desc' => esc_html__('If checked, will hide rating of listed accommodations', 'bookyourtravel'), // description
                'id' => 'accommodation_list_hide_item_rating', // field id and name
                'type' => 'checkbox', // type of field
            );
        }

        $this->accommodation_list_custom_meta_fields = apply_filters('bookyourtravel_accommodation_list_custom_meta_fields', $this->accommodation_list_custom_meta_fields);

        $this->accommodation_vacancy_fields = array(
            array(
                'label' => esc_html__('Season name', 'bookyourtravel'),
                'id' => 'season_name',
                'type' => 'text',
                'field_override_class' => 'required',
                'show_in_fs_list' => true,
            ),
            array(
                'label' => esc_html__('Start date', 'bookyourtravel'),
                'id' => 'start_date',
                'type' => 'datepicker',
                'field_container_class' => 'datepicker-wrap',
                'field_override_class' => 'required',
                'show_in_fs_list' => true,
            ),
            array(
                'label' => esc_html__('End date', 'bookyourtravel'),
                'id' => 'end_date',
                'type' => 'datepicker',
                'field_container_class' => 'datepicker-wrap',
                'field_override_class' => 'required',
                'show_in_fs_list' => true,
            ),
            array(
                'label' => esc_html__('Accommodation', 'bookyourtravel'),
                'id' => 'accommodation_id',
                'type' => 'post_select',
                'post_type' => 'accommodation',
                'field_override_class' => 'required',
                'show_in_fs_list' => true,
            ),
            array(
                'label' => esc_html__('Room type', 'bookyourtravel'),
                'id' => 'room_type_id',
                'type' => 'post_select',
                'post_type' => 'room_type',
                'field_container_class' => 'room_types',
                'field_override_class' => '',
                'show_in_fs_list' => true,
            ),
            array(
                'label' => esc_html__('Room count', 'bookyourtravel'),
                'id' => 'room_count',
                'type' => 'slider',
                'min' => '1',
                'max' => '100',
                'step' => '1',
                'field_container_class' => 'room_types',
                'field_override_class' => 'required',
                'show_in_fs_list' => true,
            ),
            array(
                'label' => esc_html__('Price per day', 'bookyourtravel'),
                'id' => 'price_per_day',
                'type' => 'number',
                'field_override_class' => 'required',
                'show_in_fs_list' => true,
            ),
            array(
                'label' => esc_html__('Price per day (child)', 'bookyourtravel'),
                'desc' => esc_html__('Price per day per child (leave blank if not different to adults)', 'bookyourtravel'),
                'id' => 'price_per_day_child',
                'type' => 'number',
                'field_container_class' => 'per_person',
            ),
            array(
                'label' => esc_html__('Weekend price per day', 'bookyourtravel'),
                'desc' => esc_html__('Leave blank if not different to regular price', 'bookyourtravel'),
                'id' => 'weekend_price_per_day',
                'type' => 'number',
                'field_container_class' => 'daily_rent',
            ),
            array(
                'label' => esc_html__('Weekend price per day (child)', 'bookyourtravel'),
                'desc' => esc_html__('Price per weekend day per child (leave blank if not different to adults)', 'bookyourtravel'),
                'id' => 'weekend_price_per_day_child',
                'type' => 'number',
                'field_container_class' => 'per_person daily_rent',
            ),
        );

        $this->accommodation_vacancy_fields = apply_filters('bookyourtravel_accommodation_vacancy_fields', $this->accommodation_vacancy_fields);

        $this->accommodation_booking_fields = array(
            array(
                'label' => esc_html__('First name', 'bookyourtravel'),
                'id' => 'first_name',
                'type' => 'text',
                'field_override_class' => 'required',
                'show_in_fs_list' => true,
            ),
            array(
                'label' => esc_html__('Last name', 'bookyourtravel'),
                'id' => 'last_name',
                'type' => 'text',
                'field_override_class' => 'required',
                'show_in_fs_list' => true,
            ),
            array(
                'label' => esc_html__('Company', 'bookyourtravel'),
                'id' => 'company',
                'type' => 'text',
                'field_override_class' => '',
            ),
            array(
                'label' => esc_html__('Email', 'bookyourtravel'),
                'id' => 'email',
                'type' => 'text',
                'field_override_class' => 'required email',
            ),
            array(
                'label' => esc_html__('Phone', 'bookyourtravel'),
                'id' => 'phone',
                'type' => 'text',
                'field_override_class' => '',
            ),
            array(
                'label' => esc_html__('Address', 'bookyourtravel'),
                'id' => 'address',
                'type' => 'text',
                'field_override_class' => '',
            ),
            array(
                'label' => esc_html__('Address 2', 'bookyourtravel'),
                'id' => 'address_2',
                'type' => 'text',
                'field_override_class' => '',
            ),
            array(
                'label' => esc_html__('Town', 'bookyourtravel'),
                'id' => 'town',
                'type' => 'text',
                'field_override_class' => '',
            ),
            array(
                'label' => esc_html__('Zip', 'bookyourtravel'),
                'id' => 'zip',
                'type' => 'text',
                'field_override_class' => '',
            ),
            array(
                'label' => esc_html__('State', 'bookyourtravel'),
                'id' => 'state',
                'type' => 'text',
                'field_override_class' => '',
            ),
            array(
                'label' => esc_html__('Country', 'bookyourtravel'),
                'id' => 'country',
                'type' => 'text',
                'field_override_class' => '',
            ),
            array(
                'label' => esc_html__('Special requirements', 'bookyourtravel'),
                'id' => 'special_requirements',
                'type' => 'textarea',
                'field_override_class' => '',
            ),

            array(
                'label' => esc_html__('Start date', 'bookyourtravel'),
                'id' => 'date_from',
                'type' => 'datepicker',
                'field_container_class' => 'datepicker-wrap',
                'field_override_class' => 'required',
                'show_in_fs_list' => true,
            ),
            array(
                'label' => esc_html__('End date', 'bookyourtravel'),
                'id' => 'date_to',
                'type' => 'datepicker',
                'field_container_class' => 'datepicker-wrap',
                'field_override_class' => 'required',
                'show_in_fs_list' => true,
            ),
            array(
                'label' => esc_html__('Accommodation', 'bookyourtravel'),
                'id' => 'accommodation_id',
                'type' => 'post_select',
                'post_type' => 'accommodation',
                'field_override_class' => 'required',
                'show_in_fs_list' => true,
            ),
            array(
                'label' => esc_html__('Room type', 'bookyourtravel'),
                'id' => 'room_type_id',
                'type' => 'post_select',
                'post_type' => 'room_type',
                'field_container_class' => 'room_types',
                'field_override_class' => '',
                'show_in_fs_list' => true,
            ),
            array(
                'label' => esc_html__('Room count', 'bookyourtravel'),
                'id' => 'room_count',
                'type' => 'slider',
                'min' => '1',
                'max' => '100',
                'step' => '1',
                'field_container_class' => 'room_types',
                'field_override_class' => 'required',
                'show_in_fs_list' => true,
            ),
            array(
                'label' => esc_html__('Adults', 'bookyourtravel'),
                'id' => 'adults',
                'type' => 'slider',
                'min' => '1',
                'max' => '100',
                'step' => '1',
                'field_override_class' => 'required',
            ),
            array(
                'label' => esc_html__('Children', 'bookyourtravel'),
                'id' => 'children',
                'type' => 'slider',
                'min' => '0',
                'max' => '100',
                'step' => '1',
                'field_override_class' => '',
            ),
            array(
                'label' => esc_html__('Total accommodation price', 'bookyourtravel'),
                'id' => 'total_accommodation_price',
                'type' => 'number',
                'field_override_class' => 'required',
            ),
            array(
                'label' => esc_html__('Total extra items price', 'bookyourtravel'),
                'id' => 'total_extra_items_price',
                'type' => 'number',
                'field_override_class' => 'required',
            ),
            array(
                'label' => esc_html__('Total price', 'bookyourtravel'),
                'id' => 'total_price',
                'type' => 'number',
                'field_override_class' => 'required',
                'show_in_fs_list' => true,
            ),
        );

        if ($bookyourtravel_theme_globals->enable_deposit_payments()) {
			$this->accommodation_booking_fields[] = array(
				'label' => esc_html__('Deposit amount', 'bookyourtravel'),
				'id' => 'cart_price',
				'type' => 'number',
				'field_override_class' => 'required',
				'show_in_fs_list' => true
			);
		}

        $this->accommodation_booking_fields = apply_filters('bookyourtravel_accommodation_booking_fields', $this->accommodation_booking_fields);
    }

    function get_custom_meta_fields() {
        $this->initialize_meta_fields();
        return $this->accommodation_custom_meta_fields;
    }

    function get_accommodation_vacancy_fields() {
        $this->initialize_meta_fields();
        return $this->accommodation_vacancy_fields;
    }

    function get_accommodation_booking_fields() {
        $this->initialize_meta_fields();
        return $this->accommodation_booking_fields;
    }

    function get_custom_meta_tabs() {
        $this->initialize_meta_fields();        
        return $this->accommodation_custom_meta_tabs;
    }

    function before_single_accommodation_content() {
        global $post, $entity_obj, $bookyourtravel_theme_globals;

        if ($post && $post->post_type == 'accommodation') {
            $accommodation_obj = new BookYourTravel_Accommodation($post);
            $entity_obj = $accommodation_obj;
            $accommodation_is_reservation_only = (int) $accommodation_obj->get_is_reservation_only();
            $use_woocommerce_for_checkout = $bookyourtravel_theme_globals->use_woocommerce_for_checkout();

            if ($bookyourtravel_theme_globals->enable_reviews()) {
                get_template_part('includes/parts/review/review', 'form');
            }

            get_template_part('includes/parts/inquiry/inquiry', 'form');
            if ($accommodation_is_reservation_only || !BookYourTravel_Theme_Utils::is_woocommerce_active() || !$use_woocommerce_for_checkout) {
                get_template_part('includes/parts/booking/form', 'details');
                get_template_part('includes/parts/booking/form', 'confirmation');
            }
        }
    }

    function initialize_ajax() {

        add_action('byt_ajax_handler_accommodation_available_start_dates_ajax_request', array($this, 'get_available_start_dates_json'));
        add_action('byt_ajax_handler_nopriv_accommodation_available_start_dates_ajax_request', array($this, 'get_available_start_dates_json'));
        add_action('byt_ajax_handler_accommodation_available_end_dates_ajax_request', array($this, 'get_available_end_dates_json'));
        add_action('byt_ajax_handler_nopriv_accommodation_available_end_dates_ajax_request', array($this, 'get_available_end_dates_json'));
        add_action('byt_ajax_handler_accommodation_get_day_price_ajax_request', array($this, 'get_prices_json'));
        add_action('byt_ajax_handler_nopriv_accommodation_get_day_price_ajax_request', array($this, 'get_prices_json'));
        add_action('byt_ajax_handler_nopriv_accommodation_load_min_price_ajax_request', array($this, 'get_min_price_json'));
        add_action('byt_ajax_handler_accommodation_load_min_price_ajax_request', array($this, 'get_min_price_json'));

        add_action('wp_ajax_accommodation_available_start_dates_ajax_request', array($this, 'get_available_start_dates_json'));
        add_action('wp_ajax_nopriv_accommodation_available_start_dates_ajax_request', array($this, 'get_available_start_dates_json'));
        add_action('wp_ajax_accommodation_available_end_dates_ajax_request', array($this, 'get_available_end_dates_json'));
        add_action('wp_ajax_nopriv_accommodation_available_end_dates_ajax_request', array($this, 'get_available_end_dates_json'));
        add_action('wp_ajax_accommodation_get_day_price_ajax_request', array($this, 'get_prices_json'));
        add_action('wp_ajax_nopriv_accommodation_get_day_price_ajax_request', array($this, 'get_prices_json'));
        add_action('wp_ajax_nopriv_accommodation_load_min_price_ajax_request', array($this, 'get_min_price_json'));
        add_action('wp_ajax_accommodation_load_min_price_ajax_request', array($this, 'get_min_price_json'));

        add_action('wp_ajax_accommodation_process_booking_ajax_request', array($this, 'process_booking_ajax_request'));
        add_action('wp_ajax_nopriv_accommodation_process_booking_ajax_request', array($this, 'process_booking_ajax_request'));

        add_action('wp_ajax_accommodation_get_fields_ajax_request', array($this, 'get_fields_ajax_request'));
    }

    function booking_form_calendar_booking_terms() {
        get_template_part('includes/parts/accommodation/single/booking-form-calendar', 'booking-terms');
    }

    function booking_form_calendar_start_summary_control() {
        get_template_part('includes/parts/accommodation/single/booking-form-calendar', 'summary-fields');
    }

    function booking_form_calendar_after_calendar_control() {
        get_template_part('includes/parts/accommodation/single/booking-form-calendar', 'fields');
    }

    function booking_form_confirmation_core_fields() {
        get_template_part('includes/parts/accommodation/single/booking-form-confirmation', 'core-fields');
    }

    function booking_form_details_core_fields() {
        get_template_part('includes/parts/accommodation/single/booking-form-details', 'core-fields');
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

        if (isset($_REQUEST)) {
            $nonce = wp_kses($_REQUEST['nonce'], array());

            if (wp_verify_nonce($nonce, 'bookyourtravel_nonce')) {

                $booking_object = $this->retrieve_booking_values_from_request();

                if ($booking_object != null) {

                    $accommodation_obj = new BookYourTravel_Accommodation($booking_object->accommodation_id);
                    if (isset($booking_object->room_type_id)) {
                        $room_type_obj = new BookYourTravel_Room_Type($booking_object->room_type_id);
                    }

                    if ($accommodation_obj != null) {

                        $booking_object->Id = $this->create_accommodation_booking($current_user->ID, $booking_object);

                        echo json_encode($booking_object->Id);

                        $use_woocommerce_for_checkout = $bookyourtravel_theme_globals->use_woocommerce_for_checkout();
                        $is_reservation_only = get_post_meta($booking_object->accommodation_id, 'accommodation_is_reservation_only', true);

                        if (!$use_woocommerce_for_checkout || !BookYourTravel_Theme_Utils::is_woocommerce_active() || $is_reservation_only) {

                            // only send email if we are not proceeding to WooCommerce checkout or if woocommerce is not active at all.
                            $admin_email = get_bloginfo('admin_email');
                            $admin_name = get_bloginfo('name');

                            $subject = esc_html__('New accommodation booking', 'bookyourtravel');

                            $message = esc_html__('New accommodation booking: ', 'bookyourtravel');
                            $message .= "\n\n";
                            $message .= sprintf(esc_html__("Accommodation: %s", 'bookyourtravel'), $accommodation_obj->get_title()) . "\n\n";

                            if ($room_type_obj) {
                                $message .= sprintf(esc_html__("Room type: %s", 'bookyourtravel'), $room_type_obj->get_title()) . "\n\n";
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
                                    $total_extra_items_price_string = $default_currency_symbol . ' ' . number_format_i18n($total_extra_items_price, $price_decimal_places);
                                } else {
                                    $total_extra_items_price_string = number_format_i18n($total_extra_items_price, $price_decimal_places) . ' ' . $default_currency_symbol;
                                }

                                $total_extra_items_price_string = preg_replace("/&nbsp;/", ' ', $total_extra_items_price_string);

                                $message .= sprintf(esc_html__("Extra items total: %s", 'bookyourtravel'), $total_extra_items_price_string) . "\n\n";
                            }

                            if ($booking_object->total_accommodation_price > 0) {

                                $total_accommodation_price = BookYourTravel_Theme_Utils::get_price_in_current_currency($booking_object->total_accommodation_price);

                                $total_accommodation_price_string = '';
                                if (!$show_currency_symbol_after) {
                                    $total_accommodation_price_string = $default_currency_symbol . ' ' . number_format_i18n($total_accommodation_price, $price_decimal_places);
                                } else {
                                    $total_accommodation_price_string = number_format_i18n($total_accommodation_price, $price_decimal_places) . ' ' . $default_currency_symbol;
                                }

                                $total_accommodation_price_string = preg_replace("/&nbsp;/", ' ', $total_accommodation_price_string);

                                $message .= sprintf(esc_html__("Reservation total: %s", 'bookyourtravel'), $total_accommodation_price_string) . "\n\n";
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

                            $contact_emails = trim(get_post_meta($booking_object->accommodation_id, 'accommodation_contact_email', true));

                            $emails_array = array();
                            if (empty($contact_emails)) {
                                $emails_array = array($admin_email);
                            } else {
                                $emails_array = explode(';', $contact_emails);
                            }

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

    function get_available_start_dates_json() {

        global $bookyourtravel_theme_globals;
        $available_dates = [];

        if (isset($_REQUEST)) {
            $nonce = wp_kses($_REQUEST['nonce'], array());

            if (wp_verify_nonce($nonce, 'bookyourtravel_nonce')) {

                $accommodation_id = isset($_REQUEST['accommodation_id']) ? intval(wp_kses($_REQUEST['accommodation_id'], array())) : 0;
                $room_type_id = isset($_REQUEST['room_type_id']) ? intval(wp_kses($_REQUEST['room_type_id'], array())) : 0;
                $month = isset($_REQUEST['month']) ? intval(wp_kses($_REQUEST['month'], array())) : 0;
                $year = isset($_REQUEST['year']) ? intval(wp_kses($_REQUEST['year'], array())) : 0;
                $month_range = isset($_REQUEST['month_range']) ? intval(wp_kses($_REQUEST['month_range'], array())) : 24;
                $rooms = isset($_REQUEST['rooms']) ? intval(wp_kses($_REQUEST['rooms'], array())) : 1;

                if ($accommodation_id > 0) {

                    $accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
                    $accommodation_checkin_week_day = $accommodation_obj->get_checkin_week_day();
                    $price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();

                    $available_dates = $this->list_available_start_dates($accommodation_id, $room_type_id, $month, $year, $month_range, $rooms);
                }
            }
        }

        echo json_encode($available_dates);

        die();
    }

    function get_available_end_dates_json() {

        global $bookyourtravel_theme_globals;
        $available_dates = [];

        if (isset($_REQUEST)) {

            $nonce = wp_kses($_REQUEST['nonce'], array());

            if (wp_verify_nonce($nonce, 'bookyourtravel_nonce')) {

                $accommodation_id = isset($_REQUEST['accommodation_id']) ? intval(wp_kses($_REQUEST['accommodation_id'], array())) : 0;
                $room_type_id = isset($_REQUEST['room_type_id']) ? intval(wp_kses($_REQUEST['room_type_id'], array())) : 0;
                $month = isset($_REQUEST['month']) ? intval(wp_kses($_REQUEST['month'], array())) : 0;
                $year = isset($_REQUEST['year']) ? intval(wp_kses($_REQUEST['year'], array())) : 0;
                $start_date = isset($_REQUEST['start_date']) ? wp_kses($_REQUEST['start_date'], array()) : null;
                $month_range = isset($_REQUEST['month_range']) ? intval(wp_kses($_REQUEST['month_range'], array())) : 4;
                $rooms = isset($_REQUEST['rooms']) ? intval(wp_kses($_REQUEST['rooms'], array())) : 1;

                if ($accommodation_id > 0) {
                    $available_dates = $this->list_available_end_dates($accommodation_id, $room_type_id, $start_date, $month, $year, $month_range, $rooms);
                }
            }
        }

        echo json_encode($available_dates);

        die();
    }

    function get_fields_ajax_request() {

        if (isset($_REQUEST)) {

            $nonce = wp_kses($_REQUEST['nonce'], array());
            $accommodation_id = intval(wp_kses($_REQUEST['accommodationId'], array()));
            $room_type_id = 0;
            if (isset($_REQUEST['roomTypeId'])) {
                $room_type_id = intval(wp_kses($_REQUEST['roomTypeId'], array()));
            }

            if (wp_verify_nonce($nonce, 'bookyourtravel_nonce')) {

                $accommodation_obj = new BookYourTravel_Accommodation((int) $accommodation_id);
                $room_type_obj = null;
                if ($room_type_id > 0) {
                    $room_type_obj = new BookYourTravel_Room_Type((int) $room_type_id);
                }

                $fields = new stdClass();

                $fields->rent_type = $accommodation_obj->get_rent_type();
                $fields->checkin_week_day = $accommodation_obj->get_checkin_week_day();
                $fields->checkout_week_day = $accommodation_obj->get_checkout_week_day();
                $fields->disabled_room_types = $accommodation_obj->get_disabled_room_types();
                $fields->is_price_per_person = $accommodation_obj->get_is_price_per_person();
                $fields->min_days_stay = $accommodation_obj->get_min_days_stay();
                $fields->max_days_stay = $accommodation_obj->get_max_days_stay();
                $fields->children_stay_free = $accommodation_obj->get_count_children_stay_free();

                if ($room_type_obj == null) {
                    $fields->min_adult_count = $accommodation_obj->get_min_adult_count();
                    $fields->max_adult_count = $accommodation_obj->get_max_adult_count();
                    $fields->min_child_count = $accommodation_obj->get_min_child_count();
                    $fields->max_child_count = $accommodation_obj->get_max_child_count();
                } else {
                    $fields->min_adult_count = $room_type_obj->get_min_adult_count();
                    $fields->max_adult_count = $room_type_obj->get_max_adult_count();
                    $fields->min_child_count = $room_type_obj->get_min_child_count();
                    $fields->max_child_count = $room_type_obj->get_max_child_count();
                }

                $fields->room_types = array();

                if (!$fields->disabled_room_types) {

                    $room_type_ids = $accommodation_obj->get_room_types();

                    if ($accommodation_obj && $room_type_ids && count($room_type_ids) > 0) {

                        for ($i = 0; $i < count($room_type_ids); $i++) {

                            $temp_id = $room_type_ids[$i];
                            $room_type_obj = new BookYourTravel_Room_Type(intval($temp_id));
                            $room_type_temp = new stdClass();
                            $room_type_temp->name = $room_type_obj->get_title();
                            $room_type_temp->id = $room_type_obj->get_id();
                            $fields->room_types[] = $room_type_temp;
                        }
                    }
                }

                echo json_encode($fields);
            }
        }

        // Always die in functions echoing ajax content
        die();
    }

    function initialize_post_type() {

        if ($this->enable_accommodations) {
            $this->register_accommodation_post_type();
            $this->register_accommodation_tag_taxonomy();
            $this->register_accommodation_type_taxonomy();
        }

        // have to make sure extra tables are created regardless of whether the post type is enabled or not in order for tables to exist if post type is enabled at a later stage.
        $this->create_accommodation_extra_tables();
    }

    function register_accommodation_post_type() {

        global $bookyourtravel_theme_globals;

        $accommodations_permalink_slug = $bookyourtravel_theme_globals->get_accommodations_permalink_slug();
        $accommodation_list_page_id = $bookyourtravel_theme_globals->get_accommodation_list_page_id();

        if ($accommodation_list_page_id > 0) {

            add_rewrite_rule(
                "{$accommodations_permalink_slug}$",
                "index.php?post_type=page&page_id={$accommodation_list_page_id}", 'top');

            add_rewrite_rule(
                "{$accommodations_permalink_slug}/page/?([1-9][0-9]*)",
                "index.php?post_type=page&page_id={$accommodation_list_page_id}&paged=\$matches[1]", 'top');
        }

        add_rewrite_rule(
            "{$accommodations_permalink_slug}/([^/]+)/page/?([1-9][0-9]*)",
            "index.php?post_type=accommodation&name=\$matches[1]&paged-byt=\$matches[2]", 'top');

        add_rewrite_tag('%paged-byt%', '([1-9][0-9]*)');

        $labels = array(
            'name' => esc_html__('Accommodations', 'bookyourtravel'),
            'singular_name' => esc_html__('Accommodation', 'bookyourtravel'),
            'menu_name' => esc_html__('Accommodations', 'bookyourtravel'),
            'all_items' => esc_html__('All Accommodations', 'bookyourtravel'),
            'view_item' => esc_html__('View Accommodation', 'bookyourtravel'),
            'add_new_item' => esc_html__('Add New Accommodation', 'bookyourtravel'),
            'add_new' => esc_html__('New Accommodation', 'bookyourtravel'),
            'edit_item' => esc_html__('Edit Accommodation', 'bookyourtravel'),
            'update_item' => esc_html__('Update Accommodation', 'bookyourtravel'),
            'search_items' => esc_html__('Search Accommodations', 'bookyourtravel'),
            'not_found' => esc_html__('No Accommodations found', 'bookyourtravel'),
            'not_found_in_trash' => esc_html__('No Accommodations found in Trash', 'bookyourtravel'),
        );

        $args = array(
            'label' => esc_html__('Accommodation', 'bookyourtravel'),
            'description' => esc_html__('Accommodation information pages', 'bookyourtravel'),
            'labels' => $labels,
            'supports' => array('title', 'editor', 'thumbnail', 'author', 'page-attributes'),
            'taxonomies' => array(),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_nav_menus' => true,
            'show_in_admin_bar' => true,
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'page',
            'show_in_rest' => true,
            'rewrite' => array('slug' => $accommodations_permalink_slug),
        );

        $args = apply_filters('bookyourtravel_register_post_type_accommodation', $args);

        register_post_type('accommodation', $args);
    }

    function register_accommodation_tag_taxonomy() {

        $labels = array(
            'name' => esc_html__('Accommodation Tags', 'bookyourtravel'),
            'singular_name' => esc_html__('Accommodation Tag', 'bookyourtravel'),
            'search_items' => esc_html__('Search Accommodation tags', 'bookyourtravel'),
            'all_items' => esc_html__('All Accommodation tags', 'bookyourtravel'),
            'parent_item' => null,
            'parent_item_colon' => null,
            'edit_item' => esc_html__('Edit Accommodation tag', 'bookyourtravel'),
            'update_item' => esc_html__('Update Accommodation tag', 'bookyourtravel'),
            'add_new_item' => esc_html__('Add New Accommodation tag', 'bookyourtravel'),
            'new_item_name' => esc_html__('New Accommodation tag Name', 'bookyourtravel'),
            'separate_items_with_commas' => esc_html__('Separate Accommodation tags with commas', 'bookyourtravel'),
            'add_or_remove_items' => esc_html__('Add or remove Accommodation tags', 'bookyourtravel'),
            'choose_from_most_used' => esc_html__('Choose from the most used Accommodation tags', 'bookyourtravel'),
            'not_found' => esc_html__('No Accommodation tags found.', 'bookyourtravel'),
            'menu_name' => esc_html__('Accommodation Tags', 'bookyourtravel'),
        );

        $args = array(
            'hierarchical' => false,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'update_count_callback' => '_update_post_term_count',
            'rewrite' => array('slug' => 'accommodation-tag'),
        );

        $args = apply_filters('bookyourtravel_register_taxonomy_accommodation_tag', $args);

        register_taxonomy('acc_tag', array('accommodation'), $args);
    }

    function register_accommodation_type_taxonomy() {

        $labels = array(
            'name' => esc_html__('Accommodation Types', 'bookyourtravel'),
            'singular_name' => __('Accommodation Type', 'bookyourtravel'),
            'search_items' => esc_html__('Search Accommodation Types', 'bookyourtravel'),
            'all_items' => esc_html__('All Accommodation Types', 'bookyourtravel'),
            'parent_item' => null,
            'parent_item_colon' => null,
            'edit_item' => esc_html__('Edit Accommodation Type', 'bookyourtravel'),
            'update_item' => esc_html__('Update Accommodation Type', 'bookyourtravel'),
            'add_new_item' => esc_html__('Add New Accommodation Type', 'bookyourtravel'),
            'new_item_name' => esc_html__('New Accommodation Type Name', 'bookyourtravel'),
            'separate_items_with_commas' => esc_html__('Separate accommodation types with commas', 'bookyourtravel'),
            'add_or_remove_items' => esc_html__('Add or remove accommodation types', 'bookyourtravel'),
            'choose_from_most_used' => esc_html__('Choose from the most used accommodation types', 'bookyourtravel'),
            'not_found' => esc_html__('No accommodation types found.', 'bookyourtravel'),
            'menu_name' => esc_html__('Accommodation Types', 'bookyourtravel'),
        );

        $args = array(
            'hierarchical' => false,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'update_count_callback' => '_update_post_term_count',
            'rewrite' => array('slug' => 'accommodation-type'),
        );

        $args = apply_filters('bookyourtravel_register_taxonomy_accommodation_type', $args);

        register_taxonomy('accommodation_type', array('accommodation'), $args);
    }

    function create_accommodation_extra_tables() {

        global $bookyourtravel_installed_version, $force_recreate_tables;

        if ($bookyourtravel_installed_version != BOOKYOURTRAVEL_VERSION || $force_recreate_tables) {

            global $wpdb;

            $sql = "CREATE TABLE " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . " (
						Id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
						season_name varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
						start_date datetime NOT NULL,
						end_date datetime NOT NULL,
						accommodation_id bigint(20) unsigned NOT NULL,
						room_type_id bigint(20) unsigned NOT NULL DEFAULT '0',
						room_count int(11) NOT NULL,
						price_per_day decimal(16,2) NOT NULL,
						price_per_day_child decimal(16,2) NOT NULL,
						weekend_price_per_day decimal(16,2) NULL,
						weekend_price_per_day_child decimal(16,2) NULL,
						PRIMARY KEY  (Id)
					);";

            // we do not execute sql directly we are calling dbDelta which cant migrate database
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($sql);

            global $EZSQL_ERROR;

            $EZSQL_ERROR = array();

            $sql = "CREATE TABLE " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " (
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
						special_requirements text CHARACTER SET utf8 COLLATE utf8_bin NULL,
						other_fields text CHARACTER SET utf8 COLLATE utf8_bin NULL,
						extra_items text CHARACTER SET utf8 COLLATE utf8_bin NULL,
						room_count int(11) NOT NULL DEFAULT '0',
						adults int(11) NOT NULL DEFAULT '0',
						children int(11) NOT NULL DEFAULT '0',
						total_accommodation_price decimal(16,2) NOT NULL DEFAULT '0.00',
						total_extra_items_price decimal(16,2) NOT NULL DEFAULT '0.00',
                        total_price decimal(16,2) NOT NULL DEFAULT '0.00',
                        cart_price decimal(16,2) NOT NULL DEFAULT '0.00',
						accommodation_id bigint(20) unsigned NOT NULL,
						room_type_id bigint(20) unsigned NOT NULL,
						date_from datetime NOT NULL,
						date_to datetime NOT NULL,
						user_id bigint(20) unsigned DEFAULT NULL,
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

    function save_accommodation($post_id) {
        delete_post_meta_by_key('_location_accommodation_ids');

        $accommodation_obj = new BookYourTravel_Accommodation($post_id);
        if ($accommodation_obj) {
            $location_id = $accommodation_obj->get_custom_field('location_post_id');

            if ($location_id > 0) {
                delete_post_meta($location_id, '_location_accommodation_count');
            }
        }
    }

    function after_delete_accommodation($post_id) {
        delete_post_meta_by_key('_location_accommodation_ids');

        $accommodation_obj = new BookYourTravel_Accommodation($post_id);
        if ($accommodation_obj) {
            $location_id = $accommodation_obj->get_custom_field('location_post_id');
            if ($location_id > 0) {
                delete_post_meta($location_id, '_location_accommodation_count');
            }
        }
    }

    function manage_edit_accommodation_columns($columns) {
        return $columns;
    }

    function remove_unnecessary_meta_boxes() {
        remove_meta_box('tagsdiv-acc_tag', 'accommodation', 'side');
        remove_meta_box('tagsdiv-accommodation_type', 'accommodation', 'side');
        remove_meta_box('tagsdiv-facility', 'accommodation', 'side');
    }

    function accommodation_admin_init() {

        if ($this->enable_accommodations) {

            $this->initialize_meta_fields();
            new Custom_Add_Meta_Box('accommodation_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->accommodation_custom_meta_fields, $this->accommodation_custom_meta_tabs, 'accommodation');

            $this->accommodation_list_meta_box = new Custom_Add_Meta_Box('accommodation_list_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->accommodation_list_custom_meta_fields, $this->accommodation_list_custom_meta_tabs, 'page');
            remove_action('add_meta_boxes', array($this->accommodation_list_meta_box, 'add_box'));
            add_action('add_meta_boxes', array($this, 'accommodation_list_add_meta_boxes'));
        }
    }

    function accommodation_list_add_meta_boxes() {

        global $post;
        $template_file = get_post_meta($post->ID, '_wp_page_template', true);
        if ($template_file == 'page-accommodation-list.php') {
            add_meta_box(
                $this->accommodation_list_meta_box->id,
                $this->accommodation_list_meta_box->title,
                array($this->accommodation_list_meta_box, 'meta_box_callback'),
                'page', 'normal', 'high'
            );
        }
    }

    function columns_head($defaults) {
        $defaults['rent_type'] = __('Rent type', 'bookyourtravel');
        return $defaults;
    }

    function columns_content($column_name, $post_ID) {
        if ($column_name == 'rent_type') {
            $accommodation_obj = new BookYourTravel_Accommodation($post_ID);
            $rent_type = $accommodation_obj->get_rent_type();
            if ($rent_type == 1) {
                echo __('Weekly', 'bookyourtravel');
            } else if ($rent_type == 2) {
                echo __('Monthly', 'bookyourtravel');
            } else {
                echo __('Daily', 'bookyourtravel');
            }
        }
    }

    /* ***************************** */
    /* Accommodation vacancy related */
    /* ***************************** */

    function list_accommodation_vacancy_end_dates($start_date, $accommodation_id, $room_type_id, $month, $year, $day, $month_range = 0) {

        global $wpdb, $bookyourtravel_theme_globals;

        $accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($accommodation_id, 'accommodation');

        if ($room_type_id > 0) {
            $room_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($room_type_id, 'room_type');
        }

        $accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
        $accommodation_is_reservation_only = $accommodation_obj->get_is_reservation_only();

        $start_date = date('Y-m-d', strtotime($start_date));

        $end_date = sprintf("%d-%d-%d", $year, $month, $day);
        if ($month_range > 0) {
            $end_date = date('Y-m-d', strtotime(sprintf("+%d months", $month_range), strtotime($end_date)));
        }
        $end_date = date("Y-m-t", strtotime($end_date)); // last day of end date month

        $sql = "SELECT 	availables.single_date, availables.available_rooms, IFNULL(SUM(bookings.room_count), 0) booked_rooms
				FROM (
					SELECT DISTINCT date_format(possible_dates.the_date, '%Y-%m-%d') single_date, SUM(vacancies.room_count) available_rooms, date_format(DATE(DATE_ADD(possible_dates.the_date, INTERVAL 1 DAY)), '%Y-%m-%d 11:59:59') as bookable_single_date
					FROM " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . " vacancies
					INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date > DATE(vacancies.start_date) AND possible_dates.the_date <= DATE(vacancies.end_date) ";

        $sql .= $wpdb->prepare("WHERE possible_dates.the_date > %s AND possible_dates.the_date <= %s AND vacancies.accommodation_id=%d ", $start_date, $start_date, $end_date, $start_date, $end_date, $accommodation_id);

        if ($room_type_id > 0) {
            $sql .= $wpdb->prepare(" AND vacancies.room_type_id=%d ", $room_type_id);
        }

        $sql .= " 	GROUP BY single_date
					) availables
					LEFT JOIN " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " bookings ON availables.bookable_single_date >= bookings.date_from AND availables.bookable_single_date < bookings.date_to ";

        $sql .= $wpdb->prepare(" AND bookings.accommodation_id=%d ", $accommodation_id);

        if ($room_type_id > 0) {
            $sql .= $wpdb->prepare(" AND bookings.room_type_id=%d ", $room_type_id);
        }

        if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$accommodation_is_reservation_only) {

            $completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
            if (!empty($completed_statuses)) {
                $sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")";
            }
        }

        $sql .= " GROUP BY availables.single_date";

        $results = $wpdb->get_results($sql);

        $available_dates = array();

        $prev_date = null;
        $next_date = null;
        foreach ($results as $result) {

            $new_date = date('Y-m-d', strtotime($result->single_date));

            if (isset($prev_date)) {
                $next_date = date('Y-m-d', strtotime($prev_date . ' +1 days'));

                if ($next_date != $new_date) {
                    // there was a break in days so days after this one are not bookable
                    break;
                }
            }

            $room_count = $result->available_rooms;
            $booked_rooms = $result->booked_rooms;

            if ($room_count > $booked_rooms) {
                $result->single_date = date('Y-m-d', strtotime($result->single_date));
                $available_dates[] = $result;
            } else if ($new_date == $start_date) {
                $result->single_date = date('Y-m-d', strtotime($result->single_date));
                $result->booked_rooms = $booked_rooms - 1;
                $available_dates[] = $result;
            } else {
                break;
            }

            $prev_date = $new_date;
        }

        return $available_dates;
    }

    function list_available_end_dates($accommodation_id, $room_type_id, $start_date, $month, $year, $month_range, $rooms) {

        global $wpdb, $bookyourtravel_theme_globals;

        $accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($accommodation_id, 'accommodation');
        if ($room_type_id > 0) {
            $room_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($room_type_id, 'room_type');
        }

        $accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
        $accommodation_is_reservation_only = $accommodation_obj->get_is_reservation_only();

        $available_dates = array();

        $start_date = date("Y-m-d", strtotime($start_date));
        $end_date = date("Y-m-t", strtotime($start_date)); // last day of end date month
        $end_date = date('Y-m-d', strtotime(sprintf("+%d months", $month_range), strtotime($end_date)));

        $sql = "
			SELECT DISTINCT (avc.number_of_available_rooms - IFNULL(bc.number_of_booked_rooms, 0)) available_rooms, avc.the_date FROM
			(
				SELECT SUM(room_count) number_of_available_rooms, possible_dates.the_date
				FROM " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . " availabilities
				INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date > DATE(availabilities.start_date) AND possible_dates.the_date <= DATE(availabilities.end_date)
				WHERE availabilities.accommodation_id = %d
				";

        if ($room_type_id > 0) {
            $sql .= $wpdb->prepare(" AND availabilities.room_type_id = %d ", $room_type_id);
        }

        $sql .= "
				GROUP BY possible_dates.the_date
			) as avc
			LEFT JOIN
			(
				SELECT SUM(bookings.room_count) number_of_booked_rooms, possible_dates.the_date booking_date
				FROM " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " bookings
				INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date > DATE(bookings.date_from) AND possible_dates.the_date <= DATE(bookings.date_to)
				WHERE bookings.accommodation_id = %d ";

        if ($room_type_id > 0) {
            $sql .= $wpdb->prepare(" AND bookings.room_type_id = %d ", $room_type_id);
        }

        if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$accommodation_is_reservation_only) {
            $completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
            if (!empty($completed_statuses)) {
                $sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")";
            }
        }

        $sql .= "
				GROUP BY possible_dates.the_date
			) as bc
			 ON bc.booking_date = avc.the_date
			HAVING available_rooms >= %d
        ";
        
        $sql = $wpdb->prepare($sql, $start_date, $start_date, $end_date, $accommodation_id, $start_date, $start_date, $end_date, $accommodation_id, $rooms);

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

            $available_date = array();
            $available_date["date"] = date('Y-m-d', strtotime($available_result->the_date));
            $available_date["available_rooms"] = $available_result->available_rooms;
            $available_dates[] = $available_date;

            $prev_date = $new_date;
        }

        return $available_dates;
    }

    function list_available_start_dates($accommodation_id, $room_type_id, $month, $year, $month_range, $rooms = 1) {

        global $wpdb, $bookyourtravel_theme_globals;

        $available_dates = array();

        $accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($accommodation_id, 'accommodation');
        if ($room_type_id > 0) {
            $room_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($room_type_id, 'room_type');
        }

        $accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
        $accommodation_is_reservation_only = $accommodation_obj->get_is_reservation_only();

        $start_date = sprintf("%d-%d-%d", $year, $month, 1);
        $start_date = date("Y-m-d", strtotime($start_date));
        $end_date = date("Y-m-t", strtotime($start_date)); // last day of end date month
        $end_date = date('Y-m-d', strtotime(sprintf("+%d months", $month_range + 16), strtotime($end_date)));

        $sql = "
			SELECT DISTINCT (avc.number_of_available_rooms - IFNULL(bc.number_of_booked_rooms, 0)) available_rooms, avc.the_date FROM
			(
				SELECT SUM(room_count) number_of_available_rooms, possible_dates.the_date
				FROM " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . " availabilities
				INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date >= DATE(availabilities.start_date) AND possible_dates.the_date < DATE(availabilities.end_date)
				WHERE availabilities.accommodation_id = %d ";

        if ($room_type_id > 0) {
            $sql .= $wpdb->prepare(" AND availabilities.room_type_id = %d ", $room_type_id);
        }

        $sql .= "
				GROUP BY possible_dates.the_date
			) as avc
			LEFT JOIN
			(
				SELECT SUM(bookings.room_count) number_of_booked_rooms, possible_dates.the_date booking_date
				FROM " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " bookings
				INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date >= DATE(bookings.date_from) AND possible_dates.the_date < DATE(bookings.date_to)
				WHERE bookings.accommodation_id = %d ";

        if ($room_type_id > 0) {
            $sql .= $wpdb->prepare(" AND bookings.room_type_id = %d ", $room_type_id);
        }

        if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$accommodation_is_reservation_only) {
            $completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
            if (!empty($completed_statuses)) {
                $sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")";
            }
        }

        $sql .= "
				GROUP BY possible_dates.the_date
			) as bc
			ON bc.booking_date = avc.the_date
			HAVING available_rooms >= %d
		";

        $sql = $wpdb->prepare($sql, $start_date, $start_date, $end_date, $accommodation_id, $start_date, $start_date, $end_date, $accommodation_id, $rooms);

        $available_results = $wpdb->get_results($sql);

        foreach ($available_results as $available_result) {
            $available_dates[] = date('Y-m-d', strtotime($available_result->the_date));
        }

        return $available_dates;
    }

    function get_min_price_json() {

        if (isset($_REQUEST)) {
            $nonce = wp_kses($_REQUEST['nonce'], array());

            if (wp_verify_nonce($nonce, 'bookyourtravel_nonce')) {
                $accommodation_id = isset($_REQUEST['accommodation_id']) ? intval(wp_kses($_REQUEST['accommodation_id'], array())) : 0;
                $room_type_id = isset($_REQUEST['room_type_id']) ? intval(wp_kses($_REQUEST['room_type_id'], array())) : 0;
                $start_date = isset($_REQUEST['start_date']) ? wp_kses($_REQUEST['start_date'], array()) : null;
                $end_date = isset($_REQUEST['end_date']) ? wp_kses($_REQUEST['end_date'], array()) : null;

                $price = $this->get_min_future_price($accommodation_id, $room_type_id, $start_date, $end_date, true);

                if ($price > 0) {
                    $price = BookYourTravel_Theme_Utils::get_price_in_current_currency($price);

                    echo json_encode($price);
                }
            }
        }

        die();
    }

    function get_min_future_price($_accommodation_id, $_room_type_id = 0, $start_date = null, $end_date = null, $skip_cache = false) {
        global $wpdb, $bookyourtravel_theme_globals;

        $accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($_accommodation_id, 'accommodation');
        if ($room_type_id > 0) {
            $room_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($_room_type_id, 'room_type');
        }

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

        $min_price_meta_key = BookYourTravel_Theme_Utils::build_min_price_meta_key("accommodation", $start_date, $end_date);
        $min_price_check_meta_key = BookYourTravel_Theme_Utils::build_min_price_check_meta_key("accommodation", $start_date, $end_date);

        $accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
        $min_price = $accommodation_obj->_get_cached_price($min_price_meta_key, $min_price_check_meta_key);
        $is_reservation_only = $accommodation_obj->get_is_reservation_only();

        if ($min_price == 0 || $skip_cache) {

            $use_referral_url = $accommodation_obj->use_referral_url();
            $referral_url = $accommodation_obj->get_referral_url();
            if ($use_referral_url && !empty($referral_url)) {
                $min_price = $accommodation_obj->get_referral_price();
            } else {
                $sql = "
				SELECT IFNULL(MIN(price_per_day), 0) min_price
				FROM
				(
					SELECT DISTINCT (avc.number_of_available_rooms - IFNULL(bc.number_of_booked_rooms, 0)) available_rooms, avc.the_date, IFNULL(avc.price_per_day, 0) price_per_day FROM
					(
						SELECT SUM(room_count) number_of_available_rooms, possible_dates.the_date, MIN(price_per_day) price_per_day
						FROM " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . " availabilities
						INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date > DATE(availabilities.start_date) AND possible_dates.the_date <= DATE(availabilities.end_date)
						WHERE availabilities.accommodation_id = %d ";

                if ($room_type_id > 0) {
                    $sql .= $wpdb->prepare(" AND availabilities.room_type_id = %d ", $room_type_id);
                }

                // , availabilities.price_per_day
                $sql .= " GROUP BY possible_dates.the_date
					) as avc
					LEFT JOIN
					(
						SELECT SUM(bookings.room_count) number_of_booked_rooms, possible_dates.the_date booking_date
						FROM " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " bookings
						INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date >= DATE(bookings.date_from) AND possible_dates.the_date <= DATE(bookings.date_to)
                        WHERE bookings.accommodation_id = %d ";
                        
						if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$is_reservation_only) {
							$completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
							if (!empty($completed_statuses)) {
								$sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ") ";
							}
						}                        

                if ($room_type_id > 0) {
                    $sql .= $wpdb->prepare(" AND bookings.room_type_id = %d ", $room_type_id);
                }

                $sql .= " GROUP BY possible_dates.the_date
					) as bc
					ON bc.booking_date = avc.the_date
					HAVING available_rooms > 0
				) as pr ";

                $sql = $wpdb->prepare($sql, $start_date, $start_date, $end_date, $accommodation_id, $start_date, $start_date, $end_date, $accommodation_id);

                $min_price = $wpdb->get_var($sql);
            }
        }

        update_post_meta($accommodation_id, $min_price_meta_key, $min_price);
        update_post_meta($accommodation_id, $min_price_check_meta_key, time());
        update_post_meta($_accommodation_id, $min_price_meta_key, $min_price);
        update_post_meta($_accommodation_id, $min_price_check_meta_key, time());         

        return $min_price;
    }

    function get_min_future_date($accommodation_id) {
        global $wpdb, $bookyourtravel_theme_globals;

        $accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($accommodation_id, 'accommodation');

		$start_date = date("Y-m-d", time());
		$end_date = date('Y-m-d', strtotime($start_date . " +50 months"));

        $accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
		$is_reservation_only = (int)$accommodation_obj->get_is_reservation_only();		
        
        $sql = "
        SELECT IFNULL(MIN(the_date), 0) min_date
        FROM
        (
            SELECT DISTINCT (avc.number_of_available_rooms - IFNULL(bc.number_of_booked_rooms, 0)) available_rooms, avc.the_date, IFNULL(avc.price_per_day, 0) price_per_day FROM
            (
                SELECT SUM(room_count) number_of_available_rooms, possible_dates.the_date, MIN(price_per_day) price_per_day
                FROM " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . " availabilities
                INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date > DATE(availabilities.start_date) AND possible_dates.the_date <= DATE(availabilities.end_date)
                WHERE availabilities.accommodation_id = %d ";

        // , availabilities.price_per_day
        $sql .= " GROUP BY possible_dates.the_date
            ) as avc
            LEFT JOIN
            (
                SELECT SUM(bookings.room_count) number_of_booked_rooms, possible_dates.the_date booking_date
                FROM " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " bookings
                INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date >= DATE(bookings.date_from) AND possible_dates.the_date < DATE(bookings.date_to)
                WHERE bookings.accommodation_id = %d ";

        if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$is_reservation_only) {
            $completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
            if (!empty($completed_statuses)) {
                $sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ") ";
            }
        }

        $sql .= " GROUP BY possible_dates.the_date
            ) as bc
            ON bc.booking_date = avc.the_date
            HAVING available_rooms > 0
        ) as pr ";

        $sql = $wpdb->prepare($sql, $start_date, $start_date, $end_date, $accommodation_id, $start_date, $start_date, $end_date, $accommodation_id);

        $min_date = $wpdb->get_var($sql);

        return $min_date;
    }    

    function get_min_static_from_price_by_location($_location_id) {
        $min_price = 0;

        global $wpdb, $bookyourtravel_theme_globals;

        $location_id = BookYourTravel_Theme_Utils::get_default_language_post_id($_location_id, 'location');
        $location_obj = new BookYourTravel_Location($location_id);

        $min_price_meta_key = BookYourTravel_Theme_Utils::build_min_price_meta_key("accommodations");
        $min_price_check_meta_key = BookYourTravel_Theme_Utils::build_min_price_check_meta_key("accommodations");
        $min_price = $location_obj->_get_cached_price($min_price_meta_key, $min_price_check_meta_key);

        if ($min_price == 0 || $skip_cache) {
            $post_ids = $location_obj->get_accommodation_ids();

            if (count($post_ids) > 0) {
                $post_ids = array_map(function ($v) {
                    return "'" . esc_sql($v) . "'";
                }, $post_ids);
                $post_ids_str = implode(',', $post_ids);                  

                $sql = "SELECT IFNULL(MIN(meta_value), 0) min_price 
                    FROM $wpdb->postmeta as meta
                    WHERE meta_key='accommodation_static_from_price' AND post_id IN ($post_ids_str) ";

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

        $min_price_meta_key = BookYourTravel_Theme_Utils::build_min_price_meta_key("accommodations", $start_date, $end_date);
        $min_price_check_meta_key = BookYourTravel_Theme_Utils::build_min_price_check_meta_key("accommodations", $start_date, $end_date);
        $min_price = $location_obj->_get_cached_price($min_price_meta_key, $min_price_check_meta_key);

        if ($min_price == 0 || $skip_cache) {

            delete_post_meta($location_id, $min_price_meta_key);
            delete_post_meta($_location_id, $min_price_meta_key);

            $accommodation_ids = $location_obj->get_accommodation_ids();

            if (count($accommodation_ids) > 0) {
                $accommodation_ids = array_map(function ($v) {
                    return "'" . esc_sql($v) . "'";
                }, $accommodation_ids);
                $accommodation_ids_str = implode(',', $accommodation_ids);

                $sql = "
                SELECT IFNULL(MIN(price_per_day), 0) min_price
                FROM
                (
                    SELECT DISTINCT (avc.number_of_available_rooms - IFNULL(bc.number_of_booked_rooms, 0)) available_rooms, avc.the_date, avc.price_per_day FROM
                    (
                        SELECT SUM(room_count) number_of_available_rooms, possible_dates.the_date, price_per_day, accommodation_id
                        FROM " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . " availabilities
                        INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date > DATE(availabilities.start_date) AND possible_dates.the_date <= DATE(availabilities.end_date)
                        WHERE availabilities.accommodation_id IN ($accommodation_ids_str) ";

                $sql .= " GROUP BY possible_dates.the_date, availabilities.price_per_day
                    ) as avc
                    LEFT JOIN
                    (
                        SELECT SUM(bookings.room_count) number_of_booked_rooms, possible_dates.the_date booking_date, accommodation_id
                        FROM " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " bookings
                        INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON possible_dates.the_date >= DATE(bookings.date_from) AND possible_dates.the_date <= DATE(bookings.date_to)
                        WHERE bookings.accommodation_id IN ($accommodation_ids_str)
                ";

                if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout()) {
                    $completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
                    if (!empty($completed_statuses)) {
                        $sql .= " AND IFNULL(woo_status, '') IN (" . $completed_statuses . ")";
                    }
                }
                
                $sql .= " GROUP BY possible_dates.the_date
                    ) as bc
                    ON bc.booking_date = avc.the_date AND bc.accommodation_id = avc.accommodation_id
                    HAVING available_rooms > 0
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

    function get_prices_json() {
        global $bookyourtravel_theme_globals;

        if (isset($_REQUEST)) {

            $nonce = wp_kses($_REQUEST['nonce'], array());

            if (wp_verify_nonce($nonce, 'bookyourtravel_nonce')) {
                $accommodation_id = isset($_REQUEST['accommodation_id']) ? intval(wp_kses($_REQUEST['accommodation_id'], array())) : 0;
                $room_type_id = isset($_REQUEST['room_type_id']) ? intval(wp_kses($_REQUEST['room_type_id'], array())) : 0;
                $search_date = isset($_REQUEST['the_date']) ? wp_kses($_REQUEST['the_date'], array()) : 0;
                $prices = $this->get_prices($search_date, $accommodation_id, $room_type_id);

                if (isset($prices->regular_price)) {
                    $prices->regular_price = BookYourTravel_Theme_Utils::get_price_in_current_currency($prices->regular_price);
                    $prices->regular_price_child = BookYourTravel_Theme_Utils::get_price_in_current_currency($prices->regular_price_child);
                    $prices->weekend_price = BookYourTravel_Theme_Utils::get_price_in_current_currency($prices->weekend_price);
                    $prices->weekend_price_child = BookYourTravel_Theme_Utils::get_price_in_current_currency($prices->weekend_price_child);
                }

                echo json_encode($prices);
            }
        }

        die();
    }

    function get_prices($search_date, $accommodation_id, $room_type_id = 0, $current_booking_id = 0) {

        global $wpdb, $bookyourtravel_theme_globals;

        $price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();        

        $accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($accommodation_id, 'accommodation');
        if ($room_type_id > 0) {
            $room_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($room_type_id, 'room_type');
        }

        $accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
        $accommodation_is_reservation_only = $accommodation_obj->get_is_reservation_only();

        $search_date = date('Y-m-d', strtotime($search_date));

        $sql = "SELECT a.availability_id, a.price_per_day regular_price, a.price_per_day_child regular_price_child, a.weekend_price_per_day weekend_price, a.weekend_price_per_day_child weekend_price_child, a.room_count, a.booked_rooms,
				(@runtot := @runtot + a.room_count) AS running_available_total
				FROM
				(
					SELECT availables.*, IFNULL(SUM(bookings.room_count), 0) booked_rooms
					FROM
					(
					SELECT availables_inner.*, date_format(DATE(availables_inner.single_date), '%Y-%m-%d 12:00:01') as bookable_single_date ";

        $sql .= $wpdb->prepare("FROM
						(
							SELECT vacancies.Id availability_id, %s single_date, vacancies.price_per_day, vacancies.price_per_day_child, IFNULL(vacancies.weekend_price_per_day, 0) weekend_price_per_day, IFNULL(vacancies.weekend_price_per_day_child, 0) weekend_price_per_day_child, vacancies.room_count
							FROM " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . " vacancies
							WHERE %s >= vacancies.start_date AND %s < vacancies.end_date AND vacancies.accommodation_id = %d ", $search_date, $search_date, $search_date, $accommodation_id);

        if ($room_type_id > 0) {
            $sql .= $wpdb->prepare(" AND vacancies.room_type_id = %d ", $room_type_id);
        }

        $sql .= $wpdb->prepare("
							GROUP BY availability_id
						) availables_inner
					) availables
					LEFT JOIN " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " bookings ON availables.bookable_single_date >= bookings.date_from AND availables.bookable_single_date < bookings.date_to
					AND bookings.accommodation_id = %d ", $accommodation_id);

        if ($room_type_id > 0) {
            $sql .= $wpdb->prepare(" AND bookings.room_type_id = %d ", $room_type_id);
        }

        if ($current_booking_id > 0) {
            $sql .= $wpdb->prepare(" AND bookings.Id <> %d ", $current_booking_id);
        }

        if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && !$accommodation_is_reservation_only) {

            $completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
            if (!empty($completed_statuses)) {
                $sql .= " AND IFNULL(bookings.woo_status, '') IN (" . $completed_statuses . ")";
            }
        }

        $sql .= " GROUP BY availables.availability_id
					ORDER BY availables.price_per_day ASC
				) a, (SELECT @runtot:=0) AS n
				GROUP BY a.availability_id
				HAVING running_available_total > booked_rooms
				ORDER BY price_per_day ASC
				LIMIT 1 ";

        $result = $wpdb->get_row($sql);

        if (isset($result)) {
            $result->is_weekend = BookYourTravel_Theme_Utils::is_weekend($search_date);
        }

        return $result;
    }

    function retrieve_booking_values_from_request($dont_calculate_totals = false) {

        global $bookyourtravel_theme_globals, $bookyourtravel_extra_item_helper;
        global $current_user;

        $enable_extra_items = $bookyourtravel_theme_globals->enable_extra_items();

        $booking_object = null;

        if (isset($_REQUEST)) {

            $booking_object = new stdClass();

            $booking_object->Id = isset($_REQUEST['booking_id']) ? intval(wp_kses($_REQUEST['booking_id'], array())) : 0;
            $booking_object->user_id = $current_user->ID;

            $booking_object->total_price = 0;
            $booking_object->total_accommodation_price = 0;
            $booking_object->total_extra_items_price = 0;

            $booking_object->accommodation_id = isset($_REQUEST['accommodation_id']) ? intval(wp_kses($_REQUEST['accommodation_id'], array())) : 0;
            $booking_object->room_type_id = isset($_REQUEST['room_type_id']) ? intval(wp_kses($_REQUEST['room_type_id'], array())) : 0;
            $booking_object->room_count = isset($_REQUEST['room_count']) ? intval(wp_kses($_REQUEST['room_count'], array())) : 1;
            $booking_object->adults = isset($_REQUEST['adults']) ? intval(wp_kses($_REQUEST['adults'], array())) : 1;
            $booking_object->children = isset($_REQUEST['children']) ? intval(wp_kses($_REQUEST['children'], array())) : 0;
            $booking_object->date_from = isset($_REQUEST['date_from']) ? date('Y-m-d', strtotime(sanitize_text_field($_REQUEST['date_from']))) : null;
            $booking_object->date_to = isset($_REQUEST['date_to']) ? date('Y-m-d', strtotime(sanitize_text_field($_REQUEST['date_to']))) : null;

            $booking_object->room_count = isset($booking_object->room_count) && $booking_object->room_count > 0 ? $booking_object->room_count : 1;

            $booking_object->accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($booking_object->accommodation_id, 'accommodation');
            if ($booking_object->room_type_id > 0) {
                $booking_object->room_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($booking_object->room_type_id, 'room_type');
            }

            $accommodation_count_children_stay_free = get_post_meta($booking_object->accommodation_id, 'accommodation_count_children_stay_free', true);
            $accommodation_count_children_stay_free = isset($accommodation_count_children_stay_free) ? intval($accommodation_count_children_stay_free) : 0;

            $booking_object->billable_children = $booking_object->children - $accommodation_count_children_stay_free;
            $booking_object->billable_children = $booking_object->billable_children > 0 ? $booking_object->billable_children : 0;

            if ($dont_calculate_totals) {
                $booking_object->total_accommodation_price = isset($_REQUEST['total_accommodation_price']) ? $_REQUEST['total_accommodation_price'] : 0;
                $booking_object->total_price = isset($_REQUEST['total_price']) ? $_REQUEST['total_price'] : 0;
            } else {
                $booking_object->total_accommodation_price = $this->calculate_total_accommodation_price($booking_object->accommodation_id, $booking_object->room_type_id, $booking_object->date_from, $booking_object->date_to, $booking_object->room_count, $booking_object->adults, $booking_object->billable_children, $booking_object->Id);
                if ($booking_object->total_accommodation_price == -1) {
                    return null;
                }
                $booking_object->total_price += $booking_object->total_accommodation_price;
            }

            $booking_object->date_from = date('Y-m-d 12:00:00', strtotime($booking_object->date_from));
            $booking_object->date_to = date('Y-m-d 12:00:00', strtotime($booking_object->date_to));

            $booking_object->extra_items = null;

            if ($dont_calculate_totals) {
                $booking_object->total_extra_items_price = isset($_REQUEST['total_extra_items_price']) ? $_REQUEST['total_extra_items_price'] : 0;
            } else if ($enable_extra_items && isset($_REQUEST['extra_items'])) {

                $booking_object->submitted_extra_items_array = (array) $_REQUEST['extra_items'];

                $booking_object->extra_items = array();

                $from_time = strtotime($booking_object->date_from);
                $to_time = strtotime($booking_object->date_to);
                $time_diff = $to_time - $from_time;
                $total_days = floor($time_diff / (60 * 60 * 24));
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
                $accommodation_deposit_percentage = get_post_meta($booking_object->accommodation_id, 'accommodation_deposit_percentage', true);
                $accommodation_deposit_percentage = isset($accommodation_deposit_percentage) && $accommodation_deposit_percentage !== "" ? intval($accommodation_deposit_percentage) : 100;
    
                if (!$dont_calculate_totals) {
                    $booking_object->cart_price = $booking_object->total_price * ($accommodation_deposit_percentage/100);
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

                    case 'first_name':{
                            $booking_object->first_name = $field_value;
                            break;}
                    case 'last_name':{
                            $booking_object->last_name = $field_value;
                            break;}
                    case 'company':{
                            $booking_object->company = $field_value;
                            break;}
                    case 'email':{
                            $booking_object->email = $field_value;
                            break;}
                    case 'phone':{
                            $booking_object->phone = $field_value;
                            break;}
                    case 'address':{
                            $booking_object->address = $field_value;
                            break;}
                    case 'address_2':{
                            $booking_object->address_2 = $field_value;
                            break;}
                    case 'town':{
                            $booking_object->town = $field_value;
                            break;}
                    case 'zip':{
                            $booking_object->zip = $field_value;
                            break;}
                    case 'state':{
                            $booking_object->state = $field_value;
                            break;}
                    case 'country':{
                            $booking_object->country = $field_value;
                            break;}
                    case 'special_requirements':{
                            $booking_object->special_requirements = $field_value;
                            break;}
                    default:{
                            $booking_object->other_fields[$field_id] = $field_value;
                            break;
                        }
                    }
                }
            }
        }

        return $booking_object;
    }

    function calculate_total_accommodation_price($accommodation_id, $room_type_id, $date_from, $date_to, $room_count, $adults, $children, $current_booking_id = 0) {

        global $wpdb;

        $accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($accommodation_id, 'accommodation');
        if ($room_type_id > 0) {
            $room_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($room_type_id, 'room_type');
        }

        $accommodation_is_price_per_person = get_post_meta($accommodation_id, 'accommodation_is_price_per_person', true);
        $accommodation_is_price_per_person = isset($accommodation_is_price_per_person) ? intval($accommodation_is_price_per_person) : 0;

        $accommodation_rent_type = get_post_meta($accommodation_id, 'accommodation_rent_type', true);
        $accommodation_rent_type = $accommodation_rent_type > 0 ? $accommodation_rent_type : 0;

        // we are actually (in terms of db data) looking for date 1 day before the to date
        // e.g. when you look to book a room from 19.12. to 20.12 you will be staying 1 night, not 2
        $date_to = date('Y-m-d', strtotime($date_to . ' -1 day'));

        $dates = BookYourTravel_Theme_Utils::get_dates_from_range($date_from, $date_to, $accommodation_rent_type);

        $total_price = 0;

        foreach ($dates as $date) {

            $date = date('Y-m-d', strtotime($date));

            $prices_row = $this->get_prices($date, $accommodation_id, $room_type_id, $current_booking_id);

            if (isset($prices_row)) {

                $regular_price = $prices_row->regular_price;
                $regular_price_child = $prices_row->regular_price_child;
                $weekend_price = isset($prices_row->weekend_price) ? $prices_row->weekend_price : 0;
                $weekend_price_child = isset($prices_row->weekend_price_child) ? $prices_row->weekend_price_child : 0;
                $is_weekend = BookYourTravel_Theme_Utils::is_weekend($date);

                if ($accommodation_is_price_per_person) {

                    $price_per_room = 0;

                    if ($is_weekend) {
                        if ($weekend_price && $weekend_price > 0) {
                            $price_per_room += ($adults * $weekend_price);
                        } else {
                            $price_per_room += ($adults * $regular_price);
                        }

                        if ($weekend_price_child && $weekend_price_child > 0) {
                            $price_per_room += ($children * $weekend_price_child);
                        } else {
                            $price_per_room += ($children * $regular_price_child);
                        }
                    } else {
                        $price_per_room += (($adults * $regular_price) + ($children * $regular_price_child));
                    }

                    $total_price += $price_per_room * $room_count;

                } else {
                    if ($is_weekend && $weekend_price && $weekend_price > 0) {
                        $total_price += ($weekend_price * $room_count);
                    } else {
                        $total_price += ($regular_price * $room_count);
                    }
                }
            } else {
                return -1;
            }
        }

        return $total_price;
    }

    function update_accommodation_booking($booking_id, $booking_object) {

        global $wpdb;

        $result = 0;

        $sql = "UPDATE " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " SET ";

        $field_sql = '';

        foreach ($booking_object as $field_key => $field_value) {

            switch ($field_key) {
            case 'user_id': $field_sql .= $wpdb->prepare("user_id = %d, ", $field_value);
                break;
            case 'accommodation_id':$field_sql .= $wpdb->prepare("accommodation_id = %d, ", $field_value);
                break;
            case 'room_type_id':$field_sql .= $wpdb->prepare("room_type_id = %d, ", $field_value);
                break;
            case 'date_from':$field_sql .= $wpdb->prepare("date_from = %s, ", $field_value);
                break;
            case 'date_to':$field_sql .= $wpdb->prepare("date_to = %s, ", $field_value);
                break;
            case 'adults':$field_sql .= $wpdb->prepare("adults = %d, ", $field_value);
                break;
            case 'children':$field_sql .= $wpdb->prepare("children = %d, ", $field_value);
                break;
            case 'room_count':$field_sql .= $wpdb->prepare("room_count = %d, ", $field_value);
                break;
            case 'user_id':$field_sql .= $wpdb->prepare("user_id = %d, ", $field_value);
                break;
            case 'first_name':$field_sql .= $wpdb->prepare("first_name = %s, ", $field_value);
                break;
            case 'last_name':$field_sql .= $wpdb->prepare("last_name = %s, ", $field_value);
                break;
            case 'company':$field_sql .= $wpdb->prepare("company = %s, ", $field_value);
                break;
            case 'email':$field_sql .= $wpdb->prepare("email = %s, ", $field_value);
                break;
            case 'phone':$field_sql .= $wpdb->prepare("phone = %s, ", $field_value);
                break;
            case 'address':$field_sql .= $wpdb->prepare("address = %s, ", $field_value);
                break;
            case 'address_2':$field_sql .= $wpdb->prepare("address_2 = %s, ", $field_value);
                break;
            case 'town':$field_sql .= $wpdb->prepare("town = %s, ", $field_value);
                break;
            case 'zip':$field_sql .= $wpdb->prepare("zip = %s, ", $field_value);
                break;
            case 'state':$field_sql .= $wpdb->prepare("state = %s, ", $field_value);
                break;
            case 'country':$field_sql .= $wpdb->prepare("country = %s, ", $field_value);
                break;
            case 'special_requirements':$field_sql .= $wpdb->prepare("special_requirements = %s, ", $field_value);
                break;
            case 'other_fields':$field_sql .= $wpdb->prepare("other_fields = %s, ", serialize($field_value));
                break;
            case 'extra_items':$field_sql .= $wpdb->prepare("extra_items = %s, ", serialize($field_value));
                break;
            case 'total_accommodation_price':$field_sql .= $wpdb->prepare("total_accommodation_price = %f, ", $field_value);
                break;
            case 'total_extra_items_price':$field_sql .= $wpdb->prepare("total_extra_items_price = %f, ", $field_value);
                break;
            case 'total_price':$field_sql .= $wpdb->prepare("total_price = %f, ", $field_value);
                break;
            case 'cart_price': $field_sql .= $wpdb->prepare("cart_price = %f, ", $field_value); break;
            case 'woo_order_id':$field_sql .= $wpdb->prepare("woo_order_id = %d, ", $field_value);
                break;
            case 'cart_key':$field_sql .= $wpdb->prepare("cart_key = %s, ", $field_value);
                break;
            case 'woo_status':$field_sql .= $wpdb->prepare("woo_status = %s, ", $field_value);
                break;
            default:break;
            }
        }

        if (!empty($field_sql)) {

            $field_sql = rtrim($field_sql, ", ");

            $sql .= $field_sql;

            $sql .= $wpdb->prepare(" WHERE Id = %d;", $booking_id);

            $result = $wpdb->query($sql);

        }

        if (isset($booking_object->accommodation_id)) {
            $this->clear_price_meta_cache($booking_object->accommodation_id);
        }

        return $result;
    }

    function delete_accommodation_booking($booking_id) {

        do_action('bookyourtravel_before_delete_accommodation_booking', $booking_id);

        global $wpdb;

        $booking = $this->get_accommodation_booking($booking_id);
        if ($booking) {
            $accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($booking->accommodation_id, 'accommodation');
            $this->clear_price_meta_cache($accommodation_id);
        }

        $sql = "DELETE FROM " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " WHERE Id = %d";
        $wpdb->query($wpdb->prepare($sql, $booking_id));
    }

    function update_booking_woocommerce_info($booking_id, $cart_key = null, $woo_order_id = null, $woo_status = null) {

        global $wpdb;

        if (isset($cart_key) || isset($woo_order_id) || isset($woo_status)) {
            $sql = "UPDATE " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . "
					SET ";

            if (isset($cart_key)) {
                $sql .= $wpdb->prepare("cart_key = %s, ", $cart_key);
            }

            if (isset($woo_order_id)) {
                $sql .= $wpdb->prepare("woo_order_id = %d, ", $woo_order_id);
            }

            if (isset($woo_status)) {
                $sql .= $wpdb->prepare("woo_status = %s, ", $woo_status);
            }

            $sql = rtrim($sql, ", ");
            $sql .= $wpdb->prepare(" WHERE Id = %d", $booking_id);

            return $wpdb->query($sql);
        }

        return null;
    }

    function get_accommodation_booking($booking_id) {

        global $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;

        $sql = "SELECT DISTINCT bookings.*, accommodations.post_title accommodation_name, room_types.post_title room_type, 'accommodation_booking' entry_type
				FROM " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " bookings ";

        if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('accommodation') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
            $sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations translations_default ON translations_default.element_type = 'post_accommodation' AND translations_default.language_code='" . BookYourTravel_Theme_Utils::get_default_language() . "' AND translations_default.element_id = bookings.accommodation_id ";
            $sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations translations ON translations.element_type = 'post_accommodation' AND translations.language_code='" . ICL_LANGUAGE_CODE . "' AND translations.trid = translations_default.trid ";
        }

        $sql .= " INNER JOIN $wpdb->posts accommodations ON ";
        if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('accommodation') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
            $sql .= " accommodations.ID = translations.element_id ";
        } else {
            $sql .= " accommodations.ID = bookings.accommodation_id ";
        }

        $sql .= $wpdb->prepare(" LEFT JOIN $wpdb->posts room_types ON room_types.ID = bookings.room_type_id
				WHERE accommodations.post_status = 'publish' AND (room_types.post_status IS NULL OR room_types.post_status = 'publish')
				AND bookings.Id = %d ", $booking_id);

        return $wpdb->get_row($sql);
    }

    function list_accommodation_bookings($paged = null, $per_page = 0, $orderby = 'Id', $order = 'ASC', $search_term = null, $user_id = 0, $author_id = null, $accommodation_id = null, $room_type_id = null) {

        global $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;

        $sql = "SELECT DISTINCT bookings.*, accommodations.post_title accommodation_name, room_types.post_title room_type, 'accommodation_booking' entry_type
				FROM " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " bookings ";

        if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('accommodation') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
            $sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations translations_default ON translations_default.element_type = 'post_accommodation' AND translations_default.language_code='" . BookYourTravel_Theme_Utils::get_default_language() . "' AND translations_default.element_id = bookings.accommodation_id ";
            $sql .= " INNER JOIN " . $wpdb->prefix . "icl_translations translations ON translations.element_type = 'post_accommodation' AND translations.language_code='" . ICL_LANGUAGE_CODE . "' AND translations.trid = translations_default.trid ";
        }

        $sql .= " INNER JOIN $wpdb->posts accommodations ON ";
        if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('accommodation') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
            $sql .= " accommodations.ID = translations.element_id ";
        } else {
            $sql .= " accommodations.ID = bookings.accommodation_id ";
        }

        $sql .= " LEFT JOIN $wpdb->posts room_types ON room_types.ID = bookings.room_type_id ";
        $sql .= " WHERE accommodations.post_status = 'publish' AND (room_types.post_status IS NULL OR room_types.post_status = 'publish') ";

        if ($accommodation_id > 0) {
            $sql .= $wpdb->prepare(" AND bookings.accommodation_id = %d ", $accommodation_id);
        }

        if ($room_type_id > 0) {
            $sql .= $wpdb->prepare(" AND bookings.room_type_id = %d ", $room_type_id);
        }        

        if ($user_id > 0) {
            $sql .= $wpdb->prepare(" AND bookings.user_id = %d ", $user_id);
        }

        if ($search_term != null && !empty($search_term)) {
            $search_term_esc = "%" . $wpdb->esc_like($search_term) . "%";
            $sql .= $wpdb->prepare(" AND 1=1 AND (LCASE(bookings.first_name) LIKE '%s' OR LCASE(bookings.last_name) LIKE '%s' OR accommodations.post_title LIKE '%s') ", $search_term, $search_term, $search_term_esc);
        }

        if (isset($author_id)) {
            $sql .= $wpdb->prepare(" AND accommodations.post_author = %d ", $author_id);
        }

        if (!empty($orderby) & !empty($order)) {
            $sql .= ' ORDER BY ' . $orderby . ' ' . $order;
        }

        $sql_count = $sql;

        if (!empty($paged) && !empty($per_page)) {
            $offset = ($paged - 1) * $per_page;
            $sql .= $wpdb->prepare(" LIMIT %d, %d ", $offset, $per_page);
        }

        $results = array(
            'total' => $wpdb->query($sql_count),
            'results' => $wpdb->get_results($sql),
        );

        return $results;
    }

    function create_accommodation_booking($user_id, $booking_object) {

        global $wpdb;

        $errors = array();

        $sql = "INSERT INTO " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . "
				(user_id, accommodation_id, room_type_id, room_count, adults, children, date_from, date_to, first_name, last_name, company, email, phone, address, address_2, town, zip, state, country, special_requirements, other_fields, extra_items, total_accommodation_price, total_extra_items_price, total_price, cart_price)
				VALUES
				(%d, %d, %d, %d, %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %f, %f, %f, %f);";

        $result = $wpdb->query($wpdb->prepare($sql, $user_id, $booking_object->accommodation_id, $booking_object->room_type_id, $booking_object->room_count, $booking_object->adults, $booking_object->children, $booking_object->date_from, $booking_object->date_to, $booking_object->first_name, $booking_object->last_name, $booking_object->company, $booking_object->email, $booking_object->phone, $booking_object->address, $booking_object->address_2, $booking_object->town, $booking_object->zip, $booking_object->state, $booking_object->country, $booking_object->special_requirements, serialize($booking_object->other_fields), serialize($booking_object->extra_items), $booking_object->total_accommodation_price, $booking_object->total_extra_items_price, $booking_object->total_price, $booking_object->cart_price));

        if (is_wp_error($result)) {
            $errors[] = $result;
        }

        $booking_object->Id = $wpdb->insert_id;

        $this->clear_price_meta_cache($booking_object->accommodation_id);

        return $booking_object->Id;
    }

    function clear_price_meta_cache($accommodation_id) {
		global $wpdb;
		$search_term = "%accommodation_min_price%";
		$sql = $wpdb->prepare("DELETE FROM $wpdb->postmeta WHERE post_id=%d AND meta_key LIKE '%s'", $accommodation_id, $search_term);
		$wpdb->query($sql);

        $accommodation_obj = new BookYourTravel_Accommodation($accommodation_id);
        if ($accommodation_obj) {
            $location_id = $accommodation_obj->get_custom_field('location_post_id');

            $search_term = "%accommodations_min_price%";
            $sql = $wpdb->prepare("DELETE FROM $wpdb->postmeta WHERE post_id=%d AND meta_key LIKE '%s'", $location_id, $search_term);
            $wpdb->query($sql);            
        }
    }

    function list_accommodations_count($paged = 0, $per_page = -1, $orderby = '', $order = '', $location_ids = array(), $accommodation_types_array = array(), $accommodation_tags_array = array(), $accommodation_facilities_array = array(), $search_args = array(), $featured_only = false, $disabled_room_types = null, $author_id = null, $include_private = false, $count_only = false) {
        $results = $this->list_accommodations($paged, $per_page, $orderby, $order, $location_ids, $accommodation_types_array, $accommodation_tags_array, $accommodation_facilities_array, $search_args, $featured_only, $disabled_room_types, $author_id, $include_private, true);
        return $results['total'];
    }

    function list_accommodations($paged = 0, $per_page = -1, $orderby = '', $order = '', $param_location_ids = array(), $accommodation_types_array = array(), $accommodation_tags_array = array(), $accommodation_facilities_array = array(), $search_args = array(), $featured_only = false, $disabled_room_types = null, $author_id = null, $include_private = false, $count_only = false) {

        global $bookyourtravel_theme_globals;
        $location_ids = array();

        if (count($param_location_ids) > 0 && is_array($param_location_ids)) {
            foreach ($param_location_ids as $location_id) {
                if ($location_id > 0) {
                    $location_id = BookYourTravel_Theme_Utils::get_default_language_post_id(intval($location_id), 'location');
                    $location_ids[] = $location_id;
                    $location_descendants = BookYourTravel_Theme_Utils::get_post_descendants($location_id, 'location');
                    foreach ($location_descendants as $location) {
                        $location_ids[] = BookYourTravel_Theme_Utils::get_default_language_post_id($location->ID, 'location');
                    }
                }
            }
        }

        if (isset($search_args['keyword']) && strlen($search_args['keyword']) > 0) {
            $args = array(
                's' => $search_args['keyword'],
                'post_type' => 'location',
                'posts_per_page'=> -1, 
                'post_status' => 'publish',
                'suppress_filters' => false,
            );

            $location_posts = get_posts($args);
            foreach ($location_posts as $location) {
                $location_ids[] = BookYourTravel_Theme_Utils::get_default_language_post_id($location->ID, 'location');
            }

            $descendant_location_ids = array();
            foreach ($location_ids as $temp_location_id) {
                $location_descendants = BookYourTravel_Theme_Utils::get_post_descendants($temp_location_id, 'location');
                foreach ($location_descendants as $location) {
                    $descendant_location_ids[] = BookYourTravel_Theme_Utils::get_default_language_post_id($location->ID, 'location');
                }
            }

            $location_ids = array_merge($descendant_location_ids, $location_ids);
        }

        $args = array(
            'post_type' => 'accommodation',
            'post_status' => array('publish'),
            'posts_per_page' => $per_page,
            'paged' => $paged,
            'orderby' => $orderby,
            'suppress_filters' => false,
            'order' => $order,
        );

        if ($orderby == 'star_count') {
            $args['meta_key'] = 'accommodation_star_count';
            $args['orderby'] = 'meta_value_num';
        } else if ($orderby == 'review_score') {
            $args['meta_key'] = 'review_score';
            $args['orderby'] = 'meta_value_num';
        } else if ($orderby == 'price' || $orderby == 'min_price') {
            if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
                $args['byt_orderby'] = 'accommodation_static_price';
            } else {
                $args['byt_orderby'] = 'accommodation_price';
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

        $meta_query = array();

        if (isset($search_args['stars']) && strlen($search_args['stars']) > 0) {
            $stars = intval($search_args['stars']);
            if ($stars > 0 & $stars <= 5) {
                $meta_query[] = array(
                    'key' => 'accommodation_star_count',
                    'value' => $stars,
                    'compare' => '>=',
                    'type' => 'numeric',
                );
            }
        }

        if (isset($search_args['rating']) && strlen($search_args['rating']) > 0) {
            $rating = floatval(intval($search_args['rating']) / 10);
            if ($rating > 0 & $rating <= 10) {
                $meta_query[] = array(
                    'relation' => 'AND',
                    array(
                        'key' => 'review_score',
                        'value' => $rating,
                        'type' => 'DECIMAL',
                        'compare' => '>=',
                    ),
                );
            }
        }

        if (isset($disabled_room_types)) {

            $args['byt_disabled_room_types'] = $disabled_room_types;
            if ($disabled_room_types) {
                $meta_query[] = array(
                    'key' => 'accommodation_disabled_room_types',
                    'value' => '1',
                    'compare' => '=',
                    'type' => 'numeric',
                );
            } else {
                $meta_query[] = array(
                    'key' => 'accommodation_disabled_room_types',
                    'compare' => 'NOT EXISTS',
                );
            }
        }

        if (isset($featured_only) && $featured_only) {
            $meta_query[] = array(
                'key' => 'accommodation_is_featured',
                'value' => 1,
                'compare' => '=',
                'type' => 'numeric',
            );
        }

        if (isset($author_id)) {
            $author_id = (int) ($author_id);
            if ($author_id > 0) {
                $args['author'] = $author_id;
            }
        }

        if (count($location_ids) > 0) {
            $meta_query[] = array(
                'key' => 'accommodation_location_post_id',
                'value' => $location_ids,
                'compare' => 'IN',
            );
            $args['byt_location_ids'] = $location_ids;
        }

		$args['tax_query'] = array();

        if (!empty($accommodation_types_array)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'accommodation_type',
                'field' => 'term_id',
                'terms' => $accommodation_types_array,
                'operator' => 'IN',
            );
        }

        if (!empty($accommodation_tags_array)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'acc_tag',
                'field' => 'term_id',
                'terms' => $accommodation_tags_array,
                'operator' => 'IN',
            );
        }

        if (!empty($accommodation_facilities_array)) {
            $args['tax_query'][] = array(
                'taxonomy' => 'facility',
                'field' => 'term_id',
                'terms' => $accommodation_facilities_array,
                'operator' => 'IN',
            );
        }

        $search_only_available = false;
        if (isset($search_args['search_only_available'])) {
            $search_only_available = $search_args['search_only_available'];
        }

        if (isset($search_args['date_from'])) {
            $args['byt_date_from'] = $search_args['date_from'];
        }
        if (isset($search_args['date_to'])) {
            $args['byt_date_to'] = $search_args['date_to'];
        }
        if (isset($search_args['rooms'])) {
            $args['byt_rooms'] = $search_args['rooms'];
        }

        if ($count_only) {
            $args['byt_count_only'] = 1;
        }

        if (isset($search_args['prices'])) {
            $args['prices'] = $search_args['prices'];
            $args['price_range_bottom'] = $bookyourtravel_theme_globals->get_price_range_bottom();
            $args['price_range_increment'] = $bookyourtravel_theme_globals->get_price_range_increment();
            $args['price_range_count'] = $bookyourtravel_theme_globals->get_price_range_count();
        }

        $args['search_only_available'] = $search_only_available;

        add_filter('posts_where', array($this, 'build_accommodations_search_where'), 10, 2);
        add_filter('posts_fields', array($this, 'build_accommodations_search_fields'), 10, 2);
        add_filter('posts_groupby', array($this, 'build_accommodations_search_groupby'), 10, 2);
        add_filter('posts_join', array($this, 'build_accommodations_search_join'), 10, 2);
        add_filter('posts_orderby', array($this, 'build_accommodations_search_orderby'), 10, 2);

        $args['meta_query'] = $meta_query;

        $posts_query = new WP_Query($args);

        echo "<div style='display:none' class='query'>";
        echo $posts_query->request;
        echo "</div>";

        if ($count_only) {
            $results = array(
                'total' => $posts_query->found_posts,
                'results' => null,
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
                'results' => $results,
            );
        }

        wp_reset_postdata();

        remove_filter('posts_where', array($this, 'build_accommodations_search_where'));
        remove_filter('posts_fields', array($this, 'build_accommodations_search_fields'));
        remove_filter('posts_groupby', array($this, 'build_accommodations_search_groupby'));
        remove_filter('posts_join', array($this, 'build_accommodations_search_join'));
        remove_filter('posts_orderby', array($this, 'build_accommodations_search_orderby'));

        return $results;
    }

    function build_accommodations_search_fields($fields, $wp_query) {

        global $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;

        if (isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'accommodation') {

            $search_only_available = false;
            if (isset($wp_query->query_vars['search_only_available'])) {
                $search_only_available = $wp_query->get('search_only_available');
            }

            $date_today = date('Y-m-d', time());
            $date_from = null;
            if (isset($wp_query->query_vars['byt_date_from'])) {
                $date_from = date('Y-m-d', strtotime($wp_query->get('byt_date_from')));
            } else {
                $date_from = $date_today;
            }

            $date_to = null;
            if (isset($wp_query->query_vars['byt_date_to'])) {
                $date_to = date('Y-m-d', strtotime($wp_query->get('byt_date_to') . ' -1 day'));
            } else {
                $date_to = date('Y-m-d', strtotime($date_from . ' +24 months'));
            }

            if (isset($date_from) && $date_from == $date_to) {
                $date_to = date('Y-m-d', strtotime($date_from . ' +7 day'));
            }

            if ($search_only_available || isset($wp_query->query_vars['byt_date_from']) || isset($wp_query->query_vars['byt_date_from'])) {

                if ((isset($date_from) || isset($date_to))) {

                    $date_range_match = ' (possible_dates.the_date >= DATE(availabilities.start_date) AND possible_dates.the_date < DATE(availabilities.end_date)) ';

                    $fields .= ", (
									SELECT SUM(rooms_available) rooms_available FROM (SELECT IFNULL(SUM(room_count), 0) rooms_available, availabilities.accommodation_id FROM " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . " availabilities ";

                    $fields .= $wpdb->prepare("INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON " . $date_range_match, $date_from, $date_from, $date_to);

                    $fields .= " WHERE 1=1 ";

                    if ($date_from != null && $date_to != null) {
                        $fields .= $wpdb->prepare(" AND (%s BETWEEN start_date AND end_date OR start_date <= %s) ", $date_from, $date_to);
                    } else if ($date_from != null) {
                        $fields .= $wpdb->prepare(" AND %s BETWEEN start_date AND end_date ", $date_from);
                    } else if ($date_to != null) {
                        $fields .= $wpdb->prepare(" AND start_date <= %s ", $date_to);
                    }

                    $fields .= $wpdb->prepare(" AND end_date >= %s ", $date_today);

                    $fields .= " GROUP BY possible_dates.the_date, availabilities.accommodation_id ) as ra ";

                    if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('accommodation') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
                        $fields .= " WHERE (accommodation_id = wpml_translations_default.element_id OR accommodation_id = wpml_translations.element_id) ";
                    } else {
                        $fields .= " WHERE accommodation_id = {$wpdb->posts}.ID ";
                    }

                    $fields .= " ) rooms_available ";

                    $date_range_match = ' (possible_dates.the_date >= DATE(bookings.date_from) AND possible_dates.the_date < DATE(bookings.date_to)) ';

                    $fields .= ", (
						SELECT IFNULL(SUM(rooms_booked), 0) rooms_booked FROM ((SELECT IFNULL(SUM(bookings.room_count), 0) rooms_booked, bookings.accommodation_id, possible_dates.the_date FROM " . BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE . " bookings ";

                    $fields .= $wpdb->prepare("INNER JOIN (" . DISTICT_DATE_RANGE_QUERY . ") possible_dates ON " . $date_range_match, $date_from, $date_from, $date_to);
                    
                    if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout()) {
                        $completed_statuses = $bookyourtravel_theme_globals->get_completed_order_woocommerce_statuses();
                        if (!empty($completed_statuses)) {
                            $fields .= " AND IFNULL(woo_status, '') IN (" . $completed_statuses . ")";
                        }
                    }

                    $fields .= " WHERE 1=1 ";
                    
                    $fields .= " GROUP BY possible_dates.the_date, accommodation_id ) as rb, ";

                    $fields .= " (SELECT MIN(start_date) min_start_date, MAX(end_date) max_end_date, accommodation_id FROM " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . " GROUP BY accommodation_id) min_max_available_dates ) ";  


                    if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('accommodation') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
                        $fields .= " WHERE (rb.accommodation_id = wpml_translations_default.element_id AND min_max_available_dates.accommodation_id = wpml_translations_default.element_id) OR (rb.accommodation_id = wpml_translations.element_id AND min_max_available_dates.accommodation_id = wpml_translations.element_id)  ";
                    } else {
                        $fields .= " WHERE rb.accommodation_id = {$wpdb->posts}.ID AND min_max_available_dates.accommodation_id = {$wpdb->posts}.ID ";
                    }

                    $fields .= " AND rb.the_date >= min_max_available_dates.min_start_date ";
                    $fields .= " AND rb.the_date < min_max_available_dates.max_end_date ";

                    $fields .= " ) rooms_booked ";

                } else {
                    $fields .= ", 1 rooms_available, 0 rooms_booked ";
                }
            } else {
                $fields .= ", 1 rooms_available, 0 rooms_booked ";
            }

            if (isset($wp_query->query_vars['byt_date_to'])) {
                $date_to = date('Y-m-d', strtotime($wp_query->get('byt_date_to')));
            } else {
                $date_to = date('Y-m-d', strtotime($date_from . ' +24 months'));
            }

            $min_price_meta_key = BookYourTravel_Theme_Utils::build_min_price_meta_key("accommodation", $date_from, $date_to);
            $min_price_check_meta_key = BookYourTravel_Theme_Utils::build_min_price_check_meta_key("accommodation", $date_from, $date_to);

			$fields_sql = ", IFNULL((SELECT price_meta2.meta_value + 0 FROM {$wpdb->postmeta} price_meta2 ";
			if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('accommodation') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
				$fields_sql .= " WHERE price_meta2.post_id={$wpdb->posts}.ID ";
			} else {
				$fields_sql .= " WHERE price_meta2.post_id={$wpdb->posts}.ID ";
			}
			
			$fields_sql .= " AND price_meta2.meta_key=%s LIMIT 1), 0) accommodation_price ";

            $fields .= $wpdb->prepare($fields_sql, $min_price_meta_key);

			$fields_sql = ", IFNULL((SELECT price_meta3.meta_value + 0 FROM {$wpdb->postmeta} price_meta3 ";
			if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('accommodation') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
				$fields_sql .= " WHERE price_meta3.post_id={$wpdb->posts}.ID ";
			} else {
				$fields_sql .= " WHERE price_meta3.post_id={$wpdb->posts}.ID ";
			}
			
			$fields_sql .= " AND price_meta3.meta_key='accommodation_static_from_price' LIMIT 1), 0) accommodation_static_price ";

            $fields .= $fields_sql;
        }

        return $fields;
    }

    function build_accommodations_search_where($where, $wp_query) {

        global $wpdb;

        if (isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'accommodation') {

            $where = str_replace('DECIMAL', 'DECIMAL(10,2)', $where);

            if (isset($wp_query->query_vars['byt_disabled_room_types'])) {
                $needed_where_part = '';
                $where_array = explode('AND', $where);
                foreach ($where_array as $where_part) {
                    if (strpos($where_part, 'post_id IS NULL') !== false) {
                        // found where part where disabled_room_types is checked for NULL
                        $needed_where_part = $where_part;
                        break;
                    }
                }

                if (!empty($needed_where_part)) {
                    $prefix = str_replace("post_id IS NULL", "", $needed_where_part);
                    $prefix = str_replace(")", "", $prefix);
                    $prefix = str_replace("(", "", $prefix);
                    $prefix = trim($prefix);
                    $where = str_replace("{$prefix}post_id IS NULL", "({$prefix}post_id IS NULL OR CAST({$prefix}meta_value AS SIGNED) = '0')", $where);
                }
            }

            if (isset($wp_query->query_vars['s']) && !empty($wp_query->query_vars['s']) && isset($wp_query->query_vars['byt_location_ids'])) {
                $needed_where_part = '';
                $where_array = explode('AND', $where);
                foreach ($where_array as $where_part) {
                    if (strpos($where_part, "meta_key = 'accommodation_location_post_id'") !== false) {
                        // found where part where disabled_room_types is checked for NULL
                        $needed_where_part = $where_part;
                        break;
                    }
                }

                if (!empty($needed_where_part)) {
                    $prefix = str_replace("meta_key = 'accommodation_location_post_id'", "", $needed_where_part);
                    $prefix = str_replace(")", "", $prefix);
                    $prefix = str_replace("(", "", $prefix);
                    $prefix = trim($prefix);

                    $location_ids = $wp_query->query_vars['byt_location_ids'];
                    $location_ids_str = "'" . implode("','", $location_ids) . "'";
                    $location_search_param_part = "{$prefix}meta_key = 'accommodation_location_post_id' AND CAST({$prefix}meta_value AS CHAR) IN ($location_ids_str)";

                    $where = str_replace($location_search_param_part, "1=1", $where);

                    $post_content_part = "OR ($wpdb->posts.post_content LIKE '%" . $wp_query->get('s') . "%')";
                    $where = str_replace($post_content_part, $post_content_part . " OR ($location_search_param_part) ", $where);
                }
            }
        }

        return $where;
    }

    function build_accommodations_search_groupby($groupby, $wp_query) {

        global $wpdb, $bookyourtravel_theme_globals;

        if (empty($groupby)) {
            $groupby = " {$wpdb->posts}.ID ";
        }

        if (!is_admin()) {

            if (isset($wp_query->query_vars['post_type']) && $wp_query->query_vars['post_type'] == 'accommodation') {

                $date_today = date('Y-m-d', time());
                $date_from = null;
                if (isset($wp_query->query_vars['byt_date_from'])) {
                    $date_from = date('Y-m-d', strtotime($wp_query->get('byt_date_from')));
                } else {
                    $date_from = $date_today;
                }

                $date_to = null;
                if (isset($wp_query->query_vars['byt_date_to'])) {
                    $date_to = date('Y-m-d', strtotime($wp_query->get('byt_date_to') . ' -1 day'));
                } else {
                    $date_to = date('Y-m-d', strtotime($date_from . ' +24 months'));
                }

                if (isset($date_from) && $date_from == $date_to) {
                    $date_to = date('Y-m-d', strtotime($date_from . ' +7 day'));
                }

                $search_only_available = false;
                if (isset($wp_query->query_vars['search_only_available'])) {
                    $search_only_available = $wp_query->get('search_only_available');
                }

                $groupby .= ' HAVING 1=1 ';

                if ($search_only_available) {
                    $groupby .= ' AND rooms_available > rooms_booked ';

                    if (isset($wp_query->query_vars['byt_rooms'])) {
                        $groupby .= $wpdb->prepare(" AND rooms_available >= %d ", $wp_query->query_vars['byt_rooms']);
                    }
                }

				$from_time = strtotime($date_from);
				$to_time = strtotime($date_to);
				$total_days = floor(($to_time - $from_time)/(60*60*24));
				$total_days = $total_days > 0 ? $total_days : 1;

				if ($search_only_available && $total_days > 1 && isset($wp_query->query_vars['byt_date_from']) && isset($wp_query->query_vars['byt_date_to'])) {
					$groupby .= $wpdb->prepare(' AND (rooms_available - rooms_booked) >= %d', $total_days);
				}

                if (isset($wp_query->query_vars['prices'])) {

                    $prices = (array) $wp_query->query_vars['prices'];
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
                                    $groupby .= $wpdb->prepare(" OR (accommodation_price >= %f AND accommodation_price <= %f ) ", $bottom, $top);
                                    $groupby .= $wpdb->prepare(" OR (accommodation_static_price >= %f AND accommodation_static_price <= %f ) ", $bottom, $top);
                                } else {
                                    $groupby .= $wpdb->prepare(" OR (accommodation_price >= %f ) ", $bottom);
                                    $groupby .= $wpdb->prepare(" OR (accommodation_static_price >= %f ) ", $bottom);
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

    function build_accommodations_search_join($join, $wp_query) {

        global $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_theme_globals;

		if (defined('ICL_LANGUAGE_CODE') && $bookyourtravel_theme_globals->is_translatable('accommodation') && (BookYourTravel_Theme_Utils::get_default_language() != ICL_LANGUAGE_CODE || $bookyourtravel_multi_language_count > 1)) {
			$join .= " LEFT JOIN {$wpdb->prefix}icl_translations wpml_translations_default ON wpml_translations_default.trid = wpml_translations.trid AND (wpml_translations_default.source_language_code IS NULL OR wpml_translations.source_language_code IS NULL) ";
		}

        return $join;
    }

    function build_accommodations_search_orderby($orderby, $wp_query) {

        global $wpdb;

        if (isset($wp_query->query_vars['byt_orderby']) && isset($wp_query->query_vars['byt_order'])) {
            $order = 'ASC';
            if ($wp_query->get('byt_order') == 'DESC') {
                $order = 'DESC';
            }

            $column = 'accommodation_price';
            if ($wp_query->get('byt_orderby') == $column) {
                $orderby = $column . ' ' . $order;
            }
            $column = 'accommodation_static_price';
            if ($wp_query->get('byt_orderby') == $column) {
                $orderby = $column . ' ' . $order;
            }
        }

        return $orderby;
    }

    function list_accommodation_vacancies($accommodation_id = 0, $room_type_id = 0, $orderby = 'Id', $order = 'ASC', $paged = null, $per_page = 0, $author_id = null) {

        global $wpdb;

        $accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($accommodation_id, 'accommodation');
        if ($room_type_id > 0) {
            $room_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($room_type_id, 'room_type');
        }

        $sql = "SELECT DISTINCT vacancies.*, accommodations.post_title accommodation_name, room_types.post_title room_type, IFNULL(accommodation_meta_is_per_person.meta_value, 0) accommodation_is_per_person, IFNULL(accommodation_meta_disabled_room_types.meta_value, 0) accommodation_disabled_room_types, 'accommodation_vacancy' entry_type
				FROM " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . " vacancies
				INNER JOIN $wpdb->posts accommodations ON accommodations.ID = vacancies.accommodation_id
				LEFT JOIN $wpdb->postmeta accommodation_meta_is_per_person ON accommodations.ID=accommodation_meta_is_per_person.post_id AND accommodation_meta_is_per_person.meta_key='accommodation_is_price_per_person'
				LEFT JOIN $wpdb->postmeta accommodation_meta_disabled_room_types ON accommodations.ID=accommodation_meta_disabled_room_types.post_id AND accommodation_meta_disabled_room_types.meta_key='accommodation_disabled_room_types'
				LEFT JOIN $wpdb->posts room_types ON room_types.ID = vacancies.room_type_id
				WHERE 1=1 ";

        if ($accommodation_id > 0) {
            $sql .= $wpdb->prepare(" AND vacancies.accommodation_id=%d ", $accommodation_id);
        }

        if ($room_type_id > 0) {
            $sql .= $wpdb->prepare(" AND vacancies.room_type_id=%d ", $room_type_id);
        }

        if (isset($author_id)) {
            $sql .= $wpdb->prepare(" AND accommodations.post_author=%d ", $author_id);
        }

        if (!empty($orderby) & !empty($order)) {
            $sql .= ' ORDER BY ' . $orderby . ' ' . $order;
        }

        $sql_count = $sql;

        if (!empty($paged) && !empty($per_page)) {
            $offset = ($paged - 1) * $per_page;
            $sql .= $wpdb->prepare(" LIMIT %d, %d ", $offset, $per_page);
        }

        $results = array(
            'total' => $wpdb->query($sql_count),
            'results' => $wpdb->get_results($sql),
        );

        return $results;
    }

    function create_accommodation_vacancy($season_name, $start_date, $end_date, $accommodation_id, $room_type_id, $room_count, $price_per_day, $price_per_day_child, $weekend_price_per_day, $weekend_price_per_day_child) {

        global $wpdb;

        $accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($accommodation_id, 'accommodation');
        if ($room_type_id > 0) {
            $room_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($room_type_id, 'room_type');
        }

        $this->clear_price_meta_cache($accommodation_id);

        $sql = "INSERT INTO " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . "
				(season_name, start_date, end_date, accommodation_id, room_type_id, room_count, price_per_day, price_per_day_child, weekend_price_per_day, weekend_price_per_day_child)
				VALUES
				(%s, %s, %s, %d, %d, %d, %f, %f, %f, %f);";

        $wpdb->query($wpdb->prepare($sql, $season_name, $start_date, $end_date, $accommodation_id, $room_type_id, $room_count, $price_per_day, $price_per_day_child, $weekend_price_per_day, $weekend_price_per_day_child));

        $vacancy_id = $wpdb->insert_id;

        return $vacancy_id;
    }

    function update_accommodation_vacancy($vacancy_id, $season_name, $start_date, $end_date, $accommodation_id, $room_type_id, $room_count, $price_per_day, $price_per_day_child, $weekend_price_per_day, $weekend_price_per_day_child) {

        global $wpdb;

        $accommodation_id = BookYourTravel_Theme_Utils::get_default_language_post_id($accommodation_id, 'accommodation');
        if ($room_type_id > 0) {
            $room_type_id = BookYourTravel_Theme_Utils::get_default_language_post_id($room_type_id, 'room_type');
        }

        $this->clear_price_meta_cache($accommodation_id);

        $start_date = date('Y-m-d', strtotime($start_date));
        $end_date = date('Y-m-d', strtotime($end_date));

        $sql = "UPDATE " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . "
				SET season_name=%s, start_date=%s, end_date=%s, accommodation_id=%d, room_type_id=%d, room_count=%d, price_per_day=%f, price_per_day_child=%f, weekend_price_per_day=%f, weekend_price_per_day_child=%f
				WHERE Id=%d";

        $wpdb->query($wpdb->prepare($sql, $season_name, $start_date, $end_date, $accommodation_id, $room_type_id, $room_count, $price_per_day, $price_per_day_child, $weekend_price_per_day, $weekend_price_per_day_child, $vacancy_id));

        return $vacancy_id;
    }

    function delete_accommodation_vacancy($vacancy_id) {

        global $wpdb;

        $vacancy = $this->get_accommodation_vacancy($vacancy_id);

        $this->clear_price_meta_cache($vacancy->accommodation_id);

        $sql = "DELETE FROM " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . "
				WHERE Id = %d";

        $wpdb->query($wpdb->prepare($sql, $vacancy_id));
    }

    function get_accommodation_vacancy($vacancy_id) {

        global $wpdb;

        $sql = "SELECT vacancies.*, accommodations.post_title accommodation_name, room_types.post_title room_type, 'accommodation_vacancy' entry_type
				FROM " . BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE . " vacancies
				INNER JOIN $wpdb->posts accommodations ON accommodations.ID = vacancies.accommodation_id
				LEFT JOIN $wpdb->posts room_types ON room_types.ID = vacancies.room_type_id
				WHERE vacancies.Id=%d ";

        return $wpdb->get_row($wpdb->prepare($sql, $vacancy_id));
    }
}

global $bookyourtravel_accommodation_helper;
// store the instance in a variable to be retrieved later and call init
$bookyourtravel_accommodation_helper = BookYourTravel_Accommodation_Helper::get_instance();
$bookyourtravel_accommodation_helper->init();

add_shortcode('byt_accommodation_card', 'byt_accommodation_card_shortcode');
function byt_accommodation_card_shortcode($atts) {

    global $accommodation_item_args;

    extract(shortcode_atts(array(
        'accommodation_id' => 0,
        'show_fields' => 'title,image,actions',
        'css' => '',
    ), $atts));

    $show_fields = explode(',', $show_fields);

    $accommodation_item_args = array();
    $accommodation_item_args['accommodation_id'] = $accommodation_id;
    if ($accommodation_id > 0) {
        $accommodation_item_args['post'] = get_post($accommodation_id);
    }
    $accommodation_item_args['hide_title'] = !in_array('title', $show_fields);
    $accommodation_item_args['hide_image'] = !in_array('image', $show_fields);
    $accommodation_item_args['hide_actions'] = !in_array('actions', $show_fields);
    $accommodation_item_args['hide_description'] = !in_array('description', $show_fields);
    $accommodation_item_args['hide_address'] = !in_array('address', $show_fields);
    $accommodation_item_args['hide_stars'] = !in_array('stars', $show_fields);
    $accommodation_item_args['hide_rating'] = !in_array('rating', $show_fields);
    $accommodation_item_args['hide_price'] = !in_array('price', $show_fields);
    $accommodation_item_args['item_class'] = 'single-card';

    $output = '';

    ob_start();
    get_template_part('includes/parts/accommodation/accommodation', 'item');

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
