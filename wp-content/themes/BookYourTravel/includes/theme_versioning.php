<?php
/**
 * BookYourTravel_Theme_Versioning class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

class BookYourTravel_Theme_Versioning {

	public static function check_theme_setup() {
		$setup_errors = self::get_theme_setup_errors();

		$html = '';
		if (count($setup_errors) > 0) {
			$html = '<p class="notok">';
			foreach($setup_errors as $error) {
				$html .= "<span>" . $error . '</span>';
			}
			$html .= '</p>';
		}

		return $html;
	}

	public static function get_theme_setup_errors() {
		$setup_errors = array();
		global $wp_filesystem;
		if (empty($wp_filesystem)) {
			require_once(ABSPATH . '/wp-admin/includes/file.php');
			WP_Filesystem();
		}
		
		if (ini_get('allow_url_fopen') != '1' && ini_get('allow_url_fopen') != 'On') {
			$setup_errors[] = esc_html__('The allow_url_fopen setting is turned off in the PHP ini!', 'bookyourtravel');
		} else {
			// can we read a file with wp filesystem?

			if (!$wp_filesystem->get_contents(get_template_directory().'/includes/imports/test.imp')) {
				$setup_errors[] = get_template_directory().'/includes/imports/test.imp';
				$setup_errors[] = esc_html__('The script couldn\'t read the test.imp file. Is it there? Does it have the permission to read?', 'bookyourtravel');
			}
		}

		// $uploads_dir = $wp_filesystem->abspath() . 'wp-content/uploads';
		// if (!$wp_filesystem->is_dir($uploads_dir)) {
		// 	if (!wp_mkdir_p($uploads_dir)) {
		// 		$setup_errors[] = esc_html__('The script couldn\'t create a directory!', 'bookyourtravel');
		// 	}
		// } else {
		// 	if (!$wp_filesystem->copy(get_template_directory().'/includes/imports/img.jpg', $wp_filesystem->abspath() . 'wp-content/uploads/img.jpg', true)) {
		// 		$setup_errors[] = esc_html__('The script couldn\'t copy a file!', 'bookyourtravel');
		// 	} else {
		// 		if (is_file($wp_filesystem->abspath() . 'wp-content/uploads/img.jpg')) {
		// 			$wp_filesystem->delete($wp_filesystem->abspath() . 'wp-content/uploads/img.jpg');
		// 		}
		// 	}
		// }

		// can we read/write database?
		global $wpdb;
		if (!$wpdb->query('CREATE TABLE IF NOT EXISTS '.$wpdb->prefix.'testing (id mediumint(9) NOT NULL AUTO_INCREMENT, test varchar(255), UNIQUE KEY id (id))')) {
			$setup_errors[] = esc_html__('The script is not allowed to write MySQL database!', 'bookyourtravel');
		} else {
			if (!$wpdb->query('TRUNCATE TABLE '.$wpdb->prefix.'testing')) {
				$setup_errors[] = esc_html__('The script is not allowed to write MySQL database!', 'bookyourtravel');
			}
		}

		return $setup_errors;
	}

	public static function list_theme_css_files() {
		$theme_files = array();

		$theme_files[] = "css\\admin\\admin_custom.css";
		$theme_files[] = "css\\admin\\composer_custom.css";
		$theme_files[] = "css\\admin\\customize_controls.css";
		$theme_files[] = "css\\admin\\gutenberg.css";
		$theme_files[] = "css\\lib\\dropzone.min.css";		
		$theme_files[] = "css\\lib\\font-awesome.min.css";
		$theme_files[] = "css\\lib\\material-icons.min.css";
		$theme_files[] = "css\\lib\\prettyPhoto.min.css";
		$theme_files[] = "css\\style-rtl.css";
		$theme_files[] = "css\\style.css";
		$theme_files[] = "includes\\framework\\css\\color-picker.min.css";
		$theme_files[] = "includes\\framework\\css\\optionsframework.min.css";
		$theme_files[] = "includes\\plugins\\metaboxes\\css\\chosen.css";
		$theme_files[] = "includes\\plugins\\metaboxes\\css\\jqueryui.css";
		$theme_files[] = "includes\\plugins\\metaboxes\\css\\meta_box.css";
		$theme_files[] = "includes\\plugins\\lightSlider\\css\\lightSlider.css";
		$theme_files[] = "style.css";
		
		foreach ($theme_files as $index => $theme_file) {
			$theme_files[$index] = self::correct_platform_slashes($theme_file);
		}		
		
		asort($theme_files);
		
		return $theme_files;
	}

	public static function list_theme_js_files() {

		$theme_files = array();

		$theme_files[] = "includes\\framework\\js\\color-picker.min.js";
		$theme_files[] = "includes\\framework\\js\\iris.min.js";
		$theme_files[] = "includes\\framework\\js\\media-uploader.js";
		$theme_files[] = "includes\\framework\\js\\options-custom.js";
		$theme_files[] = "includes\\plugins\\frontend-submit\\lib\\js\\frontend-submit-accommodations.js";
		$theme_files[] = "includes\\plugins\\frontend-submit\\lib\\js\\frontend-submit-car-rentals.js";
		$theme_files[] = "includes\\plugins\\frontend-submit\\lib\\js\\frontend-submit-cruises.js";
		$theme_files[] = "includes\\plugins\\frontend-submit\\lib\\js\\frontend-submit-tours.js";
		$theme_files[] = "includes\\plugins\\frontend-submit\\lib\\js\\frontend-submit.js";		
		$theme_files[] = "includes\\plugins\\lightSlider\\js\\jquery.lightSlider.js";
		$theme_files[] = "includes\\plugins\\lightSlider\\js\\jquery.lightSlider.min.js";
		$theme_files[] = "includes\\plugins\\metaboxes\\js\\chosen.js";
		$theme_files[] = "includes\\plugins\\metaboxes\\js\\scripts.js";
		$theme_files[] = "js\\accommodations.js";
		$theme_files[] = "js\\account.js";
		$theme_files[] = "js\\admin\\admin.js";
		$theme_files[] = "js\\admin\\admin_accommodations.js";
		$theme_files[] = "js\\admin\\admin_car_rentals.js";
		$theme_files[] = "js\\admin\\admin_cruises.js";
		$theme_files[] = "js\\admin\\admin_tours.js";	
		$theme_files[] = "js\\admin\\composer\\composer_scripts.js";
		$theme_files[] = "js\\admin\\composer\\vc_accommodation_card.js";
		$theme_files[] = "js\\admin\\composer\\vc_byt_custom_element_view.js";
		$theme_files[] = "js\\admin\\composer\\vc_car_rental_card.js";
		$theme_files[] = "js\\admin\\composer\\vc_cruise_card.js";
		$theme_files[] = "js\\admin\\composer\\vc_location_card.js";
		$theme_files[] = "js\\admin\\composer\\vc_tour_card.js";
		$theme_files[] = "js\\admin\\composer\\vc_widget_iconic_features.js";		
		$theme_files[] = "js\\admin\\customize_controls.js";
		$theme_files[] = "js\\admin\\customize_preview.js";
		$theme_files[] = "js\\admin\\optionsframework_custom.js";
		$theme_files[] = "js\\admin\\search_widget_admin.js";		
		$theme_files[] = "js\\car-rentals.js";
		$theme_files[] = "js\\contact.js";
		$theme_files[] = "js\\cruises.js";
		$theme_files[] = "js\\extra-items.js";
		$theme_files[] = "js\\gallery.js";			
		$theme_files[] = "js\\header-ribbon.js";
		$theme_files[] = "js\\inquiry.js";		
		$theme_files[] = "js\\lib\\dropzone.min.js";				
		$theme_files[] = "js\\lib\\extras.jquery.validate.min.js";
		$theme_files[] = "js\\lib\infobox.min.js";
		$theme_files[] = "js\\lib\jquery.prettyPhoto.min.js";
		$theme_files[] = "js\\lib\jquery.raty.min.js";
		$theme_files[] = "js\\lib\jquery.tablesorter.min.js";
		$theme_files[] = "js\\lib\jquery.uniform.min.js";
		$theme_files[] = "js\\lib\jquery.validate.min.js";
		$theme_files[] = "js\\maps.js";		
		$theme_files[] = "js\\reviews.js";
		$theme_files[] = "js\\scripts.js";
		$theme_files[] = "js\\search-widget.js";		
		$theme_files[] = "js\\tabs.js";
		$theme_files[] = "js\\tours.js";		

		foreach ($theme_files as $index => $theme_file) {
			$theme_files[$index] = self::correct_platform_slashes($theme_file);
		}
		
		asort($theme_files);
		
		return $theme_files;
	}

	public static function list_theme_php_files() {
		
		$theme_files = array();
		
		$theme_files[] = "404.php";
		$theme_files[] = "archive.php";
		$theme_files[] = "comments.php";
		$theme_files[] = "footer.php";
		$theme_files[] = "functions.php";
		$theme_files[] = "header.php";		
		$theme_files[] = "includes\\composer\\vc_location_card_shortcode.php";
		$theme_files[] = "includes\\customizer\\theme_customizer.php";
		$theme_files[] = "includes\\customizer\\theme_customizer_slider_control.php";
		$theme_files[] = "includes\\customizer\\theme_schemes.php";
		$theme_files[] = "includes\\framework\\includes\\class-options-framework-admin.php";
		$theme_files[] = "includes\\framework\\includes\\class-options-framework.php";
		$theme_files[] = "includes\\framework\\includes\\class-options-importer.php";
		$theme_files[] = "includes\\framework\\includes\\class-options-interface.php";
		$theme_files[] = "includes\\framework\\includes\\class-options-media-uploader.php";
		$theme_files[] = "includes\\framework\\includes\\class-options-sanitization.php";
		$theme_files[] = "includes\\framework\\options-framework.php";
		$theme_files[] = "includes\\parts\\footer\\footer-copy.php";
		$theme_files[] = "includes\\parts\\footer\\footer-nav.php";
		$theme_files[] = "includes\\parts\\footer\\login-lightbox.php";
		$theme_files[] = "includes\\parts\\footer\\register-lightbox.php";
		$theme_files[] = "includes\\parts\\header\\header-contact.php";
		$theme_files[] = "includes\\parts\\header\\header-header1.php";
		$theme_files[] = "includes\\parts\\header\\header-header10.php";
		$theme_files[] = "includes\\parts\\header\\header-header2.php";
		$theme_files[] = "includes\\parts\\header\\header-header3.php";
		$theme_files[] = "includes\\parts\\header\\header-header4.php";
		$theme_files[] = "includes\\parts\\header\\header-header5.php";
		$theme_files[] = "includes\\parts\\header\\header-header6.php";
		$theme_files[] = "includes\\parts\\header\\header-header7.php";
		$theme_files[] = "includes\\parts\\header\\header-header8.php";
		$theme_files[] = "includes\\parts\\header\\header-header9.php";
		$theme_files[] = "includes\\parts\\header\\header-logo.php";
		$theme_files[] = "includes\\parts\\header\\header-nav.php";
		$theme_files[] = "includes\\parts\\header\\header-ribbon.php";
		$theme_files[] = "includes\\parts\\header\\header-search.php";
		$theme_files[] = "includes\\parts\\header\\header-top-nav.php";
		$theme_files[] = "includes\\parts\\post\\post-item.php";
		$theme_files[] = "includes\\parts\\location\\location-item.php";
		$theme_files[] = "includes\\parts\\location\\location-list.php";
		$theme_files[] = "includes\\parts\\location\\single\\inner-nav.php";
		$theme_files[] = "includes\\parts\\location\\single\\single-content.php";
		$theme_files[] = "includes\\parts\\location\\single\\tab-content.php";
		$theme_files[] = "includes\\parts\\post\\single\\post-gallery.php";	
		$theme_files[] = "includes\\parts\\post\\single\\post-image.php";	
		$theme_files[] = "includes\\parts\\user\\user-account-menu.php";
		$theme_files[] = "includes\\plugins\\class-tgm-plugin-activation.php";
		$theme_files[] = "includes\\plugins\\metaboxes\\meta_box.php";
		$theme_files[] = "includes\\plugins\\widgets\\widget-address.php";
		$theme_files[] = "includes\\plugins\\widgets\\widget-call-to-action.php";
		$theme_files[] = "includes\\plugins\\widgets\\widget-home-feature.php";
		$theme_files[] = "includes\\plugins\\widgets\\widget-iconic-features.php";
		$theme_files[] = "includes\\plugins\\widgets\\widget-post-list.php";
		$theme_files[] = "includes\\plugins\\widgets\\widget-social.php";
		// custom
		$theme_files[] = "includes\\plugins\\widgets\\widget-home-search.php";
		$theme_files[] = "includes\\post_types\\abstracts\class-entity.php";
		$theme_files[] = "includes\\post_types\\class-post-helper.php";
		$theme_files[] = "includes\\post_types\\class-post.php";
		$theme_files[] = "includes\\post_types\\class-location-helper.php";
		$theme_files[] = "includes\\post_types\\class-location.php";
		$theme_files[] = "includes\\theme_controls.php";
		$theme_files[] = "includes\\theme_actions.php";
		$theme_files[] = "includes\\theme_ajax.php";
		$theme_files[] = "includes\\theme_filters.php";
		$theme_files[] = "includes\\theme_globals.php";
		$theme_files[] = "includes\\theme_meta_boxes.php";
		$theme_files[] = "includes\\theme_of_custom.php";
		$theme_files[] = "includes\\theme_of_default_fields.php";
		$theme_files[] = "includes\\theme_post_types.php";
		$theme_files[] = "includes\\theme_singleton.php";
		$theme_files[] = "includes\\theme_utils.php";
		$theme_files[] = "includes\\theme_versioning.php";
		$theme_files[] = "includes\\theme_visual_composer.php";		
		$theme_files[] = "index.php";
		$theme_files[] = "options.php";
		$theme_files[] = "sidebar-right-accommodation.php";
		$theme_files[] = "sidebar-right-car_rental.php";
		$theme_files[] = "sidebar-right-cruise.php";
		$theme_files[] = "sidebar-right-tour.php";
		$theme_files[] = "sidebar-hero.php";
		$theme_files[] = "single-accommodation.php";
		$theme_files[] = "single-car_rental.php";
		$theme_files[] = "single-cruise.php";
		$theme_files[] = "single-tour.php";
		$theme_files[] = "taxonomy.php";		
		$theme_files[] = "page-accommodation-list.php";
		$theme_files[] = "page-blank.php";
		$theme_files[] = "page-car_rental-list.php";
		$theme_files[] = "page-contact-form-7.php";
		$theme_files[] = "page-contact.php";
		$theme_files[] = "page-cruise-list.php";
		$theme_files[] = "page-custom-search-results.php";
		$theme_files[] = "page-tour-list.php";
		$theme_files[] = "page-location-list.php";
		$theme_files[] = "page-post-list.php";		
		$theme_files[] = "page-user-account.php";
		$theme_files[] = "page-user-content-list.php";
		$theme_files[] = "page-user-forgot-pass.php";
		$theme_files[] = "page-user-login.php";
		$theme_files[] = "page-user-register.php";
		$theme_files[] = "page-user-submit-content.php";		
		$theme_files[] = "page.php";
		$theme_files[] = "sidebar-above-footer.php";
		$theme_files[] = "sidebar-footer.php";
		$theme_files[] = "sidebar-header.php";
		$theme_files[] = "sidebar-home-content.php";
		$theme_files[] = "sidebar-home-footer.php";
		$theme_files[] = "sidebar-left.php";
		$theme_files[] = "sidebar-right.php";
		$theme_files[] = "sidebar-under-header.php";
		$theme_files[] = "sidebar.php";
		$theme_files[] = "single.php";
		$theme_files[] = "single-location.php";
		$theme_files[] = "byt_home.php";
		$theme_files[] = "includes\\admin\\theme_accommodation_booking_admin.php";
		$theme_files[] = "includes\\admin\\theme_accommodation_calendar_admin.php";
		$theme_files[] = "includes\\admin\\theme_accommodation_vacancy_admin.php";
		$theme_files[] = "includes\\admin\\theme_admin_controls.php";
		$theme_files[] = "includes\\admin\\theme_car_rental_availability_admin.php";
		$theme_files[] = "includes\\admin\\theme_car_rental_booking_admin.php";
		$theme_files[] = "includes\\admin\\theme_cruise_booking_admin.php";
		$theme_files[] = "includes\\admin\\theme_cruise_schedule_admin.php";
		$theme_files[] = "includes\\admin\\theme_tour_booking_admin.php";
		$theme_files[] = "includes\\admin\\theme_tour_schedule_admin.php";
		$theme_files[] = "includes\\composer\\vc_accommodation_card_shortcode.php";
		$theme_files[] = "includes\\composer\\vc_car_rental_card_shortcode.php";
		$theme_files[] = "includes\\composer\\vc_cruise_card_shortcode.php";
		$theme_files[] = "includes\\composer\\vc_tour_card_shortcode.php";
		$theme_files[] = "includes\\composer\\vc_widget_accommodation_list_shortcode.php";
		$theme_files[] = "includes\\composer\\vc_widget_address_shortcode.php";
		$theme_files[] = "includes\\composer\\vc_widget_call_to_action_shortcode.php";
		$theme_files[] = "includes\\composer\\vc_widget_car_rental_list_shortcode.php";
		$theme_files[] = "includes\\composer\\vc_widget_cruise_list_shortcode.php";
		$theme_files[] = "includes\\composer\\vc_widget_home_feature_shortcode.php";
		$theme_files[] = "includes\\composer\\vc_widget_iconic_features_shortcode.php";
		$theme_files[] = "includes\\composer\\vc_widget_location_list_shortcode.php";
		$theme_files[] = "includes\\composer\\vc_widget_post_list_shortcode.php";
		$theme_files[] = "includes\\composer\\vc_widget_social_shortcode.php";
		// custom
		$theme_files[] = "includes\\composer\\vc_widget_home_searching_shortcode.php";
		// custom
		$theme_files[] = "includes\\composer\\vc_widget_tour_list_shortcode.php";
		$theme_files[] = "includes\\parts\\accommodation\\accommodation-item.php";
		$theme_files[] = "includes\\parts\\accommodation\\accommodation-list.php";
		$theme_files[] = "includes\\parts\\accommodation\\single\\booking-form-calendar-booking-terms.php";
		$theme_files[] = "includes\\parts\\accommodation\\single\\booking-form-calendar-fields.php";
		$theme_files[] = "includes\\parts\\accommodation\\single\\booking-form-calendar-summary-fields.php";
		$theme_files[] = "includes\\parts\\accommodation\\single\\booking-form-confirmation-core-fields.php";
		$theme_files[] = "includes\\parts\\accommodation\\single\\booking-form-details-core-fields.php";
		$theme_files[] = "includes\\parts\\accommodation\\single\\inner-nav.php";
		$theme_files[] = "includes\\parts\\accommodation\\single\\javascript-vars.php";
		$theme_files[] = "includes\\parts\\accommodation\\single\\single-content.php";
		$theme_files[] = "includes\\parts\\accommodation\\single\\tab-content.php";
		$theme_files[] = "includes\\parts\\booking\\form-calendar.php";
		$theme_files[] = "includes\\parts\\booking\\form-confirmation.php";
		$theme_files[] = "includes\\parts\\booking\\form-details.php";
		$theme_files[] = "includes\\parts\\cabin_type\\cabin_type-item.php";
		$theme_files[] = "includes\\parts\\cabin_type\\cabin_type-list.php";
		$theme_files[] = "includes\\parts\\car_rental\\car_rental-item.php";
		$theme_files[] = "includes\\parts\\car_rental\\car_rental-list.php";
		$theme_files[] = "includes\\parts\\car_rental\\single\\booking-form-calendar-booking-terms.php";
		$theme_files[] = "includes\\parts\\car_rental\\single\\booking-form-calendar-summary-fields.php";
		$theme_files[] = "includes\\parts\\car_rental\\single\\booking-form-confirmation-core-fields.php";
		$theme_files[] = "includes\\parts\\car_rental\\single\\booking-form-details-core-fields.php";
		$theme_files[] = "includes\\parts\\car_rental\\single\\inner-nav.php";
		$theme_files[] = "includes\\parts\\car_rental\\single\\javascript-vars.php";
		$theme_files[] = "includes\\parts\\car_rental\\single\\single-content.php";
		$theme_files[] = "includes\\parts\\car_rental\\single\\tab-content.php";
		$theme_files[] = "includes\\parts\\cruise\\cruise-item.php";
		$theme_files[] = "includes\\parts\\cruise\\cruise-list.php";
		$theme_files[] = "includes\\parts\\cruise\\single\\booking-form-calendar-booking-terms.php";
		$theme_files[] = "includes\\parts\\cruise\\single\\booking-form-calendar-fields.php";
		$theme_files[] = "includes\\parts\\cruise\\single\\booking-form-calendar-summary-fields.php";
		$theme_files[] = "includes\\parts\\cruise\\single\\booking-form-confirmation-core-fields.php";
		$theme_files[] = "includes\\parts\\cruise\\single\\booking-form-details-core-fields.php";
		$theme_files[] = "includes\\parts\\cruise\\single\\inner-nav.php";
		$theme_files[] = "includes\\parts\\cruise\\single\\javascript-vars.php";
		$theme_files[] = "includes\\parts\\cruise\\single\\single-content.php";
		$theme_files[] = "includes\\parts\\cruise\\single\\tab-content.php";
		$theme_files[] = "includes\\parts\\extra_item\\form-extra-items.php";
		$theme_files[] = "includes\\parts\\header\\header-header11.php";
		$theme_files[] = "includes\\parts\\header\\header-top-nav-left.php";
		$theme_files[] = "includes\\parts\\header\\home-page-header.php";
		$theme_files[] = "includes\\parts\\inquiry\\inquiry-form.php";
		$theme_files[] = "includes\\parts\\location\\single\\javascript-vars.php";
		$theme_files[] = "includes\\parts\\post\\single\\post-content.php";
		$theme_files[] = "includes\\parts\\review\\review-form.php";
		$theme_files[] = "includes\\parts\\review\\review-item.php";
		$theme_files[] = "includes\\parts\\review\\review-list.php";
		$theme_files[] = "includes\\parts\\room_type\\room_type-item.php";
		$theme_files[] = "includes\\parts\\room_type\\room_type-list.php";
		$theme_files[] = "includes\\parts\\tour\\single\\booking-form-calendar-booking-terms.php";
		$theme_files[] = "includes\\parts\\tour\\single\\booking-form-calendar-fields.php";
		$theme_files[] = "includes\\parts\\tour\\single\\booking-form-calendar-summary-fields.php";
		$theme_files[] = "includes\\parts\\tour\\single\\booking-form-confirmation-core-fields.php";
		$theme_files[] = "includes\\parts\\tour\\single\\booking-form-details-core-fields.php";
		$theme_files[] = "includes\\parts\\tour\\single\\inner-nav.php";
		$theme_files[] = "includes\\parts\\tour\\single\\javascript-vars.php";
		$theme_files[] = "includes\\parts\\tour\\single\\single-content.php";
		$theme_files[] = "includes\\parts\\tour\\single\\tab-content.php";
		$theme_files[] = "includes\\parts\\tour\\tour-item.php";
		$theme_files[] = "includes\\parts\\tour\\tour-list.php";
		$theme_files[] = "includes\\parts\\user\\partner-account-menu.php";	
		$theme_files[] = "includes\\plugins\\frontend-submit\\frontend-submit.php";
		$theme_files[] = "includes\\plugins\\frontend-submit\\lib\\class-field-helper.php";
		$theme_files[] = "includes\\plugins\\frontend-submit\\lib\\class-html-helper.php";
		$theme_files[] = "includes\\plugins\\frontend-submit\\parts\\accommodation_booking-list.php";
		$theme_files[] = "includes\\plugins\\frontend-submit\\parts\\accommodation_vacancy-list.php";
		$theme_files[] = "includes\\plugins\\frontend-submit\\parts\\car_rental_availability-list.php";
		$theme_files[] = "includes\\plugins\\frontend-submit\\parts\\car_rental_booking-list.php";
		$theme_files[] = "includes\\plugins\\frontend-submit\\parts\\cruise_booking-list.php";
		$theme_files[] = "includes\\plugins\\frontend-submit\\parts\\cruise_schedule-list.php";
		$theme_files[] = "includes\\plugins\\frontend-submit\\parts\\list-table-body.php";
		$theme_files[] = "includes\\plugins\\frontend-submit\\parts\\list-table-head.php";
		$theme_files[] = "includes\\plugins\\frontend-submit\\parts\\tour_booking-list.php";
		$theme_files[] = "includes\\plugins\\frontend-submit\\parts\\tour_schedule-list.php";
		$theme_files[] = "includes\\plugins\\urlify\\URLify.php";
		$theme_files[] = "includes\\plugins\\widgets\\widget-accommodation-list.php";
		$theme_files[] = "includes\\plugins\\widgets\\widget-car_rental-list.php";
		$theme_files[] = "includes\\plugins\\widgets\\widget-cruise-list.php";
		$theme_files[] = "includes\\plugins\\widgets\\widget-location-list.php";
		$theme_files[] = "includes\\plugins\\widgets\\widget-search.php";
		$theme_files[] = "includes\\plugins\\widgets\\widget-tour-list.php";
		$theme_files[] = "includes\\post_types\\class-accommodation-helper.php";
		$theme_files[] = "includes\\post_types\\class-accommodation.php";
		$theme_files[] = "includes\\post_types\\class-cabin-type-helper.php";
		$theme_files[] = "includes\\post_types\\class-cabin-type.php";
		$theme_files[] = "includes\\post_types\\class-car-rental-helper.php";
		$theme_files[] = "includes\\post_types\\class-car-rental.php";
		$theme_files[] = "includes\\post_types\\class-cruise-helper.php";
		$theme_files[] = "includes\\post_types\\class-cruise.php";
		$theme_files[] = "includes\\post_types\\class-extra-item-helper.php";
		$theme_files[] = "includes\\post_types\\class-extra-item.php";
		$theme_files[] = "includes\\post_types\\class-facility-helper.php";
		$theme_files[] = "includes\\post_types\\class-review-helper.php";
		$theme_files[] = "includes\\post_types\\class-room-type-helper.php";
		$theme_files[] = "includes\\post_types\\class-room-type.php";
		$theme_files[] = "includes\\post_types\\class-tour-helper.php";
		$theme_files[] = "includes\\post_types\\class-tour.php";
		$theme_files[] = "includes\\theme_custom_ajax_handler.php";
		$theme_files[] = "includes\\theme_woocommerce.php";			
		foreach ($theme_files as $index => $theme_file) {
			$theme_files[$index] = self::correct_platform_slashes($theme_file);
		}
		
		asort($theme_files);
		
		return $theme_files;
	}
	
	static function correct_platform_slashes($path) {
		return str_replace("\\", DIRECTORY_SEPARATOR, $path);
	}	
	
	public static function render_missing_css_files( $template_path ) {
		
		$found_files = self::scan_template_files( $template_path, 'css' );
		$default_files = self::list_theme_css_files();
		
		$diff = array_diff($default_files, $found_files);
		return self::render_unversioned_or_missing_files_html($diff);
	}	
	
	public static function render_unversioned_css_files( $template_path ) {
		
		$found_files = self::scan_template_files( $template_path, 'css' );
		$default_files = self::list_theme_css_files();
		
		$diff = array_diff($found_files, $default_files);
		return self::render_unversioned_or_missing_files_html($diff);
	}
	
	public static function render_missing_js_files( $template_path ) {
		
		$found_files = self::scan_template_files( $template_path, 'js' );
		$default_files = self::list_theme_js_files();
		
		$diff = array_diff($default_files, $found_files);
		return self::render_unversioned_or_missing_files_html($diff);
	}	
	
	public static function render_unversioned_js_files( $template_path ) {
		
		$found_files = self::scan_template_files( $template_path, 'js' );
		$default_files = self::list_theme_js_files();
		
		$diff = array_diff($found_files, $default_files);
		return self::render_unversioned_or_missing_files_html($diff);
	}

	public static function render_missing_php_files( $template_path ) {
		
		$found_files = self::scan_template_files( $template_path, 'php' );
		$default_files = self::list_theme_php_files();
		
		$diff = array_diff($default_files, $found_files);
		return self::render_unversioned_or_missing_files_html($diff);
	}		
	
	public static function render_unversioned_php_files( $template_path ) {
		
		$found_files = self::scan_template_files( $template_path, 'php' );
		$default_files = self::list_theme_php_files();
		
		$diff = array_diff($found_files, $default_files);
		return self::render_unversioned_or_missing_files_html($diff);
	}
	
	private static function render_unversioned_or_missing_files_html($files) {
		$unversioned_files_html = '';
		if (count($files) > 0) {
			$unversioned_files_html = '<p class="notok">';
			foreach($files as $unversioned_file) {
				$unversioned_files_html .= "<span>" . $unversioned_file . '</span>';
			}
			$unversioned_files_html .= '</p>';
		}
		return $unversioned_files_html;	
	}
	
	private static function find_difference($array1, $array2) {
		return array_merge(array_diff($array1, $array2), array_diff($array2, $array1));
	}

	/**
	 * from WooCommerce /includes/admin/class-wc-admin-status.php
	 * Scan the template files.
	 * @param  string $template_path
	 * @return array
	 */
	public static function scan_template_files( $template_path, $extension ) {
		
		$files  = @scandir( $template_path );
		$result = array();
		if ( ! empty( $files ) ) {
			foreach ( $files as $key => $value ) {
				if ( ! in_array( $value, array( ".", ".." ) ) ) {
					if ( is_dir( $template_path . DIRECTORY_SEPARATOR . $value ) ) {
						$sub_files = self::scan_template_files( $template_path . DIRECTORY_SEPARATOR . $value, $extension );
						foreach ( $sub_files as $sub_file ) {
							$file_parts = pathinfo($sub_file);
							if (isset($file_parts['extension']) && $file_parts['extension'] == $extension) {
								$result[] = $value . DIRECTORY_SEPARATOR . $sub_file;
							}
						}
					} else {
						$file_parts = pathinfo($value);
						if (isset($file_parts['extension']) && $file_parts['extension'] == $extension) {
							$result[] = $value;
						}
					}
				}
			}
		}
		
		return $result;
	}
}