<?php
/**
 * BookYourTravel_Location_Helper class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-location.php');

class BookYourTravel_Location_Helper extends BookYourTravel_BaseSingleton {

	private $location_list_custom_meta_fields;
	private $location_list_custom_meta_tabs;
	private $location_custom_meta_fields;
	private $location_custom_meta_tabs;
	private $location_list_meta_box;

	protected function __construct() {

        // our parent class might
        // contain shared code in its constructor
        parent::__construct();
    }

    public function init() {

		add_action( 'bookyourtravel_delete_location', array( $this, 'delete_location' ), 10, 1);
		add_action( 'bookyourtravel_save_location', array( $this, 'save_location' ), 10, 1);
		add_action( 'admin_init', array($this, 'remove_unnecessary_meta_boxes') );
		add_action( 'admin_init', array( $this, 'location_admin_init' ) );
        add_action( 'bookyourtravel_initialize_post_types', array( $this, 'initialize_post_type' ), 0);
        add_filter( 'bookyourtravel_custom_taxonomy_list', array($this, 'custom_taxonomy_list'), 10, 1);

        add_action( 'byt_ajax_handler_nopriv_location_load_min_price_ajax_request', array( $this, 'get_min_price_json' ) );
		add_action( 'byt_ajax_handler_location_load_min_price_ajax_request', array( $this, 'get_min_price_json' ) );

        add_action( 'wp_ajax_nopriv_location_load_min_price_ajax_request', array( $this, 'get_min_price_json' ) );
		add_action( 'wp_ajax_location_load_min_price_ajax_request', array( $this, 'get_min_price_json' ) );

		$this->initialize_meta_fields();
    }

    function get_min_price_json() {
        global $bookyourtravel_theme_globals, $bookyourtravel_accommodation_helper, $bookyourtravel_car_rental_helper, $bookyourtravel_cruise_helper, $bookyourtravel_tour_helper;

		if ( isset($_REQUEST) ) {
			$nonce = wp_kses($_REQUEST['nonce'], array());
			if ( wp_verify_nonce( $nonce, 'bookyourtravel_nonce' ) ) {
				$location_id = isset($_REQUEST['location_id']) ? intval(wp_kses($_REQUEST['location_id'], array())) : 0;
				$start_date = isset($_REQUEST['start_date']) ? wp_kses($_REQUEST['start_date'], array()) : null;
                $end_date = isset($_REQUEST['end_date']) ? wp_kses($_REQUEST['end_date'], array()) : null;
                $post_type = isset($_REQUEST['post_type']) ? wp_kses($_REQUEST['post_type'], array()) : null;
				$price = 0;
				
                if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
                    if ($post_type == "accommodation") {
                        $price = $bookyourtravel_accommodation_helper->get_min_static_from_price_by_location($location_id);
                    } elseif ($post_type == "car_rental") {
                        $price = $bookyourtravel_car_rental_helper->get_min_static_from_price_by_location($location_id);
                    } elseif ($post_type == "cruise") {
                        $price = $bookyourtravel_cruise_helper->get_min_static_from_price_by_location($location_id);
                    } elseif ($post_type == "tour") {
                        $price = $bookyourtravel_tour_helper->get_min_static_from_price_by_location($location_id);
                    }					
                } else {
                    if ($post_type == "accommodation") {
                        $price = $bookyourtravel_accommodation_helper->get_min_future_price_by_location($location_id, $start_date, $end_date);
                    } elseif ($post_type == "car_rental") {
                        $price = $bookyourtravel_car_rental_helper->get_min_future_price_by_location($location_id, $start_date, $end_date);
                    } elseif ($post_type == "cruise") {
                        $price = $bookyourtravel_cruise_helper->get_min_future_price_by_location($location_id, $start_date, $end_date);
                    } elseif ($post_type == "tour") {
                        $price = $bookyourtravel_tour_helper->get_min_future_price_by_location($location_id, $start_date, $end_date);
                    }
                }
				
				$price = BookYourTravel_Theme_Utils::get_price_in_current_currency($price);

                if ($price > 0) {
					echo json_encode($price);
                }
            }
		}
		
		die();
    }

    function custom_taxonomy_list($taxonomies) {
		$taxonomies[] = "location_tag";
		$taxonomies[] = "location_type";

        return $taxonomies;
    }

	function get_custom_meta_fields() {
		$this->initialize_meta_fields();
		return $this->location_custom_meta_fields;
	}

	function get_custom_meta_tabs() {
		$this->initialize_meta_fields();
		return $this->location_custom_meta_tabs;
	}

	function initialize_meta_fields() {

		global $bookyourtravel_theme_globals;

		$location_feature_displays = array();
		$location_feature_displays[] = array('value' => 'gallery', 'label' => esc_html__('Image gallery', 'bookyourtravel'));
		$location_feature_displays[] = array('value' => 'image', 'label' => esc_html__('Featured image', 'bookyourtravel'));

		$this->location_custom_meta_tabs = array(
			array(
				'label' => esc_html__('General', 'bookyourtravel'),
				'id' => '_location_general_tab',
				'class' => 'general_tab'
			),
			array(
				'label' => esc_html__('Gallery', 'bookyourtravel'),
				'id' => '_location_gallery_tab',
				'class' => 'gallery_tab'
			),
			array(
				'label' => esc_html__('Content', 'bookyourtravel'),
				'id' => '_location_content_tab',
				'class' => 'content_tab'
			)
		);

		$this->location_custom_meta_fields = array(
			array( // Post ID select box
				'label'	=> esc_html__('Is Featured', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Show in lists where only featured items are shown.', 'bookyourtravel'), // description
				'id'	=> 'location_is_featured', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array(
				'label'	=> esc_html__('Ribbon text', 'bookyourtravel'),
				'desc'	=> esc_html__('If specified, this text will appear in a ribbon placed on top of the item in lists when card display mode is used.', 'bookyourtravel'),
				'id'	=> 'location_ribbon_text',
				'type'	=> 'text',
				'admin_tab_id' => 'content_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Display As Directory?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Check this option if you want to show list of descendant locations when showing this single location instead of showing what single location page usually shows. Useful for Country locations that than lists all of that country\'s cities.', 'bookyourtravel'), // description
				'id'	=> 'location_display_as_directory', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Show only top level locations?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Check this option if you want to show only top level locations when "Display As Directory" is checked.', 'bookyourtravel'), // description
				'id'	=> 'location_directory_exclude_descendant_locations', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Items per row', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('How many items do you want to show per row?', 'bookyourtravel'), // description
				'id'	=> 'location_directory_posts_per_row', // field id and name
				'std'	=> '4',
				'type'	=> 'slider',
				'min'	=> apply_filters('bookyourtravel_location_directory_posts_per_row_min', '1'),
				'max'	=> apply_filters('bookyourtravel_location_directory_posts_per_row_max', '5'),
				'step'	=> '1',
				'admin_tab_id' => 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide item titles?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide titles of listed locations', 'bookyourtravel'), // description
				'id'	=> 'location_directory_hide_item_titles', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide item images?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide images of listed locations', 'bookyourtravel'), // description
				'id'	=> 'location_directory_hide_item_images', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide item descriptions?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide descriptions of listed locations', 'bookyourtravel'), // description
				'id'	=> 'location_directory_hide_item_descriptions', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide buttons?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide buttons of listed locations', 'bookyourtravel'), // description
				'id'	=> 'location_directory_hide_item_actions', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide counts?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide counts of listed locations', 'bookyourtravel'), // description
				'id'	=> 'location_directory_hide_item_counts', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide ribbons?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide ribbons of listed locations', 'bookyourtravel'), // description
				'id'	=> 'location_directory_hide_item_ribbons', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'general_tab'
			),				
			array( // Taxonomy Select box
				'label'	=> esc_html__('Location types', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'location_type', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'tax_checkboxes', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array( // Taxonomy Select box
				'label'	=> esc_html__('Location tags', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'location_tag', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'tax_checkboxes', // type of field
				'admin_tab_id' => 'general_tab'
			),			
			array(
				'label'	=> esc_html__('General description', 'bookyourtravel'),
				'desc'	=> esc_html__('General description', 'bookyourtravel'),
				'id'	=> 'location_general_description',
				'type'	=> 'editor',
				'admin_tab_id' => 'content_tab'
			),
			array(
				'label'	=> esc_html__('Short description', 'bookyourtravel'),
				'desc'	=> esc_html__('Short description is shown in the right sidebar of a single item and as a description of an item card when the item is displayed in lists', 'bookyourtravel'),
				'id'	=> 'location_short_description',
				'type'	=> 'editor',
				'admin_tab_id' => 'content_tab'
			),
			array( // Select box
				'label'	=> esc_html__('Displayed featured element', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'location_displayed_featured_element', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'select', // type of field
				'options' => $location_feature_displays,
				'std' => 'gallery',
				'admin_tab_id' => 'gallery_tab'
			),
			array( // Repeatable & Sortable Text inputs
				'label'	=> esc_html__('Gallery images', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('A collection of images to be used in slider/gallery on single page', 'bookyourtravel'), // description
				'id'	=> 'location_images', // field id and name
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
			)
		);

		global $default_location_extra_fields;
		$location_extra_fields = $bookyourtravel_theme_globals->get_location_extra_fields();
		if (!is_array($location_extra_fields) || count($location_extra_fields) == 0)
			$location_extra_fields = $default_location_extra_fields;
		else
			$location_extra_fields = BookYourTravel_Theme_Of_Default_Fields::merge_fields_and_defaults($location_extra_fields, $default_location_extra_fields);


		foreach ($location_extra_fields as $location_extra_field) {
			$field_is_hidden = isset($location_extra_field['hide']) ? intval($location_extra_field['hide']) : 0;

			if (!$field_is_hidden) {
				$extra_field = null;
				$field_label = isset($location_extra_field['label']) ? $location_extra_field['label'] : '';
				$field_id = isset($location_extra_field['id']) ? $location_extra_field['id'] : '';
				$field_type = isset($location_extra_field['type']) ? $location_extra_field['type'] :  '';
				$field_desc = isset($location_extra_field['desc']) ? $location_extra_field['desc'] :  '';

				$field_options_array = null;
				if (isset($location_extra_field['options'])) {
					if (is_array($location_extra_field['options'])) {
						$field_options_array = $location_extra_field['options'];
					} else {
						$field_options = isset($location_extra_field['options']) ? trim($location_extra_field['options']) :  '';
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
					$min = isset($location_extra_field['min']) && strlen($location_extra_field['min']) > 0 ? intval($location_extra_field['min']) :  1;
					$max = isset($location_extra_field['max']) && strlen($location_extra_field['max']) > 0 ? intval($location_extra_field['max']) :  10;
					$step = isset($location_extra_field['step']) && strlen($location_extra_field['step']) > 0 ? intval($location_extra_field['step']) :  1;
				}

				if (!empty($field_label) && !empty($field_id) && !empty($field_type)) {
					$extra_field = array(
						'label'	=> $field_label,
						'desc'	=> $field_desc,
						'id'	=> 'location_' . $field_id,
						'type'	=> $field_type,
						'admin_tab_id'=> 'content_tab',
						'options' => $field_options_array,
						'min' => $min,
						'max' => $max,
						'step' => $step,	
					);
				}

				if ($extra_field)
					$this->location_custom_meta_fields[] = $extra_field;
			}
		}

	}

	function save_location($post_id) {
		
		$this->clear_location_meta($post_id);
		$ancestors = get_post_ancestors( $post_id );
		foreach ($ancestors as $ancestor_id) {
			$this->clear_location_meta($ancestor_id);
		}

		do_action('bookyourtravel_saved_location');
	}

	function delete_location($post_id) {
		$this->clear_location_meta($post_id);
		$ancestors = get_post_ancestors( $post_id );
		foreach ($ancestors as $ancestor_id) {
			$this->clear_location_meta($ancestor_id);
		}

		do_action('bookyourtravel_deleted_location');
	}

	function clear_location_meta($location_id) {
		delete_post_meta($location_id, '_location_tour_ids');
        delete_post_meta($location_id, '_location_tour_count');
		delete_post_meta($location_id, '_location_cruise_ids');
        delete_post_meta($location_id, '_location_cruise_count');
        delete_post_meta($location_id, '_location_accommodation_ids');
        delete_post_meta($location_id, '_location_accommodation_count');
        delete_post_meta($location_id, '_location_car_rental_ids');
        delete_post_meta($location_id, '_location_car_rental_count');
	}

	function remove_unnecessary_meta_boxes() {
		remove_meta_box('tagsdiv-location_tag', 'location', 'side');
		remove_meta_box('tagsdiv-location_type', 'location', 'side');
	}

	function register_location_tag_taxonomy() {

		$labels = array(
				'name'              => esc_html__( 'Location Tags', 'bookyourtravel' ),
				'singular_name'     => esc_html__( 'Location Tag', 'bookyourtravel' ),
				'search_items'      => esc_html__( 'Search Location tags', 'bookyourtravel' ),
				'all_items'         => esc_html__( 'All Location tags', 'bookyourtravel' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'         => esc_html__( 'Edit Location tag', 'bookyourtravel' ),
				'update_item'       => esc_html__( 'Update Location tag', 'bookyourtravel' ),
				'add_new_item'      => esc_html__( 'Add New Location tag', 'bookyourtravel' ),
				'new_item_name'     => esc_html__( 'New Location tag Name', 'bookyourtravel' ),
				'separate_items_with_commas' => esc_html__( 'Separate Location tags with commas', 'bookyourtravel' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove Location tags', 'bookyourtravel' ),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used Location tags', 'bookyourtravel' ),
				'not_found'                  => esc_html__( 'No Location tags found.', 'bookyourtravel' ),
				'menu_name'         => esc_html__( 'Location Tags', 'bookyourtravel' ),
			);

		$args = array(
				'hierarchical'      => false,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'update_count_callback' => '_update_post_term_count',
				'rewrite'           => false,
			);

		register_taxonomy( 'location_tag', array( 'location' ), $args );
	}

	function register_location_type_taxonomy() {

		$labels = array(
				'name'              => esc_html__( 'Location Types', 'bookyourtravel' ),
				'singular_name'     => esc_html__( 'Location Type', 'bookyourtravel' ),
				'search_items'      => esc_html__( 'Search Location types', 'bookyourtravel' ),
				'all_items'         => esc_html__( 'All Location types', 'bookyourtravel' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'         => esc_html__( 'Edit Location type', 'bookyourtravel' ),
				'update_item'       => esc_html__( 'Update Location type', 'bookyourtravel' ),
				'add_new_item'      => esc_html__( 'Add New Location type', 'bookyourtravel' ),
				'new_item_name'     => esc_html__( 'New Location type Name', 'bookyourtravel' ),
				'separate_items_with_commas' => esc_html__( 'Separate Location types with commas', 'bookyourtravel' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove Location types', 'bookyourtravel' ),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used Location types', 'bookyourtravel' ),
				'not_found'                  => esc_html__( 'No Location types found.', 'bookyourtravel' ),
				'menu_name'         => esc_html__( 'Location Types', 'bookyourtravel' ),
			);

		$args = array(
				'hierarchical'      => false,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'update_count_callback' => '_update_post_term_count',
				'rewrite'           => false,
			);

		register_taxonomy( 'location_type', array( 'location' ), $args );
	}	

	function location_admin_init() {

		global $bookyourtravel_theme_globals;

		$this->initialize_meta_fields();

		$sort_by_columns = array();
		$sort_by_columns[] = array('value' => 'title', 'label' => esc_html__('Location title', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'ID', 'label' => esc_html__('Location ID', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'date', 'label' => esc_html__('Publish date', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'rand', 'label' => esc_html__('Random', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'comment_count', 'label' => esc_html__('Comment count', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'menu_order', 'label' => esc_html__('Order attribute', 'bookyourtravel'));

		$sort_by_columns = apply_filters('bookyourtravel_location_list_sort_by_columns', $sort_by_columns);

		$this->location_list_custom_meta_tabs = array(
			array(
				'label' => esc_html__('Content', 'bookyourtravel'),
				'id' => '_location_list_filter_tab',
				'class' => 'filter_tab'
			),
			array(
				'label' => esc_html__('Display settings', 'bookyourtravel'),
				'id' => '_location_list_item_settings_tab',
				'class' => 'item_settings_tab'
			)
		);

		$this->location_list_custom_meta_fields = array(
			array( // Taxonomy Select box
				'label'	=> esc_html__('Location', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'location_list_location_post_id', // field id and name
				'type'	=> 'post_select', // type of field
				'post_type' => array('location'), // post types to display, options are prefixed with their post type
				'admin_tab_id' => 'filter_tab'
			),
			array( // Select box
				'label'	=> esc_html__('Sort by field', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'location_list_sort_by', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'select', // type of field
				'options' => $sort_by_columns,
				'admin_tab_id' => 'filter_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Sort descending?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will sort locations in descending order', 'bookyourtravel'), // description
				'id'	=> 'location_list_sort_descending', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'filter_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Show only top level locations?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will show only top level locations and not descendant locations', 'bookyourtravel'), // description
				'id'	=> 'location_list_exclude_descendant_locations', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'filter_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Show featured only?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will list featured locations only', 'bookyourtravel'), // description
				'id'	=> 'location_list_show_featured_only', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'filter_tab'
			),
			array( // Taxonomy Select box
				'label'	=> esc_html__('Location types', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'location_type', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'tax_checkboxes', // type of field
				'admin_tab_id' => 'filter_tab'
			),			
			array( // Taxonomy Select box
				'label'	=> esc_html__('Location tags', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'location_tag', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'tax_checkboxes', // type of field
				'admin_tab_id' => 'filter_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Items per row', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('How many items do you want to show per row?', 'bookyourtravel'), // description
				'id'	=> 'location_list_posts_per_row', // field id and name
				'std'	=> '4',
				'type'	=> 'slider',
				'min'	=> apply_filters('bookyourtravel_location_list_posts_per_row_min', '1'),
				'max'	=> apply_filters('bookyourtravel_location_list_posts_per_row_max', '5'),
				'step'	=> '1',
				'admin_tab_id' => 'filter_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Items per page', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('How many items do you want to show per page?', 'bookyourtravel'), // description
				'id'	=> 'location_list_posts_per_page', // field id and name
				'std'	=> '12',
				'type'	=> 'slider',
				'min'	=> apply_filters('bookyourtravel_location_list_posts_per_page_min', '1'),
				'max'	=> apply_filters('bookyourtravel_location_list_posts_per_page_max', '50'),
				'step'	=> '1',
				'admin_tab_id' => 'filter_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide item titles?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide titles of listed locations', 'bookyourtravel'), // description
				'id'	=> 'location_list_hide_item_titles', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'item_settings_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide item images?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide images of listed locations', 'bookyourtravel'), // description
				'id'	=> 'location_list_hide_item_images', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'item_settings_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide item descriptions?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide descriptions of listed locations', 'bookyourtravel'), // description
				'id'	=> 'location_list_hide_item_descriptions', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'item_settings_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide buttons?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide buttons of listed locations', 'bookyourtravel'), // description
				'id'	=> 'location_list_hide_item_actions', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'item_settings_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide counts?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide counts of listed locations', 'bookyourtravel'), // description
				'id'	=> 'location_list_hide_item_counts', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'item_settings_tab'
			),		
			array( // Post ID select box
				'label'	=> esc_html__('Hide ribbons?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide ribbons of listed locations', 'bookyourtravel'), // description
				'id'	=> 'location_list_hide_item_ribbons', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'item_settings_tab'
			),			
		);

		new Custom_Add_Meta_Box( 'location_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->location_custom_meta_fields, $this->location_custom_meta_tabs, 'location', true );

		$this->location_list_meta_box = new Custom_Add_Meta_Box( 'location_list_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->location_list_custom_meta_fields, $this->location_list_custom_meta_tabs, 'page' );
		remove_action( 'add_meta_boxes', array( $this->location_list_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array($this, 'location_list_add_meta_boxes') );
	}

	function location_list_add_meta_boxes() {
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file == 'page-location-list.php') {
			add_meta_box(
				$this->location_list_meta_box->id, 
				$this->location_list_meta_box->title, 
				array( $this->location_list_meta_box, 'meta_box_callback' ), 
				'page', 'normal', 'high'			
			);
		}
	}

	function initialize_post_type() {

		$this->register_location_post_type();
		$this->register_location_tag_taxonomy();
		$this->register_location_type_taxonomy();
	}

	function register_location_post_type() {

		global $bookyourtravel_theme_globals;

		$locations_permalink_slug = $bookyourtravel_theme_globals->get_locations_permalink_slug();

		$location_list_page_id = $bookyourtravel_theme_globals->get_location_list_page_id();

		if ($location_list_page_id > 0) {

			add_rewrite_rule(
				"{$locations_permalink_slug}$",
				"index.php?post_type=page&page_id={$location_list_page_id}", 'top');

			add_rewrite_rule(
				"{$locations_permalink_slug}/page/?([1-9][0-9]*)",
				"index.php?post_type=page&page_id={$location_list_page_id}&paged=\$matches[1]", 'top');

		}

		add_rewrite_rule(
			"{$locations_permalink_slug}/([^\/]+)\/page\/?([1-9][0-9]*)?",
			"index.php?post_type=location&name=\$matches[1]&paged-byt=\$matches[2]", 'top');

		add_rewrite_rule(
			"{$locations_permalink_slug}/.*/([^/]*)/page/?([1-9][0-9]*)",
			"index.php?post_type=location&name=\$matches[1]&paged-byt=\$matches[2]", 'top');


		add_rewrite_tag('%paged-byt%', '([1-9][0-9]*)');

		$labels = array(
			'name'                => esc_html__( 'Locations', 'bookyourtravel' ),
			'singular_name'       => esc_html__( 'Location', 'bookyourtravel' ),
			'menu_name'           => esc_html__( 'Locations', 'bookyourtravel' ),
			'all_items'           => esc_html__( 'All Locations', 'bookyourtravel' ),
			'view_item'           => esc_html__( 'View Location', 'bookyourtravel' ),
			'add_new_item'        => esc_html__( 'Add New Location', 'bookyourtravel' ),
			'add_new'             => esc_html__( 'New Location', 'bookyourtravel' ),
			'edit_item'           => esc_html__( 'Edit Location', 'bookyourtravel' ),
			'update_item'         => esc_html__( 'Update Location', 'bookyourtravel' ),
			'search_items'        => esc_html__( 'Search locations', 'bookyourtravel' ),
			'not_found'           => esc_html__( 'No locations found', 'bookyourtravel' ),
			'not_found_in_trash'  => esc_html__( 'No locations found in Trash', 'bookyourtravel' ),
		);
		$args = array(
			'label'               => esc_html__( 'Location', 'bookyourtravel' ),
			'description'         => esc_html__( 'Location information pages', 'bookyourtravel' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'author', 'page-attributes' ),
			'taxonomies'          => array( ),
			'hierarchical'        => true,
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
			'rewrite' => array('slug' => $locations_permalink_slug)
		);

		register_post_type( 'location', $args );

	}

	function list_locations($location_id = -1, $paged = 0, $per_page = -1, $orderby = '', $order = '', $featured_only = false,  $location_types_array = array(), $location_tags_array = array(), $author_id = null, $exclude_descendant_locations = false, $include_private = false) {

		$location_ids = array();

		if ($location_id > -1) {
			$location_id = BookYourTravel_Theme_Utils::get_current_language_post_id($location_id, 'location');
			$location_ids[] = $location_id;
			$location_ids[] = BookYourTravel_Theme_Utils::get_default_language_post_id($location_id, 'location');
			if (!$exclude_descendant_locations) {
				$location_descendants = BookYourTravel_Theme_Utils::get_post_descendants($location_id, 'location');
				foreach ($location_descendants as $location) {
					$location_ids[] = BookYourTravel_Theme_Utils::get_current_language_post_id($location->ID, 'location');
					$location_ids[] = BookYourTravel_Theme_Utils::get_default_language_post_id($location->ID, 'location');
				}
			}
		}

		$args = array(
			'post_status'		=> array('publish'),
			'post_type'         => 'location',
			'posts_per_page'    => $per_page,
			'paged' 			=> $paged,
			'orderby'           => $orderby,
			'suppress_filters' 	=> false,
			'order'				=> $order,
			'meta_query'        => array('relation' => 'AND')
		);

		if ($include_private) {
			$args['post_status'][] = 'draft';
			$args['post_status'][] = 'private';
		}

		if (isset($author_id)) {
			$author_id = intval($author_id);
			if ($author_id > 0) {
				$args['author'] = $author_id;
			}
		}

		if (count($location_ids) > 0) {
			$args['post_parent__in'] = $location_ids;
		}

		if (isset($featured_only) && $featured_only) {
			$args['meta_query'][] = array(
				'key'       => 'location_is_featured',
				'value'     => 1,
				'compare'   => '=',
				'type' => 'numeric'
			);
		}

		$args['tax_query'] = array();
		
		if (!empty($location_types_array)) {
			$args['tax_query'][] = 	array(
				'taxonomy' => 'location_type',
				'field' => 'term_id',
				'terms' => $location_types_array,
				'operator'=> 'IN'
			);
		}

		if (!empty($location_tags_array)) {
			$args['tax_query'][] = 	array(
				'taxonomy' => 'location_tag',
				'field' => 'term_id',
				'terms' => $location_tags_array,
				'operator'=> 'IN'
			);
		}

		$posts_query = new WP_Query($args);

		// echo $posts_query->request;

		$locations = array();

		if ($posts_query->have_posts() ) {
			while ( $posts_query->have_posts() ) {
				global $post;
				$posts_query->the_post();
				$locations[] = $post;
			}
		}

		$results = array(
			'total' => $posts_query->found_posts,
			'results' => $locations
		);

		wp_reset_postdata();

		return $results;
	}
}

