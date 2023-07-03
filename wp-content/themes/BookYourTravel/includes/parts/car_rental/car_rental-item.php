<?php
/**
 * Car Rental item template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $current_url, $date_from, $date_to, $bookyourtravel_theme_globals, $bookyourtravel_car_rental_helper, $car_rental_list_args, $car_rental_item_args;

if (empty($current_url)) {
	$current_url = BookYourTravel_Theme_Utils::get_current_page_url_no_query();
}

$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
$list_user_car_rentals_url = $bookyourtravel_theme_globals->get_list_user_car_rentals_url();
$submit_user_car_rentals_url = $bookyourtravel_theme_globals->get_submit_user_car_rentals_url();

$display_mode = isset($car_rental_list_args['display_mode']) ? $car_rental_list_args['display_mode'] : 'card';
$item_class = isset($car_rental_item_args['item_class']) ? $car_rental_item_args['item_class'] : '';

$car_rental_id = $car_rental_item_args['car_rental_id'];

$car_rental_permalink = '';
$car_rental_description = '';
$car_rental_title = '';
$car_rental_thumbnail_html = '';
$car_rental_ribbon_text = '';
$car_rental_base_id = 0;
$car_rental_status = '';
$car_rental_price = '';
$car_rental_review_score = 0;
$link_external = false;
$car_rental_use_referral_url = false;
$car_rental_address = "";

if ($car_rental_id > 0) {
	$car_rental_post = $car_rental_item_args['post'];
	global $post;
	if (!$post || $post->ID != $car_rental_id) {
		$post = $car_rental_post;
		setup_postdata($post);
	}

	$car_rental_obj = new BookYourTravel_Car_Rental($post);
	$car_rental_description = wpautop($car_rental_obj->get_short_description());
	$car_rental_ribbon_text = $car_rental_obj->get_ribbon_text();
	$car_rental_use_referral_url = $car_rental_obj->use_referral_url();
	$car_rental_referral_url = $car_rental_obj->get_referral_url();
	$car_rental_referral_price = $car_rental_obj->get_referral_price();

	$thumbnail_id = get_post_thumbnail_id($post->ID);
	$attachment = get_post($thumbnail_id);
	if ($attachment) {
		$image_title = $attachment->post_title; //The Title
		$car_rental_thumbnail_html = get_the_post_thumbnail($post->ID, "thumbnail", array('title' => $image_title));
	}

	if ($car_rental_use_referral_url && !empty($car_rental_referral_url)) {
		$car_rental_permalink = $car_rental_referral_url;
		$car_rental_price = $car_rental_referral_price;
		$link_external = true;
	} else {
		$car_rental_permalink = get_the_permalink();

		if ($date_from || $date_to) {
			if(strpos($car_rental_permalink,'?') !== false) {
				$car_rental_permalink .= sprintf("&from=%s&to=%s", $date_from, $date_to);
			} else {
				$car_rental_permalink .= sprintf("?from=%s&to=%s", $date_from, $date_to);
			}
        }

        $request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('l', true);
        if (count($request_values) > 0) {

            $l_part = "";
            foreach ($request_values as $key => $l) {
                $l_part .= sprintf("l[]=%d&", $l);
            }
            $l_part = rtrim($l_part, "&");

            if(strpos($car_rental_permalink,'?') !== false) {
                $car_rental_permalink .= sprintf("&%s", $l_part);
            } else {
                $car_rental_permalink .= sprintf("?%s", $l_part);
            }
        }

		if (isset($post->car_rental_price)) {
			$car_rental_price = $post->car_rental_price;
		}

		if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
			$car_rental_price = $car_rental_obj->get_static_from_price();
		}
	}

	$car_rental_title = get_the_title();
	$car_rental_address = $car_rental_obj->get_custom_field('address');
	$car_rental_base_id = $car_rental_obj->get_base_id();
	$car_rental_status = $car_rental_obj->get_status();
	$car_rental_review_score = $car_rental_obj->get_custom_field('review_score', false, true);

} else {
	$car_rental_description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit';
	$car_rental_title = 'Lorem ipsum';
	$dummy_image_source = BookYourTravel_Theme_Utils::get_file_uri('/images/uploads/img.jpg');
	$car_rental_thumbnail_html = sprintf('<img src="%s" alt="Lorem ipsum">', $dummy_image_source);
}

$item_extra_class = '';
if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
	$item_extra_class = 'skip-ajax-call';
}

if ($display_mode == 'card') {
	echo '<article ';
	// if (!$car_rental_use_referral_url) {
		echo 'data-car-rental-id="' . esc_attr($car_rental_id) . '"';
	// }
	echo ' class="car_rental_item ' . esc_attr($item_class) . ' ' . esc_attr($item_extra_class) . '">';
	echo '<div>';
} else {
	echo '<li ';
	// if (!$car_rental_use_referral_url) {
		echo 'data-car-rental-id="' . esc_attr($car_rental_id) . '"';
	// }
	echo ' class="car_rental_item ' . esc_attr($item_extra_class) . '">';
}

$action_label = esc_html__('Book now', 'bookyourtravel');

if ((!isset($car_rental_item_args['hide_image']) || !$car_rental_item_args['hide_image']) && !empty($car_rental_thumbnail_html)) {
	BookYourTravel_Theme_Controls::the_entity_figure_start($car_rental_title, $car_rental_permalink, $link_external);
	if (!isset($car_rental_item_args['hide_rating']) || !$car_rental_item_args['hide_rating']) {
		BookYourTravel_Theme_Controls::the_entity_reviews_score($car_rental_base_id, $car_rental_review_score);
	}
	BookYourTravel_Theme_Controls::the_entity_figure_middle($car_rental_thumbnail_html, $car_rental_ribbon_text);
	BookYourTravel_Theme_Controls::the_entity_figure_end($car_rental_permalink);
}

echo '<div class="details ' .
	((isset($car_rental_item_args['hide_title']) && $car_rental_item_args['hide_title']) ? "hide-title " : "") .
	((isset($car_rental_item_args['hide_actions']) && $car_rental_item_args['hide_actions']) ? "hide-actions " : "") .
	((isset($car_rental_item_args['hide_image']) && $car_rental_item_args['hide_image']) ? "hide-image " : "") .
	((isset($car_rental_item_args['hide_description']) && $car_rental_item_args['hide_description']) ? "hide-description " : "") .
	((isset($car_rental_item_args['hide_rating']) && $car_rental_item_args['hide_rating']) ? "hide-rating " : "") .
	((isset($car_rental_item_args['hide_price']) && $car_rental_item_args['hide_price']) ? "hide-price " : "") .
	((isset($car_rental_item_args['hide_address']) && $car_rental_item_args['hide_address']) ? "hide-address " : "") .
	'">';

if (!isset($car_rental_item_args['hide_title']) || !$car_rental_item_args['hide_title'] || !isset($car_rental_item_args['hide_address']) || !$car_rental_item_args['hide_address']) {
	echo "<div class='item-header'>";
}

if (!isset($car_rental_item_args['hide_title']) || !$car_rental_item_args['hide_title']) {
	BookYourTravel_Theme_Controls::the_entity_title($car_rental_title, $car_rental_permalink, $car_rental_status, $link_external);
}

if (!isset($car_rental_item_args['hide_address']) || !$car_rental_item_args['hide_address']) {
	BookYourTravel_Theme_Controls::the_entity_address($car_rental_address);
}

if (!isset($car_rental_item_args['hide_title']) || !$car_rental_item_args['hide_title'] || !isset($car_rental_item_args['hide_address']) || !$car_rental_item_args['hide_address']) {
	echo "</div>";
}

if (!isset($car_rental_item_args['hide_price']) || !$car_rental_item_args['hide_price']) {
	$price_style = "display:none";
	if ($car_rental_use_referral_url || ($bookyourtravel_theme_globals->show_static_prices_in_grids() && is_numeric($car_rental_price))) {
		$price_style = "";
	}
	BookYourTravel_Theme_Controls::the_entity_price($car_rental_price, esc_html__('From', 'bookyourtravel'), $price_style);
}

if (!isset($car_rental_item_args['hide_description']) || !$car_rental_item_args['hide_description']) {
	BookYourTravel_Theme_Controls::the_entity_description($car_rental_description);
}

if ($current_url == $list_user_car_rentals_url) {
	$action_label = esc_html__('Edit', 'bookyourtravel');
	$car_rental_permalink = esc_url( add_query_arg( 'fesid', $car_rental_id, $submit_user_car_rentals_url ));
}

if (!isset($car_rental_item_args['hide_actions']) || !$car_rental_item_args['hide_actions']) {
    echo "<div class='actions'>";
    if ($car_rental_id > 0) {
        BookYourTravel_Theme_Controls::the_link_button($car_rental_permalink, "gradient-button edit-entity", "", $action_label, true, $link_external, $car_rental_id);
        if (!empty($current_url) && $current_url == $list_user_car_rentals_url) {
            BookYourTravel_Theme_Controls::the_link_button("#", "gradient-button fes-delete-entity delete-entity", "", __("Delete", "bookyourtravel"), true, false, $car_rental_id);
        }
    }
    echo "</div>";
}

echo '</div><!--//details-->';
echo '<a href="' . esc_url($car_rental_permalink). '" class="overlay-link"></a>';
if ($display_mode == 'card') {
	echo '</div>';
	echo '</article><!--//car_rental_item-->';
} else {
	echo '</li>';
}
