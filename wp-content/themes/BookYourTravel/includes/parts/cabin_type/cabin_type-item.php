<?php
/**
 * Cabin type item template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $current_url, $bookyourtravel_theme_globals, $bookyourtravel_cabin_type_helper, $cabin_type_list_args, $cabin_type_item_args;

if (empty($current_url)) {
	$current_url = BookYourTravel_Theme_Utils::get_current_page_url_no_query();
}

$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
$list_user_cabin_types_url = $bookyourtravel_theme_globals->get_list_user_cabin_types_url();
$submit_user_cabin_types_url = $bookyourtravel_theme_globals->get_submit_user_cabin_types_url();

$display_mode = isset($cabin_type_list_args['display_mode']) ? $cabin_type_list_args['display_mode'] : 'card';
$item_class = isset($cabin_type_item_args['item_class']) ? $cabin_type_item_args['item_class'] : '';

$cabin_type_id = $cabin_type_item_args['cabin_type_id'];

$cabin_type_permalink = '';
$cabin_type_description = '';
$cabin_type_title = '';
$cabin_type_thumbnail_html = '';
$cabin_type_base_id = 0;
$cabin_type_status = '';

if ($cabin_type_id > 0) {
	$cabin_type_post = $cabin_type_item_args['post'];
	global $post;
	if ($post->ID != $cabin_type_id) {
		$post = $cabin_type_post;
		setup_postdata($post);
	}

	$cabin_type_obj = new BookYourTravel_cabin_type($post);
	$cabin_type_description = wpautop($cabin_type_obj->get_short_description());

	$thumbnail_id = get_post_thumbnail_id($post->ID);
	$attachment = get_post($thumbnail_id);
	if ($attachment) {
		$image_title = $attachment->post_title; //The Title
		$cabin_type_thumbnail_html = get_the_post_thumbnail($post->ID, "thumbnail", array('title' => $image_title));
	}

	$cabin_type_permalink = get_the_permalink();
	$cabin_type_title = get_the_title();
	$cabin_type_base_id = $cabin_type_obj->get_base_id();
	$cabin_type_status = $cabin_type_obj->get_status();
} else {
	$cabin_type_description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit';
	$cabin_type_title = 'Lorem ipsum';
	$dummy_image_source = BookYourTravel_Theme_Utils::get_file_uri('/images/uploads/img.jpg');
	$cabin_type_thumbnail_html = sprintf('<img src="%s" alt="Lorem ipsum">', $dummy_image_source);
}

if ($display_mode == 'card') {
	echo '<article data-cabin-type-id="' . esc_attr($cabin_type_id) . '" class="cabin_type_item ' . esc_attr($item_class) . '">';
	echo '<div>';
} else {
	echo '<li data-cabin-type-id="' . esc_attr($cabin_type_id) . '" class="cabin_type_item">';
}

$action_label = esc_html__('Book now', 'bookyourtravel');

if (!isset($cabin_type_item_args['hide_image']) || !$cabin_type_item_args['hide_image']) {
	BookYourTravel_Theme_Controls::the_entity_figure_start($cabin_type_title, $cabin_type_permalink);
	BookYourTravel_Theme_Controls::the_entity_figure_middle($cabin_type_thumbnail_html, '');
	BookYourTravel_Theme_Controls::the_entity_figure_end($cabin_type_permalink);
}

echo '<div class="details ' .
	((isset($cabin_type_item_args['hide_title']) && $cabin_type_item_args['hide_title']) ? "hide-title " : "") .
	((isset($cabin_type_item_args['hide_actions']) && $cabin_type_item_args['hide_actions']) ? "hide-actions " : "") .
	((isset($cabin_type_item_args['hide_image']) && $cabin_type_item_args['hide_image']) ? "hide-image " : "") .
	((isset($cabin_type_item_args['hide_description']) && $cabin_type_item_args['hide_description']) ? "hide-description " : "") .
	'">';

if (!isset($cabin_type_item_args['hide_title']) || !$cabin_type_item_args['hide_title']) {
	echo "<div class='item-header'>";
}

if (!isset($cabin_type_item_args['hide_title']) || !$cabin_type_item_args['hide_title']) {
	BookYourTravel_Theme_Controls::the_entity_title($cabin_type_title, $cabin_type_permalink, $cabin_type_status);
}

if (!isset($cabin_type_item_args['hide_title']) || !$cabin_type_item_args['hide_title']) {
	echo "</div>";
}

if (!isset($cabin_type_item_args['hide_description']) || !$cabin_type_item_args['hide_description']) {
	BookYourTravel_Theme_Controls::the_entity_description($cabin_type_description);
}

if (!isset($cabin_type_item_args['hide_actions']) || !$cabin_type_item_args['hide_actions']) {
    if (!empty($current_url) && $current_url == $list_user_cabin_types_url) {
        $action_label = esc_html__('Edit', 'bookyourtravel');
        $cabin_type_permalink = esc_url( add_query_arg( 'fesid', $cabin_type_id, $submit_user_cabin_types_url ));
    }

    echo "<div class='actions'>";
    if ($cabin_type_id > 0) {
        BookYourTravel_Theme_Controls::the_link_button($cabin_type_permalink, "gradient-button edit-entity", "", $action_label, true, false, $cabin_type_id);
        if (!empty($current_url) && $current_url == $list_user_cabin_types_url) {
            BookYourTravel_Theme_Controls::the_link_button("#", "gradient-button fes-delete-entity delete-entity", "", __("Delete", "bookyourtravel"), true, false, $cabin_type_id);
        }
    }
    echo "</div>";
}

echo '</div><!--//details-->';
if ($display_mode == 'card') {
	echo '</div>';
	echo '</article><!--//cabin_type_item-->';
} else {
	echo '</li>';
}
