<?php
/**
 * Tour item template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $current_url, $post, $date_from, $date_to, $bookyourtravel_theme_globals, $bookyourtravel_tour_helper, $tour_list_args, $tour_item_args;

if (empty($current_url)) {
	$current_url = BookYourTravel_Theme_Utils::get_current_page_url_no_query();
}

$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
$list_user_tours_url = $bookyourtravel_theme_globals->get_list_user_tours_url();
$submit_user_tours_url = $bookyourtravel_theme_globals->get_submit_user_tours_url();

$display_mode = isset($tour_list_args['display_mode']) ? $tour_list_args['display_mode'] : 'card';
$item_class = isset($tour_item_args['item_class']) ? $tour_item_args['item_class'] : '';

$tour_id = $tour_item_args['tour_id'];
$tour_permalink = '';
$tour_description = '';
$tour_title = '';
$tour_thumbnail_html = '';
$tour_ribbon_text = '';
$tour_locations = '';
$tour_base_id = 0;
$tour_status = '';
$tour_price = '';
$tour_review_score = 0;
$link_external = false;
$tour_use_referral_url = false;

if ($tour_id > 0) {
	$tour_post = $tour_item_args['post'];
	global $post;
	if (!$post || $post->ID != $tour_id) {
		$post = $tour_post;
		setup_postdata($post);
	}

	$tour_obj = new BookYourTravel_Tour($post);
	$tour_description = wpautop($tour_obj->get_short_description());
	$tour_ribbon_text = $tour_obj->get_ribbon_text();
	$tour_use_referral_url = $tour_obj->use_referral_url();
	$tour_referral_url = $tour_obj->get_referral_url();
	$tour_referral_price = $tour_obj->get_referral_price();

	if ($post) {
		$thumbnail_id = get_post_thumbnail_id($post->ID);
		$attachment = get_post($thumbnail_id);
		if ($attachment) {
			$image_title = $attachment->post_title; //The Title
			$tour_thumbnail_html = get_the_post_thumbnail($post->ID, "thumbnail", array('title' => $image_title));
		}
	}

	if ($tour_use_referral_url && !empty($tour_referral_url)) {
		$tour_permalink = $tour_referral_url;
		$tour_price = $tour_referral_price;
		$link_external = true;
	} else {
		$tour_permalink = get_the_permalink();

		if ($date_from || $date_to) {
			if(strpos($tour_permalink,'?') !== false) {
				$tour_permalink .= sprintf("&from=%s&to=%s", $date_from, $date_to);
			} else {
				$tour_permalink .= sprintf("?from=%s&to=%s", $date_from, $date_to);
			}
		}

		if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
			$tour_price = $tour_obj->get_static_from_price();
		}
	}

	$tour_title = get_the_title();
	$tour_address = $tour_obj->get_custom_field('address');
	$tour_base_id = $tour_obj->get_base_id();
	$tour_status = $tour_obj->get_status();
	$tour_review_score = $tour_obj->get_custom_field('review_score', false, true);

} else {
	$tour_description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit';
	$tour_title = 'Lorem ipsum';
	$dummy_image_source = BookYourTravel_Theme_Utils::get_file_uri('/images/uploads/img.jpg');
	$tour_thumbnail_html = sprintf('<img src="%s" alt="Lorem ipsum">', $dummy_image_source);
}

$action_label = esc_html__('Book now', 'bookyourtravel');

$item_extra_class = '';
if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
	$item_extra_class = 'skip-ajax-call';
}

if ($display_mode == 'card') {
	echo '<article ';
	// if (!$tour_use_referral_url) {
		echo 'data-tour-id="' . esc_attr($tour_id) . '"';
	// }
	echo ' class="tour_item ' . esc_attr($item_class) . ' ' . esc_attr($item_extra_class) . '">';
	echo '<div>';
} else {
	echo '<li ';
	// if (!$tour_use_referral_url) {
		echo 'data-tour-id="' . esc_attr($tour_id) . '"';
	// }
	echo ' class="tour_item ' . esc_attr($item_extra_class) . '">';
}

if (!empty($tour_thumbnail_html) && (!isset($tour_item_args['hide_image']) || !$tour_item_args['hide_image'])) {
	BookYourTravel_Theme_Controls::the_entity_figure_start($tour_title, $tour_permalink, $link_external);
	if (!isset($tour_item_args['hide_rating']) || !$tour_item_args['hide_rating']) {
		BookYourTravel_Theme_Controls::the_entity_reviews_score($tour_base_id, $tour_review_score);
	}
	BookYourTravel_Theme_Controls::the_entity_figure_middle($tour_thumbnail_html, $tour_ribbon_text);
	BookYourTravel_Theme_Controls::the_entity_figure_end($tour_permalink);
}

echo '<div class="details ' .
	((isset($tour_item_args['hide_title']) && $tour_item_args['hide_title']) ? "hide-title " : "") .
	((isset($tour_item_args['hide_actions']) && $tour_item_args['hide_actions']) ? "hide-actions " : "") .
	((isset($tour_item_args['hide_image']) && $tour_item_args['hide_image']) ? "hide-image " : "") .
	((isset($tour_item_args['hide_description']) && $tour_item_args['hide_description']) ? "hide-description " : "") .
	((isset($tour_item_args['hide_rating']) && $tour_item_args['hide_rating']) ? "hide-rating " : "") .
	((isset($tour_item_args['hide_price']) && $tour_item_args['hide_price']) ? "hide-price " : "") .
	((isset($tour_item_args['hide_address']) && $tour_item_args['hide_address']) ? "hide-address " : "") .
	'">';

if (!isset($tour_item_args['hide_title']) || !$tour_item_args['hide_title'] || !isset($tour_item_args['hide_address']) || !$tour_item_args['hide_address']) {
	echo "<div class='item-header'>";
}
if (!isset($tour_item_args['hide_title']) || !$tour_item_args['hide_title']) {
	BookYourTravel_Theme_Controls::the_entity_title($tour_title, $tour_permalink, $tour_status, $link_external);
}

if (!isset($tour_item_args['hide_address']) || !$tour_item_args['hide_address']) {
	BookYourTravel_Theme_Controls::the_entity_address($tour_address);
}
if (!isset($tour_item_args['hide_title']) || !$tour_item_args['hide_title'] || !isset($tour_item_args['hide_address']) || !$tour_item_args['hide_address']) {
	echo "</div>";
}

if (!isset($tour_item_args['hide_price']) || !$tour_item_args['hide_price']) {
	$price_style = "display:none";
	if ($tour_use_referral_url || ($bookyourtravel_theme_globals->show_static_prices_in_grids() && is_numeric($tour_price))) {
		$price_style = "";
	}
	BookYourTravel_Theme_Controls::the_entity_price($tour_price, esc_html__('From', 'bookyourtravel'), $price_style);
}

if (!isset($tour_item_args['hide_description']) || !$tour_item_args['hide_description']) {
	BookYourTravel_Theme_Controls::the_entity_description($tour_description);
}

if ($current_url == $list_user_tours_url) {
	$action_label = esc_html__('Edit', 'bookyourtravel');
	$tour_permalink = esc_url( add_query_arg( 'fesid', $tour_id, $submit_user_tours_url ));
}

if (!isset($tour_item_args['hide_actions']) || !$tour_item_args['hide_actions']) {
    echo "<div class='actions'>";
    if ($tour_id > 0) {
        BookYourTravel_Theme_Controls::the_link_button($tour_permalink, "gradient-button edit-entity", "", $action_label, true, $link_external, $tour_id);
        if (!empty($current_url) && $current_url == $list_user_tours_url) {
            BookYourTravel_Theme_Controls::the_link_button("#", "gradient-button fes-delete-entity delete-entity", "", __("Delete", "bookyourtravel"), true, false, $tour_id);
        }
    }
    echo "</div>";
}

echo '</div><!--//details-->';
echo '<a href="' . esc_url($tour_permalink). '" class="overlay-link"></a>';

if ($display_mode == 'card') {
	echo '</div>';
	echo '</article><!--//tour_item-->';
} else {
	echo '</li>';
}
