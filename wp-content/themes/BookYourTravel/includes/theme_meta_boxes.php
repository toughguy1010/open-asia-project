<?php
/**
 * BookYourTravel_Theme_Meta_Boxes class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BookYourTravel_Theme_Meta_Boxes extends BookYourTravel_BaseSingleton {

	private $enabled_frontend_list_content_types;
	private $enabled_frontend_submit_content_types;
	private $enable_accommodations;
	private $enable_tours;
	private $enable_cruises;
	private $enable_car_rentals;
	private $enable_reviews;

	private $post_list_custom_meta_fields;
	private $search_results_custom_meta_fields;
	private $user_register_custom_meta_fields;
	private $user_login_custom_meta_fields;
	private $user_forgot_password_custom_meta_fields;
	private $contact_page_custom_meta_fields;
	private $frontend_submit_custom_meta_fields;
	private $user_account_custom_meta_fields;
	private $user_content_list_custom_meta_fields;
	private $page_sidebars_custom_meta_fields;

	private $post_list_custom_meta_tabs;
	private $search_results_custom_meta_tabs;
	private $user_register_custom_meta_tabs;
	private $user_login_custom_meta_tabs;
	private $user_forgot_password_custom_meta_tabs;
	private $contact_page_custom_meta_tabs;
	private $frontend_submit_custom_meta_tabs;
	private $user_account_custom_meta_tabs;
	private $user_content_list_custom_meta_tabs;
	private $page_sidebars_custom_meta_tabs;

	private $user_register_meta_box;
	private $search_results_meta_box;
	private $user_login_meta_box;
	private $user_forgot_password_meta_box;
	private $contact_page_meta_box;
	private $frontend_submit_meta_box;
	private $user_account_meta_box;
	private $user_content_list_meta_box;
	private $page_sidebars_meta_box;

	protected function __construct() {

		global $bookyourtravel_theme_globals;

		$this->enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
		$this->enable_tours = $bookyourtravel_theme_globals->enable_tours();
		$this->enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
		$this->enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();
		$this->enable_reviews = $bookyourtravel_theme_globals->enable_reviews();
		$this->enabled_frontend_list_content_types = array();
		$this->enabled_frontend_submit_content_types = array();

        // our parent class might
        // contain shared code in its constructor
        parent::__construct();
    }

    public function init() {

		add_action( 'admin_init', array($this, 'pages_meta_box_admin_init' ) );
    }

	function pages_meta_box_admin_init() {

		if ($this->enable_accommodations) {
			$this->enabled_frontend_list_content_types[] = array('value' => 'accommodation', 'label' => esc_html__('Accommodations', 'bookyourtravel'));
			$this->enabled_frontend_list_content_types[] = array('value' => 'room_type', 'label' => esc_html__('Room types', 'bookyourtravel'));
			$this->enabled_frontend_list_content_types[] = array('value' => 'accommodation_booking', 'label' => esc_html__('Accommodation bookings', 'bookyourtravel'));
			$this->enabled_frontend_list_content_types[] = array('value' => 'accommodation_vacancy', 'label' => esc_html__('Accommodation vacancies', 'bookyourtravel'));
			$this->enabled_frontend_submit_content_types[] = array('value' => 'accommodation', 'label' => esc_html__('Accommodations', 'bookyourtravel'));
			$this->enabled_frontend_submit_content_types[] = array('value' => 'room_type', 'label' => esc_html__('Room types', 'bookyourtravel'));
			$this->enabled_frontend_submit_content_types[] = array('value' => 'accommodation_booking', 'label' => esc_html__('Accommodation bookings', 'bookyourtravel'));
			$this->enabled_frontend_submit_content_types[] = array('value' => 'accommodation_vacancy', 'label' => esc_html__('Accommodation vacancies', 'bookyourtravel'));
		}

		if ($this->enable_car_rentals) {
			$this->enabled_frontend_list_content_types[] = array('value' => 'car_rental', 'label' => esc_html__('Car rentals', 'bookyourtravel'));
			$this->enabled_frontend_list_content_types[] = array('value' => 'car_rental_availability', 'label' => esc_html__('Car rental availabilities', 'bookyourtravel'));
			$this->enabled_frontend_list_content_types[] = array('value' => 'car_rental_booking', 'label' => esc_html__('Car rental bookings', 'bookyourtravel'));
			$this->enabled_frontend_submit_content_types[] = array('value' => 'car_rental', 'label' => esc_html__('Car rentals', 'bookyourtravel'));
			$this->enabled_frontend_submit_content_types[] = array('value' => 'car_rental_availability', 'label' => esc_html__('Car rental availabilities', 'bookyourtravel'));
			$this->enabled_frontend_submit_content_types[] = array('value' => 'car_rental_booking', 'label' => esc_html__('Car rental bookings', 'bookyourtravel'));
		}

		if ($this->enable_cruises) {
			$this->enabled_frontend_list_content_types[] = array('value' => 'cruise', 'label' => esc_html__('Cruises', 'bookyourtravel'));
			$this->enabled_frontend_list_content_types[] = array('value' => 'cabin_type', 'label' => esc_html__('Cabin types', 'bookyourtravel'));
			$this->enabled_frontend_list_content_types[] = array('value' => 'cruise_schedule', 'label' => esc_html__('Cruise schedules', 'bookyourtravel'));
			$this->enabled_frontend_list_content_types[] = array('value' => 'cruise_booking', 'label' => esc_html__('Cruise bookings', 'bookyourtravel'));
			$this->enabled_frontend_submit_content_types[] = array('value' => 'cruise', 'label' => esc_html__('Cruises', 'bookyourtravel'));
			$this->enabled_frontend_submit_content_types[] = array('value' => 'cabin_type', 'label' => esc_html__('Cabin types', 'bookyourtravel'));
			$this->enabled_frontend_submit_content_types[] = array('value' => 'cruise_schedule', 'label' => esc_html__('Cruise schedules', 'bookyourtravel'));
			$this->enabled_frontend_submit_content_types[] = array('value' => 'cruise_booking', 'label' => esc_html__('Cruise bookings', 'bookyourtravel'));
		}

		$this->enabled_frontend_list_content_types[] = array('value' => 'location', 'label' => esc_html__('Locations', 'bookyourtravel'));
		$this->enabled_frontend_submit_content_types[] = array('value' => 'location', 'label' => esc_html__('Locations', 'bookyourtravel'));

		if ($this->enable_tours) {
			$this->enabled_frontend_list_content_types[] = array('value' => 'tour', 'label' => esc_html__('Tours', 'bookyourtravel'));
			$this->enabled_frontend_list_content_types[] = array('value' => 'tour_schedule', 'label' => esc_html__('Tour schedules', 'bookyourtravel'));
			$this->enabled_frontend_list_content_types[] = array('value' => 'tour_booking', 'label' => esc_html__('Tour bookings', 'bookyourtravel'));
			$this->enabled_frontend_submit_content_types[] = array('value' => 'tour', 'label' => esc_html__('Tours', 'bookyourtravel'));
			$this->enabled_frontend_submit_content_types[] = array('value' => 'tour_schedule', 'label' => esc_html__('Tour schedules', 'bookyourtravel'));
			$this->enabled_frontend_submit_content_types[] = array('value' => 'tour_booking', 'label' => esc_html__('Tour bookings', 'bookyourtravel'));
		}

		if ($this->enable_reviews) {
			$this->enabled_frontend_list_content_types[] = array('value' => 'review', 'label' => esc_html__('Reviews', 'bookyourtravel'));
		}

		$pages = get_pages();
		$pages_array = array();
		foreach ( $pages as $page ) {
			$pages_array[] = array('value' => $page->ID, 'label' => $page->post_title);
		}

		$page_sidebars = array();
		$page_sidebars[] = array('value' => '', 'label' => esc_html__('No sidebar', 'bookyourtravel'));
		$page_sidebars[] = array('value' => 'left', 'label' => esc_html__('Left sidebar', 'bookyourtravel'));
		$page_sidebars[] = array('value' => 'right', 'label' => esc_html__('Right sidebar', 'bookyourtravel'));
		$page_sidebars[] = array('value' => 'both', 'label' => esc_html__('Left and right sidebars', 'bookyourtravel'));

		$this->page_sidebars_custom_meta_tabs = null;
		$this->page_sidebars_custom_meta_fields = array(
			array( // Taxonomy Select box
				'label'	=> esc_html__('Select sidebar positioning', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'page_sidebar_positioning', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'select', // type of field
				'options' => $page_sidebars,
				'admin_tab_id' => 'general_tab'
			)
		);

		$this->contact_page_custom_meta_tabs = array(
			array(
				'label' => esc_html__('General', 'bookyourtravel'),
				'id' => '_contact_page_general_tab',
				'class' => 'general_tab'
			)
		);
		$this->contact_page_custom_meta_fields = array(
			array( // Post ID select box
				'label'	=> esc_html__('Use Google reCaptcha on this page?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Check this box if you want to use a reCaptcha control on this page and prevent spam submissions. Note: reCaptcha is configured in Theme options -> Configuration Settings.', 'bookyourtravel'), // description
				'id'	=> 'contact_page_use_recaptcha', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id'=> 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Contact page phone number', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Set the phone number to be displayed in the sidebar of this contact page', 'bookyourtravel'), // description
				'id'	=> 'contact_page_phone_number', // field id and name
				'type'	=> 'text', // type of field
				'admin_tab_id'=> 'general_tab'
			)			
		);

		$this->user_forgot_password_custom_meta_tabs = array(
			array(
				'label' => esc_html__('General', 'bookyourtravel'),
				'id' => '_user_forgot_password_general_tab',
				'class' => 'general_tab'
			)
		);
		$this->user_forgot_password_custom_meta_fields = array(
			array( // Post ID select box
				'label'	=> esc_html__('Is partner reset password page?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Check this box if users reseting their password through this form can use the frontend submit pages to submit content.', 'bookyourtravel'), // description
				'id'	=> 'user_forgot_password_can_frontend_submit', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id'=> 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Use Google reCaptcha on this page?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Check this box if you want to use a reCaptcha control on this page and prevent spam submissions. Note: reCaptcha is configured in Theme options -> Configuration Settings.', 'bookyourtravel'), // description
				'id'	=> 'user_forgot_password_use_recaptcha', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id'=> 'general_tab'
			)
		);

		$this->user_login_custom_meta_tabs = array(
			array(
				'label' => esc_html__('General', 'bookyourtravel'),
				'id' => '_user_login_page_general_tab',
				'class' => 'general_tab'
			)
		);
		$this->user_login_custom_meta_fields = array(
			array( // Post ID select box
				'label'	=> esc_html__('Is partner login page?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Check this box if users signing in through this form can use the frontend submit pages to submit content.', 'bookyourtravel'), // description
				'id'	=> 'user_login_can_frontend_submit', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id'=> 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Use Google reCaptcha on this page?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Check this box if you want to use a reCaptcha control on this page and prevent spam submissions. Note: reCaptcha is configured in Theme options -> Configuration Settings.', 'bookyourtravel'), // description
				'id'	=> 'user_login_use_recaptcha', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id'=> 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Redirect to after login override?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Select a page from this dropdown if you want to redirect users to a specific page after login. Otherwise, they will be redirected to home.', 'bookyourtravel'), // description
				'id'	=> 'user_login_redirect_to_after_login', // field id and name
				'type'	=> 'select', // type of field
				'options' => $pages_array,
				'admin_tab_id'=> 'general_tab'
			)
		);

		$this->user_register_custom_meta_tabs = array(
			array(
				'label' => esc_html__('General', 'bookyourtravel'),
				'id' => '_user_register_page_general_tab',
				'class' => 'general_tab'
			)
		);
		$this->user_register_custom_meta_fields = array(
			array( // Post ID select box
				'label'	=> esc_html__('Is partner registration page?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Check this box if users registering through this form can use the frontend submit pages to submit content.', 'bookyourtravel'), // description
				'id'	=> 'user_register_can_frontend_submit', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id'=> 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Use Google reCaptcha on this page?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('Check this box if you want to use a reCaptcha control on this page and prevent spam submissions. Note: reCaptcha is configured in Theme options -> Configuration Settings.', 'bookyourtravel'), // description
				'id'	=> 'user_register_use_recaptcha', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id'=> 'general_tab'
			),
		);

		$this->frontend_submit_custom_meta_tabs = array(
			array(
				'label' => esc_html__('General', 'bookyourtravel'),
				'id' => '_frontend_submit_page_general_tab',
				'class' => 'general_tab'
			)
		);

		$this->frontend_submit_custom_meta_fields = array(
			array( // Taxonomy Select box
				'label'	=> esc_html__('Content type', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'frontend_submit_content_type', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'select', // type of field
				'options' => $this->enabled_frontend_submit_content_types,
				'admin_tab_id'=> 'general_tab'
			)
		);

		$this->user_content_list_custom_meta_tabs = array(
			array(
				'label' => esc_html__('General', 'bookyourtravel'),
				'id' => '_user_content_list_page_general_tab',
				'class' => 'general_tab'
			)
		);
		$this->user_content_list_custom_meta_fields = array(
			array( // Select box
				'label'	=> esc_html__('User content type', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'user_content_type', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'select', // type of field
				'options' => $this->enabled_frontend_list_content_types,
				'admin_tab_id'=> 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Is partner page?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will display partner (front end submit) pages and menus', 'bookyourtravel'), // description
				'id'	=> 'user_content_list_is_partner_page', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id'=> 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Items per page', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('How many items do you want to show per page?', 'bookyourtravel'), // description
				'id'	=> 'user_content_list_posts_per_page', // field id and name
				'std'	=> '12',
				'type'	=> 'slider',
				'min'	=> apply_filters('bookyourtravel_user_content_list_posts_per_page_min', '1'),
				'max'	=> apply_filters('bookyourtravel_user_content_list_posts_per_page_max', '50'),
				'step'	=> '1',
				'admin_tab_id' => 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Items per row', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('How many items do you want to show per row?', 'bookyourtravel'), // description
				'id'	=> 'user_content_list_posts_per_row', // field id and name
				'std'	=> '4',
				'type'	=> 'slider',
				'min'	=> apply_filters('bookyourtravel_user_content_list_posts_per_row_min', '1'),
				'max'	=> apply_filters('bookyourtravel_user_content_list_posts_per_row_max', '5'),
				'step'	=> '1',
				'admin_tab_id' => 'general_tab'
			)
		);

		$this->user_account_custom_meta_tabs = array(
			array(
				'label' => esc_html__('General', 'bookyourtravel'),
				'id' => '_user_account_page_page_general_tab',
				'class' => 'general_tab'
			)
		);
		$this->user_account_custom_meta_fields = array(
			array( // Post ID select box
				'label'	=> esc_html__('Is partner page?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will display partner (front end submit) pages and menus', 'bookyourtravel'), // description
				'id'	=> 'user_account_is_partner_page', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id'=> 'general_tab'
			)
		);

		$enabled_searchable_post_types = array();
		$enabled_searchable_post_types[] = array('value' => '', 'label' => esc_html__('None', 'bookyourtravel'));

		if ($this->enable_accommodations) {
			$enabled_searchable_post_types[] = array('value' => 'accommodation', 'label' => esc_html__('Accommodations', 'bookyourtravel'));
		}

		if ($this->enable_tours) {
			$enabled_searchable_post_types[] = array('value' => 'tour', 'label' => esc_html__('Tours', 'bookyourtravel'));
		}

		if ($this->enable_cruises) {
			$enabled_searchable_post_types[] = array('value' => 'cruise', 'label' => esc_html__('Cruises', 'bookyourtravel'));
		}

		if ($this->enable_car_rentals) {
			$enabled_searchable_post_types[] = array('value' => 'car_rental', 'label' => esc_html__('Car rentals', 'bookyourtravel'));
		}

		$this->search_results_custom_meta_tabs = array(
			array(
				'label' => esc_html__('General', 'bookyourtravel'),
				'id' => '_search_list_general_tab',
				'class' => 'general_tab'
			)
		);
		$this->search_results_custom_meta_fields = array(
			array( // Select box
				'label'	=> esc_html__('Show search results by default', 'bookyourtravel'), // <label>
				'desc' => esc_html__('When a user first comes to your search results page, if they have not used the widget to search, for which post type do you want to show results for if any?', 'bookyourtravel'), // the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'search_list_default_results_post_type', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'select', // type of field
				'options' => $enabled_searchable_post_types,
				'admin_tab_id'=> 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Items per page', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('How many items do you want to show per page?', 'bookyourtravel'), // description
				'id'	=> 'search_list_posts_per_page', // field id and name
				'std'	=> '12',
				'type'	=> 'slider',
				'min'	=> apply_filters('bookyourtravel_search_list_posts_per_page_min', '1'),
				'max'	=> apply_filters('bookyourtravel_search_list_posts_per_page_max', '50'),
				'step'	=> '1',
				'admin_tab_id' => 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Items per row', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('How many items do you want to show per row?', 'bookyourtravel'), // description
				'id'	=> 'search_list_posts_per_row', // field id and name
				'std'	=> '4',
				'type'	=> 'slider',
				'min'	=> apply_filters('bookyourtravel_search_list_posts_per_row_min', '1'),
				'max'	=> apply_filters('bookyourtravel_search_list_posts_per_row_max', '5'),
				'step'	=> '1',
				'admin_tab_id' => 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide item titles?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide titles of listed search results', 'bookyourtravel'), // description
				'id'	=> 'search_list_hide_item_titles', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide item address?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide address of listed search results', 'bookyourtravel'), // description
				'id'	=> 'search_list_hide_item_address', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide item images?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide images of listed search results', 'bookyourtravel'), // description
				'id'	=> 'search_list_hide_item_images', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide item descriptions?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide descriptions of listed search results', 'bookyourtravel'), // description
				'id'	=> 'search_list_hide_item_descriptions', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide item prices?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide prices of listed search results', 'bookyourtravel'), // description
				'id'	=> 'search_list_hide_item_prices', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'general_tab'
			),			
			array( // Post ID select box
				'label'	=> esc_html__('Hide item buttons?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide buttons of listed search results', 'bookyourtravel'), // description
				'id'	=> 'search_list_hide_item_actions', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'general_tab'
			),
		);

		$this->post_list_custom_meta_tabs = array(
			array(
				'label' => esc_html__('Content', 'bookyourtravel'),
				'id' => '_post_list_general_tab',
				'class' => 'general_tab'
			),
			array(
				'label' => esc_html__('Display settings', 'bookyourtravel'),
				'id' => '_post_list_item_settings_tab',
				'class' => 'item_settings_tab'
			)
		);

		$sort_by_columns = array();
		$sort_by_columns[] = array('value' => 'title', 'label' => esc_html__('Post title', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'ID', 'label' => esc_html__('Post ID', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'date', 'label' => esc_html__('Publish date', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'rand', 'label' => esc_html__('Random', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'comment_count', 'label' => esc_html__('Comment count', 'bookyourtravel'));
		$sort_by_columns[] = array('value' => 'menu_order', 'label' => esc_html__('Order attribute', 'bookyourtravel'));

		$sort_by_columns = apply_filters('bookyourtravel_post_list_sort_by_columns', $sort_by_columns);

		$this->post_list_custom_meta_fields = array(
			array( // Select box
				'label'	=> esc_html__('Sort by field', 'bookyourtravel'), // <label>
				// the description is created in the callback function with a link to Manage the taxonomy terms
				'id'	=> 'post_list_sort_by', // field id and name, needs to be the exact name of the taxonomy
				'type'	=> 'select', // type of field
				'options' => $sort_by_columns,
				'admin_tab_id' => 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Sort descending?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will sort posts in descending order', 'bookyourtravel'), // description
				'id'	=> 'post_list_sort_descending', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'general_tab'
			),
			// array( // Taxonomy Select box
				// 'label'	=> esc_html__('Categories', 'bookyourtravel'), // <label>
				// // the description is created in the callback function with a link to Manage the taxonomy terms
				// 'id'	=> 'category', // field id and name, needs to be the exact name of the taxonomy
				// 'type'	=> 'tax_checkboxes', // type of field
				// 'admin_tab_id' => 'general_tab'
			// ),
			array( // Post ID select box
				'label'	=> esc_html__('Items per row', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('How many items do you want to show per row?', 'bookyourtravel'), // description
				'id'	=> 'post_list_posts_per_row', // field id and name
				'std'	=> '4',
				'type'	=> 'slider',
				'min'	=> apply_filters('bookyourtravel_post_list_posts_per_row_min', '1'),
				'max'	=> apply_filters('bookyourtravel_post_list_posts_per_row_max', '5'),
				'step'	=> '1',
				'admin_tab_id' => 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Items per page', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('How many items do you want to show per page?', 'bookyourtravel'), // description
				'id'	=> 'post_list_posts_per_page', // field id and name
				'std'	=> '12',
				'type'	=> 'slider',
				'min'	=> apply_filters('bookyourtravel_post_list_posts_per_page_min', '1'),
				'max'	=> apply_filters('bookyourtravel_post_list_posts_per_page_max', '50'),
				'step'	=> '1',
				'admin_tab_id' => 'general_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide item titles?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide titles of listed posts', 'bookyourtravel'), // description
				'id'	=> 'post_list_hide_item_titles', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'item_settings_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide item images?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide images of listed posts', 'bookyourtravel'), // description
				'id'	=> 'post_list_hide_item_images', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'item_settings_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide item descriptions?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide descriptions of listed posts', 'bookyourtravel'), // description
				'id'	=> 'post_list_hide_item_descriptions', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'item_settings_tab'
			),
			array( // Post ID select box
				'label'	=> esc_html__('Hide buttons?', 'bookyourtravel'), // <label>
				'desc'	=> esc_html__('If checked, will hide buttons of listed posts', 'bookyourtravel'), // description
				'id'	=> 'post_list_hide_item_actions', // field id and name
				'type'	=> 'checkbox', // type of field
				'admin_tab_id' => 'item_settings_tab'
			)
		);

		$this->post_list_meta_box = new Custom_Add_Meta_Box( 'post_list_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->post_list_custom_meta_fields, $this->post_list_custom_meta_tabs, 'page' );
		remove_action( 'add_meta_boxes', array( $this->post_list_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array( $this, 'post_list_add_meta_boxes') );

		$this->search_results_meta_box = new Custom_Add_Meta_Box( 'search_results_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->search_results_custom_meta_fields, $this->search_results_custom_meta_tabs, 'page' );
		remove_action( 'add_meta_boxes', array( $this->search_results_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array( $this, 'search_results_add_meta_boxes') );

		$this->user_register_meta_box = new Custom_Add_Meta_Box( 'user_register_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->user_register_custom_meta_fields, $this->user_register_custom_meta_tabs, 'page' );
		remove_action( 'add_meta_boxes', array( $this->user_register_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array( $this, 'user_register_add_meta_boxes') );

		$this->user_forgot_password_meta_box = new Custom_Add_Meta_Box( 'user_forgot_password_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->user_forgot_password_custom_meta_fields, $this->user_forgot_password_custom_meta_tabs, 'page' );
		remove_action( 'add_meta_boxes', array( $this->user_forgot_password_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array( $this, 'user_forgot_password_add_meta_boxes') );

		$this->user_login_meta_box = new Custom_Add_Meta_Box( 'user_login_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->user_login_custom_meta_fields, $this->user_login_custom_meta_tabs, 'page' );
		remove_action( 'add_meta_boxes', array( $this->user_login_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array( $this, 'user_login_add_meta_boxes') );

		$this->frontend_submit_meta_box = new Custom_Add_Meta_Box( 'frontend_submit_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->frontend_submit_custom_meta_fields, $this->frontend_submit_custom_meta_tabs, 'page' );
		remove_action( 'add_meta_boxes', array( $this->frontend_submit_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array( $this, 'frontend_submit_add_meta_boxes' ) );

		$this->page_sidebars_meta_box = new Custom_Add_Meta_Box( 'page_sidebars_custom_meta_fields', esc_html__('Sidebar selection', 'bookyourtravel'), $this->page_sidebars_custom_meta_fields, $this->page_sidebars_custom_meta_tabs, 'page' );
		remove_action( 'add_meta_boxes', array( $this->page_sidebars_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array( $this, 'page_sidebar_add_meta_boxes' ) );

		$this->user_account_meta_box = new Custom_Add_Meta_Box( 'user_account_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->user_account_custom_meta_fields, $this->user_account_custom_meta_tabs, 'page' );
		remove_action( 'add_meta_boxes', array( $this->user_account_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array( $this, 'user_account_add_meta_boxes' ) );

		$this->user_content_list_meta_box = new Custom_Add_Meta_Box( 'user_content_list_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->user_content_list_custom_meta_fields, $this->user_content_list_custom_meta_tabs, 'page' );
		remove_action( 'add_meta_boxes', array( $this->user_content_list_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array( $this, 'user_content_list_add_meta_boxes' ) );

		$this->contact_page_meta_box = new Custom_Add_Meta_Box( 'contact_page_custom_meta_fields', esc_html__('Extra information', 'bookyourtravel'), $this->contact_page_custom_meta_fields, $this->contact_page_custom_meta_tabs, 'page' );
		remove_action( 'add_meta_boxes', array( $this->contact_page_meta_box, 'add_box' ) );
		add_action('add_meta_boxes', array( $this, 'contact_page_add_meta_boxes') );
	}

	function user_account_add_meta_boxes() {

		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file == 'page-user-account.php') {
			add_meta_box(
				$this->user_account_meta_box->id,
				$this->user_account_meta_box->title,
				array( $this->user_account_meta_box, 'meta_box_callback' ),
				'page', 'normal', 'high'
			);
		}
	}

	function user_content_list_add_meta_boxes() {

		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file == 'page-user-content-list.php') {
			add_meta_box(
				$this->user_content_list_meta_box->id,
				$this->user_content_list_meta_box->title,
				array( $this->user_content_list_meta_box, 'meta_box_callback' ),
				'page', 'normal', 'high'
			);
		}
	}

	function page_sidebar_add_meta_boxes() {
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if (((!function_exists('is_woocommerce') || (function_exists('is_woocommerce') && !is_woocommerce())) &&
			$template_file != 'page-contact.php' &&
			$template_file != 'page-user-register.php' &&
			$template_file != 'page-user-login.php' &&
			$template_file != 'page-user-forgot-pass.php' &&
			$template_file != 'page-contact-form-7.php') ||
			(is_single() && $post->post_type == 'post')) {
			if ($post->post_type == 'post') {
				add_meta_box(
					$this->page_sidebars_meta_box->id,
					$this->page_sidebars_meta_box->title,
					array( $this->page_sidebars_meta_box, 'meta_box_callback' ),
					'post', 'normal', 'low'
				);
			} else {
				add_meta_box(
					$this->page_sidebars_meta_box->id,
					$this->page_sidebars_meta_box->title,
					array( $this->page_sidebars_meta_box, 'meta_box_callback' ),
					'page', 'normal', 'default'
				);
			}
		}
	}

	function user_register_add_meta_boxes() {
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file == 'page-user-register.php') {
			add_meta_box(
				$this->user_register_meta_box->id,
				$this->user_register_meta_box->title,
				array( $this->user_register_meta_box, 'meta_box_callback' ),
				'page', 'normal', 'high'
			);
		}
	}

	function post_list_add_meta_boxes() {
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file == 'page-post-list.php') {
			add_meta_box(
				$this->post_list_meta_box->id,
				$this->post_list_meta_box->title,
				array( $this->post_list_meta_box, 'meta_box_callback' ),
				'page', 'normal', 'high'
			);
		}
	}

	function search_results_add_meta_boxes() {
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file == 'page-custom-search-results.php') {
			add_meta_box(
				$this->search_results_meta_box->id,
				$this->search_results_meta_box->title,
				array( $this->search_results_meta_box, 'meta_box_callback' ),
				'page', 'normal', 'high'
			);
		}
	}

	function user_forgot_password_add_meta_boxes() {
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file == 'page-user-forgot-pass.php') {
			add_meta_box(
				$this->user_forgot_password_meta_box->id,
				$this->user_forgot_password_meta_box->title,
				array( $this->user_forgot_password_meta_box, 'meta_box_callback' ),
				'page', 'normal', 'high'
			);
		}
	}

	function user_login_add_meta_boxes() {
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file == 'page-user-login.php') {
			add_meta_box(
				$this->user_login_meta_box->id,
				$this->user_login_meta_box->title,
				array( $this->user_login_meta_box, 'meta_box_callback' ),
				'page', 'normal', 'high'
			);
		}
	}

	function frontend_submit_add_meta_boxes() {
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file == 'page-user-submit-content.php') {
			add_meta_box(
				$this->frontend_submit_meta_box->id,
				$this->frontend_submit_meta_box->title,
				array( $this->frontend_submit_meta_box, 'meta_box_callback' ),
				'page', 'normal', 'high'
			);
		}
	}

	function contact_page_add_meta_boxes() {
		global $post;
		$template_file = get_post_meta($post->ID,'_wp_page_template',true);
		if ($template_file == 'page-contact.php') {
			add_meta_box(
				$this->contact_page_meta_box->id,
				$this->contact_page_meta_box->title,
				array( $this->contact_page_meta_box, 'meta_box_callback' ),
				'page', 'normal', 'high'
			);
		}
	}

}

global $bookyourtravel_theme_meta_boxes;
// store the instance in a variable to be retrieved later and call init
$bookyourtravel_theme_meta_boxes = BookYourTravel_Theme_Meta_Boxes::get_instance();
