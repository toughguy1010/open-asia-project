<?php
/**
 * Room type item template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $current_url, $bookyourtravel_theme_globals, $bookyourtravel_room_type_helper, $room_type_list_args, $room_type_item_args;

if (empty($current_url)) {
	$current_url = BookYourTravel_Theme_Utils::get_current_page_url_no_query();
}

$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
$list_user_room_types_url = $bookyourtravel_theme_globals->get_list_user_room_types_url();
$submit_user_room_types_url = $bookyourtravel_theme_globals->get_submit_user_room_types_url();

$display_mode = isset($room_type_list_args['display_mode']) ? $room_type_list_args['display_mode'] : 'card';
$item_class = isset($room_type_item_args['item_class']) ? $room_type_item_args['item_class'] : '';

$room_type_id = $room_type_item_args['room_type_id'];

$room_type_permalink = '';
$room_type_description = '';
$room_type_title = '';
$room_type_thumbnail_html = '';
$room_type_base_id = 0;
$room_type_status = '';

if ($room_type_id > 0) {
	$room_type_post = $room_type_item_args['post'];
	global $post;
	if ($post->ID != $room_type_id) {
		$post = $room_type_post;
		setup_postdata($post);
	}

	$room_type_obj = new BookYourTravel_room_type($post);
	$room_type_description = wpautop($room_type_obj->get_short_description());

	$thumbnail_id = get_post_thumbnail_id($post->ID);
	$attachment = get_post($thumbnail_id);
	if ($attachment) {
		$image_title = $attachment->post_title; //The Title
		$room_type_thumbnail_html = get_the_post_thumbnail($post->ID, "thumbnail", array('title' => $image_title));
	}

	$room_type_permalink = get_the_permalink();
	$room_type_title = get_the_title();
	$room_type_base_id = $room_type_obj->get_base_id();
	$room_type_status = $room_type_obj->get_status();
} else {
	$room_type_description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit';
	$room_type_title = 'Lorem ipsum';
	$dummy_image_source = BookYourTravel_Theme_Utils::get_file_uri('/images/uploads/img.jpg');
	$room_type_thumbnail_html = sprintf('<img src="%s" alt="Lorem ipsum">', $dummy_image_source);
}

$action_label = esc_html__('Book now', 'bookyourtravel');

if ($display_mode == 'card') {
	echo '<article data-room-type-id="' . esc_attr($room_type_id) . '" class="room_type_item ' . esc_attr($item_class) . '">';
	echo '<div>';
} else {
	echo '<li data-room-type-id="' . esc_attr($room_type_id) . '" class="room_type_item">';
}

if (!isset($room_type_item_args['hide_image']) || !$room_type_item_args['hide_image']) {
	BookYourTravel_Theme_Controls::the_entity_figure_start($room_type_title, $room_type_permalink);
	BookYourTravel_Theme_Controls::the_entity_figure_middle($room_type_thumbnail_html, '');
	BookYourTravel_Theme_Controls::the_entity_figure_end($room_type_permalink);
}

echo '<div class="details ' .
	((isset($room_type_item_args['hide_title']) && $room_type_item_args['hide_title']) ? "hide-title " : "") .
	((isset($room_type_item_args['hide_actions']) && $room_type_item_args['hide_actions']) ? "hide-actions " : "") .
	((isset($room_type_item_args['hide_image']) && $room_type_item_args['hide_image']) ? "hide-image " : "") .
	((isset($room_type_item_args['hide_description']) && $room_type_item_args['hide_description']) ? "hide-description " : "") .
	'">';

if (!isset($room_type_item_args['hide_title']) || !$room_type_item_args['hide_title']) {
	echo "<div class='item-header'>";
}

if (!isset($room_type_item_args['hide_title']) || !$room_type_item_args['hide_title']) {
	BookYourTravel_Theme_Controls::the_entity_title($room_type_title, $room_type_permalink, $room_type_status);
}

if (!isset($room_type_item_args['hide_title']) || !$room_type_item_args['hide_title']) {
	echo "</div>";
}

if (!isset($room_type_item_args['hide_description']) || !$room_type_item_args['hide_description']) {
	BookYourTravel_Theme_Controls::the_entity_description($room_type_description);
}

if (!isset($room_type_item_args['hide_actions']) || !$room_type_item_args['hide_actions']) {
    if (!empty($current_url) && $current_url == $list_user_room_types_url) {
        $action_label = esc_html__('Edit', 'bookyourtravel');
        $room_type_permalink = esc_url( add_query_arg( 'fesid', $room_type_id, $submit_user_room_types_url ));
    }

    echo "<div class='actions'>";
    if ($room_type_id > 0) {
        BookYourTravel_Theme_Controls::the_link_button($room_type_permalink, "gradient-button edit-entity", "", $action_label, true, false, $room_type_id);
        if (!empty($current_url) && $current_url == $list_user_room_types_url) {
            BookYourTravel_Theme_Controls::the_link_button("#", "gradient-button fes-delete-entity delete-entity", "", __("Delete", "bookyourtravel"), true, false, $room_type_id);
        }
    }
    echo "</div>";
}

echo '</div><!--//details-->';
if ($display_mode == 'card') {
	echo '</div>';
	echo '</article><!--//room_type_item-->';
} else {
	echo '</li>';
}
