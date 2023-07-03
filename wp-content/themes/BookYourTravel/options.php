<?php
/**
 * options.php
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 *
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 * If you are making your theme translatable, you should replace 'bookyourtravel'
 * with the actual text domain for your theme.  Read more:
 * http://codex.wordpress.org/Function_Reference/load_theme_textdomain
 */
 
function optionsframework_my_create_page_status_row($label, $page_uri) {
	
	$create_page_uri = get_admin_url(get_current_blog_id(), "post-new.php?post_type=page");
	return sprintf("<tr><td>%s</td><td>%s <a target='_blank' href='%s'>%s</a></td></tr>", 
		$label, 
		(empty($page_uri) ? sprintf("<span class='notok'>%s</span>", esc_html__("Missing", "bookyourtravel")) : sprintf("<span class='allgood'>%s</span>", esc_html__("Ok", "bookyourtravel"))), 
		(empty($page_uri) ? $create_page_uri : $page_uri), 
		(empty($page_uri) ? esc_html__("Create", "bookyourtravel") : esc_html__('View', 'bookyourtravel'))
	);
}
 
function optionsframework_options() {

	global $bookyourtravel_theme_globals;
	
	$search_results_view_array = array(
		'0' => esc_html__('Grid view', 'bookyourtravel'),
		'1' => esc_html__('List view', 'bookyourtravel'),
	);	
	
	$pages = get_pages(); 
	$pages_array = array();
	$pages_array[0] = esc_html__('Select page', 'bookyourtravel');
	foreach ( $pages as $page ) {
		$pages_array[$page->ID] = $page->post_title;
	}

	$page_sidebars = array(
		'' => esc_html__('No sidebar', 'bookyourtravel'),
		'left' => esc_html__('Left sidebar', 'bookyourtravel'),
		'right' => esc_html__('Right sidebar', 'bookyourtravel'),
		'both' => esc_html__('Left and right sidebars', 'bookyourtravel'),
	);
	$page_sidebars = apply_filters('bookyourtravel_options_page_sidebars', $page_sidebars);		
	
	$items_per_row = array(
		'1' => esc_html__('One', 'bookyourtravel'),
		'2' => esc_html__('Two', 'bookyourtravel'),
		'3' => esc_html__('Three', 'bookyourtravel'),
		'4' => esc_html__('Four', 'bookyourtravel'),
		'5' => esc_html__('Five', 'bookyourtravel')
	);
	$items_per_row = apply_filters('bookyourtravel_options_items_per_row', $items_per_row);		

	$items_per_page = array();
	for ($i = 1; $i <= 50; $i++) {
		$items_per_page[$i] = $i;
	}
	$items_per_page = apply_filters('bookyourtravel_options_items_per_page', $items_per_page);			
	
	$calendar_month_ranges = array(
		'1' => esc_html__('One', 'bookyourtravel'),
		'2' => esc_html__('Two', 'bookyourtravel'),
		'3' => esc_html__('Three', 'bookyourtravel'),
		'4' => esc_html__('Four', 'bookyourtravel'),
		'5' => esc_html__('Five', 'bookyourtravel'),
		'6' => esc_html__('Six', 'bookyourtravel')		
	);		
	$calendar_month_ranges = apply_filters('bookyourtravel_options_calendar_month_ranges', $calendar_month_ranges);			

	$pause_seconds_array = array(
		'1' => esc_html__('One second', 'bookyourtravel'),
		'2' => esc_html__('Two seconds', 'bookyourtravel'),
		'3' => esc_html__('Three seconds', 'bookyourtravel'),
		'4' => esc_html__('Four seconds', 'bookyourtravel'),
		'5' => esc_html__('Five seconds', 'bookyourtravel'),
		'6' => esc_html__('Six seconds', 'bookyourtravel'),
		'7' => esc_html__('Seven seconds', 'bookyourtravel'),
		'8' => esc_html__('Eight seconds', 'bookyourtravel'),
		'9' => esc_html__('Nine seconds', 'bookyourtravel'),
		'10' => esc_html__('Ten seconds', 'bookyourtravel'),
	);
	
	$pause_seconds_array = apply_filters('bookyourtravel_options_pause_seconds', $pause_seconds_array);		

	$price_decimals_array = array(
		'0' => esc_html__('Zero (e.g. $200)', 'bookyourtravel'),
		'1' => esc_html__('One  (e.g. $200.0)', 'bookyourtravel'),
		'2' => esc_html__('Two (e.g. $200.00)', 'bookyourtravel'),
	);
	
	$price_decimals_array = apply_filters('bookyourtravel_options_price_decimals', $price_decimals_array);	
	
	$options = array();

	$options[] = array(
		'name' => esc_html__('General Settings', 'bookyourtravel'),
		'type' => 'heading');

	$options[] = array(
		'name' => esc_html__('Company name', 'bookyourtravel'),
		'desc' => esc_html__('Company name displayed on the contact us page.', 'bookyourtravel'),
		'id' => 'contact_company_name',
		'std' => 'Book Your Travel LLC',
		'class' => 'mini',
		'type' => 'text');		
		
	$options[] = array(
		'name' => esc_html__('Contact address street', 'bookyourtravel'),
		'desc' => esc_html__('Contact address street displayed on the contact us page.', 'bookyourtravel'),
		'id' => 'contact_address_street',
		'std' => '1400 Pennsylvania Ave',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Contact address city', 'bookyourtravel'),
		'desc' => esc_html__('Contact address city displayed on the contact us page.', 'bookyourtravel'),
		'id' => 'contact_address_city',
		'std' => 'Washington DC',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Contact address country', 'bookyourtravel'),
		'desc' => esc_html__('Contact address country displayed on the contact us page.', 'bookyourtravel'),
		'id' => 'contact_address_country',
		'std' => 'USA',
		'class' => 'mini',
		'type' => 'text');
	
	$options[] = array(
		'name' => esc_html__('Contact email', 'bookyourtravel'),
		'desc' => esc_html__('Contact email displayed on the contact us page.', 'bookyourtravel'),
		'id' => 'contact_email',
		'std' => 'info at bookyourtravel',
		'class' => 'mini',
		'type' => 'text');		
		
	$options[] = array(
		'name' => esc_html__('Business address latitude', 'bookyourtravel'),
		'desc' => esc_html__('Enter your business address latitude to use for contact form map', 'bookyourtravel'),
		'id' => 'business_address_latitude',
		'std' => '49.47216',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Business address longitude', 'bookyourtravel'),
		'desc' => esc_html__('Enter your business address longitude to use for contact form map', 'bookyourtravel'),
		'id' => 'business_address_longitude',
		'std' => '-123.76307',
		'class' => 'mini',
		'type' => 'text');	
		
	$options[] = array(
		'name' => esc_html__('Configuration Settings', 'bookyourtravel'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => esc_html__('Google maps api key', 'bookyourtravel'),
		'desc' => esc_html__('You must go to https://developers.google.com/maps/documentation/javascript/get-api-key to get a key which enables Google Maps on your website. After you do, enter it in the field below to enable this feature.', 'bookyourtravel'),
		'id' => 'google_maps_key',
		'std' => '',
		'class' => 'mini', //mini, tiny, small
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Google reCaptcha API v2 key', 'bookyourtravel'),
		'desc' => esc_html__('You must go to https://www.google.com/recaptcha/admin to get a key and secret which enables Google reCaptcha for forms on your website. After you do, enter the key in the field below to enable this feature.', 'bookyourtravel'),
		'id' => 'google_recaptcha_key',
		'std' => '',
		'class' => 'mini', //mini, tiny, small
		'type' => 'text');	
		
	$options[] = array(
		'name' => esc_html__('Google reCaptcha API v2 secret', 'bookyourtravel'),
		'desc' => esc_html__('You must go to https://www.google.com/recaptcha/admin to get a key and secret which enables Google reCaptcha for forms on your website. After you do, enter the secret in the field below to enable this feature.', 'bookyourtravel'),
		'id' => 'google_recaptcha_secret',
		'std' => '',
		'class' => 'mini', //mini, tiny, small
		'type' => 'text');
				
	$options[] = array(
		'name' => esc_html__('Pause between slides of single lightSlider gallery', 'bookyourtravel'),
		'desc' => esc_html__('Number of seconds to pause between showing each slide in the lightSlider gallery used on single accommodation, tour, cruise and car rental pages.', 'bookyourtravel'),
		'id' => 'light_slider_pause_between_slides',
		'std' => '3',
		'type' => 'select',
		'class' => 'mini',
		'options' => $pause_seconds_array);

	$options[] = array(
		'name' => esc_html__('Use custom ajax handler to improve price loading speed?', 'bookyourtravel'),
		'desc' => esc_html__('Override the default admin-ajax.php use with wp-content/themes/bookyourtravel/includes/theme_custom_ajax_handler.php which is a slimmer and faster version for ajax handling (note: may require your server to allow ajax calls to this custom file).', 'bookyourtravel'),
		'id' => 'use_custom_ajax_handler',
		'std' => '0',
		'type' => 'checkbox');		

	$options[] = array(
		'name' => esc_html__('Show static "from" prices in grid displays?', 'bookyourtravel'),
		'desc' => esc_html__('A useful alternative for users with limited hosting plans that would rather show fixed rates instead of real-time prices based on created availability in order to increase website speed. Warning: If this option is checked the system will not display real-time prices based on availability.', 'bookyourtravel'),
		'id' => 'show_static_prices_in_grids',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => esc_html__('Price decimal places', 'bookyourtravel'),
		'desc' => esc_html__('Number of decimal places to show for prices', 'bookyourtravel'),
		'id' => 'price_decimal_places',
		'std' => '0',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $price_decimals_array);
		
	$options[] = array(
		'name' => esc_html__('Default currency symbol', 'bookyourtravel'),
		'desc' => esc_html__('What is your default currency symbol', 'bookyourtravel'),
		'id' => 'default_currency_symbol',
		'std' => '$',
		'class' => 'mini', //mini, tiny, small
		'type' => 'text');

	$options[] = array(
		'name' => esc_html__('Show currency symbol after price?', 'bookyourtravel'),
		'desc' => esc_html__('If this option is checked, currency symbol will show up after the price, instead of before (e.g. 150 $ instead of $150).', 'bookyourtravel'),
		'id' => 'show_currency_symbol_after',
		'std' => '0',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Hide loading animation?', 'bookyourtravel'),
		'desc' => esc_html__('If this option is checked, the main loading animation is hidden on all pages.', 'bookyourtravel'),
		'id' => 'hide_loading_animation',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => esc_html__('Enable deposits / partial payments', 'bookyourtravel'),
		'desc' => esc_html__('Enable deposits / partial payments for bookings', 'bookyourtravel'),
		'id' => 'enable_deposit_payments',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => esc_html__('Enable RTL', 'bookyourtravel'),
		'desc' => esc_html__('Enable site-wide right-to-left text support', 'bookyourtravel'),
		'id' => 'enable_rtl',
		'std' => '0',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Enable GDPR fields', 'bookyourtravel'),
		'desc' => esc_html__('Enable GDPR checkboxes for booking, inquiry, review, contact, account forms.', 'bookyourtravel'),
		'id' => 'enable_gdpr',
		'std' => '0',
		'type' => 'checkbox');		
		
	$options[] = array(
		'name' => esc_html__('Basic GDPR agreement text', 'bookyourtravel'),
		'desc' => esc_html__('Basic GDPR agreement text that appears next to checkboxes in the review and contact forms.', 'bookyourtravel'),
		'id' => 'basic_gdpr_agreement_text',
		'std' => "I have read the <a href='/privacy-policy'>privacy policy</a> and I agree with <a href='/terms-and-conditions'>terms and conditions</a>.",
		'class' => 'mini', //mini, tiny, small
		'type' => 'textarea');
		
	$options[] = array(
		'name' => esc_html__('Hide required/recommended plugins notice', 'bookyourtravel'),
		'desc' => esc_html__('Check to hide required/recommended plugins notice', 'bookyourtravel'),
		'id' => 'hide_required_plugins_notice',
		'std' => '0',
		'type' => 'checkbox');
		
	$default_classes = "3d_rotation\nac_unit\naccess_alarm\naccess_alarms\naccess_time\naccessibility\naccessible\naccount_balance\naccount_balance_wallet\naccount_box\naccount_circle\nadb\nadd\nadd_a_photo\nadd_alarm\nadd_alert\nadd_box\nadd_circle\nadd_circle_outline\nadd_location\nadd_shopping_cart\nadd_to_photos\nadd_to_queue\nadjust\nairline_seat_flat\nairline_seat_flat_angled\nairline_seat_individual_suite\nairline_seat_legroom_extra\nairline_seat_legroom_normal\nairline_seat_legroom_reduced\nairline_seat_recline_extra\nairline_seat_recline_normal\nairplanemode_active\nairplanemode_inactive\nairplay\nairport_shuttle\nalarm\nalarm_add\nalarm_off\nalarm_on\nalbum\nall_inclusive\nall_out\nandroid\nannouncement\napps\narchive\narrow_back\narrow_downward\narrow_drop_down\narrow_drop_down_circle\narrow_drop_up\narrow_forward\narrow_upward\nart_track\naspect_ratio\nassessment\nassignment\nassignment_ind\nassignment_late\nassignment_return\nassignment_returned\nassignment_turned_in\nassistant\nassistant_photo\nattach_file\nattach_money\nattachment\naudiotrack\nautorenew\nav_timer\nbackspace\nbackup\nbattery_alert\nbattery_charging_full\nbattery_full\nbattery_std\nbattery_unknown\nbeach_access\nbeenhere\nblock\nbluetooth\nbluetooth_audio\nbluetooth_connected\nbluetooth_disabled\nbluetooth_searching\nblur_circular\nblur_linear\nblur_off\nblur_on\nbook\nbookmark\nbookmark_border\nborder_all\nborder_bottom\nborder_clear\nborder_color\nborder_horizontal\nborder_inner\nborder_left\nborder_outer\nborder_right\nborder_style\nborder_top\nborder_vertical\nbranding_watermark\nbrightness_1\nbrightness_2\nbrightness_3\nbrightness_4\nbrightness_5\nbrightness_6\nbrightness_7\nbrightness_auto\nbrightness_high\nbrightness_low\nbrightness_medium\nbroken_image\nbrush\nbubble_chart\nbug_report\nbuild\nburst_mode\nbusiness\nbusiness_center\ncached\ncake\ncall\ncall_end\ncall_made\ncall_merge\ncall_missed\ncall_missed_outgoing\ncall_received\ncall_split\ncall_to_action\ncamera\ncamera_alt\ncamera_enhance\ncamera_front\ncamera_rear\ncamera_roll\ncancel\ncard_giftcard\ncard_membership\ncard_travel\ncasino\ncast\ncast_connected\ncenter_focus_strong\ncenter_focus_weak\nchange_history\nchat\nchat_bubble\nchat_bubble_outline\ncheck\ncheck_box\ncheck_box_outline_blank\ncheck_circle\nchevron_left\nchevron_right\nchild_care\nchild_friendly\nchrome_reader_mode\nclass\nclear\nclear_all\nclose\nclosed_caption\ncloud\ncloud_circle\ncloud_done\ncloud_download\ncloud_off\ncloud_queue\ncloud_upload\ncode\ncollections\ncollections_bookmark\ncolor_lens\ncolorize\ncomment\ncompare\ncompare_arrows\ncomputer\nconfirmation_number\ncontact_mail\ncontact_phone\ncontacts\ncontent_copy\ncontent_cut\ncontent_paste\ncontrol_point\ncontrol_point_duplicate\ncopyright\ncreate\ncreate_new_folder\ncredit_card\ncrop\ncrop_16_9\ncrop_3_2\ncrop_5_4\ncrop_7_5\ncrop_din\ncrop_free\ncrop_landscape\ncrop_original\ncrop_portrait\ncrop_rotate\ncrop_square\ndashboard\ndata_usage\ndate_range\ndehaze\ndelete\ndelete_forever\ndelete_sweep\ndescription\ndesktop_mac\ndesktop_windows\ndetails\ndeveloper_board\ndeveloper_mode\ndevice_hub\ndevices\ndevices_other\ndialer_sip\ndialpad\ndirections\ndirections_bike\ndirections_boat\ndirections_bus\ndirections_car\ndirections_railway\ndirections_run\ndirections_subway\ndirections_transit\ndirections_walk\ndisc_full\ndns\ndo_not_disturb\ndo_not_disturb_alt\ndo_not_disturb_off\ndo_not_disturb_on\ndock\ndomain\ndone\ndone_all\ndonut_large\ndonut_small\ndrafts\ndrag_handle\ndrive_eta\ndvr\nedit\nedit_location\neject\nemail\nenhanced_encryption\nequalizer\nerror\nerror_outline\neuro_symbol\nev_station\nevent\nevent_available\nevent_busy\nevent_note\nevent_seat\nexit_to_app\nexpand_less\nexpand_more\nexplicit\nexplore\nexposure\nexposure_neg_1\nexposure_neg_2\nexposure_plus_1\nexposure_plus_2\nexposure_zero\nextension\nface\nfast_forward\nfast_rewind\nfavorite\nfavorite_border\nfeatured_play_list\nfeatured_video\nfeedback\nfiber_dvr\nfiber_manual_record\nfiber_new\nfiber_pin\nfiber_smart_record\nfile_download\nfile_upload\nfilter\nfilter_1\nfilter_2\nfilter_3\nfilter_4\nfilter_5\nfilter_6\nfilter_7\nfilter_8\nfilter_9\nfilter_9_plus\nfilter_b_and_w\nfilter_center_focus\nfilter_drama\nfilter_frames\nfilter_hdr\nfilter_list\nfilter_none\nfilter_tilt_shift\nfilter_vintage\nfind_in_page\nfind_replace\nfingerprint\nfirst_page\nfitness_center\nflag\nflare\nflash_auto\nflash_off\nflash_on\nflight\nflight_land\nflight_takeoff\nflip\nflip_to_back\nflip_to_front\nfolder\nfolder_open\nfolder_shared\nfolder_special\nfont_download\nformat_align_center\nformat_align_justify\nformat_align_left\nformat_align_right\nformat_bold\nformat_clear\nformat_color_fill\nformat_color_reset\nformat_color_text\nformat_indent_decrease\nformat_indent_increase\nformat_italic\nformat_line_spacing\nformat_list_bulleted\nformat_list_numbered\nformat_paint\nformat_quote\nformat_shapes\nformat_size\nformat_strikethrough\nformat_textdirection_l_to_r\nformat_textdirection_r_to_l\nformat_underlined\nforum\nforward\nforward_10\nforward_30\nforward_5\nfree_breakfast\nfullscreen\nfullscreen_exit\nfunctions\ng_translate\ngamepad\ngames\ngavel\ngesture\nget_app\ngif\ngolf_course\ngps_fixed\ngps_not_fixed\ngps_off\ngrade\ngradient\ngrain\ngraphic_eq\ngrid_off\ngrid_on\ngroup\ngroup_add\ngroup_work\nhd\nhdr_off\nhdr_on\nhdr_strong\nhdr_weak\nheadset\nheadset_mic\nhealing\nhearing\nhelp\nhelp_outline\nhigh_quality\nhighlight\nhighlight_off\nhistory\nhome\nhot_tub\nhotel\nhourglass_empty\nhourglass_full\nhttp\nhttps\nimage\nimage_aspect_ratio\nimport_contacts\nimport_export\nimportant_devices\ninbox\nindeterminate_check_box\ninfo\ninfo_outline\ninput\ninsert_chart\ninsert_comment\ninsert_drive_file\ninsert_emoticon\ninsert_invitation\ninsert_link\ninsert_photo\ninvert_colors\ninvert_colors_off\niso\nkeyboard\nkeyboard_arrow_down\nkeyboard_arrow_left\nkeyboard_arrow_right\nkeyboard_arrow_up\nkeyboard_backspace\nkeyboard_capslock\nkeyboard_hide\nkeyboard_return\nkeyboard_tab\nkeyboard_voice\nkitchen\nlabel\nlabel_outline\nlandscape\nlanguage\nlaptop\nlaptop_chromebook\nlaptop_mac\nlaptop_windows\nlast_page\nlaunch\nlayers\nlayers_clear\nleak_add\nleak_remove\nlens\nlibrary_add\nlibrary_books\nlibrary_music\nlightbulb_outline\nline_style\nline_weight\nlinear_scale\nlink\nlinked_camera\nlist\nlive_help\nlive_tv\nlocal_activity\nlocal_airport\nlocal_atm\nlocal_bar\nlocal_cafe\nlocal_car_wash\nlocal_convenience_store\nlocal_dining\nlocal_drink\nlocal_florist\nlocal_gas_station\nlocal_grocery_store\nlocal_hospital\nlocal_hotel\nlocal_laundry_service\nlocal_library\nlocal_mall\nlocal_movies\nlocal_offer\nlocal_parking\nlocal_pharmacy\nlocal_phone\nlocal_pizza\nlocal_play\nlocal_post_office\nlocal_printshop\nlocal_see\nlocal_shipping\nlocal_taxi\nlocation_city\nlocation_disabled\nlocation_off\nlocation_on\nlocation_searching\nlock\nlock_open\nlock_outline\nlooks\nlooks_3\nlooks_4\nlooks_5\nlooks_6\nlooks_one\nlooks_two\nloop\nloupe\nlow_priority\nloyalty\nmail\nmail_outline\nmap\nmarkunread\nmarkunread_mailbox\nmemory\nmenu\nmerge_type\nmessage\nmic\nmic_none\nmic_off\nmms\nmode_comment\nmode_edit\nmonetization_on\nmoney_off\nmonochrome_photos\nmood\nmood_bad\nmore\nmore_horiz\nmore_vert\nmotorcycle\nmouse\nmove_to_inbox\nmovie\nmovie_creation\nmovie_filter\nmultiline_chart\nmusic_note\nmusic_video\nmy_location\nnature\nnature_people\nnavigate_before\nnavigate_next\nnavigation\nnear_me\nnetwork_cell\nnetwork_check\nnetwork_locked\nnetwork_wifi\nnew_releases\nnext_week\nnfc\nno_encryption\nno_sim\nnot_interested\nnote\nnote_add\nnotifications\nnotifications_active\nnotifications_none\nnotifications_off\nnotifications_paused\noffline_pin\nondemand_video\nopacity\nopen_in_browser\nopen_in_new\nopen_with\npages\npageview\npalette\npan_tool\npanorama\npanorama_fish_eye\npanorama_horizontal\npanorama_vertical\npanorama_wide_angle\nparty_mode\npause\npause_circle_filled\npause_circle_outline\npayment\npeople\npeople_outline\nperm_camera_mic\nperm_contact_calendar\nperm_data_setting\nperm_device_information\nperm_identity\nperm_media\nperm_phone_msg\nperm_scan_wifi\nperson\nperson_add\nperson_outline\nperson_pin\nperson_pin_circle\npersonal_video\npets\nphone\nphone_android\nphone_bluetooth_speaker\nphone_forwarded\nphone_in_talk\nphone_iphone\nphone_locked\nphone_missed\nphone_paused\nphonelink\nphonelink_erase\nphonelink_lock\nphonelink_off\nphonelink_ring\nphonelink_setup\nphoto\nphoto_album\nphoto_camera\nphoto_filter\nphoto_library\nphoto_size_select_actual\nphoto_size_select_large\nphoto_size_select_small\npicture_as_pdf\npicture_in_picture\npicture_in_picture_alt\npie_chart\npie_chart_outlined\npin_drop\nplace\nplay_arrow\nplay_circle_filled\nplay_circle_outline\nplay_for_work\nplaylist_add\nplaylist_add_check\nplaylist_play\nplus_one\npoll\npolymer\npool\nportable_wifi_off\nportrait\npower\npower_input\npower_settings_new\npregnant_woman\npresent_to_all\nprint\npriority_high\npublic\npublish\nquery_builder\nquestion_answer\nqueue\nqueue_music\nqueue_play_next\nradio\nradio_button_checked\nradio_button_unchecked\nrate_review\nreceipt\nrecent_actors\nrecord_voice_over\nredeem\nredo\nrefresh\nremove\nremove_circle\nremove_circle_outline\nremove_from_queue\nremove_red_eye\nremove_shopping_cart\nreorder\nrepeat\nrepeat_one\nreplay\nreplay_10\nreplay_30\nreplay_5\nreply\nreply_all\nreport\nreport_problem\nrestaurant\nrestaurant_menu\nrestore\nrestore_page\nring_volume\nroom\nroom_service\nrotate_90_degrees_ccw\nrotate_left\nrotate_right\nrounded_corner\nrouter\nrowing\nrss_feed\nrv_hookup\nsatellite\nsave\nscanner\nschedule\nschool\nscreen_lock_landscape\nscreen_lock_portrait\nscreen_lock_rotation\nscreen_rotation\nscreen_share\nsd_card\nsd_storage\nsearch\nsecurity\nselect_all\nsend\nsentiment_dissatisfied\nsentiment_neutral\nsentiment_satisfied\nsentiment_very_dissatisfied\nsentiment_very_satisfied\nsettings\nsettings_applications\nsettings_backup_restore\nsettings_bluetooth\nsettings_brightness\nsettings_cell\nsettings_ethernet\nsettings_input_antenna\nsettings_input_component\nsettings_input_composite\nsettings_input_hdmi\nsettings_input_svideo\nsettings_overscan\nsettings_phone\nsettings_power\nsettings_remote\nsettings_system_daydream\nsettings_voice\nshare\nshop\nshop_two\nshopping_basket\nshopping_cart\nshort_text\nshow_chart\nshuffle\nsignal_cellular_4_bar\nsignal_cellular_connected_no_internet_4_bar\nsignal_cellular_no_sim\nsignal_cellular_null\nsignal_cellular_off\nsignal_wifi_4_bar\nsignal_wifi_4_bar_lock\nsignal_wifi_off\nsim_card\nsim_card_alert\nskip_next\nskip_previous\nslideshow\nslow_motion_video\nsmartphone\nsmoke_free\nsmoking_rooms\nsms\nsms_failed\nsnooze\nsort\nsort_by_alpha\nspa\nspace_bar\nspeaker\nspeaker_group\nspeaker_notes\nspeaker_notes_off\nspeaker_phone\nspellcheck\nstar\nstar_border\nstar_half\nstars\nstay_current_landscape\nstay_current_portrait\nstay_primary_landscape\nstay_primary_portrait\nstop\nstop_screen_share\nstorage\nstore\nstore_mall_directory\nstraighten\nstreetview\nstrikethrough_s\nstyle\nsubdirectory_arrow_left\nsubdirectory_arrow_right\nsubject\nsubscriptions\nsubtitles\nsubway\nsupervisor_account\nsurround_sound\nswap_calls\nswap_horiz\nswap_vert\nswap_vertical_circle\nswitch_camera\nswitch_video\nsync\nsync_disabled\nsync_problem\nsystem_update\nsystem_update_alt\ntab\ntab_unselected\ntablet\ntablet_android\ntablet_mac\ntag_faces\ntap_and_play\nterrain\ntext_fields\ntext_format\ntextsms\ntexture\ntheaters\nthumb_down\nthumb_up\nthumbs_up_down\ntime_to_leave\ntimelapse\ntimeline\ntimer\ntimer_10\ntimer_3\ntimer_off\ntitle\ntoc\ntoday\ntoll\ntonality\ntouch_app\ntoys\ntrack_changes\ntraffic\ntrain\ntram\ntransfer_within_a_station\ntransform\ntranslate\ntrending_down\ntrending_flat\ntrending_up\ntune\nturned_in\nturned_in_not\ntv\nunarchive\nundo\nunfold_less\nunfold_more\nupdate\nusb\nverified_user\nvertical_align_bottom\nvertical_align_center\nvertical_align_top\nvibration\nvideo_call\nvideo_label\nvideo_library\nvideocam\nvideocam_off\nvideogame_asset\nview_agenda\nview_array\nview_carousel\nview_column\nview_comfy\nview_compact\nview_day\nview_headline\nview_list\nview_module\nview_quilt\nview_stream\nview_week\nvignette\nvisibility\nvisibility_off\nvoice_chat\nvoicemail\nvolume_down\nvolume_mute\nvolume_off\nvolume_up\nvpn_key\nvpn_lock\nwallpaper\nwarning\nwatch\nwatch_later\nwb_auto\nwb_cloudy\nwb_incandescent\nwb_iridescent\nwb_sunny\nwc\nweb\nweb_asset\nweekend\nwhatshot\nwidgets\nwifi\nwifi_lock\nwifi_tethering\nwork\nwrap_text\nyoutube_searched_for\nzoom_in\nzoom_out\nzoom_out_map";
	
	$options[] = array(
		'name' => esc_html__('Iconic Features Widget classes', 'bookyourtravel'),
		'desc' => esc_html__('The css classes used for features icons in Iconic Features Widget on home page and in other sidebars', 'bookyourtravel'),
		'id' => 'iconic_features_widget_classes',
		'std' => $default_classes,
		'class' => '', //mini, tiny, small
		'type' => 'textarea');
		
	$options[] = array(
		'name' => esc_html__('Number of rows in the booking calendar', 'bookyourtravel'),
		'desc' => esc_html__('Set the vertical layout of the calendar on the availability page', 'bookyourtravel'),
		'id' => 'calendar_month_rows',
		'std' => '2',
		'type' => 'select',
		'options' => $calendar_month_ranges);		

	$options[] = array(
		'name' => esc_html__('Number of months per row in the booking calendar', 'bookyourtravel'),
		'desc' => esc_html__('Set the horizontal layout of the calendar on the availability page', 'bookyourtravel'),
		'id' => 'calendar_month_cols',
		'std' => '2',
		'type' => 'select',
		'options' => $calendar_month_ranges);
		
	$options[] = array(
		'name' => esc_html__('Search Settings', 'bookyourtravel'),
		'type' => 'heading');		
		
	$options[] = array(
		'name' => esc_html__('Search only available properties', 'bookyourtravel'),
		'desc' => esc_html__('Search displays only properties with valid vacancies/schedules etc', 'bookyourtravel'),
		'id' => 'search_only_available_properties',
		'std' => '1',
		'type' => 'checkbox');	


	$options[] = array(
		'name' => esc_html__('Custom search results default view', 'bookyourtravel'),
		'desc' => esc_html__('Custom search results default view (grid or list view)', 'bookyourtravel'),
		'id' => 'search_results_default_view',
		'std' => '0',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $search_results_view_array);
		
	$options[] = array(
		'name' => esc_html__('Price range bottom', 'bookyourtravel'),
		'desc' => esc_html__('Bottom value of price range used in search form (usually 0)', 'bookyourtravel'),
		'id' => 'price_range_bottom',
		'std' => '0',
		'type' => 'text',
		'class' => 'mini');

	$options[] = array(
		'name' => esc_html__('Price range increment', 'bookyourtravel'),
		'desc' => esc_html__('Increment value of price range used in search form (default 50)', 'bookyourtravel'),
		'id' => 'price_range_increment',
		'std' => '50',
		'type' => 'text',
		'class' => 'mini');

	$options[] = array(
		'name' => esc_html__('Price range increment count', 'bookyourtravel'),
		'desc' => esc_html__('Increment count of price range used in search form (default 5)', 'bookyourtravel'),
		'id' => 'price_range_count',
		'std' => '5',
		'type' => 'text',
		'class' => 'mini');		
		
	$options[] = array(
		'name' => esc_html__('Page Settings', 'bookyourtravel'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => esc_html__('Terms &amp; conditions page url', 'bookyourtravel'),
		'desc' => esc_html__('Terms &amp; conditions page url', 'bookyourtravel'),
		'id' => 'terms_page_url',
		'std' => '',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $pages_array);		

	$options[] = array(
		'name' => esc_html__('Publish frontend submitted content immediately?', 'bookyourtravel'),
		'desc' => esc_html__('When users submit content via frontend, do you publish it immediately or do you leave it for admin to review?', 'bookyourtravel'),
		'id' => 'publish_frontend_submissions_immediately',
		'std' => '0',
		'type' => 'checkbox');		
		
	$sort_by_columns = array(
		'title' => esc_html__('Post title', 'bookyourtravel'),
		'ID' => esc_html__('Post ID', 'bookyourtravel'),
		'date' => esc_html__('Publish date', 'bookyourtravel'),
		'rand' => esc_html__('Random', 'bookyourtravel'),
	);
		
	$options[] = array(
		'name' => esc_html__('Taxonomy archive pages sidebar position', 'bookyourtravel'),
		'desc' => esc_html__('Select the position (if any) of sidebars to appear on all taxonomy archive pages of your website.', 'bookyourtravel'),
		'id' => 'taxonomy_pages_sidebar_position',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $page_sidebars);

	$options[] = array(
		'name' => esc_html__('Taxonomy archive pages items per page', 'bookyourtravel'),
		'desc' => esc_html__('Select how many items will appear per page on all taxonomy archive pages of your website.', 'bookyourtravel'),
		'id' => 'taxonomy_pages_items_per_page',
		'std' => '12',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $items_per_page);			
		
	$options[] = array(
		'name' => esc_html__('Taxonomy archive pages items per row', 'bookyourtravel'),
		'desc' => esc_html__('Select how many items will appear per row on all taxonomy archive pages of your website.', 'bookyourtravel'),
		'id' => 'taxonomy_pages_items_per_row',
		'std' => '3',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $items_per_row);
		
	$options[] = array(
		'name' => esc_html__('Taxonomy archive pages sort items by field', 'bookyourtravel'),
		'desc' => esc_html__('What field do you want taxonomy archive pages to be sorted by?', 'bookyourtravel'),
		'id' => 'taxonomy_pages_sort_by_field',
		'std' => 'title',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $sort_by_columns);	

	$options[] = array(
		'name' => esc_html__('Taxonomy archive pages sort items descending', 'bookyourtravel'),
		'desc' => esc_html__('Do you want to sort taxonomy archive pages items in descending order?', 'bookyourtravel'),
		'id' => 'taxonomy_pages_sort_descending',
		'std' => '0',
		'type' => 'checkbox');	
		
	$options[] = array(
		'name' => esc_html__('Taxonomy archive pages hide item titles', 'bookyourtravel'),
		'desc' => esc_html__('Hide item titles on taxonomy archive pages', 'bookyourtravel'),
		'id' => 'taxonomy_pages_hide_item_titles',
		'std' => '0',
		'type' => 'checkbox');		

	$options[] = array(
		'name' => esc_html__('Taxonomy archive pages hide item descriptions', 'bookyourtravel'),
		'desc' => esc_html__('Hide item descriptions on taxonomy archive pages', 'bookyourtravel'),
		'id' => 'taxonomy_pages_hide_item_descriptions',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => esc_html__('Taxonomy archive pages hide item images', 'bookyourtravel'),
		'desc' => esc_html__('Hide item images on taxonomy archive pages', 'bookyourtravel'),
		'id' => 'taxonomy_pages_hide_item_images',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => esc_html__('Taxonomy archive pages hide item actions', 'bookyourtravel'),
		'desc' => esc_html__('Hide item actions on taxonomy archive pages', 'bookyourtravel'),
		'id' => 'taxonomy_pages_hide_item_actions',
		'std' => '0',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Taxonomy archive pages hide item prices', 'bookyourtravel'),
		'desc' => esc_html__('Hide item prices on taxonomy archive pages', 'bookyourtravel'),
		'id' => 'taxonomy_pages_hide_item_prices',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => esc_html__('Taxonomy archive pages hide item address', 'bookyourtravel'),
		'desc' => esc_html__('Hide item address on taxonomy archive pages', 'bookyourtravel'),
		'id' => 'taxonomy_pages_hide_item_address',
		'std' => '0',
		'type' => 'checkbox');

	$options[] = array(
		'name' => esc_html__('Taxonomy archive pages hide item stars (if applicable)', 'bookyourtravel'),
		'desc' => esc_html__('Hide item stars (if applicable) on taxonomy archive pages', 'bookyourtravel'),
		'id' => 'taxonomy_pages_hide_item_stars',
		'std' => '0',
		'type' => 'checkbox');

	if ($bookyourtravel_theme_globals->enable_reviews()) {
		$options[] = array(
			'name' => esc_html__('Taxonomy archive pages hide item ratings', 'bookyourtravel'),
			'desc' => esc_html__('Hide item ratings on taxonomy archive pages', 'bookyourtravel'),
			'id' => 'taxonomy_pages_hide_item_ratings',
			'std' => '0',
			'type' => 'checkbox');
	}

	$sliders_array = array();
	if (class_exists ('RevSlider')) {
	
		$options[] = array(
			'name' => esc_html__('Show slider', 'bookyourtravel'),
			'desc' => esc_html__('Show slider on home page', 'bookyourtravel'),
			'id' => 'frontpage_show_slider',
			'std' => '0',
			'type' => 'checkbox');

		try {
			$revs = new RevSlider();
			if (method_exists($revs, "get_sliders")) {
				$sa = $revs->get_sliders();

				foreach($sa as $slider){
					$sliders_array[] = $slider->alias;
				}			
			}
		} catch(Exception $e) {
		}
		
		if (count($sliders_array) > 0) {
			$options[] = array(
				'name' => esc_html__('Homepage slider', 'bookyourtravel'),
				'desc' => esc_html__('Select homepage slider from existing sliders', 'bookyourtravel'),
				'id' => 'homepage_slider',
				'std' => '',
				'type' => 'select',
				'class' => 'mini', //mini, tiny, small
				'options' => $sliders_array);
		}
	}
	
	$options[] = array(
		'name' => esc_html__('Post Types', 'bookyourtravel'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => esc_html__('Enable Accommodations', 'bookyourtravel'),
		'desc' => esc_html__('Enable "Accommodations" data-type', 'bookyourtravel'),
		'id' => 'enable_accommodations',
		'std' => '1',
		'type' => 'checkbox');			
		
	$options[] = array(
		'name' => esc_html__('Enable Tours', 'bookyourtravel'),
		'desc' => esc_html__('Enable "Tours" data-type', 'bookyourtravel'),
		'id' => 'enable_tours',
		'std' => '1',
		'type' => 'checkbox');	

	$options[] = array(
		'name' => esc_html__('Enable Car rentals', 'bookyourtravel'),
		'desc' => esc_html__('Enable "Car rentals" data-type', 'bookyourtravel'),
		'id' => 'enable_car_rentals',
		'std' => '1',
		'type' => 'checkbox');	
		
	$options[] = array(
		'name' => esc_html__('Enable Cruises', 'bookyourtravel'),
		'desc' => esc_html__('Enable "Cruises" data-type', 'bookyourtravel'),
		'id' => 'enable_cruises',
		'std' => '1',
		'type' => 'checkbox');	

	$options[] = array(
		'name' => esc_html__('Enable Reviews', 'bookyourtravel'),
		'desc' => esc_html__('Enable "Reviews" data-type', 'bookyourtravel'),
		'id' => 'enable_reviews',
		'std' => '1',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Enable Extra Items', 'bookyourtravel'),
		'desc' => esc_html__('Enable "Extra Items" data-type to charge people for things like full-board, wifi, tour guides, fuel etc', 'bookyourtravel'),
		'id' => 'enable_extra_items',
		'std' => '1',
		'type' => 'checkbox');	
	
	$options[] = array(
		'name' => esc_html__('Locations', 'bookyourtravel'),
		'type' => 'heading');

	$counts_in_location_options = array(
		"accommodation" => __("Show accommodations count", "bookyourtravel"),
		"car_rental" => __("Show car rentals count", "bookyourtravel"),
		"cruise" => __("Show cruises count", "bookyourtravel"),
		"tour" => __("Show tours count", "bookyourtravel"),
	);
		
	$options[] = array(
		'type' => 'multicheck',	
		'name' => esc_html__('Show property count in location items', 'bookyourtravel'),
		'desc' => esc_html__('Show property count in location items shown on location list pages (Note: will only apply if specific post type is enabled).', 'bookyourtravel'),
		'id' => 'show_counts_in_location_items',
		'std' => '',
		'options' => $counts_in_location_options);
		
	$prices_in_location_options = array(
		"accommodation" => __("Show accommodations price", "bookyourtravel"),
		"car_rental" => __("Show car rentals price", "bookyourtravel"),
		"cruise" => __("Show cruises price", "bookyourtravel"),
		"tour" => __("Show tours price", "bookyourtravel"),
	);

	$options[] = array(
		'type' => 'multicheck',	
		'name' => esc_html__('Show property price in location items', 'bookyourtravel'),
		'desc' => esc_html__('Show property price in location items shown on location list pages (Note: will only apply if specific post type is enabled, maximum allowed is 2 due to layout restrictions!).', 'bookyourtravel'),
		'id' => 'show_prices_in_location_items',
		'std' => '',
		'options' => $prices_in_location_options);
	
	$allowed_tags = array();
	$allowed_tags['strong'] = array('class' => array());
	$allowed_tags['span'] = array('class' => array());
	$allowed_tags['br'] = array();
	$allowed_tags['a'] = array('class' => array(), 'href' => array());
		
	$options[] = array(
		'name' => esc_html__('Single location permalink slug', 'bookyourtravel'),
		'desc' => wp_kses(__('The permalink slug used for single locations (by default it is set to "location". <br /><strong>Note:</strong> Please make sure you flush your rewrite rules after changing this setting. You can do so by navigating to <a href="/wp-admin/options-permalink.php">Settings->Permalinks</a> and clicking "Save Changes".', 'bookyourtravel'), $allowed_tags),
		'id' => 'locations_permalink_slug',
		'std' => 'location',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Tabs displayed on single location page.', 'bookyourtravel'),
		'desc' => esc_html__('Use drag&drop to change order of tabs.', 'bookyourtravel'),
		'id' => 'location_tabs',
		'std' => 'Tab name',
		'type' => 'repeat_tab');
		
	$options[] = array(
		'name' => esc_html__('Extra fields displayed on single location page.', 'bookyourtravel'),
		'desc' => esc_html__('Select the tab your field is displayed on from the tab dropdown.', 'bookyourtravel'),
		'id' => 'location_extra_fields',
		'std' => 'Default field label',
		'type' => 'repeat_extra_field');
		
	$options[] = array(
		'name' => esc_html__('Accommodations', 'bookyourtravel'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => esc_html__('Single accommodation permalink slug', 'bookyourtravel'),
		'desc' => wp_kses(__('The permalink slug used for creating single accommodations (by default it is set to "hotel". <br /><strong>Note:</strong> Please make sure you flush your rewrite rules after changing this setting. You can do so by navigating to <a href="/wp-admin/options-permalink.php">Settings->Permalinks</a> and clicking "Save Changes".', 'bookyourtravel'), $allowed_tags),
		'id' => 'accommodations_permalink_slug',
		'std' => 'hotel',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Disable star count field for accommodations', 'bookyourtravel'),
		'desc' => esc_html__('Check to disable the star count field for all accommodations', 'bookyourtravel'),
		'id' => 'disable_star_count_accommodations',
		'std' => '0',
		'class' => 'disable_star_count_accommodations',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Tabs displayed on single accommodation page.', 'bookyourtravel'),
		'desc' => esc_html__('Use drag&drop to change order of tabs.', 'bookyourtravel'),
		'id' => 'accommodation_tabs',
		'std' => 'Tab name',
		'type' => 'repeat_tab');
		
	$options[] = array(
		'name' => esc_html__('Extra fields displayed on single accommodation page.', 'bookyourtravel'),
		'desc' => esc_html__('Select the tab your field is displayed on from the tab dropdown.', 'bookyourtravel'),
		'id' => 'accommodation_extra_fields',
		'std' => 'Default field label',
		'type' => 'repeat_extra_field');

	$options[] = array(
		'name' => esc_html__('Tours', 'bookyourtravel'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => esc_html__('Single tour permalink slug', 'bookyourtravel'),
		'desc' => wp_kses(__('The permalink slug used for single tours (by default it is set to "tour". <br /><strong>Note:</strong> Please make sure you flush your rewrite rules after changing this setting. You can do so by navigating to <a href="/wp-admin/options-permalink.php">Settings->Permalinks</a> and clicking "Save Changes".', 'bookyourtravel'), $allowed_tags),
		'id' => 'tours_permalink_slug',
		'std' => 'tour',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Tabs displayed on single tour page.', 'bookyourtravel'),
		'desc' => esc_html__('Use drag&drop to change order of tabs.', 'bookyourtravel'),
		'id' => 'tour_tabs',
		'std' => 'Tab name',
		'type' => 'repeat_tab');
		
	$options[] = array(
		'name' => esc_html__('Extra fields displayed on single tour page.', 'bookyourtravel'),
		'desc' => esc_html__('Select the tab your field is displayed on from the tab dropdown.', 'bookyourtravel'),
		'id' => 'tour_extra_fields',
		'std' => 'Default field label',
		'type' => 'repeat_extra_field');
		
	$options[] = array(
		'name' => esc_html__('Car Rentals', 'bookyourtravel'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => esc_html__('Single car rental permalink slug', 'bookyourtravel'),
		'desc' => wp_kses(__('The permalink slug used for single car rentals (by default it is set to "car-rental". <br /><strong>Note:</strong> Please make sure you flush your rewrite rules after changing this setting. You can do so by navigating to <a href="/wp-admin/options-permalink.php">Settings->Permalinks</a> and clicking "Save Changes".', 'bookyourtravel'), $allowed_tags),
		'id' => 'car_rentals_permalink_slug',
		'std' => 'car-rental',
		'type' => 'text');

	$options[] = array(
		'name' => esc_html__('Are the same cars made available from multiple locations?', 'bookyourtravel'),
		'desc' => esc_html__('Enable this if you have your cars available in multiple locations by moving the same vehicle from one location to another. Otherwise the amount of available vehicles will get multiplied by the number of locations each vehicle belongs to.', 'bookyourtravel'),
		'id' => 'car_rentals_available_per_location_only',
		'std' => '0',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => esc_html__('Tabs displayed on single car rental page.', 'bookyourtravel'),
		'desc' => esc_html__('Use drag&drop to change order of tabs.', 'bookyourtravel'),
		'id' => 'car_rental_tabs',
		'std' => 'Tab name',
		'type' => 'repeat_tab');
		
	$options[] = array(
		'name' => esc_html__('Extra fields displayed on single car rental page.', 'bookyourtravel'),
		'desc' => esc_html__('Select the tab your field is displayed on from the tab dropdown.', 'bookyourtravel'),
		'id' => 'car_rental_extra_fields',
		'std' => 'Default field label',
		'type' => 'repeat_extra_field');		
		
	$options[] = array(
		'name' => esc_html__('Cruises', 'bookyourtravel'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => esc_html__('Single cruise permalink slug', 'bookyourtravel'),
		'desc' => wp_kses(__('The permalink slug used for single cruises (by default it is set to "cruise". <br /><strong>Note:</strong> Please make sure you flush your rewrite rules after changing this setting. You can do so by navigating to <a href="/wp-admin/options-permalink.php">Settings->Permalinks</a> and clicking "Save Changes".', 'bookyourtravel'), $allowed_tags),
		'id' => 'cruises_permalink_slug',
		'std' => 'cruise',
		'type' => 'text');
		
	$options[] = array(
		'name' => esc_html__('Tabs displayed on single cruise page.', 'bookyourtravel'),
		'desc' => esc_html__('Use drag&drop to change order of tabs.', 'bookyourtravel'),
		'id' => 'cruise_tabs',
		'std' => 'Tab name',
		'type' => 'repeat_tab');
		
	$options[] = array(
		'name' => esc_html__('Extra fields displayed on single cruise page.', 'bookyourtravel'),
		'desc' => esc_html__('Select the tab your field is displayed on from the tab dropdown.', 'bookyourtravel'),
		'id' => 'cruise_extra_fields',
		'std' => 'Default field label',
		'type' => 'repeat_extra_field');		
	

	$options[] = array(
		'name' => esc_html__('Reviews', 'bookyourtravel'),
		'type' => 'heading');
		
	$options[] = array(
		'text' => esc_html__('Synchronise reviews', 'bookyourtravel'),
		'name' => esc_html__('Synchronise review totals', 'bookyourtravel'),
		'desc' => esc_html__('Click this button to synchronise review totals if your review totals are out of sync', 'bookyourtravel'),
		'id' => 'synchronise_reviews',
		'std' => 'Default',
		'type' => 'link_button_field');
		
	$options[] = array(
		'name' => esc_html__('Accommodation review fields', 'bookyourtravel'),
		'desc' => esc_html__('Review fields for single accommodation', 'bookyourtravel'),
		'id' => 'accommodation_review_fields',
		'std' => 'Default review field label',
		'type' => 'repeat_review_field');
		
	$options[] = array(
		'name' => esc_html__('Tour review fields', 'bookyourtravel'),
		'desc' => esc_html__('Review fields for single tour.', 'bookyourtravel'),
		'id' => 'tour_review_fields',
		'std' => 'Default review field label',
		'type' => 'repeat_review_field');
		
	$options[] = array(
		'name' => esc_html__('Cruise review fields', 'bookyourtravel'),
		'desc' => esc_html__('Review fields for single cruise.', 'bookyourtravel'),
		'id' => 'cruise_review_fields',
		'std' => 'Default review field label',
		'type' => 'repeat_review_field');

	$options[] = array(
		'name' => esc_html__('Car rental review fields', 'bookyourtravel'),
		'desc' => esc_html__('Review fields for single car rental.', 'bookyourtravel'),
		'id' => 'car_rental_review_fields',
		'std' => 'Default review field label',
		'type' => 'repeat_review_field');
		
	$options[] = array(
		'name' => esc_html__('Review form thank you message', 'bookyourtravel'),
		'desc' => esc_html__('The message to display after review form has been submitted.', 'bookyourtravel'),
		'id' => 'review_form_thank_you',
		'std' => __('Thank you for submitting a review.', 'bookyourtravel'),
		'class' => '', //mini, tiny, small
		'type' => 'textarea');		
		
	$options[] = array(
		'name' => esc_html__('Inquiry Forms', 'bookyourtravel'),
		'type' => 'heading');
		
	if ($bookyourtravel_theme_globals->is_recaptcha_usable()) {
		$options[] = array(
			'name' => esc_html__('Enable reCaptcha', 'bookyourtravel'),
			'desc' => esc_html__('Enable Google reCaptcha for inquiry forms', 'bookyourtravel'),
			'id' => 'enable_inquiry_recaptcha',
			'std' => '0',
			'type' => 'checkbox');			
	}
		
	$options[] = array(
		'name' => esc_html__('Inquiry form fields', 'bookyourtravel'),
		'desc' => esc_html__('Inquiry form fields accommodations, tours, cruises and car rentals.', 'bookyourtravel'),
		'id' => 'inquiry_form_fields',
		'std' => 'Default form field label',
		'type' => 'repeat_form_field');
		
	$options[] = array(
		'name' => esc_html__('Inquiry form thank you message', 'bookyourtravel'),
		'desc' => esc_html__('The message to display after inquiry form has been submitted.', 'bookyourtravel'),
		'id' => 'inquiry_form_thank_you',
		'std' => __('Thank you for submitting an inquiry. We will get back to you as soon as we can.', 'bookyourtravel'),
		'class' => '', //mini, tiny, small
		'type' => 'textarea');
	
	$options[] = array(
		'name' => esc_html__('Booking Forms', 'bookyourtravel'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => esc_html__('Booking form fields', 'bookyourtravel'),
		'desc' => esc_html__('Booking form fields for accommodations, tours, cruises and car rentals.', 'bookyourtravel'),
		'id' => 'booking_form_fields',
		'std' => 'Default form field label',
		'type' => 'repeat_form_field');
		
	$options[] = array(
		'name' => esc_html__('Booking form thank you message', 'bookyourtravel'),
		'desc' => esc_html__('The message to display after booking form has been submitted (if WooCommerce is not used).', 'bookyourtravel'),
		'id' => 'booking_form_thank_you',
		'std' => __('Thank you! We will get back you with regards your booking within 24 hours.', 'bookyourtravel'),
		'class' => '', //mini, tiny, small
		'type' => 'textarea');		
		
	global $bookyourtravel_installed_version;
	$bookyourtravel_needs_update = get_option( '_byt_needs_update', 0 );
	$bookyourtravel_version_before_update = get_option('_byt_version_before_update', 0);	
	
	$options[] = array(
		'name' => esc_html__('Database Upgrades', 'bookyourtravel'),
		'type' => 'heading');

	if ($bookyourtravel_needs_update && null !== $bookyourtravel_installed_version && $bookyourtravel_version_before_update < $bookyourtravel_installed_version ) {
					
		$options[] = array(
			'text' => esc_html__('Click here to upgrade', 'bookyourtravel'),
			'name' => esc_html__('Your Book Your Travel database needs an upgrade!', 'bookyourtravel'),
			'desc' => sprintf(__('Your current database version is <strong>%s</strong>, while the current theme version is <strong>%s</strong>.', 'bookyourtravel'), $bookyourtravel_version_before_update, $bookyourtravel_installed_version),
			'id' => 'upgrade_bookyourtravel_db',
			'std' => 'Default',
			'type' => 'link_button_field');
	} else {
		$options[] = array(
			'name' => esc_html__('Database upgrade', 'bookyourtravel'),
			'text' => '<span class="allgood">' . esc_html__('All ok', 'bookyourtravel') . '</span>',
			'desc' => esc_html__('Database is up to date. No action required!', 'bookyourtravel'),
			'id' => 'upgrade_bookyourtravel_db_no_action',
			'std' => '',
			'type' => 'page_status_info_field');
	}
	
	if (BookYourTravel_Theme_Utils::is_woocommerce_active()) {
	
		$options[] = array(
			'name' => esc_html__('WooCommerce integration', 'bookyourtravel'),
			'type' => 'heading');

		$options[] = array(
			'name' => esc_html__('Use WooCommerce for checkout', 'bookyourtravel'),
			'desc' => esc_html__('Use WooCommerce to enable payment after booking', 'bookyourtravel'),
			'id' => 'use_woocommerce_for_checkout',
			'std' => '0',
			'type' => 'checkbox');
			
		$status_array = array (
			'pending' => esc_html__('Pending', 'bookyourtravel'),
			'on-hold' => esc_html__('On hold', 'bookyourtravel'),
			'completed' => esc_html__('Completed', 'bookyourtravel'),
			'processing' => esc_html__('Processing', 'bookyourtravel'),
			'cancelled' => esc_html__('Cancelled', 'bookyourtravel'),
			'initiated' => esc_html__('Initiated', 'bookyourtravel'),
		);
		
		$options[] = array(
			'name' => esc_html__('Completed order WooCommerce statuses', 'bookyourtravel'),
			'desc' => esc_html__('Which WooCommerce statuses do you want to consider as completed so that the item is no longer treated as available?', 'bookyourtravel'),
			'id' => 'completed_order_woocommerce_statuses',
			'options' => $status_array,
			'std' => 'completed',
			'class' => '', //mini, tiny, small
			'type' => 'multicheck');
			
		$options[] = array(
			'name' => esc_html__('WooCommerce pages sidebar position', 'bookyourtravel'),
			'desc' => esc_html__('Select the position (if any) of sidebars to appear on all WooCommerce-specific pages of your website.', 'bookyourtravel'),
			'id' => 'woocommerce_pages_sidebar_position',
			'std' => 'three',
			'type' => 'select',
			'class' => 'mini', //mini, tiny, small
			'options' => $page_sidebars);
	}	
	
	$options[] = array(
		'name' => esc_html__('Status', 'bookyourtravel'),
		'type' => 'heading');

	$parent_directory = get_template_directory();


	$options[] = array(
		'name' => esc_html__('General', 'bookyourtravel'),
		'id' => 'page_related_sub_heading',
		'std' => '',
		'type' => 'sub_heading');

	$setup_errors = BookYourTravel_Theme_Versioning::check_theme_setup();
	
	$options[] = array(
		'name' => esc_html__('Theme setup', 'bookyourtravel'),
		'desc' => esc_html__('Theme setup issues are listed in red below', 'bookyourtravel'),		
		'text' => $setup_errors,
		'id' => 'parent_theme_setup',
		'type' => 'file_status_info_field');
/*
	$options[] = array(
		'name' => esc_html__('Pages', 'bookyourtravel'),
		'id' => 'page_related_sub_heading',
		'std' => '',
		'type' => 'sub_heading');
		
	$page_status_string = "<table>";
	$page_status_string .= optionsframework_my_create_page_status_row(esc_html__("Login page:", "bookyourtravel"), $bookyourtravel_theme_globals->get_login_page_url());
	$page_status_string .= optionsframework_my_create_page_status_row(esc_html__("Register page:", "bookyourtravel"), $bookyourtravel_theme_globals->get_register_page_url());
	$page_status_string .= optionsframework_my_create_page_status_row(esc_html__("Reset password page:", "bookyourtravel"), $bookyourtravel_theme_globals->get_reset_password_page_url());
	$page_status_string .= optionsframework_my_create_page_status_row(esc_html__("My account page:", "bookyourtravel"), $bookyourtravel_theme_globals->get_my_account_page_url());
	$page_status_string .= "</table>";
	
	$options[] = array(
		'name' => esc_html__('User pages', 'bookyourtravel'),
		'text' => $page_status_string,
		'desc' => esc_html__('The pages listed here are related to user accounts.', 'bookyourtravel'),
		'id' => 'user_related_page_status',
		'std' => '',
		'type' => 'page_status_info_field');
		
	$page_status_string = "<table>";
	$page_status_string .= optionsframework_my_create_page_status_row(esc_html__("Login page:", "bookyourtravel"), $bookyourtravel_theme_globals->get_partner_login_page_url());
	$page_status_string .= optionsframework_my_create_page_status_row(esc_html__("Register page:", "bookyourtravel"), $bookyourtravel_theme_globals->get_partner_register_page_url());
	$page_status_string .= optionsframework_my_create_page_status_row(esc_html__("Reset password page:", "bookyourtravel"), $bookyourtravel_theme_globals->get_partner_reset_password_page_url());
	$page_status_string .= "</table>";
	
	$options[] = array(
		'name' => esc_html__('Partner pages', 'bookyourtravel'),
		'text' => $page_status_string,
		'desc' => esc_html__('The pages listed here are related to partner accounts.', 'bookyourtravel'),
		'id' => 'partner_related_page_status',
		'std' => '',
		'type' => 'page_status_info_field');
	
	if ($bookyourtravel_theme_globals->enable_accommodations()) {
	
		$page_status_string = '';		
	
		$options[] = array(
			'name' => esc_html__('Accommodation pages', 'bookyourtravel'),
			'text' => '',
			'desc' => esc_html__('The pages listed here are related to the accommodations custom post type.', 'bookyourtravel'),
			'id' => 'accommodation_post_type_related_page_status',
			'std' => '',
			'type' => 'page_status_info_field');
	}
	
	$options[] = array(
		'name' => __('Theme files', 'bookyourtravel'),
		'id' => 'file_related_sub_heading',
		'std' => '',
		'type' => 'sub_heading');		
	
	$unversioned_css_html = BookYourTravel_Theme_Versioning::render_unversioned_css_files($parent_directory);
	$unversioned_js_html = BookYourTravel_Theme_Versioning::render_unversioned_js_files($parent_directory);
	$unversioned_php_html = BookYourTravel_Theme_Versioning::render_unversioned_php_files($parent_directory);
	$missing_css_html = BookYourTravel_Theme_Versioning::render_missing_css_files($parent_directory);
	$missing_js_html = BookYourTravel_Theme_Versioning::render_missing_js_files($parent_directory);
	$missing_php_html = BookYourTravel_Theme_Versioning::render_missing_php_files($parent_directory);	
	
	$options[] = array(
		'name' => esc_html__('Unversioned parent theme css files', 'bookyourtravel'),
		'text' => $unversioned_css_html,
		'desc' => esc_html__('The files marked in red here are found in the BookYourTravel parent theme folder (wp-content/themes/BookYourTravel) and have either been added by you or are deprecated and no longer in use by our theme in which case they can be safely removed without affecting your website.', 'bookyourtravel'),
		'id' => 'parent_theme_unversioned_css_files',
		'std' => '',
		'type' => 'file_status_info_field');		

	$options[] = array(
		'name' => esc_html__('Unversioned parent theme javascript files', 'bookyourtravel'),
		'text' => $unversioned_js_html,		
		'desc' => esc_html__('The files marked in red here are found in the BookYourTravel parent theme folder (wp-content/themes/BookYourTravel) and have either been added by you or are deprecated and no longer in use by our theme in which case they can be safely removed without affecting your website.', 'bookyourtravel'),
		'id' => 'parent_theme_unversioned_js_files',
		'std' => '',
		'type' => 'file_status_info_field');
		
	$options[] = array(
		'name' => esc_html__('Unversioned parent theme php files', 'bookyourtravel'),
		'text' => $unversioned_php_html,		
		'desc' => esc_html__('The files marked in red here are found in the BookYourTravel parent theme folder (wp-content/themes/BookYourTravel) and have either been added by you or are deprecated and no longer in use by our theme in which case they can be safely removed without affecting your website.', 'bookyourtravel'),
		'id' => 'parent_theme_unversioned_php_files',
		'std' => '',
		'type' => 'file_status_info_field');
		
	$options[] = array(
		'name' => esc_html__('Missing parent theme css files', 'bookyourtravel'),
		'text' => $missing_css_html,
		'desc' => esc_html__('The files marked in red here are are missing from the BookYourTravel parent theme folder (wp-content/themes/BookYourTravel) while they exist in the original downloaded from ThemeForest.', 'bookyourtravel'),
		'id' => 'parent_theme_missing_css_files',
		'std' => '',
		'type' => 'file_status_info_field');		

	$options[] = array(
		'name' => esc_html__('Missing parent theme javascript files', 'bookyourtravel'),
		'text' => $missing_js_html,		
		'desc' => esc_html__('The files marked in red here are missing from the BookYourTravel parent theme folder (wp-content/themes/BookYourTravel) while they exist in the original downloaded from ThemeForest.', 'bookyourtravel'),
		'id' => 'parent_theme_missing_js_files',
		'std' => '',
		'type' => 'file_status_info_field');
		
	$options[] = array(
		'name' => esc_html__('Missing parent theme php files', 'bookyourtravel'),
		'text' => $missing_php_html,		
		'desc' => esc_html__('The files marked in red here are missing from the BookYourTravel parent theme folder (wp-content/themes/BookYourTravel) while they exist in the original downloaded from ThemeForest.', 'bookyourtravel'),
		'id' => 'parent_theme_missing_php_files',
		'std' => '',
		'type' => 'file_status_info_field');		
*/	
	return $options;
}