<?php
/**
 * BookYourTravel_Theme_Globals class
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BookYourTravel_Theme_Globals extends BookYourTravel_BaseSingleton {

	protected function __construct() {
        // our parent class might
        // contain shared code in its constructor
        parent::__construct();
    }

    public function init() {
	
    }

	public function get_current_language_code() {
		return substr(get_locale(), 0, 2);
	}
	
	public function is_translatable($post_type) {
		$is_translatable = false;						
		global $sitepress;
		if ($sitepress && function_exists('is_post_type_translated')) {
			$is_translatable = is_post_type_translated($post_type);
		}
		return $is_translatable;
	}	
		
	function get_calendar_month_rows() {
		$number_of_month_rows = intval(of_get_option('calendar_month_rows', 2));
		return $number_of_month_rows > 0 ? $number_of_month_rows : 2;
	}
	
	function get_calendar_month_cols() {
		$calendar_month_cols = intval(of_get_option('calendar_month_cols', 2));
		return $calendar_month_cols > 0 ? $calendar_month_cols : 2;
	}	
		
	public function get_site_url() {
		return site_url();
	}	
	
	public function get_hide_breadcrumbs() {	
		$breadcrumbs_hidden = get_theme_mod('hide_breadcrumbs', '0');
		return empty($breadcrumbs_hidden) || $breadcrumbs_hidden == '0' ? false : true;		
	}
	
	public function enable_inquiry_recaptcha() {
		return (int)of_get_option('enable_inquiry_recaptcha', 0);
	}	
	
	public function use_custom_ajax_handler() {
		return (int)of_get_option('use_custom_ajax_handler', 0);
	}

	public function get_inquiry_form_thank_you() {
		return of_get_option('inquiry_form_thank_you', __('Thank you for submitting an inquiry. We will get back to you as soon as we can.', 'bookyourtravel'));
	}
	
	public function get_booking_form_thank_you() {
		return of_get_option('booking_form_thank_you', __('Thank you! We will get back you with regards your booking within 24 hours.', 'bookyourtravel'));
	}
	
	public function get_inquiry_form_fields() {
		
		global $default_inquiry_form_fields;
		$inquiry_form_fields = of_get_option('inquiry_form_fields');
		if (!is_array($inquiry_form_fields) || count($inquiry_form_fields) == 0) {
			$inquiry_form_fields = $default_inquiry_form_fields;
		}
			
		return $inquiry_form_fields;
	}	
	
	public function get_booking_form_fields() {
		
		global $default_booking_form_fields;
		
		$booking_form_fields = of_get_option('booking_form_fields');
		if (!is_array($booking_form_fields) || count($booking_form_fields) == 0) {
			$booking_form_fields = $default_booking_form_fields;
		}
			
		return $booking_form_fields;
	}
	
	public function get_taxonomy_pages_sidebar_position() {
		return of_get_option('taxonomy_pages_sidebar_position', 'left');
	}
	
	public function get_taxonomy_pages_sort_by_field() {
		return of_get_option('taxonomy_pages_sort_by_field', 'title');
	}

	public function taxonomy_pages_sort_descending() {
		return (int)of_get_option('taxonomy_pages_sort_descending', 0);
	}
	
	public function get_taxonomy_pages_items_per_page() {
		return of_get_option('taxonomy_pages_items_per_page', 12);
	}

	public function get_taxonomy_pages_items_per_row() {
		return of_get_option('taxonomy_pages_items_per_row', 3);
	}

	public function publish_frontend_submissions_immediately() {
		return (int)of_get_option('publish_frontend_submissions_immediately', 0);
	}	
	
	public function taxonomy_pages_hide_item_titles() {
		return (int)of_get_option('taxonomy_pages_hide_item_titles', 0);
	}

	public function taxonomy_pages_hide_item_descriptions() {
		return (int)of_get_option('taxonomy_pages_hide_item_descriptions', 0);
	}

	public function taxonomy_pages_hide_item_actions() {
		return (int)of_get_option('taxonomy_pages_hide_item_actions', 0);
	}

	public function taxonomy_pages_hide_item_images() {
		return (int)of_get_option('taxonomy_pages_hide_item_images', 0);
	}

	public function taxonomy_pages_hide_item_prices() {
		return (int)of_get_option('taxonomy_pages_hide_item_prices', 0);
	}

	public function taxonomy_pages_hide_item_address() {
		return (int)of_get_option('taxonomy_pages_hide_item_address', 0);
	}

	public function taxonomy_pages_hide_item_ratings() {
		return (int)of_get_option('taxonomy_pages_hide_item_ratings', 0);
	}

	public function taxonomy_pages_hide_item_stars() {
		return (int)of_get_option('taxonomy_pages_hide_item_stars', 0);
	}

	public function get_cart_page_url() {
	
		$cart_page_url = '';
		if (function_exists('wc_get_page_id') && BookYourTravel_Theme_Utils::is_woocommerce_active()) {
			$cart_page_id = wc_get_page_id( 'cart' );
			$cart_page_id = BookYourTravel_Theme_Utils::get_current_language_page_id($cart_page_id);
			$cart_page_url = get_permalink($cart_page_id);
		}
	
		return $cart_page_url;
	}	
	
	public function completed_order_woocommerce_statuses_contains($check_status) {
		$completed_statuses = of_get_option('completed_order_woocommerce_statuses', '');		
		$contains = false;
		
		if (is_array($completed_statuses) && count($completed_statuses) > 0) {
			foreach ($completed_statuses as $status => $state) {
				if ($state == '1') {
					if ($status == $check_status) {
						$contains = true;
						break;
					}
				}
			}
		}				
		
		return $contains;
	}
	
	public function get_completed_order_woocommerce_statuses() {
		
		$completed_statuses = of_get_option('completed_order_woocommerce_statuses', '');
		
		$completed_statuses_str = '';
		
		if (is_array($completed_statuses) && count($completed_statuses) > 0) {
			foreach ($completed_statuses as $status => $state) {
				if ($state == '1') {
					if ($status == 'initiated') {
						$completed_statuses_str .= "''";
					} else {
						$completed_statuses_str .= "'" . $status . "',";
					}
				}
			}
		}
		
		$completed_statuses_str = rtrim($completed_statuses_str, ",");
		
		return $completed_statuses_str;		
	}	
	
	public function use_woocommerce_for_checkout() {

		$use_woocommerce_for_checkout = of_get_option('use_woocommerce_for_checkout', 0);
		$use_woocommerce_for_checkout = BookYourTravel_Theme_Utils::is_woocommerce_active() && $use_woocommerce_for_checkout ? 1 : 0;
		return $use_woocommerce_for_checkout;
	}
	
	public function get_woocommerce_pages_sidebar_position() {
		return of_get_option('woocommerce_pages_sidebar_position', null);
	}
	
	public function get_woocommerce_product_placeholder_image() {
		$product_placeholder_image = of_get_option( 'woocommerce_product_placeholder_image', '' );
		return $product_placeholder_image;
	}	
	
	public function disable_star_count($post_type) {
		return (int)of_get_option('disable_star_count_' . $post_type . 's', 0);
	}

	public function are_car_rentals_available_per_location_only() {
		return (int)of_get_option('car_rentals_available_per_location_only', 0);
	}
	
	public function enable_extra_items() {
		return (int)of_get_option('enable_extra_items', 1);
	}
	
	public function hide_loading_animation() {
		return (int)of_get_option('hide_loading_animation', 0);
	}
		
	public function get_tour_extra_fields() {
		
		global $default_tour_extra_fields;
		$tour_extra_fields = of_get_option('tour_extra_fields');
		if (!is_array($tour_extra_fields) || count($tour_extra_fields) == 0)
			$tour_extra_fields = $default_tour_extra_fields;
			
		return $tour_extra_fields;
	}
	
	public function get_cruise_extra_fields() {
		
		global $default_cruise_extra_fields;
		$cruise_extra_fields = of_get_option('cruise_extra_fields');
		if (!is_array($cruise_extra_fields) || count($cruise_extra_fields) == 0)
			$cruise_extra_fields = $default_cruise_extra_fields;
			
		return $cruise_extra_fields;
	}
	
	public function get_location_extra_fields() {
			
		global $default_location_extra_fields;
		$location_extra_fields = of_get_option('location_extra_fields');
		if (!is_array($location_extra_fields) || count($location_extra_fields) == 0)
			$location_extra_fields = $default_location_extra_fields;
	
		return $location_extra_fields;
	}
	
	public function get_location_tabs() {
	
		global $default_location_tabs;
		$location_tabs = of_get_option('location_tabs');
		if (!is_array($location_tabs) || count($location_tabs) == 0 || count($location_tabs) < count($default_location_tabs))
			$location_tabs = $default_location_tabs;
		return $location_tabs;
		
	}	
	
	public function get_locations_permalink_slug() {
		return of_get_option('locations_permalink_slug', 'location');
	}
	
	public function get_home_page_id() {

		$pages_array = get_pages(array(
			'meta_key' => '_wp_page_template',
			'meta_value' => 'byt_home.php',
			'sort_column' => 'menu_order'			
		));
		
		$page = null;
		if (count($pages_array) > 0) {
			$page = $pages_array[0];
		}
		
		return isset($page) ? $page->ID : 0;
	}	

	public function get_search_results_page_id() {

		$pages_array = get_pages(array(
			'meta_key' => '_wp_page_template',
			'meta_value' => 'page-custom-search-results.php',
			'sort_column' => 'menu_order'			
		));
		
		$page = null;
		if (count($pages_array) > 0) {
			$page = $pages_array[0];
		}
		
		return isset($page) ? $page->ID : 0;
	}

	public function get_location_list_page_id() {

		$pages_array = get_pages(array(
			'meta_key' => '_wp_page_template',
			'meta_value' => 'page-location-list.php',
			'sort_column' => 'menu_order'			
		));
		
		$page = null;
		if (count($pages_array) > 0) {
			$page = $pages_array[0];
		}
		
		return isset($page) ? $page->ID : 0;
	}
	
	public function get_car_rental_list_page_id() {

		$pages_array = get_pages(array(
			'meta_key' => '_wp_page_template',
			'meta_value' => 'page-car_rental-list.php',
			'sort_column' => 'menu_order'
		));

		$page = null;
		if (count($pages_array) > 0) {
			$page = $pages_array[0];
		}
		
		return isset($page) ? $page->ID : 0;
	}	
	
	public function get_cruise_list_page_id() {

		$pages_array = get_pages(array(
			'meta_key' => '_wp_page_template',
			'meta_value' => 'page-cruise-list.php',
			'sort_column' => 'menu_order'
		));

		$page = null;
		if (count($pages_array) > 0) {
			$page = $pages_array[0];
		}
		
		return isset($page) ? $page->ID : 0;
	}	
	
	public function get_tour_list_page_id() {

		$pages_array = get_pages(array(
			'meta_key' => '_wp_page_template',
			'meta_value' => 'page-tour-list.php',
			'sort_column' => 'menu_order'
		));

		$page = null;
		if (count($pages_array) > 0) {
			$page = $pages_array[0];
		}
		
		return isset($page) ? $page->ID : 0;
	}	

	public function get_accommodation_list_page_id() {

		$pages_array = get_pages(array(
			'meta_key' => '_wp_page_template',
			'meta_value' => 'page-accommodation-list.php',
			'sort_column' => 'menu_order'
		));

		$page = null;
		if (count($pages_array) > 0) {
			$page = $pages_array[0];
		}
		
		return isset($page) ? $page->ID : 0;
	}

	public function get_accommodations_permalink_slug() {
		return of_get_option('accommodations_permalink_slug', 'hotel');
	}
	
	public function get_car_rentals_permalink_slug() {
		return of_get_option('car_rentals_permalink_slug', 'car-rental');
	}
	
	public function get_cruises_permalink_slug() {
		return of_get_option('cruises_permalink_slug', 'cruise');
	}
	
	public function get_tours_permalink_slug() {
		return of_get_option('tours_permalink_slug', 'tour');
	}	
	
	public function get_tour_tabs() {
	
		global $default_tour_tabs;
		$tour_tabs = of_get_option('tour_tabs');
		if (!is_array($tour_tabs) || count($tour_tabs) == 0 || count($tour_tabs) < count($default_tour_tabs))
			$tour_tabs = $default_tour_tabs;
		return $tour_tabs;
		
	}
	
	public function get_cruise_tabs() {
	
		global $default_cruise_tabs;
		$cruise_tabs = of_get_option('cruise_tabs');
		if (!is_array($cruise_tabs) || count($cruise_tabs) == 0 || count($cruise_tabs) < count($default_cruise_tabs))
			$cruise_tabs = $default_cruise_tabs;
		return $cruise_tabs;
		
	}
	
	public function get_car_rental_tabs() {
	
		global $default_car_rental_tabs;
		$car_rental_tabs = of_get_option('car_rental_tabs');
		if (!is_array($car_rental_tabs) || count($car_rental_tabs) == 0 || count($car_rental_tabs) < count($default_car_rental_tabs))
			$car_rental_tabs = $default_car_rental_tabs;
		return $car_rental_tabs;
		
	}
	
	public function get_review_form_thank_you() {
		return of_get_option('review_form_thank_you', __('Thank you for submitting your review.', 'bookyourtravel'));
	}	

	public function get_tour_review_fields() {
		
		global $default_tour_review_fields;
		$tour_review_fields = of_get_option('tour_review_fields');
		if (!is_array($tour_review_fields) || count($tour_review_fields) == 0)
			$tour_review_fields = $default_tour_review_fields;
			
		return $tour_review_fields;
	}
	
	public function get_cruise_review_fields() {
		
		global $default_cruise_review_fields;
		$cruise_review_fields = of_get_option('cruise_review_fields');
		if (!is_array($cruise_review_fields) || count($cruise_review_fields) == 0)
			$cruise_review_fields = $default_cruise_review_fields;
			
		return $cruise_review_fields;
	}
	
	public function get_car_rental_review_fields() {
		
		global $default_car_rental_review_fields;
		$car_rental_review_fields = of_get_option('car_rental_review_fields');
		if (!is_array($car_rental_review_fields) || count($car_rental_review_fields) == 0)
			$car_rental_review_fields = $default_car_rental_review_fields;
			
		return $car_rental_review_fields;
	}
	
	public function get_car_rental_extra_fields() {
		
		global $default_car_rental_extra_fields;
		$car_rental_extra_fields = of_get_option('car_rental_extra_fields');
		if (!is_array($car_rental_extra_fields) || count($car_rental_extra_fields) == 0)
			$car_rental_extra_fields = $default_car_rental_extra_fields;
			
		return $car_rental_extra_fields;
	}	
	
	public function get_accommodation_tabs() {
	
		global $default_accommodation_tabs;
		$accommodation_tabs = of_get_option('accommodation_tabs');
		if (!is_array($accommodation_tabs) || count($accommodation_tabs) == 0 || count($accommodation_tabs) < count($default_accommodation_tabs))
			$accommodation_tabs = $default_accommodation_tabs;
		return $accommodation_tabs;
		
	}
	
	public function get_accommodation_extra_fields() {
		
		global $default_accommodation_extra_fields;
		$accommodation_extra_fields = of_get_option('accommodation_extra_fields');
		if (!is_array($accommodation_extra_fields) || count($accommodation_extra_fields) == 0)
			$accommodation_extra_fields = $default_accommodation_extra_fields;
			
		return $accommodation_extra_fields;
	}
	
	public function get_iconic_features_icon_classes() {
		$default_classes = "3d_rotation\nac_unit\naccess_alarm\naccess_alarms\naccess_time\naccessibility\naccessible\naccount_balance\naccount_balance_wallet\naccount_box\naccount_circle\nadb\nadd\nadd_a_photo\nadd_alarm\nadd_alert\nadd_box\nadd_circle\nadd_circle_outline\nadd_location\nadd_shopping_cart\nadd_to_photos\nadd_to_queue\nadjust\nairline_seat_flat\nairline_seat_flat_angled\nairline_seat_individual_suite\nairline_seat_legroom_extra\nairline_seat_legroom_normal\nairline_seat_legroom_reduced\nairline_seat_recline_extra\nairline_seat_recline_normal\nairplanemode_active\nairplanemode_inactive\nairplay\nairport_shuttle\nalarm\nalarm_add\nalarm_off\nalarm_on\nalbum\nall_inclusive\nall_out\nandroid\nannouncement\napps\narchive\narrow_back\narrow_downward\narrow_drop_down\narrow_drop_down_circle\narrow_drop_up\narrow_forward\narrow_upward\nart_track\naspect_ratio\nassessment\nassignment\nassignment_ind\nassignment_late\nassignment_return\nassignment_returned\nassignment_turned_in\nassistant\nassistant_photo\nattach_file\nattach_money\nattachment\naudiotrack\nautorenew\nav_timer\nbackspace\nbackup\nbattery_alert\nbattery_charging_full\nbattery_full\nbattery_std\nbattery_unknown\nbeach_access\nbeenhere\nblock\nbluetooth\nbluetooth_audio\nbluetooth_connected\nbluetooth_disabled\nbluetooth_searching\nblur_circular\nblur_linear\nblur_off\nblur_on\nbook\nbookmark\nbookmark_border\nborder_all\nborder_bottom\nborder_clear\nborder_color\nborder_horizontal\nborder_inner\nborder_left\nborder_outer\nborder_right\nborder_style\nborder_top\nborder_vertical\nbranding_watermark\nbrightness_1\nbrightness_2\nbrightness_3\nbrightness_4\nbrightness_5\nbrightness_6\nbrightness_7\nbrightness_auto\nbrightness_high\nbrightness_low\nbrightness_medium\nbroken_image\nbrush\nbubble_chart\nbug_report\nbuild\nburst_mode\nbusiness\nbusiness_center\ncached\ncake\ncall\ncall_end\ncall_made\ncall_merge\ncall_missed\ncall_missed_outgoing\ncall_received\ncall_split\ncall_to_action\ncamera\ncamera_alt\ncamera_enhance\ncamera_front\ncamera_rear\ncamera_roll\ncancel\ncard_giftcard\ncard_membership\ncard_travel\ncasino\ncast\ncast_connected\ncenter_focus_strong\ncenter_focus_weak\nchange_history\nchat\nchat_bubble\nchat_bubble_outline\ncheck\ncheck_box\ncheck_box_outline_blank\ncheck_circle\nchevron_left\nchevron_right\nchild_care\nchild_friendly\nchrome_reader_mode\nclass\nclear\nclear_all\nclose\nclosed_caption\ncloud\ncloud_circle\ncloud_done\ncloud_download\ncloud_off\ncloud_queue\ncloud_upload\ncode\ncollections\ncollections_bookmark\ncolor_lens\ncolorize\ncomment\ncompare\ncompare_arrows\ncomputer\nconfirmation_number\ncontact_mail\ncontact_phone\ncontacts\ncontent_copy\ncontent_cut\ncontent_paste\ncontrol_point\ncontrol_point_duplicate\ncopyright\ncreate\ncreate_new_folder\ncredit_card\ncrop\ncrop_16_9\ncrop_3_2\ncrop_5_4\ncrop_7_5\ncrop_din\ncrop_free\ncrop_landscape\ncrop_original\ncrop_portrait\ncrop_rotate\ncrop_square\ndashboard\ndata_usage\ndate_range\ndehaze\ndelete\ndelete_forever\ndelete_sweep\ndescription\ndesktop_mac\ndesktop_windows\ndetails\ndeveloper_board\ndeveloper_mode\ndevice_hub\ndevices\ndevices_other\ndialer_sip\ndialpad\ndirections\ndirections_bike\ndirections_boat\ndirections_bus\ndirections_car\ndirections_railway\ndirections_run\ndirections_subway\ndirections_transit\ndirections_walk\ndisc_full\ndns\ndo_not_disturb\ndo_not_disturb_alt\ndo_not_disturb_off\ndo_not_disturb_on\ndock\ndomain\ndone\ndone_all\ndonut_large\ndonut_small\ndrafts\ndrag_handle\ndrive_eta\ndvr\nedit\nedit_location\neject\nemail\nenhanced_encryption\nequalizer\nerror\nerror_outline\neuro_symbol\nev_station\nevent\nevent_available\nevent_busy\nevent_note\nevent_seat\nexit_to_app\nexpand_less\nexpand_more\nexplicit\nexplore\nexposure\nexposure_neg_1\nexposure_neg_2\nexposure_plus_1\nexposure_plus_2\nexposure_zero\nextension\nface\nfast_forward\nfast_rewind\nfavorite\nfavorite_border\nfeatured_play_list\nfeatured_video\nfeedback\nfiber_dvr\nfiber_manual_record\nfiber_new\nfiber_pin\nfiber_smart_record\nfile_download\nfile_upload\nfilter\nfilter_1\nfilter_2\nfilter_3\nfilter_4\nfilter_5\nfilter_6\nfilter_7\nfilter_8\nfilter_9\nfilter_9_plus\nfilter_b_and_w\nfilter_center_focus\nfilter_drama\nfilter_frames\nfilter_hdr\nfilter_list\nfilter_none\nfilter_tilt_shift\nfilter_vintage\nfind_in_page\nfind_replace\nfingerprint\nfirst_page\nfitness_center\nflag\nflare\nflash_auto\nflash_off\nflash_on\nflight\nflight_land\nflight_takeoff\nflip\nflip_to_back\nflip_to_front\nfolder\nfolder_open\nfolder_shared\nfolder_special\nfont_download\nformat_align_center\nformat_align_justify\nformat_align_left\nformat_align_right\nformat_bold\nformat_clear\nformat_color_fill\nformat_color_reset\nformat_color_text\nformat_indent_decrease\nformat_indent_increase\nformat_italic\nformat_line_spacing\nformat_list_bulleted\nformat_list_numbered\nformat_paint\nformat_quote\nformat_shapes\nformat_size\nformat_strikethrough\nformat_textdirection_l_to_r\nformat_textdirection_r_to_l\nformat_underlined\nforum\nforward\nforward_10\nforward_30\nforward_5\nfree_breakfast\nfullscreen\nfullscreen_exit\nfunctions\ng_translate\ngamepad\ngames\ngavel\ngesture\nget_app\ngif\ngolf_course\ngps_fixed\ngps_not_fixed\ngps_off\ngrade\ngradient\ngrain\ngraphic_eq\ngrid_off\ngrid_on\ngroup\ngroup_add\ngroup_work\nhd\nhdr_off\nhdr_on\nhdr_strong\nhdr_weak\nheadset\nheadset_mic\nhealing\nhearing\nhelp\nhelp_outline\nhigh_quality\nhighlight\nhighlight_off\nhistory\nhome\nhot_tub\nhotel\nhourglass_empty\nhourglass_full\nhttp\nhttps\nimage\nimage_aspect_ratio\nimport_contacts\nimport_export\nimportant_devices\ninbox\nindeterminate_check_box\ninfo\ninfo_outline\ninput\ninsert_chart\ninsert_comment\ninsert_drive_file\ninsert_emoticon\ninsert_invitation\ninsert_link\ninsert_photo\ninvert_colors\ninvert_colors_off\niso\nkeyboard\nkeyboard_arrow_down\nkeyboard_arrow_left\nkeyboard_arrow_right\nkeyboard_arrow_up\nkeyboard_backspace\nkeyboard_capslock\nkeyboard_hide\nkeyboard_return\nkeyboard_tab\nkeyboard_voice\nkitchen\nlabel\nlabel_outline\nlandscape\nlanguage\nlaptop\nlaptop_chromebook\nlaptop_mac\nlaptop_windows\nlast_page\nlaunch\nlayers\nlayers_clear\nleak_add\nleak_remove\nlens\nlibrary_add\nlibrary_books\nlibrary_music\nlightbulb_outline\nline_style\nline_weight\nlinear_scale\nlink\nlinked_camera\nlist\nlive_help\nlive_tv\nlocal_activity\nlocal_airport\nlocal_atm\nlocal_bar\nlocal_cafe\nlocal_car_wash\nlocal_convenience_store\nlocal_dining\nlocal_drink\nlocal_florist\nlocal_gas_station\nlocal_grocery_store\nlocal_hospital\nlocal_hotel\nlocal_laundry_service\nlocal_library\nlocal_mall\nlocal_movies\nlocal_offer\nlocal_parking\nlocal_pharmacy\nlocal_phone\nlocal_pizza\nlocal_play\nlocal_post_office\nlocal_printshop\nlocal_see\nlocal_shipping\nlocal_taxi\nlocation_city\nlocation_disabled\nlocation_off\nlocation_on\nlocation_searching\nlock\nlock_open\nlock_outline\nlooks\nlooks_3\nlooks_4\nlooks_5\nlooks_6\nlooks_one\nlooks_two\nloop\nloupe\nlow_priority\nloyalty\nmail\nmail_outline\nmap\nmarkunread\nmarkunread_mailbox\nmemory\nmenu\nmerge_type\nmessage\nmic\nmic_none\nmic_off\nmms\nmode_comment\nmode_edit\nmonetization_on\nmoney_off\nmonochrome_photos\nmood\nmood_bad\nmore\nmore_horiz\nmore_vert\nmotorcycle\nmouse\nmove_to_inbox\nmovie\nmovie_creation\nmovie_filter\nmultiline_chart\nmusic_note\nmusic_video\nmy_location\nnature\nnature_people\nnavigate_before\nnavigate_next\nnavigation\nnear_me\nnetwork_cell\nnetwork_check\nnetwork_locked\nnetwork_wifi\nnew_releases\nnext_week\nnfc\nno_encryption\nno_sim\nnot_interested\nnote\nnote_add\nnotifications\nnotifications_active\nnotifications_none\nnotifications_off\nnotifications_paused\noffline_pin\nondemand_video\nopacity\nopen_in_browser\nopen_in_new\nopen_with\npages\npageview\npalette\npan_tool\npanorama\npanorama_fish_eye\npanorama_horizontal\npanorama_vertical\npanorama_wide_angle\nparty_mode\npause\npause_circle_filled\npause_circle_outline\npayment\npeople\npeople_outline\nperm_camera_mic\nperm_contact_calendar\nperm_data_setting\nperm_device_information\nperm_identity\nperm_media\nperm_phone_msg\nperm_scan_wifi\nperson\nperson_add\nperson_outline\nperson_pin\nperson_pin_circle\npersonal_video\npets\nphone\nphone_android\nphone_bluetooth_speaker\nphone_forwarded\nphone_in_talk\nphone_iphone\nphone_locked\nphone_missed\nphone_paused\nphonelink\nphonelink_erase\nphonelink_lock\nphonelink_off\nphonelink_ring\nphonelink_setup\nphoto\nphoto_album\nphoto_camera\nphoto_filter\nphoto_library\nphoto_size_select_actual\nphoto_size_select_large\nphoto_size_select_small\npicture_as_pdf\npicture_in_picture\npicture_in_picture_alt\npie_chart\npie_chart_outlined\npin_drop\nplace\nplay_arrow\nplay_circle_filled\nplay_circle_outline\nplay_for_work\nplaylist_add\nplaylist_add_check\nplaylist_play\nplus_one\npoll\npolymer\npool\nportable_wifi_off\nportrait\npower\npower_input\npower_settings_new\npregnant_woman\npresent_to_all\nprint\npriority_high\npublic\npublish\nquery_builder\nquestion_answer\nqueue\nqueue_music\nqueue_play_next\nradio\nradio_button_checked\nradio_button_unchecked\nrate_review\nreceipt\nrecent_actors\nrecord_voice_over\nredeem\nredo\nrefresh\nremove\nremove_circle\nremove_circle_outline\nremove_from_queue\nremove_red_eye\nremove_shopping_cart\nreorder\nrepeat\nrepeat_one\nreplay\nreplay_10\nreplay_30\nreplay_5\nreply\nreply_all\nreport\nreport_problem\nrestaurant\nrestaurant_menu\nrestore\nrestore_page\nring_volume\nroom\nroom_service\nrotate_90_degrees_ccw\nrotate_left\nrotate_right\nrounded_corner\nrouter\nrowing\nrss_feed\nrv_hookup\nsatellite\nsave\nscanner\nschedule\nschool\nscreen_lock_landscape\nscreen_lock_portrait\nscreen_lock_rotation\nscreen_rotation\nscreen_share\nsd_card\nsd_storage\nsearch\nsecurity\nselect_all\nsend\nsentiment_dissatisfied\nsentiment_neutral\nsentiment_satisfied\nsentiment_very_dissatisfied\nsentiment_very_satisfied\nsettings\nsettings_applications\nsettings_backup_restore\nsettings_bluetooth\nsettings_brightness\nsettings_cell\nsettings_ethernet\nsettings_input_antenna\nsettings_input_component\nsettings_input_composite\nsettings_input_hdmi\nsettings_input_svideo\nsettings_overscan\nsettings_phone\nsettings_power\nsettings_remote\nsettings_system_daydream\nsettings_voice\nshare\nshop\nshop_two\nshopping_basket\nshopping_cart\nshort_text\nshow_chart\nshuffle\nsignal_cellular_4_bar\nsignal_cellular_connected_no_internet_4_bar\nsignal_cellular_no_sim\nsignal_cellular_null\nsignal_cellular_off\nsignal_wifi_4_bar\nsignal_wifi_4_bar_lock\nsignal_wifi_off\nsim_card\nsim_card_alert\nskip_next\nskip_previous\nslideshow\nslow_motion_video\nsmartphone\nsmoke_free\nsmoking_rooms\nsms\nsms_failed\nsnooze\nsort\nsort_by_alpha\nspa\nspace_bar\nspeaker\nspeaker_group\nspeaker_notes\nspeaker_notes_off\nspeaker_phone\nspellcheck\nstar\nstar_border\nstar_half\nstars\nstay_current_landscape\nstay_current_portrait\nstay_primary_landscape\nstay_primary_portrait\nstop\nstop_screen_share\nstorage\nstore\nstore_mall_directory\nstraighten\nstreetview\nstrikethrough_s\nstyle\nsubdirectory_arrow_left\nsubdirectory_arrow_right\nsubject\nsubscriptions\nsubtitles\nsubway\nsupervisor_account\nsurround_sound\nswap_calls\nswap_horiz\nswap_vert\nswap_vertical_circle\nswitch_camera\nswitch_video\nsync\nsync_disabled\nsync_problem\nsystem_update\nsystem_update_alt\ntab\ntab_unselected\ntablet\ntablet_android\ntablet_mac\ntag_faces\ntap_and_play\nterrain\ntext_fields\ntext_format\ntextsms\ntexture\ntheaters\nthumb_down\nthumb_up\nthumbs_up_down\ntime_to_leave\ntimelapse\ntimeline\ntimer\ntimer_10\ntimer_3\ntimer_off\ntitle\ntoc\ntoday\ntoll\ntonality\ntouch_app\ntoys\ntrack_changes\ntraffic\ntrain\ntram\ntransfer_within_a_station\ntransform\ntranslate\ntrending_down\ntrending_flat\ntrending_up\ntune\nturned_in\nturned_in_not\ntv\nunarchive\nundo\nunfold_less\nunfold_more\nupdate\nusb\nverified_user\nvertical_align_bottom\nvertical_align_center\nvertical_align_top\nvibration\nvideo_call\nvideo_label\nvideo_library\nvideocam\nvideocam_off\nvideogame_asset\nview_agenda\nview_array\nview_carousel\nview_column\nview_comfy\nview_compact\nview_day\nview_headline\nview_list\nview_module\nview_quilt\nview_stream\nview_week\nvignette\nvisibility\nvisibility_off\nvoice_chat\nvoicemail\nvolume_down\nvolume_mute\nvolume_off\nvolume_up\nvpn_key\nvpn_lock\nwallpaper\nwarning\nwatch\nwatch_later\nwb_auto\nwb_cloudy\nwb_incandescent\nwb_iridescent\nwb_sunny\nwc\nweb\nweb_asset\nweekend\nwhatshot\nwidgets\nwifi\nwifi_lock\nwifi_tethering\nwork\nwrap_text\nyoutube_searched_for\nzoom_in\nzoom_out\nzoom_out_map";
		$saved_classes = of_get_option('iconic_features_widget_classes', $default_classes);
		return ($saved_classes != $default_classes ? $saved_classes . $default_classes : $saved_classes);
	}

	function hide_required_plugins_notice() {
		return intval(of_get_option('hide_required_plugins_notice', 0));
	}
	
	function get_sidebar_number_of_columns($sidebar_id) {
		$number_of_columns = 4;
		
		switch ($sidebar_id) {
			case 'under-header': $number_of_columns = $this->get_sidebar_under_header_number_of_columns(); break;
			case 'hero': $number_of_columns = $this->get_sidebar_hero_number_of_columns(); break;
			case 'left': $number_of_columns = $this->get_sidebar_left_number_of_columns(); break;
			case 'right': $number_of_columns = $this->get_sidebar_right_number_of_columns(); break;
			case 'right-accommodation': $number_of_columns = $this->get_sidebar_right_accommodation_number_of_columns(); break;
			case 'right-tour': $number_of_columns = $this->get_sidebar_right_tour_number_of_columns(); break;
			case 'right-cruise': $number_of_columns = $this->get_sidebar_right_cruise_number_of_columns(); break;
			case 'right-car_rental': $number_of_columns = $this->get_sidebar_right_car_rental_number_of_columns(); break;
			case 'footer': $number_of_columns = $this->get_sidebar_footer_number_of_columns(); break;
			case 'above-footer': $number_of_columns = $this->get_sidebar_above_footer_number_of_columns(); break;
			case 'home-content': $number_of_columns = $this->get_sidebar_home_content_number_of_columns(); break;
			case 'home-footer': $number_of_columns = $this->get_sidebar_home_footer_number_of_columns(); break;
			default: $number_of_columns = 4;break;
		}
		
		return $number_of_columns;
	}
	
	function get_sidebar_hero_number_of_columns() {
		return get_theme_mod('sidebar_hero_number_of_columns', 1);	
	}

	function get_sidebar_under_header_number_of_columns() {
		return get_theme_mod('sidebar_under_header_number_of_columns', 4);
	}

	function get_sidebar_left_number_of_columns() {
		return get_theme_mod('sidebar_left_number_of_columns', 1);
	}

	function get_sidebar_right_number_of_columns() {
		return get_theme_mod('sidebar_right_number_of_columns', 1);
	}

	function get_sidebar_right_accommodation_number_of_columns() {
		return get_theme_mod('sidebar_right_accommodation_number_of_columns', 1);
	}

	function get_sidebar_right_tour_number_of_columns() {
		return get_theme_mod('sidebar_right_tour_number_of_columns', 1);
	}

	function get_sidebar_right_cruise_number_of_columns() {
		return get_theme_mod('sidebar_right_cruise_number_of_columns', 1);
	}

	function get_sidebar_right_car_rental_number_of_columns() {
		return get_theme_mod('sidebar_right_car_rental_number_of_columns', 1);
	}

	function get_sidebar_footer_number_of_columns() {
		return get_theme_mod('sidebar_footer_number_of_columns', 4);
	}

	function get_sidebar_above_footer_number_of_columns() {
		return get_theme_mod('sidebar_above_footer_number_of_columns', 1);
	}

	function get_sidebar_home_content_number_of_columns() {
		return get_theme_mod('sidebar_home_content_number_of_columns', 1);
	}

	function get_sidebar_home_footer_number_of_columns() {
		return get_theme_mod('sidebar_home_footer_number_of_columns', 1);
	}

	public function get_base_font() {	
		$base_font = get_theme_mod('base_font', 'Open+Sans');
		return empty($base_font) ? 'Open+Sans' : $base_font;		
	}

	public function get_heading_font() {	
		$heading_font = get_theme_mod('heading_font', 'Roboto+Slab');
		return empty($heading_font) ? 'Roboto+Slab' : $heading_font;		
	}
	
	public function check_single_layout($nav_layout) {
		if ($nav_layout != 'left' && $nav_layout != 'right' && $nav_layout != 'above')
			return 'left';
		return $nav_layout;
	}	
	
	public function get_location_single_layout() {	
		$nav_layout = get_theme_mod('byt_location_single_layout', 'left');
		$nav_layout = $this->check_single_layout($nav_layout);
		return empty($nav_layout) || $nav_layout == 'left' ? 'left' : $nav_layout;		
	}

	public function get_accommodation_single_layout() {	
		$nav_layout = get_theme_mod('byt_accommodation_single_layout', 'left');
		$nav_layout = $this->check_single_layout($nav_layout);
		return $nav_layout;
	}

	public function get_tour_single_layout() {	
		$nav_layout = get_theme_mod('byt_tour_single_layout', 'left');
		$nav_layout = $this->check_single_layout($nav_layout);
		return $nav_layout;
	}

	public function get_car_rental_single_layout() {	
		$nav_layout = get_theme_mod('byt_car_rental_single_layout', 'left');
		$nav_layout = $this->check_single_layout($nav_layout);
		return $nav_layout;
	}
	
	public function get_cruise_single_layout() {	
		$nav_layout = get_theme_mod('byt_cruise_single_layout', 'left');
		$nav_layout = $this->check_single_layout($nav_layout);
		return $nav_layout;
	}	
	
	public function get_website_layout() {	
		$website_layout = get_theme_mod('website_layout', 'wide');
		return empty($website_layout) || $website_layout == 'wide' ? 'wide' : $website_layout;		
	}
	
	public function check_single_sidebar_position($sidebar_position) {
		if ($sidebar_position != 'left' && $sidebar_position != 'right' && $sidebar_position != 'both' && $sidebar_position != 'none')
			return 'right';
		return $sidebar_position;
	}		
	
	public function get_location_single_sidebar_position() {	
		$sidebar_position = get_theme_mod('byt_location_single_sidebar_position', 'right');
		$sidebar_position = $this->check_single_sidebar_position($sidebar_position);
		return $sidebar_position;
	}
	
	public function get_accommodation_single_sidebar_position() {	
		$sidebar_position = get_theme_mod('byt_accommodation_single_sidebar_position', 'right');
		$sidebar_position = $this->check_single_sidebar_position($sidebar_position);
		return $sidebar_position;
	}

	public function get_tour_single_sidebar_position() {	
		$sidebar_position = get_theme_mod('byt_tour_single_sidebar_position', 'right');
		$sidebar_position = $this->check_single_sidebar_position($sidebar_position);
		return $sidebar_position;
	}	
	
	public function get_cruise_single_sidebar_position() {	
		$sidebar_position = get_theme_mod('byt_cruise_single_sidebar_position', 'right');
		$sidebar_position = $this->check_single_sidebar_position($sidebar_position);
		return $sidebar_position;
	}	
	
	public function get_car_rental_single_sidebar_position() {	
		$sidebar_position = get_theme_mod('byt_car_rental_single_sidebar_position', 'right');
		$sidebar_position = $this->check_single_sidebar_position($sidebar_position);
		return $sidebar_position;
	}
	
	public function get_header_layout() {	
		$header_layout = get_theme_mod('header_layout', 'header1');
		return empty($header_layout) || $header_layout == 'default' ? 'header1' : $header_layout;		
	}
	
	public function get_header_sticky() {	
		$header_sticky = get_theme_mod('header_sticky', '0');
		return empty($header_sticky) || $header_sticky == '0' ? false : true;		
	}

	public function get_disable_theme_header() {	
		$header_disable_theme_header = get_theme_mod('header_disable_theme_header', '0');
		return empty($header_disable_theme_header) || $header_disable_theme_header == '0' ? false : true;		
	}

	public function get_disable_theme_footer() {	
		$footer_disable_theme_footer = get_theme_mod('footer_disable_theme_footer', '0');
		return empty($footer_disable_theme_footer) || $footer_disable_theme_footer == '0' ? false : true;		
	}
	
	public function get_header_overlay() {	
		$header_overlay = get_theme_mod('header_overlay', '0');
		return empty($header_overlay) || $header_overlay == '0' ? false : true;		
	}
	
	public function get_home_header_transparent() {	
		$home_header_transparent = get_theme_mod('home_header_transparent', '0');
		return empty($home_header_transparent) || $home_header_transparent == '0' ? false : true;		
	}	
	
	public function get_header_minicart() {	
		$header_minicart = get_theme_mod('header_minicart', '0');
		return empty($header_minicart) || $header_minicart == '0' ? false : true;		
	}	
	
	public function get_copyright_footer() {
	
		$copy = get_theme_mod('footer_copyright_text', '');
		if (!empty($copy)) 
			return $copy;
	
		return of_get_option('copyright_footer', '&copy; 2013 - 2023 ThemeEnergy.com');
	}
	
	public function get_color_scheme_style_sheet() {
		return of_get_option('color_scheme_select', '');
	}
	
	public function get_theme_logo_src() {
	
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		$image = wp_get_attachment_image_src( $custom_logo_id , 'full' );
		$logo_src = is_array($image) && count($image) > 0 ? $image[0] : '';

		if (empty($logo_src)) {
			$logo_src = BookYourTravel_Theme_Utils::get_file_uri('/images/txt/logo.png');
		}
		
		return $logo_src;
	}
	
	public function get_header_contact_message() {
		return get_theme_mod('header_contact_message', __('24/7 Support number', 'bookyourtravel'));
	}
	
	public function get_contact_phone_number() {
		$number = get_theme_mod('header_contact_number', '');
		if (!empty($number)) 
			return $number;
			
		return of_get_option('contact_phone_number', '1-555-555-555');
	}
	
	public function get_contact_address_street() {
		return of_get_option('contact_address_street', '');
	}
	
	public function get_contact_address_city() {
		return of_get_option('contact_address_city', '');
	}
	
	public function get_contact_address_country() {
		return of_get_option('contact_address_country', '');
	}
	
	public function get_contact_company_name() {
		return of_get_option('contact_company_name', '');
	}
	
	public function get_business_address_latitude() {
		return of_get_option('business_address_latitude', '');
	}
	
	public function get_business_address_longitude() {
		return of_get_option('business_address_longitude', '');
	}	
	
	public function get_contact_email() {
		return of_get_option('contact_email', '');
	}

	public function enable_deposit_payments() {
		return $this->use_woocommerce_for_checkout() && intval(of_get_option('enable_deposit_payments', 0));
	}
	
	public function enable_rtl() {
		return intval(of_get_option('enable_rtl', 0));
	}
	
	public function enable_gdpr() {
		return intval(of_get_option('enable_gdpr', 0));
	}
	
	public function get_basic_gdpr_agreement_text() {
		return of_get_option('basic_gdpr_agreement_text', "I have read the <a href='/privacy-policy'>privacy policy</a> and I agree with <a href='/terms-and-conditions'>terms and conditions</a>.");
	}
	
	public function get_terms_page_url() {
		global $terms_page_url;
		
		if (isset($terms_page_url) && !empty($terms_page_url))
			return $terms_page_url;

		$terms_page_url = '';
		$terms_page_url_id = BookYourTravel_Theme_Utils::get_current_language_page_id(of_get_option('terms_page_url', ''));

		if ($terms_page_url_id > 0)
			$terms_page_url = get_permalink($terms_page_url_id);

		return $terms_page_url;
	}
	
	public function permalinks_enabled() {
		
		$structure = get_option('permalink_structure');
		
		return !empty($structure);		
	}	
	
	public function get_price_decimal_places() {
		return (int)of_get_option('price_decimal_places', 0);
	}

	public function get_user_currency() {
		global $woocommerce_wpml, $WOOCS;

		if (isset($woocommerce_wpml) && isset($woocommerce_wpml->multi_currency) && method_exists($woocommerce_wpml->multi_currency, 'get_client_currency')) {
			return $woocommerce_wpml->multi_currency->get_client_currency();
		} else if (isset($WOOCS)) {
			return $WOOCS->current_currency;
		}

		$user_currency = of_get_option('default_currency_symbol', '$');
		$user_currency = apply_filters('bookyourtravel_get_default_currenty', $user_currency);

		return $user_currency;
	}

	public function get_default_currency() {
		global $woocommerce_wpml, $WOOCS;

		if (isset($woocommerce_wpml) && isset($woocommerce_wpml->multi_currency) && method_exists($woocommerce_wpml->multi_currency, 'get_client_currency')) {
			return $woocommerce_wpml->multi_currency->get_default_currency();
		} else if (isset($WOOCS)) {
			return $WOOCS->default_currency;
		}

		$currency = of_get_option('default_currency_symbol', '$');
		$currency = apply_filters('bookyourtravel_get_default_currenty', $currency);

		return $currency;
	}
	
	public function get_default_currency_symbol() {
		global $woocommerce_wpml, $WOOCS, $woocommerce;

		if (isset($woocommerce)) {
			if (isset($woocommerce_wpml) && isset($woocommerce_wpml->multi_currency) && method_exists($woocommerce_wpml->multi_currency, 'get_client_currency')) {
				return get_woocommerce_currency_symbol($woocommerce_wpml->multi_currency->get_client_currency());
			} else if (isset($WOOCS)) {
				return get_woocommerce_currency_symbol($WOOCS->current_currency);
			}
		}

		return of_get_option('default_currency_symbol', '$');
	}

	public function show_static_prices_in_grids() {
		return of_get_option('show_static_prices_in_grids', 0);
	}
	
	public function show_currency_symbol_after() {
		global $woocommerce_wpml;

		if (isset($woocommerce_wpml) && isset($woocommerce_wpml->multi_currency) && method_exists($woocommerce_wpml->multi_currency, 'get_client_currency')) {
			$currency_code = $woocommerce_wpml->multi_currency->get_client_currency();
			if (isset($woocommerce_wpml->multi_currency->currencies) && isset($woocommerce_wpml->multi_currency->currencies[$currency_code]) && isset($woocommerce_wpml->multi_currency->currencies[$currency_code]['position'])) {
				$currency_position = $woocommerce_wpml->multi_currency->currencies[$currency_code]['position'];
				if ($currency_position == 'right' || $currency_position == 'right_space') {
					return 1;
				} else {
					return 0;
				}
			}
		}

		return (int)of_get_option('show_currency_symbol_after', 0);
	}
	
	public function get_light_slider_pause_between_slides() {
		return (int)of_get_option('light_slider_pause_between_slides', 3);
	}
	
	public function show_counts_in_location_items() {
		return of_get_option('show_counts_in_location_items', array());
	}

	public function show_prices_in_location_items() {
		return of_get_option('show_prices_in_location_items', array());
	}

	private function get_ids_from_sql_results($results, $id_key) {
		$ids = array();

		foreach ($results as $result) {
			$ids[] = $result->$id_key;
		}

		return $ids;
	}

	private function get_special_page_urls($template_name, $key) {

		global $wpdb;

		$sql = "SELECT * FROM $wpdb->postmeta postmeta INNER JOIN $wpdb->posts posts ON postmeta.post_id = posts.ID WHERE meta_key = '_wp_page_template' AND meta_value = %s AND posts.post_type='page' ";
		$sql = $wpdb->prepare($sql, $template_name);

		$template_postmeta_results = $wpdb->get_results($sql);
		$template_page_ids = $this->get_ids_from_sql_results($template_postmeta_results, 'post_id');

		$pages_array = array();
		$pages_array['user'] = '';
		$pages_array['partner'] = '';

		foreach ($template_page_ids as $page_id) {
			$sql = "SELECT * FROM $wpdb->postmeta WHERE meta_key = %s AND post_id = %d ";
			$sql = $wpdb->prepare($sql, $key, $page_id);

			$template_key_value_results = $wpdb->get_results($sql);

			$page_url = '';

			if (count($template_key_value_results) > 0) {
				foreach ($template_key_value_results as $result) {
					$page_url_id = $result->post_id;
					$page_url_id = BookYourTravel_Theme_Utils::get_current_language_page_id($page_url_id);
					$page_url = get_permalink($page_url_id);

					if ($result->meta_value == '1') {
						$pages_array['partner'] = $page_url;
					} else {
						$pages_array['user'] = $page_url;
					}
				}
			} else {
				$page_url_id = $page_id;
				$page_url_id = BookYourTravel_Theme_Utils::get_current_language_page_id($page_url_id);
				$page_url = get_permalink($page_id);				
				$pages_array['user'] = $page_url;
			}
		}

		return $pages_array;
	}

	private function get_special_page_ids($template_name, $key) {

		global $wpdb;

		$sql = "SELECT * FROM $wpdb->postmeta WHERE meta_key = '_wp_page_template' AND meta_value = %s ";
		$sql = $wpdb->prepare($sql, $template_name);

		$template_postmeta_results = $wpdb->get_results($sql);
		$template_page_ids = $this->get_ids_from_sql_results($template_postmeta_results, 'post_id');

		$pages_array = array();
		$pages_array['user'] = '';
		$pages_array['partner'] = '';

		foreach ($template_page_ids as $page_id) {
			$sql = "SELECT * FROM $wpdb->postmeta WHERE meta_key = %s AND post_id = %d ";
			$sql = $wpdb->prepare($sql, $key, $page_id);

			$template_key_value_results = $wpdb->get_results($sql);

			if (count($template_key_value_results) > 0) {
				foreach ($template_key_value_results as $result) {
					$page_url_id = $result->post_id;
					$page_url_id = BookYourTravel_Theme_Utils::get_current_language_page_id($page_url_id);

					if ($result->meta_value == '1') {
						$pages_array['partner'] = $page_url_id;
					} else {
						$pages_array['user'] = $page_url_id;
					}
				}
			} else {
				$page_url_id = $page_id;
				$page_url_id = BookYourTravel_Theme_Utils::get_current_language_page_id($page_url_id);
				$pages_array['user'] = $page_url_id;
			}
		}

		return $pages_array;
	}

	private function get_page_url_by_template_and_key_value($template_name, $key, $value) {
		
		global $wpdb;

		$sql = "SELECT * FROM $wpdb->postmeta WHERE meta_key = '_wp_page_template' AND meta_value = %s ";
		$sql = $wpdb->prepare($sql, $template_name);
		
		$template_postmeta_results = $wpdb->get_results($sql);
		$template_page_ids = $this->get_ids_from_sql_results($template_postmeta_results, 'post_id');

		$sql = "";
		$key_page_ids = array();

		foreach ($template_page_ids as $page_id) {
			$temp_key_page_ids = array();
			$sql = "SELECT * FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %s AND post_id = %d ";
			$sql = $wpdb->prepare($sql, $key, $value, $page_id);
			$template_key_value_results = $wpdb->get_results($sql);
			$temp_key_page_ids = $this->get_ids_from_sql_results($template_key_value_results, 'post_id');
			$key_page_ids = array_merge($key_page_ids, $temp_key_page_ids);
		}
		
		$page_url_id = 0;
		$page_url = '';

		if (count($key_page_ids) == 0) {
			if (count($template_page_ids) > 0) {
				$page_url_id = $template_page_ids[0];
				$page_url_id = BookYourTravel_Theme_Utils::get_current_language_page_id($page_url_id);
				$page_url = get_permalink($page_url_id);
			}
		} else {
			$found_id = 0;
			foreach ($key_page_ids as $key_id) {
				if (in_array($key_id, $template_page_ids)) {
					$found_id = $key_id;
					break;
				}
			}

			if ($found_id > 0) {
				$page_url_id = $found_id;
				$page_url_id = BookYourTravel_Theme_Utils::get_current_language_page_id($page_url_id);
				$page_url = get_permalink($page_url_id);
			}
		}
		
		return $page_url;
	}
	
	private function get_page_url_by_template($template_name) {
	
		global $wpdb;
		
		$sql = "SELECT * FROM $wpdb->postmeta WHERE meta_key = '_wp_page_template' AND meta_value = %s ";
		$sql = $wpdb->prepare($sql, $template_name);
		
		$postmeta_results = $wpdb->get_results($sql);
		
		$page_url_id = 0;
		$page_url = '';
		
		if (count($postmeta_results)) {
			$page_url_id = $postmeta_results[0]->post_id;
			$page_url_id = BookYourTravel_Theme_Utils::get_current_language_page_id($page_url_id);
			$page_url = get_permalink($page_url_id);
		}

		return $page_url;
	}
	
	private function get_page_title($template_name) {
	
		$sql = "SELECT * FROM $wpdb->postmeta WHERE key = '_wp_page_template' AND value = %s ";
		$sql = $wpdb->prepare($sql, $template_name);
		
		$postmeta_results = $wpdb->get_results($sql);
		
		$page_url_id = 0;
		$page_title = '';
		
		if (count($postmeta_results)) {
			$page_url_id = $postmeta_results[0]->post_id;
			$page_url_id = BookYourTravel_Theme_Utils::get_current_language_page_id($page_url_id);
			$page_title = get_title($page_url_id);
		}

		return $page_title;
	}
	
	
	public function get_price_range_bottom() {
		return intval(of_get_option('price_range_bottom', '0'));		
	}
	
	public function get_price_range_increment() {
		$price_range_increment = intval(of_get_option('price_range_increment', '50'));
		return $price_range_increment > 0 ? $price_range_increment : 50;
	}

	public function get_price_range_count() {
		$price_range_count = intval(of_get_option('price_range_count', '5'));
		return $price_range_count > 0 ? $price_range_count : 5;		
	}
	
	public function search_only_available_properties() {
		return of_get_option('search_only_available_properties', '0');
	}
	
	public function get_search_results_default_view() {
		return (int)of_get_option('search_results_default_view', 0);
	}	
	
	public function get_custom_search_results_page_url() {
	
		global $custom_search_results_page_url;
		
		if (isset($custom_search_results_page_url) && !empty($custom_search_results_page_url))
			return $custom_search_results_page_url;
	
		$custom_search_results_page_url = $this->get_page_url_by_template('page-custom-search-results.php');

		return $custom_search_results_page_url;	
	}
	
	public function get_contact_page_url() {
	
		global $contact_page_url;
		
		if (isset($contact_page_url) && !empty($contact_page_url))
			return $contact_page_url;
	
		$contact_page_url = $this->get_page_url_by_template('page-contact.php');

		return $contact_page_url;
	}
	
	public function get_list_frontend_submit_content_type_url($content_type) {
		global ${'user_list_' . $content_type . '_url'};
		
		if (isset(${'user_list_' . $content_type . '_url'}) && !empty(${'user_list_' . $content_type . '_url'}))
			return ${'user_list_' . $content_type . '_url'};	
	
		${'user_list_' . $content_type . '_url'} = $this->get_page_url_by_template_and_key_value('page-user-content-list.php', 'user_content_type', $content_type);

		return ${'user_list_' . $content_type . '_url'};		
	}
	
	public function get_submit_frontend_submit_content_type_url($content_type) {
		global ${'user_submit_' . $content_type . '_url'};
		
		if (isset(${'user_submit_' . $content_type . '_url'}) && !empty(${'user_submit_' . $content_type . '_url'}))
			return ${'user_submit_' . $content_type . '_url'};	
	
		${'user_submit_' . $content_type . '_url'} = $this->get_page_url_by_template_and_key_value('page-user-submit-content.php', 'frontend_submit_content_type', $content_type);

		return ${'user_submit_' . $content_type . '_url'};		
	}
	
	public function get_list_user_accommodations_url() {
		return $this->get_list_frontend_submit_content_type_url('accommodation');	
	}
	
	public function get_submit_user_accommodations_url() {
		return $this->get_submit_frontend_submit_content_type_url('accommodation');
	}
	
	public function get_list_user_room_types_url() {
		return $this->get_list_frontend_submit_content_type_url('room_type');	
	}
	
	public function get_submit_user_room_types_url() {
		return $this->get_submit_frontend_submit_content_type_url('room_type');
	}
	
	public function get_list_user_cabin_types_url() {
		return $this->get_list_frontend_submit_content_type_url('cabin_type');	
	}
	
	public function get_submit_user_cabin_types_url() {
		return $this->get_submit_frontend_submit_content_type_url('cabin_type');
	}	

	public function get_list_user_accommodation_vacancies_url() {
		return $this->get_list_frontend_submit_content_type_url('accommodation_vacancy');
	}
	
	public function get_submit_user_accommodation_vacancies_url() {
		return $this->get_submit_frontend_submit_content_type_url('accommodation_vacancy');
	}	
	
	public function get_list_user_accommodation_bookings_url() {
		return $this->get_list_frontend_submit_content_type_url('accommodation_booking');
	}
	
	public function get_submit_user_accommodation_bookings_url() {
		return $this->get_submit_frontend_submit_content_type_url('accommodation_booking');
	}	
	
	public function get_list_user_tours_url() {
		return $this->get_list_frontend_submit_content_type_url('tour');
	}
	
	public function get_submit_user_tours_url() {
		return $this->get_submit_frontend_submit_content_type_url('tour');
	}
	
	public function get_list_user_tour_schedules_url() {
		return $this->get_list_frontend_submit_content_type_url('tour_schedule');
	}
	
	public function get_submit_user_tour_schedules_url() {
		return $this->get_submit_frontend_submit_content_type_url('tour_schedule');
	}

	public function get_list_user_tour_bookings_url() {
		return $this->get_list_frontend_submit_content_type_url('tour_booking');
	}
	
	public function get_submit_user_tour_bookings_url() {
		return $this->get_submit_frontend_submit_content_type_url('tour_booking');
	}
	
	public function get_list_user_cruises_url() {
		return $this->get_list_frontend_submit_content_type_url('cruise');
	}
	
	public function get_submit_user_cruises_url() {
		return $this->get_submit_frontend_submit_content_type_url('cruise');
	}
	
	public function get_list_user_cruise_schedules_url() {
		return $this->get_list_frontend_submit_content_type_url('cruise_schedule');
	}
	
	public function get_submit_user_cruise_schedules_url() {
		return $this->get_submit_frontend_submit_content_type_url('cruise_schedule');
	}
	
	public function get_list_user_cruise_bookings_url() {
		return $this->get_list_frontend_submit_content_type_url('cruise_booking');
	}
	
	public function get_submit_user_cruise_bookings_url() {
		return $this->get_submit_frontend_submit_content_type_url('cruise_booking');
	}
	
	public function get_list_user_car_rentals_url() {
		return $this->get_list_frontend_submit_content_type_url('car_rental');
	}
	
	public function get_submit_user_car_rentals_url() {
		return $this->get_submit_frontend_submit_content_type_url('car_rental');
	}
	
	public function get_list_user_car_rental_bookings_url() {
		return $this->get_list_frontend_submit_content_type_url('car_rental_booking');
	}
	
	public function get_submit_user_car_rental_bookings_url() {
		return $this->get_submit_frontend_submit_content_type_url('car_rental_booking');
	}
	
	public function get_list_user_car_rental_availabilities_url() {
		return $this->get_list_frontend_submit_content_type_url('car_rental_availability');
	}
	
	public function get_submit_user_car_rental_availabilities_url() {
		return $this->get_submit_frontend_submit_content_type_url('car_rental_availability');
	}

	public function get_list_user_locations_url() {
		return $this->get_list_frontend_submit_content_type_url('location');
	}
	
	public function get_submit_user_locations_url() {
		return $this->get_submit_frontend_submit_content_type_url('location');
	}
	
	public function get_my_account_page_url() {

		global $my_account_page_url;
		
		if (isset($my_account_page_url) && !empty($my_account_page_url))
			return $my_account_page_url;
	
		$my_account_page_urls = $this->get_special_page_urls('page-user-account.php', 'user_account_is_partner_page');

		return $my_account_page_urls['user'];
	}

	public function get_partner_account_page_url() {
		global $partner_account_page_url;
		
		if (isset($partner_account_page_url) && !empty($partner_account_page_url))
			return $partner_account_page_url;
		
		$partner_account_page_urls = $this->get_special_page_urls('page-user-account.php', 'user_account_is_partner_page');

		$url = $partner_account_page_urls['partner'];
		if (empty($url)) {
			$url = $partner_account_page_urls['user'];
		}

		return $url;
	}	
	
	public function get_login_page_url() {

		global $login_page_url;
		
		if (isset($login_page_url) && !empty($login_page_url))
			return $login_page_url;	
	
		$login_page_urls = $this->get_special_page_urls('page-user-login.php', 'user_login_can_frontend_submit');
		
		$url = $login_page_urls['user'];

		return $url;
	}

	public function get_partner_login_page_url() {

		global $partner_login_page_url;
		
		if (isset($partner_login_page_url) && !empty($partner_login_page_url))
			return $partner_login_page_url;
		
		$partner_login_page_urls = $this->get_special_page_urls('page-user-login.php', 'user_login_can_frontend_submit');
		
		$url = $partner_login_page_urls['partner'];
		if (empty($url)) {
			$url = $partner_login_page_urls['user'];
		}

		return $url;
	}

	public function get_register_page_url() {

		global $register_page_url;
		
		if (isset($register_page_url) && !empty($register_page_url))
			return $register_page_url;	
	
		$register_page_urls = $this->get_special_page_urls('page-user-register.php', 'user_register_can_frontend_submit');
		
		$url = $register_page_urls['user'];

		if (empty($url))
			$url = home_url('/') . 'wp-login.php?action=register';

		return $url;
	}

	public function get_register_page_id() {
	
		$register_page_ids = $this->get_special_page_ids('page-user-register.php', 'user_register_can_frontend_submit');
		
		$page_id = isset($register_page_ids['user']) ? intval($register_page_ids['user']) : 0;

		return $page_id;
	}

	public function get_login_page_id() {
	
		$register_page_ids = $this->get_special_page_ids('page-user-login.php', 'user_login_can_frontend_submit');
		
		$page_id = isset($register_page_ids['user']) ? intval($register_page_ids['user']) : 0;

		return $page_id;
	}

	public function get_partner_register_page_url() {

		global $partner_register_page_url;
		
		if (isset($partner_register_page_url) && !empty($partner_register_page_url))
			return $partner_register_page_url;
		
		$partner_register_page_urls = $this->get_special_page_urls('page-user-register.php', 'user_register_can_frontend_submit');

		$url = $partner_register_page_urls['partner'];
		if (empty($url)) {
			$url = $partner_register_page_urls['user'];
		}

		if (empty($url))
			$url = home_url('/') . 'wp-login.php?action=register';

		return $url;
	}	

	public function get_reset_password_page_url() {

		global $reset_password_page_url;
		
		if (isset($reset_password_page_url) && !empty($reset_password_page_url))
			return $reset_password_page_url;	

		$reset_password_page_urls = $this->get_special_page_urls('page-user-forgot-pass.php', 'user_forgot_password_can_frontend_submit');

		$url = $reset_password_page_urls['user'];

		if (empty($url))
			$url = home_url('/') . 'wp-login.php?action=lostpassword';

		return $url;
	}

	public function get_partner_reset_password_page_url() {

		global $partner_reset_password_page_url;
		
		if (isset($partner_reset_password_page_url) && !empty($partner_reset_password_page_url))
			return $partner_reset_password_page_url;
		
		$partner_reset_password_page_urls = $this->get_special_page_urls('page-user-forgot-pass.php', 'user_forgot_password_can_frontend_submit');
		
		$partner_reset_password_page_url = $partner_reset_password_page_urls['partner'];

		if (empty($partner_reset_password_page_url))
			$partner_reset_password_page_url = home_url('/') . 'wp-login.php?action=lostpassword';

		return $partner_reset_password_page_url;
	}

	public function enable_accommodations() {
		return intval(of_get_option('enable_accommodations', 1));
	}
	
	public function enable_reviews() {
		return intval(of_get_option('enable_reviews', 1));
	}
	
	public function enable_tours() {
		return intval(of_get_option('enable_tours', 1));
	}
	
	public function enable_cruises() {
		return intval(of_get_option('enable_cruises', 1));
	}
	
	public function enable_car_rentals() {
		return intval(of_get_option('enable_car_rentals', 1));
	}
	
	public function get_google_recaptcha_key() {
		return of_get_option('google_recaptcha_key', '');
	}
	
	public function get_google_recaptcha_secret() {
		return of_get_option('google_recaptcha_secret', '');
	}
	
	public function get_google_maps_key() {
		return of_get_option('google_maps_key', '');
	}
	
	public function is_recaptcha_usable() {
		$key = $this->get_google_recaptcha_key();
		$secret = $this->get_google_recaptcha_secret();
		return (!empty($key) && !empty($secret));
	}
	
	public function frontpage_show_slider() {
		return of_get_option('frontpage_show_slider', '1');
	}
	
	public function get_homepage_slider() {
        return of_get_option('homepage_slider', '-1');
    }

    public function is_inside_elementor_editor() {
        return class_exists('\Elementor\Plugin') && (\Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode());
    }

    public function is_inline_vc_editor() {
        return function_exists('vc_is_inline') && vc_is_inline();
    }
}

global $bookyourtravel_theme_globals;
// store the instance in a variable to be retrieved later and call init
$bookyourtravel_theme_globals = BookYourTravel_Theme_Globals::get_instance();