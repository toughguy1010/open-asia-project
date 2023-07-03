<?php
//mimic the actuall admin-ajax
define('DOING_AJAX', true);

if (!isset( $_REQUEST['action']))
    die('-1');

$current_file_path_parsed = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $current_file_path_parsed[0] . 'wp-load.php' );

//make sure you update this line
//to the relative location of the wp-load.php
// require_once(ABSPATH  . '/wp-load.php');

//Typical headers
header('Content-Type: text/html');
send_nosniff_header();

//Disable caching
header('Cache-Control: no-cache');
header('Pragma: no-cache');

$action = esc_attr(trim($_REQUEST['action']));

// A bit of security
$allowed_actions = array(
    'car_rental_available_start_dates_ajax_request',
    'car_rental_available_end_dates_ajax_request',
	'car_rental_load_min_price_ajax_request',
	'car_rental_get_day_price_ajax_request',
	'accommodation_available_start_dates_ajax_request',
	'accommodation_available_end_dates_ajax_request',
	'accommodation_get_day_price_ajax_request',
	'accommodation_load_min_price_ajax_request',
	'tour_available_dates_ajax_request',
	'tour_get_day_price_ajax_request',
	'tour_load_min_price_ajax_request',
	'cruise_available_dates_ajax_request',
	'cruise_get_day_price_ajax_request',
    'cruise_load_min_price_ajax_request',
	'location_load_min_price_ajax_request'
);

if(in_array($action, $allowed_actions)){
    if(is_user_logged_in())
        do_action('byt_ajax_handler_' . $action);
    else
        do_action('byt_ajax_handler_nopriv_' . $action);
}
else{
    die('-1');
}