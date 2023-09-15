<?php
/**
 * Book Your Travel functions and definitions.
 *
 * Sets up the theme and provides some helper functions, which are used
 * in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.18.9
 *
 */

if ( ! defined( 'BOOKYOURTRAVEL_VERSION' ) )
    define( 'BOOKYOURTRAVEL_VERSION', '8.18.9' );

if ( ! defined( 'BOOKYOURTRAVEL_FRONTEND_SUBMIT_ROLE' ) )
    define( 'BOOKYOURTRAVEL_FRONTEND_SUBMIT_ROLE', 'byt_frontend_contributor' );

if ( ! defined( 'BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE' ) )
    define( 'BOOKYOURTRAVEL_ACCOMMODATION_VACANCIES_TABLE', $wpdb->prefix . 'byt_accommodation_vacancies' );

if ( ! defined( 'BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE' ) )
    define( 'BOOKYOURTRAVEL_ACCOMMODATION_BOOKINGS_TABLE', $wpdb->prefix . 'byt_accommodation_bookings' );

if ( ! defined( 'BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE' ) )
    define( 'BOOKYOURTRAVEL_CAR_RENTAL_BOOKINGS_TABLE', $wpdb->prefix . 'byt_car_rental_bookings' );

if ( ! defined( 'BOOKYOURTRAVEL_CAR_RENTAL_AVAILABILITIES_TABLE' ) )
    define( 'BOOKYOURTRAVEL_CAR_RENTAL_AVAILABILITIES_TABLE', $wpdb->prefix . 'byt_car_rental_availabilities' );

if ( ! defined( 'BOOKYOURTRAVEL_TOUR_SCHEDULE_TABLE' ) )
    define( 'BOOKYOURTRAVEL_TOUR_SCHEDULE_TABLE', $wpdb->prefix . 'byt_tour_schedule' );

if ( ! defined( 'BOOKYOURTRAVEL_TOUR_BOOKING_TABLE' ) )
    define( 'BOOKYOURTRAVEL_TOUR_BOOKING_TABLE', $wpdb->prefix . 'byt_tour_booking' );

if ( ! defined( 'BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE' ) )
    define( 'BOOKYOURTRAVEL_CRUISE_SCHEDULE_TABLE', $wpdb->prefix . 'byt_cruise_schedule' );

if ( ! defined( 'BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE' ) )
    define( 'BOOKYOURTRAVEL_CRUISE_BOOKING_TABLE', $wpdb->prefix . 'byt_cruise_booking' );

if ( ! defined( 'BOOKYOURTRAVEL_ALT_DATE_FORMAT' ) )
    define( 'BOOKYOURTRAVEL_ALT_DATE_FORMAT', 'yy-mm-dd' );

if ( ! defined( 'BOOKYOURTRAVEL_ACCOMMODATIONS_JS_PATH' ) )
    define( 'BOOKYOURTRAVEL_ACCOMMODATIONS_JS_PATH', '/js/accommodations.js' );

if ( ! defined( 'BOOKYOURTRAVEL_CAR_RENTALS_JS_PATH' ) )
    define( 'BOOKYOURTRAVEL_CAR_RENTALS_JS_PATH', '/js/car-rentals.js' );

if ( ! defined( 'BOOKYOURTRAVEL_CRUISES_JS_PATH' ) )
    define( 'BOOKYOURTRAVEL_CRUISES_JS_PATH', '/js/cruises.js' );

if ( ! defined( 'BOOKYOURTRAVEL_TOURS_JS_PATH' ) )
    define( 'BOOKYOURTRAVEL_TOURS_JS_PATH', '/js/tours.js' );

if ( ! defined( 'BOOKYOURTRAVEL_EXTRA_ITEMS_JS_PATH' ) )
    define( 'BOOKYOURTRAVEL_EXTRA_ITEMS_JS_PATH', '/js/extra-items.js' );

// disable wpbakery upgrade prevention
if (function_exists('vc_manager')) {
    vc_manager()->disableUpdater(true);
    vc_manager()->setIsAsTheme( true );
}

// Shim to fix the late load of the WooCommerce autoloader.
if ( defined( 'WC_PLUGIN_FILE' ) && ! function_exists( 'wc_get_loop_prop' ) ) {
	$woocommerce_file = dirname( WC_PLUGIN_FILE ) . '/includes/wc-template-functions.php';
	if ( file_exists( $woocommerce_file ) ) {
		require_once $woocommerce_file;						
	}
}

require_once get_template_directory() . '/includes/plugins/urlify/URLify.php';
require_once get_template_directory() . '/includes/theme_utils.php';
require_once get_template_directory() . '/includes/theme_versioning.php';
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_singleton.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_of_default_fields.php');

