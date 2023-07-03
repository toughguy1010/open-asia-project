<?php
/**
 * Accommodation item template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $current_url, $guests, $rooms, $date_from, $date_to, $bookyourtravel_theme_globals, $bookyourtravel_accommodation_helper, $accommodation_list_args, $accommodation_item_args;

if (empty($current_url)) {
	$current_url = BookYourTravel_Theme_Utils::get_current_page_url_no_query();
}

$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
$list_user_accommodations_url = $bookyourtravel_theme_globals->get_list_user_accommodations_url();
$submit_user_accommodations_url = $bookyourtravel_theme_globals->get_submit_user_accommodations_url();

$display_mode = isset($accommodation_list_args['display_mode']) ? $accommodation_list_args['display_mode'] : 'card';
$item_class = isset($accommodation_item_args['item_class']) ? $accommodation_item_args['item_class'] : '';

$accommodation_id = $accommodation_item_args['accommodation_id'];
$accommodation_permalink = '';
$accommodation_description = '';
$accommodation_title = '';
$accommodation_thumbnail_html = '';
$accommodation_ribbon_text = '';
$accommodation_rent_type_str = '';
$accommodation_address = '';
$accommodation_base_id = 0;
$accommodation_status = '';
$accommodation_star_count = '';
$accommodation_price = 0;
$accommodation_review_score = 0;
$link_external = false;
$accommodation_use_referral_url = false;

if ($accommodation_id > 0) {
	$accommodation_post = $accommodation_item_args['post'];
	global $post;
	if (!$post || $post->ID != $accommodation_id) {
		$post = $accommodation_post;
		setup_postdata($post);
	}

	$accommodation_obj = new BookYourTravel_Accommodation($post);
	$accommodation_description = wpautop($accommodation_obj->get_short_description());
	$accommodation_ribbon_text = $accommodation_obj->get_ribbon_text();
	$accommodation_use_referral_url = $accommodation_obj->use_referral_url();
	$accommodation_referral_url = $accommodation_obj->get_referral_url();
	$accommodation_referral_price = $accommodation_obj->get_referral_price();

	if ($post) {
		$thumbnail_id = get_post_thumbnail_id($post->ID);
		$attachment = get_post($thumbnail_id);
		if ($attachment) {
			$image_title = $attachment->post_title; //The Title
			$accommodation_thumbnail_html = get_the_post_thumbnail($post->ID, "thumbnail", array('title' => $image_title));
		}
	}

	if ($accommodation_use_referral_url && !empty($accommodation_referral_url)) {
		$accommodation_permalink = $accommodation_referral_url;
		$accommodation_price = $accommodation_referral_price;
		$link_external = true;
	} else {
		$accommodation_permalink = get_the_permalink();

		if ($date_from || $date_to || $rooms || $guests) {
			if(strpos($accommodation_permalink,'?') !== false) {
				$accommodation_permalink .= sprintf("&from=%s&to=%s&guests=%d&rooms=%d", $date_from, $date_to, $guests, $rooms);
			} else {
				$accommodation_permalink .= sprintf("?from=%s&to=%s&guests=%d&rooms=%d", $date_from, $date_to, $guests, $rooms);
			}
		}

		if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
			$accommodation_price = $accommodation_obj->get_static_from_price();
		}
	}

	$accommodation_title = get_the_title();
	$accommodation_rent_type_str = $accommodation_obj->get_formatted_rent_type();
	$accommodation_address = $accommodation_obj->get_custom_field('address');
	$accommodation_base_id = $accommodation_obj->get_base_id();
	$accommodation_status = $accommodation_obj->get_status();
	$accommodation_star_count = $accommodation_obj->get_custom_field('star_count');
	$accommodation_review_score = $accommodation_obj->get_custom_field('review_score', false, true);

} else {
	$accommodation_description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit';
	$accommodation_title = 'Lorem ipsum';
	$dummy_image_source = BookYourTravel_Theme_Utils::get_file_uri('/images/uploads/img.jpg');
	$accommodation_thumbnail_html = sprintf('<img src="%s" alt="Lorem ipsum">', $dummy_image_source);
}

$item_extra_class = '';
if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
	$item_extra_class = 'skip-ajax-call';
}

if ($display_mode == 'card') {
	echo '<article ';
	// if (!$accommodation_use_referral_url) {
		echo 'data-accommodation-id="' . esc_attr($accommodation_id) . '"';
	// }
	echo ' class="accommodation_item ' . esc_attr($item_class) . ' ' . esc_attr($item_extra_class) . '">';
	echo '<div>';
} else {
	echo '<li ';
	// if (!$accommodation_use_referral_url) {
		echo 'data-accommodation-id="' . esc_attr($accommodation_id) . '"';
	// }
	echo ' class="accommodation_item ' . esc_attr($item_extra_class) . '">';
}

$action_label = esc_html__('Book now', 'bookyourtravel');

if ((!isset($accommodation_item_args['hide_image']) || !$accommodation_item_args['hide_image']) && !empty($accommodation_thumbnail_html)) {
	BookYourTravel_Theme_Controls::the_entity_figure_start($accommodation_title, $accommodation_permalink, $link_external);
	if (!isset($accommodation_item_args['hide_rating']) || !$accommodation_item_args['hide_rating']) {
		BookYourTravel_Theme_Controls::the_entity_reviews_score($accommodation_base_id, $accommodation_review_score);
	}
	BookYourTravel_Theme_Controls::the_entity_figure_middle($accommodation_thumbnail_html, $accommodation_ribbon_text);
	BookYourTravel_Theme_Controls::the_entity_figure_end($accommodation_permalink);
}

echo '<div class="details ' .
	((isset($accommodation_item_args['hide_title']) && $accommodation_item_args['hide_title']) ? "hide-title " : "") .
	((isset($accommodation_item_args['hide_actions']) && $accommodation_item_args['hide_actions']) ? "hide-actions " : "") .
	((isset($accommodation_item_args['hide_image']) && $accommodation_item_args['hide_image']) ? "hide-image " : "") .
	((isset($accommodation_item_args['hide_description']) && $accommodation_item_args['hide_description']) ? "hide-description " : "") .
	((isset($accommodation_item_args['hide_address']) && $accommodation_item_args['hide_address']) ? "hide-address " : "") .
	((isset($accommodation_item_args['hide_stars']) && $accommodation_item_args['hide_stars']) ? "hide-stars " : "") .
	((isset($accommodation_item_args['hide_rating']) && $accommodation_item_args['hide_rating']) ? "hide-rating " : "") .
	((isset($accommodation_item_args['hide_price']) && $accommodation_item_args['hide_price']) ? "hide-price " : "") .
	'">';

if (!isset($accommodation_item_args['hide_title']) || !$accommodation_item_args['hide_title'] || !isset($accommodation_item_args['hide_address']) || !$accommodation_item_args['hide_address']) {
	echo "<div class='item-header'>";
}

if (!isset($accommodation_item_args['hide_title']) || !$accommodation_item_args['hide_title']) {
	BookYourTravel_Theme_Controls::the_entity_title_start($accommodation_title, $accommodation_permalink, $link_external);
	BookYourTravel_Theme_Controls::the_entity_title_middle($accommodation_title, $accommodation_permalink, $accommodation_status);
	if (!isset($accommodation_item_args['hide_stars']) || !$accommodation_item_args['hide_stars']) {
		BookYourTravel_Theme_Controls::the_entity_stars($accommodation_star_count);
	}
	BookYourTravel_Theme_Controls::the_entity_title_end($accommodation_permalink);
}

if (!isset($accommodation_item_args['hide_address']) || !$accommodation_item_args['hide_address']) {
	BookYourTravel_Theme_Controls::the_entity_address($accommodation_address);
}

if (!isset($accommodation_item_args['hide_title']) || !$accommodation_item_args['hide_title'] || !isset($accommodation_item_args['hide_address']) || !$accommodation_item_args['hide_address']) {
	echo "</div>";
}

if (!isset($accommodation_item_args['hide_price']) || !$accommodation_item_args['hide_price']) {
	$price_style = "display:none";
	if ($accommodation_use_referral_url || ($bookyourtravel_theme_globals->show_static_prices_in_grids() && is_numeric($accommodation_price))) {
		$price_style = "";
	}
	BookYourTravel_Theme_Controls::the_entity_price($accommodation_price, esc_html__('From', 'bookyourtravel'), $price_style);
}

if (!isset($accommodation_item_args['hide_description']) || !$accommodation_item_args['hide_description']) {
	BookYourTravel_Theme_Controls::the_entity_description($accommodation_description);
}

if (!isset($accommodation_item_args['hide_actions']) || !$accommodation_item_args['hide_actions']) {
    if ($current_url == $list_user_accommodations_url) {
        $action_label = esc_html__('Edit', 'bookyourtravel');
        $accommodation_permalink = esc_url( add_query_arg( 'fesid', $accommodation_id, $submit_user_accommodations_url ));
    }

    echo "<div class='actions'>";
    if ($accommodation_id > 0) {
        BookYourTravel_Theme_Controls::the_link_button($accommodation_permalink, "gradient-button edit-entity", "", $action_label, true, $link_external, $accommodation_id);
        if (!empty($current_url) && $current_url == $list_user_accommodations_url) {
            BookYourTravel_Theme_Controls::the_link_button("#", "gradient-button fes-delete-entity delete-entity", "", __("Delete", "bookyourtravel"), true, false, $accommodation_id);
        }
    }
    echo "</div>";
}

echo '</div><!--//details-->';
echo '<a href="' . esc_url($accommodation_permalink). '" class="overlay-link"></a>';
if ($display_mode == 'card') {
	echo '</div>';
	echo '</article><!--//accommodation_item-->';
} else {
	echo '</li>';
}
