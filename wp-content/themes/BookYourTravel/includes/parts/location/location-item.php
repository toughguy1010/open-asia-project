<?php
/**
 * Location item template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $current_url, $post, $location_item_args, $location_list_args, $bookyourtravel_theme_globals, $bookyourtravel_location_helper;

if (empty($current_url)) {
	$current_url = BookYourTravel_Theme_Utils::get_current_page_url_no_query();
}

$enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
$enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
$enable_tours = $bookyourtravel_theme_globals->enable_tours();
$enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();

$show_counts_in_location_items = $bookyourtravel_theme_globals->show_counts_in_location_items();

$show_accommodation_count_in_location_items = $enable_accommodations && isset($show_counts_in_location_items["accommodation"]) && $show_counts_in_location_items["accommodation"];
$show_cruise_count_in_location_items = $enable_cruises && isset($show_counts_in_location_items["cruise"]) && $show_counts_in_location_items["cruise"];
$show_tour_count_in_location_items = $enable_tours && isset($show_counts_in_location_items["tour"]) && $show_counts_in_location_items["tour"];
$show_car_rental_count_in_location_items = $enable_car_rentals && isset($show_counts_in_location_items["car_rental"]) && $show_counts_in_location_items["car_rental"];

$show_prices_in_location_items = $bookyourtravel_theme_globals->show_prices_in_location_items();
$show_accommodation_prices_in_location_items = $enable_accommodations && isset($show_prices_in_location_items['accommodation']) && $show_prices_in_location_items['accommodation'];
$show_car_rental_prices_in_location_items = $enable_car_rentals && isset($show_prices_in_location_items['car_rental']) && $show_prices_in_location_items['car_rental'];
$show_cruise_prices_in_location_items = $enable_cruises && isset($show_prices_in_location_items['cruise']) && $show_prices_in_location_items['cruise'];
$show_tour_prices_in_location_items = $enable_tours && isset($show_prices_in_location_items['tour']) && $show_prices_in_location_items['tour'];

$location_id = $location_item_args['location_id'];
$location_permalink = '';
$location_description = '';
$location_title = '';
$location_status = '';
$location_thumbnail_html = '';
$location_ribbon_text = '';

$location_obj = null;

if ($location_id > 0) {
	$location_post = $location_item_args['post'];
	global $post;
	if ($post->ID != $location_id) {
		$post = $location_post;
		setup_postdata($post);
	}

	$location_obj = new BookYourTravel_Location($post);
	$location_description = wpautop($location_obj->get_short_description());
	$location_ribbon_text = $location_obj->get_ribbon_text();
	$location_permalink = get_the_permalink();
	$location_title = get_the_title();
	$location_status = $location_obj->get_status();

	$thumbnail_id = get_post_thumbnail_id($post->ID);
	$attachment = get_post($thumbnail_id);
	if ($attachment) {
		$image_title = $attachment->post_title; //The Title
		$location_thumbnail_html = get_the_post_thumbnail($post->ID, "thumbnail", array('title' => $image_title));
	}
} else {
	$location_description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit';
	$location_title = 'Lorem ipsum';
	$dummy_image_source = BookYourTravel_Theme_Utils::get_file_uri('/images/uploads/img.jpg');
	$location_thumbnail_html = sprintf('<img src="%s" alt="Lorem ipsum">', $dummy_image_source);
}

$accommodation_count = $cruise_count = $tour_count = $car_rental_count = 0;

if ($location_obj != null && $location_id > 0) {
	if ($show_accommodation_count_in_location_items) {
		$accommodation_count = (int)$location_obj->get_accommodation_count();
	}

	if ($show_cruise_count_in_location_items) {
		$cruise_count = (int)$location_obj->get_cruise_count();
	}

	if ($show_tour_count_in_location_items) {
		$tour_count = (int)$location_obj->get_tour_count();
	}

	if ($show_car_rental_count_in_location_items) {
		$car_rental_count = (int)$location_obj->get_car_rental_count();
	}
}

$list_user_locations_url = $bookyourtravel_theme_globals->get_list_user_locations_url();
$submit_user_locations_url = $bookyourtravel_theme_globals->get_submit_user_locations_url();

$action_label = esc_html__('View all', 'bookyourtravel');

$item_class = isset($location_item_args['item_class']) ? $location_item_args['item_class'] : '';
$display_mode = isset($location_list_args['display_mode']) ? $location_list_args['display_mode'] : 'card';

if ($display_mode == 'card') {
	echo '<article class="location_item ' . esc_attr($item_class) . '">';
	echo '<div>';
} else {
	echo '<li>';
}

if (!empty($location_thumbnail_html) && (!isset($location_item_args['hide_image']) || !$location_item_args['hide_image'])) {
	BookYourTravel_Theme_Controls::the_entity_figure($location_title, $location_permalink, $location_thumbnail_html, $location_ribbon_text);
}

echo '<div class="details ' .
	((isset($location_item_args['hide_title']) && $location_item_args['hide_title']) ? "hide-title " : "") .
	((isset($location_item_args['hide_actions']) && $location_item_args['hide_actions']) ? "hide-actions " : "") .
	((isset($location_item_args['hide_image']) && $location_item_args['hide_image']) ? "hide-image " : "") .
	((isset($location_item_args['hide_description']) && $location_item_args['hide_description']) ? "hide-description " : "") .
	((isset($location_item_args['hide_counts']) && $location_item_args['hide_counts']) ? "hide-counts " : "") .
	((isset($location_item_args['hide_ribbon']) && $location_item_args['hide_ribbon']) ? "hide-ribbon " : "") .
    '">';

if (!isset($location_item_args['hide_title']) || !$location_item_args['hide_title']) {
	echo "<div class='item-header'>";
	BookYourTravel_Theme_Controls::the_entity_title($location_title, $location_permalink, $location_status);
	echo "</div>";
}

if (!isset($location_item_args['hide_description']) || !$location_item_args['hide_description']) {
	BookYourTravel_Theme_Controls::the_entity_description($location_description);
}

if (!isset($location_item_args['hide_counts']) || !$location_item_args['hide_counts']) {
	if ($show_accommodation_count_in_location_items || $show_tour_count_in_location_items || $show_cruise_count_in_location_items || $show_car_rental_count_in_location_items) {
		echo '<div class="counts">';
		if ($show_accommodation_count_in_location_items) {
			BookYourTravel_Theme_Of_Custom::the_field_inner("", "count accommodation_count", $accommodation_count . " " . esc_html__('Accommodations', 'bookyourtravel'), '', '', false, false, false);
		}
		if ($show_tour_count_in_location_items) {
			BookYourTravel_Theme_Of_Custom::the_field_inner("", "count tour_count", $tour_count . " " . esc_html__('Tours', 'bookyourtravel'), '', '', false, false);
		}
		if ($show_cruise_count_in_location_items) {
			BookYourTravel_Theme_Of_Custom::the_field_inner("", "count cruise_count", $cruise_count . " " . esc_html__('Cruises', 'bookyourtravel'), '', '', false, false);
		}
		if ($show_car_rental_count_in_location_items) {
			BookYourTravel_Theme_Of_Custom::the_field_inner("", "count car_rental_count", $car_rental_count . " " . esc_html__('Car rentals', 'bookyourtravel'), '', '', false, false);
		}
		echo '</div><!--counts-->';
	}
}

if (!isset($location_item_args['hide_ribbon']) || !$location_item_args['hide_ribbon']) {
	if ($show_tour_prices_in_location_items || $show_cruise_prices_in_location_items || $show_car_rental_prices_in_location_items || $show_accommodation_prices_in_location_items) {
		BookYourTravel_Theme_Controls::the_price_ribbon($location_id, $location_permalink, $show_accommodation_prices_in_location_items, $show_car_rental_prices_in_location_items, $show_cruise_prices_in_location_items, $show_tour_prices_in_location_items);
	}
}

if (!empty($current_url) && $current_url == $list_user_locations_url) {
	$action_label = esc_html__('Edit', 'bookyourtravel');
	$location_permalink = esc_url( add_query_arg( 'fesid', $location_id, $submit_user_locations_url ));
}

if (!isset($location_item_args['hide_actions']) || !$location_item_args['hide_actions']) {
    echo "<div class='actions'>";
    if ($location_id > 0) {
        BookYourTravel_Theme_Controls::the_link_button($location_permalink, "gradient-button edit-entity", "", $action_label, true, false, $location_id);
        if (!empty($current_url) && $current_url == $list_user_locations_url) {
            BookYourTravel_Theme_Controls::the_link_button("#", "gradient-button fes-delete-entity delete-entity", "", __("Delete", "bookyourtravel"), true, false, $location_id);
        }
    }
    echo "</div>";
}

echo '</div><!--//details-->';
echo '<a href="' . esc_url($location_permalink). '" class="overlay-link"></a>';

if ($display_mode == 'card') {
	echo '</div>';
	echo '</article><!--//location_item-->';
} else {
	echo '</li>';
}
