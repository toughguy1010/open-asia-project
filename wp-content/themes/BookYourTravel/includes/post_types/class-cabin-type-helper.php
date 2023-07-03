<?php
/**
 * BookYourTravel_Cabin_Type_Helper class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-cabin-type.php');
 
class BookYourTravel_Cabin_Type_Helper extends BookYourTravel_BaseSingleton {

	private $enable_cruises;
	private $cabin_type_custom_meta_fields;
	private $cabin_type_custom_meta_tabs;

	protected function __construct() {
	
		global $bookyourtravel_theme_globals;
		
		$this->enable_cruises = $bookyourtravel_theme_globals->enable_cruises();

        // our parent class might
        // contain shared code in its constructor
        parent::__construct();		
    }
	
    public function init() {

		add_action('bookyourtravel_initialize_post_types', array( $this, 'initialize_post_type' ), 0);
	
		if ($this->enable_cruises) {
		
			add_action( 'admin_init', array( $this, 'cabin_type_admin_init' ) );
			
			$this->initialize_meta_fields();			
		}
	}

	function initialize_post_type() {

		global $bookyourtravel_theme_globals;	
		$this->enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
	
		if ($this->enable_cruises) {
			$this->register_cabin_type_post_type();
		}
	}
	
	function get_custom_meta_fields() {
        $this->initialize_meta_fields();		
		return $this->cabin_type_custom_meta_fields;
	}

	function get_custom_meta_tabs() {
		$this->initialize_meta_fields();		
		return $this->cabin_type_custom_meta_tabs;
	}
	
	function initialize_meta_fields() {

		$this->cabin_type_custom_meta_tabs = array(
			array(
				'label' => esc_html__('General', 'bookyourtravel'),
				'id' => '_cabin_type_general_tab',
				'class' => 'general_tab'
			),
		array(
			'label' => esc_html__('Booking', 'bookyourtravel'),
			'id' => '_cabin_type_booking_tab_tab',
			'class' => 'booking_tab'
		),					
			array(
				'label' => esc_html__('Images', 'bookyourtravel'),
				'id' => '_cabin_type_images_tab',
				'class' => 'images_tab'
			)
		);	
				
		$this->cabin_type_custom_meta_fields = array(
			array(
				'label'	=> esc_html__('Max adult count', 'bookyourtravel'),
				'desc'	=> esc_html__('How many adults are allowed in the cabin?', 'bookyourtravel'),
				'id'	=> 'cabin_type_max_count',
				'type'	=> 'slider',
				'min'	=> '0',
				'max'	=> '10',
				'step'	=> '1',
				'admin_tab_id'=> 'booking_tab'
			),
			array(
				'label'	=> esc_html__('Max child count', 'bookyourtravel'),
				'desc'	=> esc_html__('How many children are allowed in the cabin?', 'bookyourtravel'),
				'id'	=> 'cabin_type_max_child_count',
				'type'	=> 'slider',
				'min'	=> '0',
				'max'	=> '10',
				'step'	=> '1',
				'admin_tab_id'=> 'booking_tab'
			),
			array(
				'label'	=> esc_html__('Bed size', 'bookyourtravel'),
				'desc'	=> esc_html__('How big is/are the beds?', 'bookyourtravel'),
				'id'	=> 'cabin_type_bed_size',
				'type'	=> 'text',					
				'admin_tab_id'=> 'general_tab'
			),
			array(
				'label'	=> esc_html__('Cabin size', 'bookyourtravel'),
				'desc'	=> esc_html__('What is the cabin size (m2)?', 'bookyourtravel'),
				'id'	=> 'cabin_type_room_size',
				'type'	=> 'text',
				'admin_tab_id'=> 'general_tab'
			),
			array(
				'label'	=> esc_html__('Cabin meta information', 'bookyourtravel'),
				'desc'	=> esc_html__('What other information applies to this specific cabin type?', 'bookyourtravel'),
				'id'	=> 'cabin_type_meta',
				'type'	=> 'text',
				'admin_tab_id'=> 'general_tab'
			),
			array( // Taxonomy Select box
				'label'	=> esc_html__('Facilities', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'facility', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'tax_checkboxes', // type of field
				'admin_tab_id'=> 'general_tab'
			),
			array( // Repeatable & Sortable Text inputs
				'label'	=> esc_html__('Gallery images', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('A collection of images to be used in gallery of the room type', 'bookyourtravel'), // description
				'id'	=> 'cabin_type_images', // field id and name
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
				'admin_tab_id'=> 'images_tab'
			),				
		);
	}
	
	function cabin_type_admin_init() {
			
		if ($this->enable_cruises) {

			new Custom_Add_Meta_Box( 'cabin_type_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->cabin_type_custom_meta_fields, $this->cabin_type_custom_meta_tabs, 'cabin_type' );
		}
	}
		
	function register_cabin_type_post_type() {
		
		$labels = array(
			'name'                => esc_html__( 'Cabin types', 'bookyourtravel' ),
			'singular_name'       => esc_html__( 'Cabin type', 'bookyourtravel' ),
			'menu_name'           => esc_html__( 'Cabin types', 'bookyourtravel' ),
			'all_items'           => esc_html__( 'Cabin types', 'bookyourtravel' ),
			'view_item'           => esc_html__( 'View Cabin type', 'bookyourtravel' ),
			'add_new_item'        => esc_html__( 'Add New Cabin type', 'bookyourtravel' ),
			'add_new'             => esc_html__( 'New Cabin type', 'bookyourtravel' ),
			'edit_item'           => esc_html__( 'Edit Cabin type', 'bookyourtravel' ),
			'update_item'         => esc_html__( 'Update Cabin type', 'bookyourtravel' ),
			'search_items'        => esc_html__( 'Search Cabin types', 'bookyourtravel' ),
			'not_found'           => esc_html__( 'No Cabin types found', 'bookyourtravel' ),
			'not_found_in_trash'  => esc_html__( 'No Cabin types found in Trash', 'bookyourtravel' ),
		);
		$args = array(
			'label'               => esc_html__( 'Cabin type', 'bookyourtravel' ),
			'description'         => esc_html__( 'Cabin type information pages', 'bookyourtravel' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'author', 'page-attributes' ),
			'taxonomies'          => array( ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'edit.php?post_type=cruise',
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_icon'           => '',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'page',
			'show_in_rest'		  => true,			
			'rewrite' => false,
		);
		register_post_type( 'cabin_type', $args );	
	}
	
	function list_cabin_types( $author_id = null, $include_private = false, $cruise_id = null ) {

		$args = array(
		   'post_type' => 'cabin_type',
		   'post_status' => array('publish'),
		   'posts_per_page' => -1,
		   'suppress_filters' => 0,
		   'orderby' => 'title',
		   'order' => 'ASC'
		);

		if ($include_private) {
			$args['post_status'][] = 'draft';
			$args['post_status'][] = 'private';
		}

		if (isset($author_id) && $author_id > 0) {
			$args['author'] = intval($author_id);
		}
		
		$meta_query = array('relation' => 'AND');

		if (isset($cruise_id) && $cruise_id > 0) {
			$meta_query[] = array(
				'key'       => 'cabin_type_cruise_post_ids',
				'value'     => serialize((string)$cruise_id),
				'compare'   => 'LIKE'
			);	
		}
		
		$args['meta_query'] = $meta_query;
		
		$query = new WP_Query($args);

		return $query;
	}
}

global $bookyourtravel_cabin_type_helper;
// store the instance in a variable to be retrieved later and call init
$bookyourtravel_cabin_type_helper = BookYourTravel_Cabin_Type_Helper::get_instance();
$bookyourtravel_cabin_type_helper->init();