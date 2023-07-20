<?php
/**
 * Cruise item template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $current_url, $date_from, $date_to, $guests, $cabins, $bookyourtravel_theme_globals, $bookyourtravel_cruise_helper, $cruise_list_args, $cruise_item_args;

if (empty($current_url)) {
	$current_url = BookYourTravel_Theme_Utils::get_current_page_url_no_query();
}

$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
$list_user_cruises_url = $bookyourtravel_theme_globals->get_list_user_cruises_url();
$submit_user_cruises_url = $bookyourtravel_theme_globals->get_submit_user_cruises_url();

$display_mode = isset($cruise_list_args['display_mode']) ? $cruise_list_args['display_mode'] : 'card';
$item_class = isset($cruise_item_args['item_class']) ? $cruise_item_args['item_class'] : '';

$cruise_id = $cruise_item_args['cruise_id'];
$cruise_permalink = '';
$cruise_description = '';
$cruise_title = '';
$cruise_thumbnail_html = '';
$cruise_ribbon_text = '';
$cruise_base_id = 0;
$cruise_status = '';
$cruise_locations = '';
$cruise_price = '';
$cruise_review_score = 0;
$link_external = false;
$cruise_use_referral_url = false;

if ($cruise_id > 0) {
	$cruise_post = $cruise_item_args['post'];
	global $post;
	if (!$post || $post->ID != $cruise_id) {
		$post = $cruise_post;
		setup_postdata($post);
	}

	$cruise_obj = new BookYourTravel_Cruise($post);
	$cruise_description = wpautop($cruise_obj->get_short_description());
	$cruise_ribbon_text = $cruise_obj->get_ribbon_text();
	$cruise_use_referral_url = $cruise_obj->use_referral_url();
	$cruise_referral_url = $cruise_obj->get_referral_url();
	$cruise_referral_price = $cruise_obj->get_referral_price();

	$thumbnail_id = get_post_thumbnail_id($post->ID);
	$attachment = get_post($thumbnail_id);
	if ($attachment) {
		$image_title = $attachment->post_title; //The Title
		$cruise_thumbnail_html = get_the_post_thumbnail($post->ID, "thumbnail", array('title' => $image_title));
	}

	if ($cruise_use_referral_url && !empty($cruise_referral_url)) {
		$cruise_permalink = $cruise_referral_url;
		$cruise_price = $cruise_referral_price;
		$link_external = true;
	} else {
		$cruise_permalink = get_the_permalink();

		if ($date_from || $date_to || $cabins || $guests) {
			if(strpos($cruise_permalink,'?') !== false) {
				$cruise_permalink .= sprintf("&from=%s&to=%s&guests=%d&cabins=%d", $date_from, $date_to, $guests, $cabins);
			} else {
				$cruise_permalink .= sprintf("?from=%s&to=%s&guests=%d&cabins=%d", $date_from, $date_to, $guests, $cabins);
			}
		}

		if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
			$cruise_price = $cruise_obj->get_static_from_price();
		}
	}

	$cruise_title = get_the_title();
	$cruise_base_id = $cruise_obj->get_base_id();
	$cruise_status = $cruise_obj->get_status();
	$cruise_review_score = $cruise_obj->get_custom_field('review_score', false, true);
	$cruise_locations = $cruise_obj->get_formatted_locations();
	$cruise_address = $cruise_obj->get_custom_field('address');

} else {
	$cruise_description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit';
	$cruise_title = 'Lorem ipsum';
	$dummy_image_source = BookYourTravel_Theme_Utils::get_file_uri('/images/uploads/img.jpg');
	$cruise_thumbnail_html = sprintf('<img src="%s" alt="Lorem ipsum">', $dummy_image_source);
}

$item_extra_class = '';
if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
	$item_extra_class = 'skip-ajax-call';
}

if ($display_mode == 'card') {
	echo '<article ';
	// if (!$cruise_use_referral_url) {
		echo 'data-cruise-id="' . esc_attr($cruise_id) . '"';
	// }
	echo ' class="cruise_item ' . esc_attr($item_class) . ' ' . esc_attr($item_extra_class) . '">';
	echo '<div>';
} else {
	echo '<li ';
	// if (!$cruise_use_referral_url) {
		echo 'data-cruise-id="' . esc_attr($cruise_id) . '"';
	// }
	echo ' class="cruise_item' . esc_attr($item_extra_class) . '">';
}

$action_label = esc_html__('Book now', 'bookyourtravel');

if (!empty($cruise_thumbnail_html) && (!isset($cruise_item_args['hide_image']) || !$cruise_item_args['hide_image'])) {
	BookYourTravel_Theme_Controls::the_entity_figure_start($cruise_title, $cruise_permalink, $link_external);
	if (!isset($cruise_item_args['hide_rating']) || !$cruise_item_args['hide_rating']) {
		BookYourTravel_Theme_Controls::the_entity_reviews_score($cruise_base_id, $cruise_review_score);
	}
	BookYourTravel_Theme_Controls::the_entity_figure_middle($cruise_thumbnail_html, $cruise_ribbon_text);
	BookYourTravel_Theme_Controls::the_entity_figure_end($cruise_permalink);
}

echo '<div class="details ' .
	((isset($cruise_item_args['hide_title']) && $cruise_item_args['hide_title']) ? "hide-title " : "") .
	((isset($cruise_item_args['hide_actions']) && $cruise_item_args['hide_actions']) ? "hide-actions " : "") .
	((isset($cruise_item_args['hide_image']) && $cruise_item_args['hide_image']) ? "hide-image " : "") .
	((isset($cruise_item_args['hide_description']) && $cruise_item_args['hide_description']) ? "hide-description " : "") .
	((isset($cruise_item_args['hide_rating']) && $cruise_item_args['hide_rating']) ? "hide-rating " : "") .
	((isset($cruise_item_args['hide_price']) && $cruise_item_args['hide_price']) ? "hide-price " : "") .
	((isset($cruise_item_args['hide_address']) && $cruise_item_args['hide_address']) ? "hide-locations " : "") .
	'">';

if (!isset($cruise_item_args['hide_title']) || !$cruise_item_args['hide_title'] || !isset($cruise_item_args['hide_address']) || !$cruise_item_args['hide_address']) {
	echo "<div class='item-header'>";
}

if (!isset($cruise_item_args['hide_title']) || !$cruise_item_args['hide_title']) {
	BookYourTravel_Theme_Controls::the_entity_title($cruise_title, $cruise_permalink, $cruise_status, $link_external);
}

if (!isset($cruise_item_args['hide_address']) || !$cruise_item_args['hide_address']) {
	BookYourTravel_Theme_Controls::the_entity_address($cruise_address);
}

if (!isset($cruise_item_args['hide_title']) || !$cruise_item_args['hide_title'] || !isset($cruise_item_args['hide_address']) || !$cruise_item_args['hide_address']) {
	echo "</div>";
}

if (!isset($cruise_item_args['hide_price']) || !$cruise_item_args['hide_price']) {
	$price_style = "display:none";
	if ($cruise_use_referral_url || ($bookyourtravel_theme_globals->show_static_prices_in_grids() && is_numeric($cruise_price))) {
		$price_style = "";
	}
	BookYourTravel_Theme_Controls::the_entity_price($cruise_price, esc_html__('From', 'bookyourtravel'), $price_style);
}

if (!isset($cruise_item_args['hide_description']) || !$cruise_item_args['hide_description']) {
	BookYourTravel_Theme_Controls::the_entity_description($cruise_description);
}

if ($current_url == $list_user_cruises_url) {
	$action_label = esc_html__('Edit', 'bookyourtravel');
	$cruise_permalink = esc_url( add_query_arg( 'fesid', $cruise_id, $submit_user_cruises_url ));
}

if (!isset($cruise_item_args['hide_actions']) || !$cruise_item_args['hide_actions']) {
    echo "<div class='actions'>";
    if ($cruise_id > 0) {
        BookYourTravel_Theme_Controls::the_link_button($cruise_permalink, "gradient-button edit-entity", "", $action_label, true, $link_external, $cruise_id);
        if (!empty($current_url) && $current_url == $list_user_cruises_url) {
            BookYourTravel_Theme_Controls::the_link_button("#", "gradient-button fes-delete-entity delete-entity", "", __("Delete", "bookyourtravel"), true, false, $cruise_id);
        }
    }
    echo "</div>";
}

echo '</div><!--//details-->';
echo '<a href="' . esc_url($cruise_permalink). '" class="overlay-link"></a>';
if ($display_mode == 'card') {
	echo '</div>';
	echo '</article><!--//cruise_item-->';
} else {
	echo '</li>';
}
