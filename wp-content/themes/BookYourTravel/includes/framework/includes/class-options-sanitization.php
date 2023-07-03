<?php
/**
 * @package   Options_Framework
 * @author    Devin Price <devin@wptheming.com>
 * @license   GPL-2.0+
 * @link      http://wptheming.com
 * @copyright 2010-2014 WP Theming
 */

/**
 * Sanitization for text input
 *
 * @link http://developer.wordpress.org/reference/functions/sanitize_text_field/
 */
add_filter( 'of_sanitize_text', 'sanitize_text_field' );

/**
 * Sanitization for password input
 *
 * @link http://developer.wordpress.org/reference/functions/sanitize_text_field/
 */
add_filter( 'of_sanitize_password', 'sanitize_text_field' );

/**
 * Sanitization for select input
 *
 * Validates that the selected option is a valid option.
 */
add_filter( 'of_sanitize_select', 'of_sanitize_enum', 10, 2 );

/**
 * Sanitization for radio input
 *
 * Validates that the selected option is a valid option.
 */
add_filter( 'of_sanitize_radio', 'of_sanitize_enum', 10, 2 );

/**
 * Sanitization for image selector
 *
 * Validates that the selected option is a valid option.
 */
add_filter( 'of_sanitize_images', 'of_sanitize_enum', 10, 2 );

/**
 * Sanitization for textarea field
 *
 * @param $input string
 * @return $output sanitized string
 */
function of_sanitize_textarea( $input ) {
	global $allowedposttags;
	$output = wp_kses( $input, $allowedposttags );
	return $output;
}
add_filter( 'of_sanitize_textarea', 'of_sanitize_textarea' );

/**
 * Sanitization for checkbox input
 *
 * @param $input string (1 or empty) checkbox state
 * @return $output '1' or false
 */
function of_sanitize_checkbox( $input ) {
	if ( $input ) {
		$output = '1';
	} else {
		$output = false;
	}
	return $output;
}
add_filter( 'of_sanitize_checkbox', 'of_sanitize_checkbox' );

/**
 * Sanitization for multicheck
 *
 * @param array of checkbox values
 * @return array of sanitized values ('1' or false)
 */
function of_sanitize_multicheck( $input, $option ) {
	$output = array();
	if ( is_array( $input ) ) {
		foreach( $option['options'] as $key => $value ) {
			$output[$key] = false;
		}
		foreach( $input as $key => $value ) {
			if ( array_key_exists( $key, $option['options'] ) && $value ) {
				$output[$key] = '1';
			}
		}
	}
	return $output;
}
add_filter( 'of_sanitize_multicheck', 'of_sanitize_multicheck', 10, 2 );

/**
 * File upload sanitization.
 *
 * Returns a sanitized filepath if it has a valid extension.
 *
 * @param string $input filepath
 * @returns string $output filepath
 */
function of_sanitize_upload( $input ) {
	$output = '';
	$filetype = wp_check_filetype( $input );
	if ( $filetype["ext"] ) {
		$output = esc_url( $input );
	}
	return $output;
}
add_filter( 'of_sanitize_upload', 'of_sanitize_upload' );

/**
 * Sanitization for editor input.
 *
 * Returns unfiltered HTML if user has permissions.
 *
 * @param string $input
 * @returns string $output
 */
function of_sanitize_editor( $input ) {
	if ( current_user_can( 'unfiltered_html' ) ) {
		$output = $input;
	}
	else {
		global $allowedposttags;
		$output = wpautop( wp_kses( $input, $allowedposttags ) );
	}
	return $output;
}
add_filter( 'of_sanitize_editor', 'of_sanitize_editor' );

/**
 * Sanitization of input with allowed tags and wpautotop.
 *
 * Allows allowed tags in html input and ensures tags close properly.
 *
 * @param string $input
 * @returns string $output
 */
function of_sanitize_allowedtags( $input ) {
	global $allowedtags;
	$output = wpautop( wp_kses( $input, $allowedtags ) );
	return $output;
}

/**
 * Sanitization of input with allowed post tags and wpautotop.
 *
 * Allows allowed post tags in html input and ensures tags close properly.
 *
 * @param string $input
 * @returns string $output
 */
function of_sanitize_allowedposttags( $input ) {
	global $allowedposttags;
	$output = wpautop( wp_kses( $input, $allowedposttags) );
	return $output;
}

/**
 * Validates that the $input is one of the avilable choices
 * for that specific option.
 *
 * @param string $input
 * @returns string $output
 */
function of_sanitize_enum( $input, $option ) {
	$output = '';
	if ( array_key_exists( $input, $option['options'] ) ) {
		$output = $input;
	}
	return $output;
}

/**
 * Sanitization for background option.
 *
 * @returns array $output
 */
function of_sanitize_background( $input ) {

	$output = wp_parse_args( $input, array(
		'color' => '',
		'image'  => '',
		'repeat'  => 'repeat',
		'position' => 'top center',
		'attachment' => 'scroll'
	) );

	$output['color'] = apply_filters( 'of_sanitize_hex', $input['color'] );
	$output['image'] = apply_filters( 'of_sanitize_upload', $input['image'] );
	$output['repeat'] = apply_filters( 'of_background_repeat', $input['repeat'] );
	$output['position'] = apply_filters( 'of_background_position', $input['position'] );
	$output['attachment'] = apply_filters( 'of_background_attachment', $input['attachment'] );

	return $output;
}
add_filter( 'of_sanitize_background', 'of_sanitize_background' );

