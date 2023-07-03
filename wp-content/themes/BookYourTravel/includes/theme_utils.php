<?php
/**
 * BookYourTravel_Theme_Utils class
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

if ( ! defined( 'DISTICT_DATE_RANGE_QUERY' ) ) {
	define("DISTICT_DATE_RANGE_QUERY", "
		select d.the_date from
		(
			select adddate(%s,t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) the_date from
			(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t0,
			(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t1,
			(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t2,
			(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t3,
			(select 0 i union select 1 union select 2 union select 3 union select 4 union select 5 union select 6 union select 7 union select 8 union select 9) t4
			having the_date between %s and %s
			ORDER BY the_date
		) d
		where DATE(d.the_date) >= DATE(CURDATE())
		group by d.the_date
		order by d.the_date
	");
}

class BookYourTravel_Theme_Utils {

	public static function get_price_in_current_currency($price) {
		global $bookyourtravel_theme_globals, $woocommerce_wpml;
		global $WOOCS;

		$current_currency = $bookyourtravel_theme_globals->get_user_currency();
		$default_currency = $bookyourtravel_theme_globals->get_default_currency();

		if ($current_currency !== $default_currency) {
			if (isset($woocommerce_wpml) && isset($woocommerce_wpml->multi_currency) && method_exists($woocommerce_wpml->multi_currency, 'get_client_currency')) {
				$price = $woocommerce_wpml->multi_currency->prices->convert_price_amount($price, $current_currency);
			} else if (isset($WOOCS)) {
				$price = $WOOCS->woocs_exchange_value($price);
		    } else {
				$price = apply_filters('bookyourtravel_get_price_in_current_currency', $price);
			}
		}

		return $price;
	}

	public static function parseBool($input) {
		$in = strtolower($input);
		return ($in === true || $in == 'true' || $in == '1' || $in == 'on' || $in == 'yes');
	}

    public static function get_all_image_sizes() {
        global $_wp_additional_image_sizes;
        $default_image_sizes = array( 'thumbnail', 'medium', 'large' );

        foreach ( $default_image_sizes as $size ) {
            $image_sizes[$size]['width']	= intval( get_option( "{$size}_size_w") );
            $image_sizes[$size]['height'] = intval( get_option( "{$size}_size_h") );
            $image_sizes[$size]['crop']	= get_option( "{$size}_crop" ) ? get_option( "{$size}_crop" ) : false;
        }

        if ( isset( $_wp_additional_image_sizes ) && count( $_wp_additional_image_sizes ) )
            $image_sizes = array_merge( $image_sizes, $_wp_additional_image_sizes );

        return $image_sizes;
    }

	/**
	 * Function that either renders or echos image tag in the form of
	 * <img class="image_css_class" id="$image_id" src="$image_src" title="$image_title" alt="$image_alt" />
	 */
	public static function render_image($image_css_class, $image_id, $image_src, $image_title, $image_alt, $echo = true) {
		if ( !empty( $image_src) ) {
			$ret_val = sprintf("<img class='%s'", $image_css_class);
			if (!empty($image_id)) {
				$ret_val .= sprintf(" id='%s'", $image_id);
			}
			$ret_val .= sprintf(" src='%s' title='%s' alt='%s' />", $image_src, $image_title, $image_alt);

			$ret_val = apply_filters('bookyourtravel_render_image', $ret_val, $image_css_class, $image_id, $image_src, $image_title, $image_alt);
			if ($echo)
				echo $ret_val;
			else
				return $ret_val;
		}
		return "";
	}

	public static function cleanup_shortcodes_in_tab_field_content($content) {
		if (preg_match_all( '/'. get_shortcode_regex() .'/s', $content, $matches )) {
			foreach ( $matches[2] as $i => $sc ) {
				$now = $matches[0][$i];
				$content = str_replace( $now, $now . "\n\r", $content );
			}
		}
		return $content;
	}

	public static function fix_shortcodes_autop($content) {
		return strtr($content, [
			"<p>[" => "[",
			"]</p>" => "]",
		]);
	}

	public static function check_user_role( $role, $user_id = null ) {

		if ( is_numeric( $user_id ) )
			$user = get_userdata( $user_id );
		else
			$user = wp_get_current_user();

		if ( empty( $user ) )
			return false;

		return in_array( $role, (array) $user->roles );
	}

	public static function build_min_price_check_meta_key($post_type, $date_from = null, $date_to = null) {
		$meta_key = sprintf("_%s_min_price_check", $post_type);

		if (isset($date_from) && strlen($date_from) > 0) {
			$meta_key .= ':' . $date_from;
		}

		if (isset($date_to) && strlen($date_to) > 0) {
			$meta_key .= ':' . $date_to;
		}

		return $meta_key;
	}

	public static function build_min_price_meta_key($post_type, $date_from = NULL, $date_to = NULL) {
		$meta_key = sprintf("_%s_min_price", $post_type);

		if (isset($date_from) && strlen($date_from) > 0) {
			$meta_key .= ':' . $date_from;
		}

		if (isset($date_to) && strlen($date_to) > 0) {
			$meta_key .= ':' . $date_to;
		}

		return $meta_key;
	}

	public static function retrieve_array_of_values_from_query_string($key, $are_numbers = false) {
		$values_array = array();
		$query_string = explode("&",$_SERVER['QUERY_STRING']);
		foreach ($query_string as $part) {
			if (strpos($part, $key) !== false) {
				$split = strpos($part,"=");
				$key_value = explode("=", $part);
				if (count($key_value) == 2) {
					$found_key = trim($key_value[0]);
					$found_key = urldecode($found_key);
					$found_key = preg_replace( '/\[\d*\]/', '', $found_key );
					$found_key = str_replace('[]', '', $found_key);
					$value = trim($key_value[1]);
					if ($key == $found_key && !empty($value))
						$values_array[] = $are_numbers ? intval($value) : $value;
				}
			}
		}
		return $values_array;
	}

	/*
	 * from: http://stackoverflow.com/questions/16702398/convert-a-php-date-format-to-a-jqueryui-datepicker-date-format
	 * Matches each symbol of PHP date format standard
	 * with jQuery equivalent codeword
	 * @author Tristan Jahier
	 */
	public static function dateformat_PHP_to_jQueryUI($php_format) {
		$SYMBOLS_MATCHING = array(
			// Day
			'd' => 'dd',
			'D' => 'D',
			'j' => 'd',
			'l' => 'DD',
			'N' => '',
			'S' => '',
			'w' => '',
			'z' => 'o',
			// Week
			'W' => '',
			// Month
			'F' => 'MM',
			'm' => 'mm',
			'M' => 'M',
			'n' => 'm',
			't' => '',
			// Year
			'L' => '',
			'o' => '',
			'Y' => 'yy',
			'y' => 'y',
			// Time
			'a' => '',
			'A' => '',
			'B' => '',
			'g' => '',
			'G' => '',
			'h' => '',
			'H' => '',
			'i' => '',
			's' => '',
			'u' => ''
		);
		$jqueryui_format = "";
		$escaping = false;
		for($i = 0; $i < strlen($php_format); $i++)
		{
			$char = $php_format[$i];
			if($char === '\\') // PHP date format escaping character
			{
				$i++;
				if($escaping) $jqueryui_format .= $php_format[$i];
				else $jqueryui_format .= '\'' . $php_format[$i];
				$escaping = true;
			}
			else
			{
				if($escaping) { $jqueryui_format .= "'"; $escaping = false; }
				if(isset($SYMBOLS_MATCHING[$char]))
					$jqueryui_format .= $SYMBOLS_MATCHING[$char];
				else
					$jqueryui_format .= $char;
			}
		}
		return $jqueryui_format;
	}

	public static function string_contains($haystack, $needle) {
		if (strpos($haystack, $needle) !== FALSE)
			return true;
		else
			return false;
	}

	public static function strip_tags_and_shorten_by_words($acontent, $words) {
		$acontent = wp_strip_all_tags($acontent);
		return implode(' ', array_slice(explode(' ', $acontent), 0, $words));
	}

	public static function strip_tags_and_shorten($content, $character_count) {
		$content = wp_strip_all_tags($content);
		return (mb_strlen($content) > $character_count) ? mb_substr($content, 0, $character_count).' ' : $content;
	}

	public static function get_post_descendants($parent_id, $post_type) {
		$children = array();
		$posts = get_posts( array( 'posts_per_page' => -1, 'post_status' => 'publish', 'post_type' => $post_type, 'post_parent' => $parent_id, 'suppress_filters' => false ));
		foreach( $posts as $child ) {
			$gchildren = BookYourTravel_Theme_Utils::get_post_descendants($child->ID, $post_type);
			if( !empty($gchildren) ) {
				$children = array_merge($children, $gchildren);
			}
		}
		$children = array_merge($children,$posts);
		return $children;
	}

	// period_type = 0, increase by day
	// period_type = 1, increase by 7 days (week)
	// period_type = 2, incraese by 1 month
	public static function get_dates_from_range($start, $end, $period_type = 0) {

		$dates = array($start);

		while(end($dates) < $end) {
			if ($period_type == 1) {
				$new_date = date('Y-m-d', strtotime(end($dates).' +7 day'));
				if ($new_date < $end) {
					$dates[] = $new_date;
				} else {
					break;
				}
			} else if ($period_type == 2) {
				$new_date = date('Y-m-d', strtotime(end($dates).' +1 month'));
				if ($new_date < $end) {
					$dates[] = $new_date;
				} else {
					break;
				}
			} else {
				$dates[] = date('Y-m-d', strtotime(end($dates).' +1 day'));
			}
		}
		return $dates;
	}

	public static function is_weekend($date) {
        $is_weekend = (date('N', strtotime($date)) >= 6);
        
        return apply_filters("bookyourtravel_is_weekend", $is_weekend, $date);
	}

	public static function get_php_days_of_week() {

		$days_of_week = array();

		$days_of_week[0] = esc_html__('Sunday', 'bookyourtravel');
		$days_of_week[1] = esc_html__('Monday', 'bookyourtravel');
		$days_of_week[2] = esc_html__('Tuesday', 'bookyourtravel');
		$days_of_week[3] = esc_html__('Wednesday', 'bookyourtravel');
		$days_of_week[4] = esc_html__('Thursday', 'bookyourtravel');
		$days_of_week[5] = esc_html__('Friday', 'bookyourtravel');
		$days_of_week[6] = esc_html__('Saturday', 'bookyourtravel');

		return $days_of_week;
	}

	public static function get_days_of_week() {

		$days_of_week = array();

		$days_of_week[0] = esc_html__('Monday', 'bookyourtravel');
		$days_of_week[1] = esc_html__('Tuesday', 'bookyourtravel');
		$days_of_week[2] = esc_html__('Wednesday', 'bookyourtravel');
		$days_of_week[3] = esc_html__('Thursday', 'bookyourtravel');
		$days_of_week[4] = esc_html__('Friday', 'bookyourtravel');
		$days_of_week[5] = esc_html__('Saturday', 'bookyourtravel');
		$days_of_week[6] = esc_html__('Sunday', 'bookyourtravel');

		return $days_of_week;
	}

	public static function get_page_sidebar_positioning($page_id) {

		$page_custom_fields = get_post_custom( $page_id);

		$page_sidebar_positioning = null;
		if (isset($page_custom_fields['page_sidebar_positioning'])) {
			$page_sidebar_positioning = $page_custom_fields['page_sidebar_positioning'][0];
			$page_sidebar_positioning = empty($page_sidebar_positioning) ? '' : $page_sidebar_positioning;
		}

		return $page_sidebar_positioning;
	}

	public static function get_image_id_from_url($image_url) {
		global $wpdb;
		$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ));
        if (!$attachment) {
            $image_url = wp_make_link_relative($image_url);
            $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid RLIKE '%s';", $image_url ));
            if (!$attachment) {
                $image_url = basename($image_url);
                $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid RLIKE '%s';", $image_url ));
            }
        }
		return count($attachment) > 0 ? $attachment[0] : 0;
	}

	public static function get_page_section_class($page_sidebar_positioning) {

		$section_class = 'full-width';
		if ($page_sidebar_positioning == 'both')
			$section_class = 'one-half';
		else if ($page_sidebar_positioning == 'left' || $page_sidebar_positioning == 'right')
			$section_class = 'three-fourth';

		return $section_class;
	}

	public static function get_item_class($section_class) {

		$item_class = 'one-fourth';
		if ($section_class == 'one-half')
			$item_class = 'one-half';
		else if ($section_class == 'three-fourth')
			$item_class = 'one-third';

		return $item_class;
	}

	public static function get_item_class_by_row_posts($posts_per_row) {

		$card_layout_classes = array(
			'full-width',
			'one-half',
			'one-third',
			'one-fourth',
			'one-fifth'
		);

		$card_layout_classes = apply_filters("bookyourtravel_card_layout_classes", $card_layout_classes);

		$item_class = isset($card_layout_classes[$posts_per_row - 1]) ? $card_layout_classes[$posts_per_row - 1] : 'one-fourth';

		return $item_class;
	}

	/**
	 * from https://gist.github.com/stephenharris/5532899
	 * Lightens/darkens a given colour (hex format), returning the altered colour in hex format.7
	 * @param str $hex Colour as hexadecimal (with or without hash);
	 * @percent float $percent Decimal ( 0.2 = lighten by 20%(), -0.4 = darken by 40%() )
	 * @return str Lightened/Darkend colour as hexadecimal (with hash);
	 */
	public static function color_luminance( $hex, $percent ) {

		// validate hex string

		$hex = preg_replace( '/[^0-9a-f]/i', '', $hex );
		$new_hex = '#';

		if ( strlen( $hex ) < 6 ) {
			$hex = $hex[0] + $hex[0] + $hex[1] + $hex[1] + $hex[2] + $hex[2];
		}

		// convert to decimal and change luminosity
		for ($i = 0; $i < 3; $i++) {
			$dec = hexdec( substr( $hex, $i*2, 2 ) );
			$dec = min( max( 0, $dec + $dec * $percent ), 255 );
			$new_hex .= str_pad( dechex( $dec ) , 2, 0, STR_PAD_LEFT );
		}

		return $new_hex;
	}

	public static function get_file_path($relative_path_to_file) {
		if (is_child_theme()) {
			if (file_exists( get_stylesheet_directory() . $relative_path_to_file ) )
				return get_stylesheet_directory() . $relative_path_to_file;
			else
				return get_template_directory() . $relative_path_to_file;
		}
		return get_template_directory() . $relative_path_to_file;
	}

	public static function get_sidebar_widget_layout_class($sidebar_id) {

		global $bookyourtravel_theme_globals;
		$sidebar_number_of_columns = $bookyourtravel_theme_globals->get_sidebar_number_of_columns($sidebar_id);

		$layout_class = 'full-width';

		switch($sidebar_number_of_columns) {
			case 5: $layout_class = 'one-fifth'; break;
			case 4: $layout_class = 'one-fourth'; break;
			case 3: $layout_class = 'one-third'; break;
			case 2: $layout_class = 'one-half'; break;
			case 1: $layout_class = 'full-width'; break;
			default: break;
		}

		return apply_filters('bookyourtravel_sidebar_widget_layout_class', $layout_class, $sidebar_id, $sidebar_number_of_columns);
	}

	public static function get_file_uri($relative_path_to_file) {
		if (is_child_theme()) {
			if (file_exists( get_stylesheet_directory() . $relative_path_to_file ) )
				return get_stylesheet_directory_uri() . $relative_path_to_file;
			else
				return get_template_directory_uri() . $relative_path_to_file;
		}
		return get_template_directory_uri() . $relative_path_to_file;
	}

	public static function is_woocommerce_active() {
		return class_exists('WooCommerce');
	}

	public static function is_wpml_active() {
		return class_exists('SitePress');
	}

	public static function custom_array_search($array, $key, $value)
	{
		$results = array();

		if (is_array($array)) {
			if (isset($array[$key]) && $array[$key] == $value) {
				$results[] = $array;
			}

			foreach ($array as $subarray) {
				$results = array_merge($results, BookYourTravel_Theme_Utils::custom_array_search($subarray, $key, $value));
			}
		}

		return $results;
	}

	public static function get_current_language_post_id($id, $post_type = 'post', $return_original_if_missing = true) {
		if(function_exists('icl_object_id')) {
			return icl_object_id($id, $post_type, $return_original_if_missing);
		} else {
			return $id;
		}
	}

	public static function get_language_post_id($id, $post_type, $language, $return_original_if_missing = true) {
		global $sitepress;
		if ($sitepress) {
			if(function_exists('icl_object_id')) {
				return icl_object_id($id, $post_type, $return_original_if_missing, $language);
			} else {
				return $id;
			}
		}
		return $id;
	}

	public static function get_current_language_page_id($id) {
		if(function_exists('icl_object_id')) {
			return icl_object_id($id,'page',true);
		} else {
			return $id;
		}
	}

	public static function get_current_page_url_no_query() {

		global $wp;
		$current_url = add_query_arg( '', '', home_url( $wp->request ) );
		return $current_url;
	}

	public static function get_current_page_url() {

		global $wp;
        $query_string = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : "";
		$current_url = add_query_arg( $query_string, '', home_url( $wp->request ) );
		return $current_url;
	}

	public static function new_user_notification( $user_id ) {

		$user = get_userdata( $user_id );

		// The blogname option is escaped with esc_html on the way into the database in sanitize_option
		// we want to reverse this for the plain text arena of emails.
		$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

		global $bookyourtravel_theme_globals;

		$switched_locale = switch_to_locale( get_user_locale( $user ) );

		$login_page_url	= $bookyourtravel_theme_globals->get_login_page_url();

		$message = esc_html__( 'Thank you for registering. You may now log in using the credentials you supplied when you created your account.', 'bookyourtravel' ) . "\r\n\r\n";

		$message .= $login_page_url . "\r\n";

		$subject = sprintf(__("[%s] Thank you for registering!", "bookyourtravel"), $blogname);

		$message = apply_filters('bookyourtravel_new_user_notification_message', $message);
		$subject = apply_filters('bookyourtravel_new_user_notification_subject', $subject);

		wp_mail($user->user_email, $subject, $message);

		if ( $switched_locale ) {
			restore_previous_locale();
		}
	}

	public static function reset_password_notification( $user_id ) {

		global $bookyourtravel_theme_globals;
		$user = get_userdata( $user_id );

		if( !$user || !$user->user_resetpassword_key )
			return false;

		$switched_locale = switch_to_locale( get_user_locale( $user ) );

		$user_can_frontend_submit = in_array(BOOKYOURTRAVEL_FRONTEND_SUBMIT_ROLE, (array) $user->roles );
		$scoped_reset_password_page_url = $user_can_frontend_submit ? $bookyourtravel_theme_globals->get_partner_reset_password_page_url() : $bookyourtravel_theme_globals->get_reset_password_page_url();

		$reset_password_url = esc_url_raw ( add_query_arg(
			array(
				'action' => 'resetpassword',
				'user_id' => $user->ID,
				'resetpassword_key' => $user->user_resetpassword_key
			),
			$scoped_reset_password_page_url
		) );

		$subject = get_bloginfo( 'name' ) . esc_html__( ' - Reset Password ', 'bookyourtravel' );

		$message = esc_html__( 'To reset your password please go to the following url: ', 'bookyourtravel' );
		$message .= "\r\n";
		$message .= $reset_password_url;
		$message .= "\r\n";
		$message .= "\r\n";
		$message .= esc_html__( 'This link will remain valid for the next 24 hours.', 'bookyourtravel' );
		$message .= esc_html__( 'In case you did not request a password reset, please ignore this email.', 'bookyourtravel' );

		$message = apply_filters('bookyourtravel_reset_password_notification_message', $message);
		$subject = apply_filters('bookyourtravel_reset_password_notification_subject', $subject);

		wp_mail( $user->user_email, $subject, $message);

		if ( $switched_locale ) {
			restore_previous_locale();
		}
	}

	public static function new_password_notification( $user_id, $new_password ) {

		$user = get_userdata( $user_id );

		if( !$user || !$new_password )
			return false;

		$switched_locale = switch_to_locale( get_user_locale( $user ) );

		$subject = get_bloginfo( 'name' ) . esc_html__( ' - New Password ', 'bookyourtravel' );

		$message = esc_html__( 'Your password was successfully reset. ', 'bookyourtravel' );
		$message .= "\r\n";
		$message .= "\r\n";
		$message .= esc_html__( 'Your new password is:', 'bookyourtravel' );
		$message .= ' ' . $new_password;

		$message = apply_filters('bookyourtravel_new_password_notification_message', $message);
		$subject = apply_filters('bookyourtravel_new_password_notification_subject', $subject);

		wp_mail( $user->user_email, $subject, $message );

		if ( $switched_locale ) {
			restore_previous_locale();
		}
	}

	public static function reset_password( $user_id, $reset_password_key ) {
		$user = get_userdata( $user_id );

		if($user && $user->user_resetpassword_key && $user->user_resetpassword_key === $reset_password_key) {
			// check reset password time
			if(!$user->user_resetpassword_datetime || strtotime( $user->user_resetpassword_datetime ) < time() - ( 24 * 60 * 60 ))
				return false;

			// reset password
			$userdata = array(
				'ID' => $user->ID,
				'user_pass' => wp_generate_password( 8, false )
			);

			wp_update_user( $userdata );
			delete_user_meta( $user->ID, 'user_resetpassword_key' );

			return $userdata['user_pass'];
		} else{
			return false;
		}
	}

	public static function get_allowed_content_tags_array() {

		global $allowedtags;

		$allowedtags = array(
			'table' => array(
				'class' => array(), 'style' => array(), 'id' => array()
			),
            'tbody' => array(
				'class' => array(), 'style' => array()
			),
            'tr' => array(),
            'td' => array(
                'class' => array(), 'style' => array()
            ),
            'thead' => array(),
            'tfoot' => array(),
			'a' => array(
				'class' => array(), 'rel' => array(), 'style' => array(), 'id' => array(), 'href' => array(), 'title' => array()
			),
			'div' => array(
				'class' => array(), 'id' => array(), 'style' => array()
			),
			'span' => array(
				'class' => array(), 'id' => array(), 'style' => array()
			),
			'ul' => array(
				'id' => array(),
				'class' => array()
			),
			'li' => array(
				'class' => array(),
			),
			'p' => array(
				'class' => array(),
			),
			'b' => array(
				'class' => array(),
			),
			'i' => array(
				'class' => array(),
			),
			'h1' => array(
				'class' => array(),
			),
			'h2' => array(
				'class' => array(),
			),
			'h3' => array(
				'class' => array(),
			),
			'h4' => array(
				'class' => array(),
			),
			'h5' => array(
				'class' => array(),
			),
			'h6' => array(
				'class' => array(),
			),
			'em' => array(),
			'strong' => array(),
			'img' => array(
				'src' => array(),
				'title' => array(),
				'alt' => array()
			)
		);

		return apply_filters( 'bookyourtravel_allowed_content_tags_array', $allowedtags );
	}

	public static function get_allowed_widgets_tags_array() {

		global $allowedtags;

		$allowedtags = array(
			'a' => array(
				'href' => array(),
				'title' => array()
			),
			'nav' => array(
				'role' => array(),
				'class' => array(),
				'id' => array(),
			),
			'ul' => array(
				'class' => array(),
				'id' => array(),
			),
			'li' => array(
				'class' => array(),
			),
			'em' => array(),
			'strong' => array(),
			'div' => array(
				'class' => array(),
				'id' => array(),
			),
			'span' => array(
				'class' => array(),
				'id' => array(),
			),
			'p' => array(
				'class' => array(),
				'id' => array(),
			),
			'h1' => array(
				'class' => array(),
				'id' => array(),
			),
			'h2' => array(
				'class' => array(),
				'id' => array(),
			),
			'h3' => array(
				'class' => array(),
				'id' => array(),
			),
			'h4' => array(
				'class' => array(),
				'id' => array(),
			),
			'h5' => array(
				'class' => array(),
				'id' => array(),
			),
			'h6' => array(
				'class' => array(),
				'id' => array(),
			),
		);

		return apply_filters( 'bookyourtravel_allowed_widgets_tags', $allowedtags );
	}

	public static function check_file_exists($relative_path_to_file) {
		return (file_exists(get_stylesheet_directory() . $relative_path_to_file) || file_exists(get_template_directory() . $relative_path_to_file));
	}

	public static function string_starts_with($haystack, $needle) {
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
	}

	public static function get_default_language_post_id($id, $post_type) {
		global $sitepress;
		if ($sitepress) {
			$default_language = $sitepress->get_default_language();
			if(function_exists('icl_object_id')) {
				return icl_object_id($id, $post_type, true, $default_language);
			} else {
				return $id;
			}
		}
		return $id;
	}

	public static function get_default_language() {
		global $sitepress;
		if ($sitepress) {
			return $sitepress->get_default_language();
		} else if (defined('WPLANG')) {
			return WPLANG;
		} else
			return "en";
	}

	public static function comment_end() {

	}

	public static function comment($comment, $args, $depth) {
	   $GLOBALS['comment'] = $comment;
		?>
		<!--single comment-->
		<article <?php echo comment_class('clearfix', null, null, false); ?> id="article-comment-<?php comment_ID() ?>">
			<div class="third">
				<figure><?php echo get_avatar( $comment->comment_author_email, 70 ); ?></figure>
				<address>
					<span><?php echo get_comment_author_link(); ?></span><br />
					<?php echo date_i18n(get_option('date_format'), strtotime( get_comment_time( "Y-m-d" ) ) ); ?>
				</address>
				<div class="comment-meta commentmetadata"><?php edit_comment_link(esc_html__('(Edit)', 'bookyourtravel'),'  ','') ?></div>
			</div>
			<?php if ($comment->comment_approved == '0') : ?>
			<em><?php esc_html_e('Your comment is awaiting moderation.', 'bookyourtravel') ?></em>
			<?php endif; ?>
			<div class="comment-content"><?php echo get_comment_text(); ?></div>
			<?php
				$reply_link = get_comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth'])));
				$reply_link = str_replace('comment-reply-link', 'comment-reply-link reply', $reply_link);
				$reply_link = str_replace('comment-reply-login', 'comment-reply-login reply', $reply_link);
			?>
			<?php echo wp_kses($reply_link, array('a' => array('href' => array(),'rel' => array(),'class' => array(),'onclick' => array(),'aria-label' => array()))); ?>
		</article>
		<!--//single comment-->
	<?php
	}

    public static function get_google_font_family_choices()
    {
        $fonts = array(
            'ABeeZee' => 'ABeeZee',
            'Abel' => 'Abel',
            'Abhaya Libre' => 'Abhaya Libre',
            'Abril Fatface' => 'Abril Fatface',
            'Aclonica' => 'Aclonica',
            'Acme' => 'Acme',
            'Actor' => 'Actor',
            'Adamina' => 'Adamina',
            'Advent Pro' => 'Advent Pro',
            'Aguafina Script' => 'Aguafina Script',
            'Akronim' => 'Akronim',
            'Aladin' => 'Aladin',
            'Alata' => 'Alata',
            'Alatsi' => 'Alatsi',
            'Aldrich' => 'Aldrich',
            'Alef' => 'Alef',
            'Alegreya' => 'Alegreya',
            'Alegreya SC' => 'Alegreya SC',
            'Alegreya Sans' => 'Alegreya Sans',
            'Alegreya Sans SC' => 'Alegreya Sans SC',
            'Aleo' => 'Aleo',
            'Alex Brush' => 'Alex Brush',
            'Alfa Slab One' => 'Alfa Slab One',
            'Alice' => 'Alice',
            'Alike' => 'Alike',
            'Alike Angular' => 'Alike Angular',
            'Allan' => 'Allan',
            'Allerta' => 'Allerta',
            'Allerta Stencil' => 'Allerta Stencil',
            'Allura' => 'Allura',
            'Almarai' => 'Almarai',
            'Almendra' => 'Almendra',
            'Almendra Display' => 'Almendra Display',
            'Almendra SC' => 'Almendra SC',
            'Amarante' => 'Amarante',
            'Amaranth' => 'Amaranth',
            'Amatic SC' => 'Amatic SC',
            'Amethysta' => 'Amethysta',
            'Amiko' => 'Amiko',
            'Amiri' => 'Amiri',
            'Amita' => 'Amita',
            'Anaheim' => 'Anaheim',
            'Andada' => 'Andada',
            'Andika' => 'Andika',
            'Angkor' => 'Angkor',
            'Annie Use Your Telescope' => 'Annie Use Your Telescope',
            'Anonymous Pro' => 'Anonymous Pro',
            'Antic' => 'Antic',
            'Antic Didone' => 'Antic Didone',
            'Antic Slab' => 'Antic Slab',
            'Anton' => 'Anton',
            'Arapey' => 'Arapey',
            'Arbutus' => 'Arbutus',
            'Arbutus Slab' => 'Arbutus Slab',
            'Architects Daughter' => 'Architects Daughter',
            'Archivo' => 'Archivo',
            'Archivo Black' => 'Archivo Black',
            'Archivo Narrow' => 'Archivo Narrow',
            'Aref Ruqaa' => 'Aref Ruqaa',
            'Arima Madurai' => 'Arima Madurai',
            'Arimo' => 'Arimo',
            'Arizonia' => 'Arizonia',
            'Armata' => 'Armata',
            'Arsenal' => 'Arsenal',
            'Artifika' => 'Artifika',
            'Arvo' => 'Arvo',
            'Arya' => 'Arya',
            'Asap' => 'Asap',
            'Asap Condensed' => 'Asap Condensed',
            'Asar' => 'Asar',
            'Asset' => 'Asset',
            'Assistant' => 'Assistant',
            'Astloch' => 'Astloch',
            'Asul' => 'Asul',
            'Athiti' => 'Athiti',
            'Atma' => 'Atma',
            'Atomic Age' => 'Atomic Age',
            'Aubrey' => 'Aubrey',
            'Audiowide' => 'Audiowide',
            'Autour One' => 'Autour One',
            'Average' => 'Average',
            'Average Sans' => 'Average Sans',
            'Averia Gruesa Libre' => 'Averia Gruesa Libre',
            'Averia Libre' => 'Averia Libre',
            'Averia Sans Libre' => 'Averia Sans Libre',
            'Averia Serif Libre' => 'Averia Serif Libre',
            'B612' => 'B612',
            'B612 Mono' => 'B612 Mono',
            'Bad Script' => 'Bad Script',
            'Bahiana' => 'Bahiana',
            'Bahianita' => 'Bahianita',
            'Bai Jamjuree' => 'Bai Jamjuree',
            'Baloo 2' => 'Baloo 2',
            'Baloo Bhai 2' => 'Baloo Bhai 2',
            'Baloo Bhaina 2' => 'Baloo Bhaina 2',
            'Baloo Chettan 2' => 'Baloo Chettan 2',
            'Baloo Da 2' => 'Baloo Da 2',
            'Baloo Paaji 2' => 'Baloo Paaji 2',
            'Baloo Tamma 2' => 'Baloo Tamma 2',
            'Baloo Tammudu 2' => 'Baloo Tammudu 2',
            'Baloo Thambi 2' => 'Baloo Thambi 2',
            'Balthazar' => 'Balthazar',
            'Bangers' => 'Bangers',
            'Barlow' => 'Barlow',
            'Barlow Condensed' => 'Barlow Condensed',
            'Barlow Semi Condensed' => 'Barlow Semi Condensed',
            'Barriecito' => 'Barriecito',
            'Barrio' => 'Barrio',
            'Basic' => 'Basic',
            'Baskervville' => 'Baskervville',
            'Battambang' => 'Battambang',
            'Baumans' => 'Baumans',
            'Bayon' => 'Bayon',
            'Be Vietnam' => 'Be Vietnam',
            'Bebas Neue' => 'Bebas Neue',
            'Belgrano' => 'Belgrano',
            'Bellefair' => 'Bellefair',
            'Belleza' => 'Belleza',
            'Bellota' => 'Bellota',
            'Bellota Text' => 'Bellota Text',
            'BenchNine' => 'BenchNine',
            'Bentham' => 'Bentham',
            'Berkshire Swash' => 'Berkshire Swash',
            'Beth Ellen' => 'Beth Ellen',
            'Bevan' => 'Bevan',
            'Big Shoulders Display' => 'Big Shoulders Display',
            'Big Shoulders Text' => 'Big Shoulders Text',
            'Bigelow Rules' => 'Bigelow Rules',
            'Bigshot One' => 'Bigshot One',
            'Bilbo' => 'Bilbo',
            'Bilbo Swash Caps' => 'Bilbo Swash Caps',
            'BioRhyme' => 'BioRhyme',
            'BioRhyme Expanded' => 'BioRhyme Expanded',
            'Biryani' => 'Biryani',
            'Bitter' => 'Bitter',
            'Black And White Picture' => 'Black And White Picture',
            'Black Han Sans' => 'Black Han Sans',
            'Black Ops One' => 'Black Ops One',
            'Blinker' => 'Blinker',
            'Bokor' => 'Bokor',
            'Bonbon' => 'Bonbon',
            'Boogaloo' => 'Boogaloo',
            'Bowlby One' => 'Bowlby One',
            'Bowlby One SC' => 'Bowlby One SC',
            'Brawler' => 'Brawler',
            'Bree Serif' => 'Bree Serif',
            'Bubblegum Sans' => 'Bubblegum Sans',
            'Bubbler One' => 'Bubbler One',
            'Buda' => 'Buda',
            'Buenard' => 'Buenard',
            'Bungee' => 'Bungee',
            'Bungee Hairline' => 'Bungee Hairline',
            'Bungee Inline' => 'Bungee Inline',
            'Bungee Outline' => 'Bungee Outline',
            'Bungee Shade' => 'Bungee Shade',
            'Butcherman' => 'Butcherman',
            'Butterfly Kids' => 'Butterfly Kids',
            'Cabin' => 'Cabin',
            'Cabin Condensed' => 'Cabin Condensed',
            'Cabin Sketch' => 'Cabin Sketch',
            'Caesar Dressing' => 'Caesar Dressing',
            'Cagliostro' => 'Cagliostro',
            'Cairo' => 'Cairo',
            'Caladea' => 'Caladea',
            'Calistoga' => 'Calistoga',
            'Calligraffitti' => 'Calligraffitti',
            'Cambay' => 'Cambay',
            'Cambo' => 'Cambo',
            'Candal' => 'Candal',
            'Cantarell' => 'Cantarell',
            'Cantata One' => 'Cantata One',
            'Cantora One' => 'Cantora One',
            'Capriola' => 'Capriola',
            'Cardo' => 'Cardo',
            'Carme' => 'Carme',
            'Carrois Gothic' => 'Carrois Gothic',
            'Carrois Gothic SC' => 'Carrois Gothic SC',
            'Carter One' => 'Carter One',
            'Catamaran' => 'Catamaran',
            'Caudex' => 'Caudex',
            'Caveat' => 'Caveat',
            'Caveat Brush' => 'Caveat Brush',
            'Cedarville Cursive' => 'Cedarville Cursive',
            'Ceviche One' => 'Ceviche One',
            'Chakra Petch' => 'Chakra Petch',
            'Changa' => 'Changa',
            'Changa One' => 'Changa One',
            'Chango' => 'Chango',
            'Charm' => 'Charm',
            'Charmonman' => 'Charmonman',
            'Chathura' => 'Chathura',
            'Chau Philomene One' => 'Chau Philomene One',
            'Chela One' => 'Chela One',
            'Chelsea Market' => 'Chelsea Market',
            'Chenla' => 'Chenla',
            'Cherry Cream Soda' => 'Cherry Cream Soda',
            'Cherry Swash' => 'Cherry Swash',
            'Chewy' => 'Chewy',
            'Chicle' => 'Chicle',
            'Chilanka' => 'Chilanka',
            'Chivo' => 'Chivo',
            'Chonburi' => 'Chonburi',
            'Cinzel' => 'Cinzel',
            'Cinzel Decorative' => 'Cinzel Decorative',
            'Clicker Script' => 'Clicker Script',
            'Coda' => 'Coda',
            'Coda Caption' => 'Coda Caption',
            'Codystar' => 'Codystar',
            'Coiny' => 'Coiny',
            'Combo' => 'Combo',
            'Comfortaa' => 'Comfortaa',
            'Comic Neue' => 'Comic Neue',
            'Coming Soon' => 'Coming Soon',
            'Concert One' => 'Concert One',
            'Condiment' => 'Condiment',
            'Content' => 'Content',
            'Contrail One' => 'Contrail One',
            'Convergence' => 'Convergence',
            'Cookie' => 'Cookie',
            'Copse' => 'Copse',
            'Corben' => 'Corben',
            'Cormorant' => 'Cormorant',
            'Cormorant Garamond' => 'Cormorant Garamond',
            'Cormorant Infant' => 'Cormorant Infant',
            'Cormorant SC' => 'Cormorant SC',
            'Cormorant Unicase' => 'Cormorant Unicase',
            'Cormorant Upright' => 'Cormorant Upright',
            'Courgette' => 'Courgette',
            'Courier Prime' => 'Courier Prime',
            'Cousine' => 'Cousine',
            'Coustard' => 'Coustard',
            'Covered By Your Grace' => 'Covered By Your Grace',
            'Crafty Girls' => 'Crafty Girls',
            'Creepster' => 'Creepster',
            'Crete Round' => 'Crete Round',
            'Crimson Pro' => 'Crimson Pro',
            'Crimson Text' => 'Crimson Text',
            'Croissant One' => 'Croissant One',
            'Crushed' => 'Crushed',
            'Cuprum' => 'Cuprum',
            'Cute Font' => 'Cute Font',
            'Cutive' => 'Cutive',
            'Cutive Mono' => 'Cutive Mono',
            'DM Sans' => 'DM Sans',
            'DM Serif Display' => 'DM Serif Display',
            'DM Serif Text' => 'DM Serif Text',
            'Damion' => 'Damion',
            'Dancing Script' => 'Dancing Script',
            'Dangrek' => 'Dangrek',
            'Darker Grotesque' => 'Darker Grotesque',
            'David Libre' => 'David Libre',
            'Dawning of a New Day' => 'Dawning of a New Day',
            'Days One' => 'Days One',
            'Dekko' => 'Dekko',
            'Delius' => 'Delius',
            'Delius Swash Caps' => 'Delius Swash Caps',
            'Delius Unicase' => 'Delius Unicase',
            'Della Respira' => 'Della Respira',
            'Denk One' => 'Denk One',
            'Devonshire' => 'Devonshire',
            'Dhurjati' => 'Dhurjati',
            'Didact Gothic' => 'Didact Gothic',
            'Diplomata' => 'Diplomata',
            'Diplomata SC' => 'Diplomata SC',
            'Do Hyeon' => 'Do Hyeon',
            'Dokdo' => 'Dokdo',
            'Domine' => 'Domine',
            'Donegal One' => 'Donegal One',
            'Doppio One' => 'Doppio One',
            'Dorsa' => 'Dorsa',
            'Dosis' => 'Dosis',
            'Dr Sugiyama' => 'Dr Sugiyama',
            'Duru Sans' => 'Duru Sans',
            'Dynalight' => 'Dynalight',
            'EB Garamond' => 'EB Garamond',
            'Eagle Lake' => 'Eagle Lake',
            'East Sea Dokdo' => 'East Sea Dokdo',
            'Eater' => 'Eater',
            'Economica' => 'Economica',
            'Eczar' => 'Eczar',
            'El Messiri' => 'El Messiri',
            'Electrolize' => 'Electrolize',
            'Elsie' => 'Elsie',
            'Elsie Swash Caps' => 'Elsie Swash Caps',
            'Emblema One' => 'Emblema One',
            'Emilys Candy' => 'Emilys Candy',
            'Encode Sans' => 'Encode Sans',
            'Encode Sans Condensed' => 'Encode Sans Condensed',
            'Encode Sans Expanded' => 'Encode Sans Expanded',
            'Encode Sans Semi Condensed' => 'Encode Sans Semi Condensed',
            'Encode Sans Semi Expanded' => 'Encode Sans Semi Expanded',
            'Engagement' => 'Engagement',
            'Englebert' => 'Englebert',
            'Enriqueta' => 'Enriqueta',
            'Erica One' => 'Erica One',
            'Esteban' => 'Esteban',
            'Euphoria Script' => 'Euphoria Script',
            'Ewert' => 'Ewert',
            'Exo' => 'Exo',
            'Exo 2' => 'Exo 2',
            'Expletus Sans' => 'Expletus Sans',
            'Fahkwang' => 'Fahkwang',
            'Fanwood Text' => 'Fanwood Text',
            'Farro' => 'Farro',
            'Farsan' => 'Farsan',
            'Fascinate' => 'Fascinate',
            'Fascinate Inline' => 'Fascinate Inline',
            'Faster One' => 'Faster One',
            'Fasthand' => 'Fasthand',
            'Fauna One' => 'Fauna One',
            'Faustina' => 'Faustina',
            'Federant' => 'Federant',
            'Federo' => 'Federo',
            'Felipa' => 'Felipa',
            'Fenix' => 'Fenix',
            'Finger Paint' => 'Finger Paint',
            'Fira Code' => 'Fira Code',
            'Fira Mono' => 'Fira Mono',
            'Fira Sans' => 'Fira Sans',
            'Fira Sans Condensed' => 'Fira Sans Condensed',
            'Fira Sans Extra Condensed' => 'Fira Sans Extra Condensed',
            'Fjalla One' => 'Fjalla One',
            'Fjord One' => 'Fjord One',
            'Flamenco' => 'Flamenco',
            'Flavors' => 'Flavors',
            'Fondamento' => 'Fondamento',
            'Fontdiner Swanky' => 'Fontdiner Swanky',
            'Forum' => 'Forum',
            'Francois One' => 'Francois One',
            'Frank Ruhl Libre' => 'Frank Ruhl Libre',
            'Freckle Face' => 'Freckle Face',
            'Fredericka the Great' => 'Fredericka the Great',
            'Fredoka One' => 'Fredoka One',
            'Freehand' => 'Freehand',
            'Fresca' => 'Fresca',
            'Frijole' => 'Frijole',
            'Fruktur' => 'Fruktur',
            'Fugaz One' => 'Fugaz One',
            'GFS Didot' => 'GFS Didot',
            'GFS Neohellenic' => 'GFS Neohellenic',
            'Gabriela' => 'Gabriela',
            'Gaegu' => 'Gaegu',
            'Gafata' => 'Gafata',
            'Galada' => 'Galada',
            'Galdeano' => 'Galdeano',
            'Galindo' => 'Galindo',
            'Gamja Flower' => 'Gamja Flower',
            'Gayathri' => 'Gayathri',
            'Gelasio' => 'Gelasio',
            'Gentium Basic' => 'Gentium Basic',
            'Gentium Book Basic' => 'Gentium Book Basic',
            'Geo' => 'Geo',
            'Geostar' => 'Geostar',
            'Geostar Fill' => 'Geostar Fill',
            'Germania One' => 'Germania One',
            'Gidugu' => 'Gidugu',
            'Gilda Display' => 'Gilda Display',
            'Girassol' => 'Girassol',
            'Give You Glory' => 'Give You Glory',
            'Glass Antiqua' => 'Glass Antiqua',
            'Glegoo' => 'Glegoo',
            'Gloria Hallelujah' => 'Gloria Hallelujah',
            'Goblin One' => 'Goblin One',
            'Gochi Hand' => 'Gochi Hand',
            'Gorditas' => 'Gorditas',
            'Gothic A1' => 'Gothic A1',
            'Gotu' => 'Gotu',
            'Goudy Bookletter 1911' => 'Goudy Bookletter 1911',
            'Graduate' => 'Graduate',
            'Grand Hotel' => 'Grand Hotel',
            'Gravitas One' => 'Gravitas One',
            'Great Vibes' => 'Great Vibes',
            'Grenze' => 'Grenze',
            'Griffy' => 'Griffy',
            'Gruppo' => 'Gruppo',
            'Gudea' => 'Gudea',
            'Gugi' => 'Gugi',
            'Gupter' => 'Gupter',
            'Gurajada' => 'Gurajada',
            'Habibi' => 'Habibi',
            'Halant' => 'Halant',
            'Hammersmith One' => 'Hammersmith One',
            'Hanalei' => 'Hanalei',
            'Hanalei Fill' => 'Hanalei Fill',
            'Handlee' => 'Handlee',
            'Hanuman' => 'Hanuman',
            'Happy Monkey' => 'Happy Monkey',
            'Harmattan' => 'Harmattan',
            'Headland One' => 'Headland One',
            'Heebo' => 'Heebo',
            'Henny Penny' => 'Henny Penny',
            'Hepta Slab' => 'Hepta Slab',
            'Herr Von Muellerhoff' => 'Herr Von Muellerhoff',
            'Hi Melody' => 'Hi Melody',
            'Hind' => 'Hind',
            'Hind Guntur' => 'Hind Guntur',
            'Hind Madurai' => 'Hind Madurai',
            'Hind Siliguri' => 'Hind Siliguri',
            'Hind Vadodara' => 'Hind Vadodara',
            'Holtwood One SC' => 'Holtwood One SC',
            'Homemade Apple' => 'Homemade Apple',
            'Homenaje' => 'Homenaje',
            'IBM Plex Mono' => 'IBM Plex Mono',
            'IBM Plex Sans' => 'IBM Plex Sans',
            'IBM Plex Sans Condensed' => 'IBM Plex Sans Condensed',
            'IBM Plex Serif' => 'IBM Plex Serif',
            'IM Fell DW Pica' => 'IM Fell DW Pica',
            'IM Fell DW Pica SC' => 'IM Fell DW Pica SC',
            'IM Fell Double Pica' => 'IM Fell Double Pica',
            'IM Fell Double Pica SC' => 'IM Fell Double Pica SC',
            'IM Fell English' => 'IM Fell English',
            'IM Fell English SC' => 'IM Fell English SC',
            'IM Fell French Canon' => 'IM Fell French Canon',
            'IM Fell French Canon SC' => 'IM Fell French Canon SC',
            'IM Fell Great Primer' => 'IM Fell Great Primer',
            'IM Fell Great Primer SC' => 'IM Fell Great Primer SC',
            'Ibarra Real Nova' => 'Ibarra Real Nova',
            'Iceberg' => 'Iceberg',
            'Iceland' => 'Iceland',
            'Imprima' => 'Imprima',
            'Inconsolata' => 'Inconsolata',
            'Inder' => 'Inder',
            'Indie Flower' => 'Indie Flower',
            'Inika' => 'Inika',
            'Inknut Antiqua' => 'Inknut Antiqua',
            'Inria Sans' => 'Inria Sans',
            'Inria Serif' => 'Inria Serif',
            'Inter' => 'Inter',
            'Irish Grover' => 'Irish Grover',
            'Istok Web' => 'Istok Web',
            'Italiana' => 'Italiana',
            'Italianno' => 'Italianno',
            'Itim' => 'Itim',
            'Jacques Francois' => 'Jacques Francois',
            'Jacques Francois Shadow' => 'Jacques Francois Shadow',
            'Jaldi' => 'Jaldi',
            'Jim Nightshade' => 'Jim Nightshade',
            'Jockey One' => 'Jockey One',
            'Jolly Lodger' => 'Jolly Lodger',
            'Jomhuria' => 'Jomhuria',
            'Jomolhari' => 'Jomolhari',
            'Josefin Sans' => 'Josefin Sans',
            'Josefin Slab' => 'Josefin Slab',
            'Joti One' => 'Joti One',
            'Jua' => 'Jua',
            'Judson' => 'Judson',
            'Julee' => 'Julee',
            'Julius Sans One' => 'Julius Sans One',
            'Junge' => 'Junge',
            'Jura' => 'Jura',
            'Just Another Hand' => 'Just Another Hand',
            'Just Me Again Down Here' => 'Just Me Again Down Here',
            'K2D' => 'K2D',
            'Kadwa' => 'Kadwa',
            'Kalam' => 'Kalam',
            'Kameron' => 'Kameron',
            'Kanit' => 'Kanit',
            'Kantumruy' => 'Kantumruy',
            'Karla' => 'Karla',
            'Karma' => 'Karma',
            'Katibeh' => 'Katibeh',
            'Kaushan Script' => 'Kaushan Script',
            'Kavivanar' => 'Kavivanar',
            'Kavoon' => 'Kavoon',
            'Kdam Thmor' => 'Kdam Thmor',
            'Keania One' => 'Keania One',
            'Kelly Slab' => 'Kelly Slab',
            'Kenia' => 'Kenia',
            'Khand' => 'Khand',
            'Khmer' => 'Khmer',
            'Khula' => 'Khula',
            'Kirang Haerang' => 'Kirang Haerang',
            'Kite One' => 'Kite One',
            'Knewave' => 'Knewave',
            'KoHo' => 'KoHo',
            'Kodchasan' => 'Kodchasan',
            'Kosugi' => 'Kosugi',
            'Kosugi Maru' => 'Kosugi Maru',
            'Kotta One' => 'Kotta One',
            'Koulen' => 'Koulen',
            'Kranky' => 'Kranky',
            'Kreon' => 'Kreon',
            'Kristi' => 'Kristi',
            'Krona One' => 'Krona One',
            'Krub' => 'Krub',
            'Kulim Park' => 'Kulim Park',
            'Kumar One' => 'Kumar One',
            'Kumar One Outline' => 'Kumar One Outline',
            'Kurale' => 'Kurale',
            'La Belle Aurore' => 'La Belle Aurore',
            'Lacquer' => 'Lacquer',
            'Laila' => 'Laila',
            'Lakki Reddy' => 'Lakki Reddy',
            'Lalezar' => 'Lalezar',
            'Lancelot' => 'Lancelot',
            'Lateef' => 'Lateef',
            'Lato' => 'Lato',
            'League Script' => 'League Script',
            'Leckerli One' => 'Leckerli One',
            'Ledger' => 'Ledger',
            'Lekton' => 'Lekton',
            'Lemon' => 'Lemon',
            'Lemonada' => 'Lemonada',
            'Lexend Deca' => 'Lexend Deca',
            'Lexend Exa' => 'Lexend Exa',
            'Lexend Giga' => 'Lexend Giga',
            'Lexend Mega' => 'Lexend Mega',
            'Lexend Peta' => 'Lexend Peta',
            'Lexend Tera' => 'Lexend Tera',
            'Lexend Zetta' => 'Lexend Zetta',
            'Libre Barcode 128' => 'Libre Barcode 128',
            'Libre Barcode 128 Text' => 'Libre Barcode 128 Text',
            'Libre Barcode 39' => 'Libre Barcode 39',
            'Libre Barcode 39 Extended' => 'Libre Barcode 39 Extended',
            'Libre Barcode 39 Extended Text' => 'Libre Barcode 39 Extended Text',
            'Libre Barcode 39 Text' => 'Libre Barcode 39 Text',
            'Libre Baskerville' => 'Libre Baskerville',
            'Libre Caslon Display' => 'Libre Caslon Display',
            'Libre Caslon Text' => 'Libre Caslon Text',
            'Libre Franklin' => 'Libre Franklin',
            'Life Savers' => 'Life Savers',
            'Lilita One' => 'Lilita One',
            'Lily Script One' => 'Lily Script One',
            'Limelight' => 'Limelight',
            'Linden Hill' => 'Linden Hill',
            'Literata' => 'Literata',
            'Liu Jian Mao Cao' => 'Liu Jian Mao Cao',
            'Livvic' => 'Livvic',
            'Lobster' => 'Lobster',
            'Lobster Two' => 'Lobster Two',
            'Londrina Outline' => 'Londrina Outline',
            'Londrina Shadow' => 'Londrina Shadow',
            'Londrina Sketch' => 'Londrina Sketch',
            'Londrina Solid' => 'Londrina Solid',
            'Long Cang' => 'Long Cang',
            'Lora' => 'Lora',
            'Love Ya Like A Sister' => 'Love Ya Like A Sister',
            'Loved by the King' => 'Loved by the King',
            'Lovers Quarrel' => 'Lovers Quarrel',
            'Luckiest Guy' => 'Luckiest Guy',
            'Lusitana' => 'Lusitana',
            'Lustria' => 'Lustria',
            'M PLUS 1p' => 'M PLUS 1p',
            'M PLUS Rounded 1c' => 'M PLUS Rounded 1c',
            'Ma Shan Zheng' => 'Ma Shan Zheng',
            'Macondo' => 'Macondo',
            'Macondo Swash Caps' => 'Macondo Swash Caps',
            'Mada' => 'Mada',
            'Magra' => 'Magra',
            'Maiden Orange' => 'Maiden Orange',
            'Maitree' => 'Maitree',
            'Major Mono Display' => 'Major Mono Display',
            'Mako' => 'Mako',
            'Mali' => 'Mali',
            'Mallanna' => 'Mallanna',
            'Mandali' => 'Mandali',
            'Manjari' => 'Manjari',
            'Mansalva' => 'Mansalva',
            'Manuale' => 'Manuale',
            'Marcellus' => 'Marcellus',
            'Marcellus SC' => 'Marcellus SC',
            'Marck Script' => 'Marck Script',
            'Margarine' => 'Margarine',
            'Markazi Text' => 'Markazi Text',
            'Marko One' => 'Marko One',
            'Marmelad' => 'Marmelad',
            'Martel' => 'Martel',
            'Martel Sans' => 'Martel Sans',
            'Marvel' => 'Marvel',
            'Mate' => 'Mate',
            'Mate SC' => 'Mate SC',
            'Maven Pro' => 'Maven Pro',
            'McLaren' => 'McLaren',
            'Meddon' => 'Meddon',
            'MedievalSharp' => 'MedievalSharp',
            'Medula One' => 'Medula One',
            'Meera Inimai' => 'Meera Inimai',
            'Megrim' => 'Megrim',
            'Meie Script' => 'Meie Script',
            'Merienda' => 'Merienda',
            'Merienda One' => 'Merienda One',
            'Merriweather' => 'Merriweather',
            'Merriweather Sans' => 'Merriweather Sans',
            'Metal' => 'Metal',
            'Metal Mania' => 'Metal Mania',
            'Metamorphous' => 'Metamorphous',
            'Metrophobic' => 'Metrophobic',
            'Michroma' => 'Michroma',
            'Milonga' => 'Milonga',
            'Miltonian' => 'Miltonian',
            'Miltonian Tattoo' => 'Miltonian Tattoo',
            'Mina' => 'Mina',
            'Miniver' => 'Miniver',
            'Miriam Libre' => 'Miriam Libre',
            'Mirza' => 'Mirza',
            'Miss Fajardose' => 'Miss Fajardose',
            'Mitr' => 'Mitr',
            'Modak' => 'Modak',
            'Modern Antiqua' => 'Modern Antiqua',
            'Mogra' => 'Mogra',
            'Molengo' => 'Molengo',
            'Molle' => 'Molle',
            'Monda' => 'Monda',
            'Monofett' => 'Monofett',
            'Monoton' => 'Monoton',
            'Monsieur La Doulaise' => 'Monsieur La Doulaise',
            'Montaga' => 'Montaga',
            'Montez' => 'Montez',
            'Montserrat' => 'Montserrat',
            'Montserrat Alternates' => 'Montserrat Alternates',
            'Montserrat Subrayada' => 'Montserrat Subrayada',
            'Moul' => 'Moul',
            'Moulpali' => 'Moulpali',
            'Mountains of Christmas' => 'Mountains of Christmas',
            'Mouse Memoirs' => 'Mouse Memoirs',
            'Mr Bedfort' => 'Mr Bedfort',
            'Mr Dafoe' => 'Mr Dafoe',
            'Mr De Haviland' => 'Mr De Haviland',
            'Mrs Saint Delafield' => 'Mrs Saint Delafield',
            'Mrs Sheppards' => 'Mrs Sheppards',
            'Mukta' => 'Mukta',
            'Mukta Mahee' => 'Mukta Mahee',
            'Mukta Malar' => 'Mukta Malar',
            'Mukta Vaani' => 'Mukta Vaani',
            'Muli' => 'Muli',
            'Mystery Quest' => 'Mystery Quest',
            'NTR' => 'NTR',
            'Nanum Brush Script' => 'Nanum Brush Script',
            'Nanum Gothic' => 'Nanum Gothic',
            'Nanum Gothic Coding' => 'Nanum Gothic Coding',
            'Nanum Myeongjo' => 'Nanum Myeongjo',
            'Nanum Pen Script' => 'Nanum Pen Script',
            'Neucha' => 'Neucha',
            'Neuton' => 'Neuton',
            'New Rocker' => 'New Rocker',
            'News Cycle' => 'News Cycle',
            'Niconne' => 'Niconne',
            'Niramit' => 'Niramit',
            'Nixie One' => 'Nixie One',
            'Nobile' => 'Nobile',
            'Nokora' => 'Nokora',
            'Norican' => 'Norican',
            'Nosifer' => 'Nosifer',
            'Notable' => 'Notable',
            'Nothing You Could Do' => 'Nothing You Could Do',
            'Noticia Text' => 'Noticia Text',
            'Noto Sans' => 'Noto Sans',
            'Noto Sans HK' => 'Noto Sans HK',
            'Noto Sans JP' => 'Noto Sans JP',
            'Noto Sans KR' => 'Noto Sans KR',
            'Noto Sans SC' => 'Noto Sans SC',
            'Noto Sans TC' => 'Noto Sans TC',
            'Noto Serif' => 'Noto Serif',
            'Noto Serif JP' => 'Noto Serif JP',
            'Noto Serif KR' => 'Noto Serif KR',
            'Noto Serif SC' => 'Noto Serif SC',
            'Noto Serif TC' => 'Noto Serif TC',
            'Nova Cut' => 'Nova Cut',
            'Nova Flat' => 'Nova Flat',
            'Nova Mono' => 'Nova Mono',
            'Nova Oval' => 'Nova Oval',
            'Nova Round' => 'Nova Round',
            'Nova Script' => 'Nova Script',
            'Nova Slim' => 'Nova Slim',
            'Nova Square' => 'Nova Square',
            'Numans' => 'Numans',
            'Nunito' => 'Nunito',
            'Nunito Sans' => 'Nunito Sans',
            'Odibee Sans' => 'Odibee Sans',
            'Odor Mean Chey' => 'Odor Mean Chey',
            'Offside' => 'Offside',
            'Old Standard TT' => 'Old Standard TT',
            'Oldenburg' => 'Oldenburg',
            'Oleo Script' => 'Oleo Script',
            'Oleo Script Swash Caps' => 'Oleo Script Swash Caps',
            'Open Sans' => 'Open Sans',
            'Open Sans Condensed' => 'Open Sans Condensed',
            'Oranienbaum' => 'Oranienbaum',
            'Orbitron' => 'Orbitron',
            'Oregano' => 'Oregano',
            'Orienta' => 'Orienta',
            'Original Surfer' => 'Original Surfer',
            'Oswald' => 'Oswald',
            'Over the Rainbow' => 'Over the Rainbow',
            'Overlock' => 'Overlock',
            'Overlock SC' => 'Overlock SC',
            'Overpass' => 'Overpass',
            'Overpass Mono' => 'Overpass Mono',
            'Ovo' => 'Ovo',
            'Oxanium' => 'Oxanium',
            'Oxygen' => 'Oxygen',
            'Oxygen Mono' => 'Oxygen Mono',
            'PT Mono' => 'PT Mono',
            'PT Sans' => 'PT Sans',
            'PT Sans Caption' => 'PT Sans Caption',
            'PT Sans Narrow' => 'PT Sans Narrow',
            'PT Serif' => 'PT Serif',
            'PT Serif Caption' => 'PT Serif Caption',
            'Pacifico' => 'Pacifico',
            'Padauk' => 'Padauk',
            'Palanquin' => 'Palanquin',
            'Palanquin Dark' => 'Palanquin Dark',
            'Pangolin' => 'Pangolin',
            'Paprika' => 'Paprika',
            'Parisienne' => 'Parisienne',
            'Passero One' => 'Passero One',
            'Passion One' => 'Passion One',
            'Pathway Gothic One' => 'Pathway Gothic One',
            'Patrick Hand' => 'Patrick Hand',
            'Patrick Hand SC' => 'Patrick Hand SC',
            'Pattaya' => 'Pattaya',
            'Patua One' => 'Patua One',
            'Pavanam' => 'Pavanam',
            'Paytone One' => 'Paytone One',
            'Peddana' => 'Peddana',
            'Peralta' => 'Peralta',
            'Permanent Marker' => 'Permanent Marker',
            'Petit Formal Script' => 'Petit Formal Script',
            'Petrona' => 'Petrona',
            'Philosopher' => 'Philosopher',
            'Piedra' => 'Piedra',
            'Pinyon Script' => 'Pinyon Script',
            'Pirata One' => 'Pirata One',
            'Plaster' => 'Plaster',
            'Play' => 'Play',
            'Playball' => 'Playball',
            'Playfair Display' => 'Playfair Display',
            'Playfair Display SC' => 'Playfair Display SC',
            'Podkova' => 'Podkova',
            'Poiret One' => 'Poiret One',
            'Poller One' => 'Poller One',
            'Poly' => 'Poly',
            'Pompiere' => 'Pompiere',
            'Pontano Sans' => 'Pontano Sans',
            'Poor Story' => 'Poor Story',
            'Poppins' => 'Poppins',
            'Port Lligat Sans' => 'Port Lligat Sans',
            'Port Lligat Slab' => 'Port Lligat Slab',
            'Pragati Narrow' => 'Pragati Narrow',
            'Prata' => 'Prata',
            'Preahvihear' => 'Preahvihear',
            'Press Start 2P' => 'Press Start 2P',
            'Pridi' => 'Pridi',
            'Princess Sofia' => 'Princess Sofia',
            'Prociono' => 'Prociono',
            'Prompt' => 'Prompt',
            'Prosto One' => 'Prosto One',
            'Proza Libre' => 'Proza Libre',
            'Public Sans' => 'Public Sans',
            'Puritan' => 'Puritan',
            'Purple Purse' => 'Purple Purse',
            'Quando' => 'Quando',
            'Quantico' => 'Quantico',
            'Quattrocento' => 'Quattrocento',
            'Quattrocento Sans' => 'Quattrocento Sans',
            'Questrial' => 'Questrial',
            'Quicksand' => 'Quicksand',
            'Quintessential' => 'Quintessential',
            'Qwigley' => 'Qwigley',
            'Racing Sans One' => 'Racing Sans One',
            'Radley' => 'Radley',
            'Rajdhani' => 'Rajdhani',
            'Rakkas' => 'Rakkas',
            'Raleway' => 'Raleway',
            'Raleway Dots' => 'Raleway Dots',
            'Ramabhadra' => 'Ramabhadra',
            'Ramaraja' => 'Ramaraja',
            'Rambla' => 'Rambla',
            'Rammetto One' => 'Rammetto One',
            'Ranchers' => 'Ranchers',
            'Rancho' => 'Rancho',
            'Ranga' => 'Ranga',
            'Rasa' => 'Rasa',
            'Rationale' => 'Rationale',
            'Ravi Prakash' => 'Ravi Prakash',
            'Red Hat Display' => 'Red Hat Display',
            'Red Hat Text' => 'Red Hat Text',
            'Redressed' => 'Redressed',
            'Reem Kufi' => 'Reem Kufi',
            'Reenie Beanie' => 'Reenie Beanie',
            'Revalia' => 'Revalia',
            'Rhodium Libre' => 'Rhodium Libre',
            'Ribeye' => 'Ribeye',
            'Ribeye Marrow' => 'Ribeye Marrow',
            'Righteous' => 'Righteous',
            'Risque' => 'Risque',
            'Roboto' => 'Roboto',
            'Roboto Condensed' => 'Roboto Condensed',
            'Roboto Mono' => 'Roboto Mono',
            'Roboto Slab' => 'Roboto Slab',
            'Rochester' => 'Rochester',
            'Rock Salt' => 'Rock Salt',
            'Rokkitt' => 'Rokkitt',
            'Romanesco' => 'Romanesco',
            'Ropa Sans' => 'Ropa Sans',
            'Rosario' => 'Rosario',
            'Rosarivo' => 'Rosarivo',
            'Rouge Script' => 'Rouge Script',
            'Rozha One' => 'Rozha One',
            'Rubik' => 'Rubik',
            'Rubik Mono One' => 'Rubik Mono One',
            'Ruda' => 'Ruda',
            'Rufina' => 'Rufina',
            'Ruge Boogie' => 'Ruge Boogie',
            'Ruluko' => 'Ruluko',
            'Rum Raisin' => 'Rum Raisin',
            'Ruslan Display' => 'Ruslan Display',
            'Russo One' => 'Russo One',
            'Ruthie' => 'Ruthie',
            'Rye' => 'Rye',
            'Sacramento' => 'Sacramento',
            'Sahitya' => 'Sahitya',
            'Sail' => 'Sail',
            'Saira' => 'Saira',
            'Saira Condensed' => 'Saira Condensed',
            'Saira Extra Condensed' => 'Saira Extra Condensed',
            'Saira Semi Condensed' => 'Saira Semi Condensed',
            'Saira Stencil One' => 'Saira Stencil One',
            'Salsa' => 'Salsa',
            'Sanchez' => 'Sanchez',
            'Sancreek' => 'Sancreek',
            'Sansita' => 'Sansita',
            'Sarabun' => 'Sarabun',
            'Sarala' => 'Sarala',
            'Sarina' => 'Sarina',
            'Sarpanch' => 'Sarpanch',
            'Satisfy' => 'Satisfy',
            'Sawarabi Gothic' => 'Sawarabi Gothic',
            'Sawarabi Mincho' => 'Sawarabi Mincho',
            'Scada' => 'Scada',
            'Scheherazade' => 'Scheherazade',
            'Schoolbell' => 'Schoolbell',
            'Scope One' => 'Scope One',
            'Seaweed Script' => 'Seaweed Script',
            'Secular One' => 'Secular One',
            'Sedgwick Ave' => 'Sedgwick Ave',
            'Sedgwick Ave Display' => 'Sedgwick Ave Display',
            'Sen' => 'Sen',
            'Sevillana' => 'Sevillana',
            'Seymour One' => 'Seymour One',
            'Shadows Into Light' => 'Shadows Into Light',
            'Shadows Into Light Two' => 'Shadows Into Light Two',
            'Shanti' => 'Shanti',
            'Share' => 'Share',
            'Share Tech' => 'Share Tech',
            'Share Tech Mono' => 'Share Tech Mono',
            'Shojumaru' => 'Shojumaru',
            'Short Stack' => 'Short Stack',
            'Shrikhand' => 'Shrikhand',
            'Siemreap' => 'Siemreap',
            'Sigmar One' => 'Sigmar One',
            'Signika' => 'Signika',
            'Signika Negative' => 'Signika Negative',
            'Simonetta' => 'Simonetta',
            'Single Day' => 'Single Day',
            'Sintony' => 'Sintony',
            'Sirin Stencil' => 'Sirin Stencil',
            'Six Caps' => 'Six Caps',
            'Skranji' => 'Skranji',
            'Slabo 13px' => 'Slabo 13px',
            'Slabo 27px' => 'Slabo 27px',
            'Slackey' => 'Slackey',
            'Smokum' => 'Smokum',
            'Smythe' => 'Smythe',
            'Sniglet' => 'Sniglet',
            'Snippet' => 'Snippet',
            'Snowburst One' => 'Snowburst One',
            'Sofadi One' => 'Sofadi One',
            'Sofia' => 'Sofia',
            'Solway' => 'Solway',
            'Song Myung' => 'Song Myung',
            'Sonsie One' => 'Sonsie One',
            'Sorts Mill Goudy' => 'Sorts Mill Goudy',
            'Source Code Pro' => 'Source Code Pro',
            'Source Sans Pro' => 'Source Sans Pro',
            'Source Serif Pro' => 'Source Serif Pro',
            'Space Mono' => 'Space Mono',
            'Spartan' => 'Spartan',
            'Special Elite' => 'Special Elite',
            'Spectral' => 'Spectral',
            'Spectral SC' => 'Spectral SC',
            'Spicy Rice' => 'Spicy Rice',
            'Spinnaker' => 'Spinnaker',
            'Spirax' => 'Spirax',
            'Squada One' => 'Squada One',
            'Sree Krushnadevaraya' => 'Sree Krushnadevaraya',
            'Sriracha' => 'Sriracha',
            'Srisakdi' => 'Srisakdi',
            'Staatliches' => 'Staatliches',
            'Stalemate' => 'Stalemate',
            'Stalinist One' => 'Stalinist One',
            'Stardos Stencil' => 'Stardos Stencil',
            'Stint Ultra Condensed' => 'Stint Ultra Condensed',
            'Stint Ultra Expanded' => 'Stint Ultra Expanded',
            'Stoke' => 'Stoke',
            'Strait' => 'Strait',
            'Stylish' => 'Stylish',
            'Sue Ellen Francisco' => 'Sue Ellen Francisco',
            'Suez One' => 'Suez One',
            'Sulphur Point' => 'Sulphur Point',
            'Sumana' => 'Sumana',
            'Sunflower' => 'Sunflower',
            'Sunshiney' => 'Sunshiney',
            'Supermercado One' => 'Supermercado One',
            'Sura' => 'Sura',
            'Suranna' => 'Suranna',
            'Suravaram' => 'Suravaram',
            'Suwannaphum' => 'Suwannaphum',
            'Swanky and Moo Moo' => 'Swanky and Moo Moo',
            'Syncopate' => 'Syncopate',
            'Tajawal' => 'Tajawal',
            'Tangerine' => 'Tangerine',
            'Taprom' => 'Taprom',
            'Tauri' => 'Tauri',
            'Taviraj' => 'Taviraj',
            'Teko' => 'Teko',
            'Telex' => 'Telex',
            'Tenali Ramakrishna' => 'Tenali Ramakrishna',
            'Tenor Sans' => 'Tenor Sans',
            'Text Me One' => 'Text Me One',
            'Thasadith' => 'Thasadith',
            'The Girl Next Door' => 'The Girl Next Door',
            'Tienne' => 'Tienne',
            'Tillana' => 'Tillana',
            'Timmana' => 'Timmana',
            'Tinos' => 'Tinos',
            'Titan One' => 'Titan One',
            'Titillium Web' => 'Titillium Web',
            'Tomorrow' => 'Tomorrow',
            'Trade Winds' => 'Trade Winds',
            'Trirong' => 'Trirong',
            'Trocchi' => 'Trocchi',
            'Trochut' => 'Trochut',
            'Trykker' => 'Trykker',
            'Tulpen One' => 'Tulpen One',
            'Turret Road' => 'Turret Road',
            'Ubuntu' => 'Ubuntu',
            'Ubuntu Condensed' => 'Ubuntu Condensed',
            'Ubuntu Mono' => 'Ubuntu Mono',
            'Ultra' => 'Ultra',
            'Uncial Antiqua' => 'Uncial Antiqua',
            'Underdog' => 'Underdog',
            'Unica One' => 'Unica One',
            'UnifrakturCook' => 'UnifrakturCook',
            'UnifrakturMaguntia' => 'UnifrakturMaguntia',
            'Unkempt' => 'Unkempt',
            'Unlock' => 'Unlock',
            'Unna' => 'Unna',
            'VT323' => 'VT323',
            'Vampiro One' => 'Vampiro One',
            'Varela' => 'Varela',
            'Varela Round' => 'Varela Round',
            'Vast Shadow' => 'Vast Shadow',
            'Vesper Libre' => 'Vesper Libre',
            'Viaoda Libre' => 'Viaoda Libre',
            'Vibes' => 'Vibes',
            'Vibur' => 'Vibur',
            'Vidaloka' => 'Vidaloka',
            'Viga' => 'Viga',
            'Voces' => 'Voces',
            'Volkhov' => 'Volkhov',
            'Vollkorn' => 'Vollkorn',
            'Vollkorn SC' => 'Vollkorn SC',
            'Voltaire' => 'Voltaire',
            'Waiting for the Sunrise' => 'Waiting for the Sunrise',
            'Wallpoet' => 'Wallpoet',
            'Walter Turncoat' => 'Walter Turncoat',
            'Warnes' => 'Warnes',
            'Wellfleet' => 'Wellfleet',
            'Wendy One' => 'Wendy One',
            'Wire One' => 'Wire One',
            'Work Sans' => 'Work Sans',
            'Yanone Kaffeesatz' => 'Yanone Kaffeesatz',
            'Yantramanav' => 'Yantramanav',
            'Yatra One' => 'Yatra One',
            'Yellowtail' => 'Yellowtail',
            'Yeon Sung' => 'Yeon Sung',
            'Yeseva One' => 'Yeseva One',
            'Yesteryear' => 'Yesteryear',
            'Yrsa' => 'Yrsa',
            'ZCOOL KuaiLe' => 'ZCOOL KuaiLe',
            'ZCOOL QingKe HuangYou' => 'ZCOOL QingKe HuangYou',
            'ZCOOL XiaoWei' => 'ZCOOL XiaoWei',
            'Zeyada' => 'Zeyada',
            'Zhi Mang Xing' => 'Zhi Mang Xing',
            'Zilla Slab' => 'Zilla Slab',
            'Zilla Slab Highlight' => 'Zilla Slab Highlight',
		);
		
		return apply_filters('bookyourtravel_google_fonts', $fonts);
	}
	
	public static function get_web_safe_family_choices() {
		$fonts = array();

		$fonts['Arial'] = 'Arial';
		$fonts['Arial Black'] = 'Arial Black';
		$fonts['Comic Sans MS'] = 'Comic Sans MS';
		$fonts['Courier New'] = 'Courier New';
		$fonts['Geneva'] = 'Geneva';
		$fonts['Georgia'] = 'Georgia';
		$fonts['Helvetica'] = 'Helvetica';
		$fonts['Impact'] = 'Impact';
		$fonts['Lucida Console'] = 'Lucida Console';
		$fonts['Lucida Sans Unicode'] = 'Lucida Sans Unicode';
		$fonts['Monaco'] = 'Monaco';
		$fonts['Palatino Linotype'] = 'Palatino Linotype';
		$fonts['Times New Roman'] = 'Times New Roman';
		$fonts['Tahoma'] = 'Tahoma';
		$fonts['Trebuchet MS'] = 'Trebuchet MS';
        $fonts['Verdana'] = 'Verdana';
		
		return apply_filters('bookyourtravel_web_safe_fonts', $fonts);
	}

	public static function is_web_safe_font($font_family) {
		$web_safe_fonts = self::get_web_safe_family_choices();

		return array_key_exists($font_family, $web_safe_fonts);
    }
   
}


function bookyourtravel_comment($comment, $args, $depth) {
	BookYourTravel_Theme_Utils::comment($comment, $args, $depth);
}

function bookyourtravel_comment_end($comment, $args, $depth) {
	BookYourTravel_Theme_Utils::comment_end($comment, $args, $depth);
}

if (!function_exists('bookyourtravel_footer_status')) {
	function bookyourtravel_footer_status() {
		if (WP_DEBUG) {
			$num_queries = get_num_queries();
			$timer = timer_stop(0);
			echo '<!-- ' . $num_queries . ' queries in ' . $timer . ' seconds. -->';
		}
	}
}