global $bookyourtravel_location_helper;
// store the instance in a variable to be retrieved later and call init
$bookyourtravel_location_helper = BookYourTravel_Location_Helper::get_instance();
$bookyourtravel_location_helper->init();

add_shortcode( 'byt_location_card', 'byt_location_card_shortcode');
function byt_location_card_shortcode($atts) {

	global $location_item_args;

	extract(shortcode_atts(array(
	  'location_id' => 0,
      'show_fields' => 'title,image,actions,description',
      'css' => ''
	), $atts));

	$show_fields = explode(',', $show_fields);

	$location_item_args = array();
	$location_item_args['location_id'] = $location_id;
	if ($location_id > 0) {
		$location_item_args['post']	= get_post($location_id);
	}
	$location_item_args['hide_title'] = !in_array('title', $show_fields);
	$location_item_args['hide_image'] = !in_array('image', $show_fields);
	$location_item_args['hide_actions'] = !in_array('actions', $show_fields);
	$location_item_args['hide_description'] = !in_array('description', $show_fields);
	$location_item_args['hide_counts'] = !in_array('counts', $show_fields);
	$location_item_args['hide_ribbon'] = !in_array('ribbon', $show_fields);		
	$location_item_args['item_class'] = 'single-card';

	$output = '';

	ob_start();
	get_template_part('includes/parts/location/location', 'item');

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