/**
 * Sanitization for background repeat
 *
 * @returns string $value if it is valid
 */
function of_sanitize_background_repeat( $value ) {
	$recognized = of_recognized_background_repeat();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'of_default_background_repeat', current( $recognized ) );
}
add_filter( 'of_background_repeat', 'of_sanitize_background_repeat' );

/**
 * Sanitization for background position
 *
 * @returns string $value if it is valid
 */
function of_sanitize_background_position( $value ) {
	$recognized = of_recognized_background_position();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'of_default_background_position', current( $recognized ) );
}
add_filter( 'of_background_position', 'of_sanitize_background_position' );

/**
 * Sanitization for background attachment
 *
 * @returns string $value if it is valid
 */
function of_sanitize_background_attachment( $value ) {
	$recognized = of_recognized_background_attachment();
	if ( array_key_exists( $value, $recognized ) ) {
		return $value;
	}
	return apply_filters( 'of_default_background_attachment', current( $recognized ) );
}
add_filter( 'of_background_attachment', 'of_sanitize_background_attachment' );

/**
 * Get recognized background repeat settings
 *
 * @return   array
 */
function of_recognized_background_repeat() {
	$default = array(
		'no-repeat' => __( 'No Repeat', 'bookyourtravel' ),
		'repeat-x'  => __( 'Repeat Horizontally', 'bookyourtravel' ),
		'repeat-y'  => __( 'Repeat Vertically', 'bookyourtravel' ),
		'repeat'    => __( 'Repeat All', 'bookyourtravel' ),
		);
	return apply_filters( 'of_recognized_background_repeat', $default );
}

/**
 * Get recognized background positions
 *
 * @return   array
 */
function of_recognized_background_position() {
	$default = array(
		'top left'      => __( 'Top Left', 'bookyourtravel' ),
		'top center'    => __( 'Top Center', 'bookyourtravel' ),
		'top right'     => __( 'Top Right', 'bookyourtravel' ),
		'center left'   => __( 'Middle Left', 'bookyourtravel' ),
		'center center' => __( 'Middle Center', 'bookyourtravel' ),
		'center right'  => __( 'Middle Right', 'bookyourtravel' ),
		'bottom left'   => __( 'Bottom Left', 'bookyourtravel' ),
		'bottom center' => __( 'Bottom Center', 'bookyourtravel' ),
		'bottom right'  => __( 'Bottom Right', 'bookyourtravel')
		);
	return apply_filters( 'of_recognized_background_position', $default );
}

/**
 * Get recognized background attachment
 *
 * @return   array
 */
function of_recognized_background_attachment() {
	$default = array(
		'scroll' => __( 'Scroll Normally', 'bookyourtravel' ),
		'fixed'  => __( 'Fixed in Place', 'bookyourtravel')
		);
	return apply_filters( 'of_recognized_background_attachment', $default );
}

/**
 * Sanitize a color represented in hexidecimal notation.
 *
 * @param    string    Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @param    string    The value that this function should return if it cannot be recognized as a color.
 * @return   string
 */

function of_sanitize_hex( $hex, $default = '' ) {
	if ( of_validate_hex( $hex ) ) {
		return $hex;
	}
	return $default;
}
add_filter( 'of_sanitize_color', 'of_sanitize_hex' );

/**
 * Get recognized font sizes.
 *
 * Returns an indexed array of all recognized font sizes.
 * Values are integers and represent a range of sizes from
 * smallest to largest.
 *
 * @return   array
 */

function of_recognized_font_sizes() {
	$sizes = range( 9, 71 );
	$sizes = apply_filters( 'of_recognized_font_sizes', $sizes );
	$sizes = array_map( 'absint', $sizes );
	return $sizes;
}




/**
 * Get recognized font styles.
 *
 * Returns an array of all recognized font styles.
 * Keys are intended to be stored in the database
 * while values are ready for display in in html.
 *
 * @return   array
 */
function of_recognized_font_styles() {
	$default = array(
		'normal'      => __( 'Normal', 'bookyourtravel' ),
		'italic'      => __( 'Italic', 'bookyourtravel' ),
		'bold'        => __( 'Bold', 'bookyourtravel' ),
		'bold italic' => __( 'Bold Italic', 'bookyourtravel' )
		);
	return apply_filters( 'of_recognized_font_styles', $default );
}

/**
 * Is a given string a color formatted in hexidecimal notation?
 *
 * @param    string    Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @return   bool
 */
function of_validate_hex( $hex ) {
	$hex = trim( $hex );
	/* Strip recognized prefixes. */
	if ( 0 === strpos( $hex, '#' ) ) {
		$hex = substr( $hex, 1 );
	}
	elseif ( 0 === strpos( $hex, '%23' ) ) {
		$hex = substr( $hex, 3 );
	}
	/* Regex match. */
	if ( 0 === preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) {
		return false;
	}
	else {
		return true;
	}
}