global $wpdb, $bookyourtravel_multi_language_count, $bookyourtravel_installed_version;

$bookyourtravel_multi_language_count = 1;
global $sitepress;
if ($sitepress) {
	$active_languages = $sitepress->get_active_languages();
	$sitepress_settings = $sitepress->get_settings();
	$hidden_languages = array();
	if (isset($sitepress_settings['hidden_languages']))
		$hidden_languages = $sitepress_settings['hidden_languages'];
	$bookyourtravel_multi_language_count = count($active_languages) + count($hidden_languages);
}

$bookyourtravel_installed_version = get_option('bookyourtravel_version', null);

if (  null !== $bookyourtravel_installed_version && $bookyourtravel_installed_version != 0 && $bookyourtravel_installed_version < BOOKYOURTRAVEL_VERSION) {
	update_option( '_byt_needs_update', 1 );
	update_option( '_byt_version_before_update', $bookyourtravel_installed_version );
}

if (null == $bookyourtravel_installed_version || $bookyourtravel_installed_version < BOOKYOURTRAVEL_VERSION) {
    update_option('bookyourtravel_version', BOOKYOURTRAVEL_VERSION);
}

if(!function_exists('optionsframework_option_name')) {
    function optionsframework_option_name() {

		// This gets the theme name from the stylesheet (lowercase and without spaces)
		$themename = get_option( 'stylesheet' );
		$themename = preg_replace( "/\W/", "_", strtolower( $themename ) );

        $optionsframework_settings = get_option('optionsframework');
        $optionsframework_settings['id'] = $themename;
        update_option('optionsframework', $optionsframework_settings);
    }
}

if ( !function_exists( 'optionsframework_init' ) ) {
	define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/includes/framework/' );
	require_once BookYourTravel_Theme_Utils::get_file_path('/includes/framework/options-framework.php');
}

require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/metaboxes/meta_box.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_globals.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_of_custom.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_controls.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/class-tgm-plugin-activation.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/abstracts/class-entity.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-post-helper.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-location-helper.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-review-helper.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-extra-item-helper.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-facility-helper.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-room-type-helper.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-accommodation-helper.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-tour-helper.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-cabin-type-helper.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-cruise-helper.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/post_types/class-car-rental-helper.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/admin/theme_admin_controls.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/admin/theme_accommodation_vacancy_admin.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/admin/theme_accommodation_booking_admin.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/admin/theme_accommodation_calendar_admin.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/admin/theme_tour_schedule_admin.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/admin/theme_tour_booking_admin.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/admin/theme_tour_calendar_admin.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/admin/theme_cruise_schedule_admin.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/admin/theme_cruise_booking_admin.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/admin/theme_cruise_calendar_admin.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/admin/theme_car_rental_availability_admin.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/admin/theme_car_rental_booking_admin.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/admin/theme_car_rental_calendar_admin.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_woocommerce.php');

require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/frontend-submit/frontend-submit.php');

if ( class_exists('Vc_Manager') ) {
	require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_visual_composer.php');
}

require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_post_types.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_actions.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_filters.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_ajax.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/customizer/theme_customizer.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_meta_boxes.php');

require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-accommodation-list.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-tour-list.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-cruise-list.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-car_rental-list.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-location-list.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-post-list.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-address.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-social.php');
// custom
// require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-home-search.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-home-feature.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-call-to-action.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-iconic-features.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/plugins/widgets/widget-search.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/includes/theme_ical.php');

// custom vc_elements
require_once BookYourTravel_Theme_Utils::get_file_path('/visual_composer/vc_map.php');
require_once BookYourTravel_Theme_Utils::get_file_path('/visual_composer/short_code.php');


function register_custom_menus() {
    register_nav_menus(
        array(
            'contact-info' => __('Contact Info'),
            'about-menu' => __('About us'),
            'destinations-menu' => __('Our Destination'),
            'tour-menu' => __('Our Tour'),
            'tour-style' => __('Tour Style'),
            'user-menu' => __('User Infor'),
            'social-menu' => __('Lets Get Social'),
            'shore' => __('Shore Excursions'),
        )
    );
}
add_action('after_setup_theme', 'register_custom_menus');


// function enqueue_my_script() {
//     // Register the script
//     wp_register_script('my-custom-script', get_template_directory_uri() . 'js/tour-list.js', array('jquery'), '1.0', true);

//     // Enqueue the script
//     wp_enqueue_script('my-custom-script');
// }
// echo  get_template_directory_uri() . '/js/tour-list.js';
// // Hook into the 'wp_enqueue_scripts' action
// add_action('wp_enqueue_scripts', 'enqueue_my_script');

