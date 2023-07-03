<?php

/**
 * Post list item template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $post, $bookyourtravel_theme_globals, $bookyourtravel_post_helper, $post_item_args;

if (!$post_item_args) {
	$post_item_args = array();
}

$post_id = $post->ID;
$post_obj = new BookYourTravel_Post($post);
$base_id = $post_obj->get_base_id();
$tags = get_the_tags();

$post_description = get_the_excerpt();
if (empty(trim($post_description))) {
	$post_description = $post_obj->get_custom_field($post_obj->get_post_type() . '_short_description', false);
}

$post_permalink = get_the_permalink();
$post_title = get_the_title();
$post_status = $post_obj->get_status();

$thumbnail_html = "";
$thumbnail_id = get_post_thumbnail_id($post->ID);
$attachment = get_post($thumbnail_id);
if ($attachment) {
	$image_title = $attachment->post_title; //The Title
	$thumbnail_html = get_the_post_thumbnail($post->ID, array(600, 600), array('title' => $image_title));
}

$post_class = isset($post_item_args['item_class']) ? $post_item_args['item_class'] : 'one-third';
$display_mode = isset($post_item_args['display_mode']) ? $post_item_args['display_mode'] : 'card';

if ($display_mode == 'card') {
	echo ' <article class="post_item ' . esc_attr($post_class) . '">';
	echo '<div class = "post_item_wrap">';
} else {
	echo '<li>';
}

if ((!isset($post_item_args['hide_image']) || !$post_item_args['hide_image']) && !empty($thumbnail_html)) {
	BookYourTravel_Theme_Controls::the_entity_figure($post_title, $post_permalink, $thumbnail_html, "");
}

echo '<div class="details ' .
	((isset($post_item_args['hide_title']) && $post_item_args['hide_title']) ? "hide-title " : "") .
	((isset($post_item_args['hide_actions']) && $post_item_args['hide_actions']) ? "hide-actions " : "") .
	((isset($post_item_args['hide_image']) && $post_item_args['hide_image']) ? "hide-image " : "") .
	((isset($post_item_args['hide_description']) && $post_item_args['hide_description']) ? "hide-description " : "") .
	'">';

if (!isset($post_item_args['hide_title']) || !$post_item_args['hide_title']) {
	// $tags = get_the_tags($post_id);
	// echo $tags;
	if (!empty($tags)) {
		BookYourTravel_Theme_Controls::the_entity_tags($tags, '');
	}

	echo "<div class='item-header'>";
	BookYourTravel_Theme_Controls::the_entity_title($post_title, $post_permalink, $post_status);
	echo "</div>";
}

if (!isset($post_item_args['hide_description']) || !$post_item_args['hide_description']) {
	BookYourTravel_Theme_Controls::the_entity_description($post_description);
}

// if (!isset($post_item_args['hide_actions']) || !$post_item_args['hide_actions']) {
//     echo '<div class="actions">';
//     echo '<a href="' . esc_attr($post_permalink) . '" title="' . esc_attr__('More info', 'bookyourtravel') . '" class=" gradient-button">' . esc_attr__('More info', 'bookyourtravel') . '</a>';
//     echo '</div>';
// }

if ($display_mode == 'card') {
	echo '</div>';
	echo '</article><!--//post_item-->';
} else {
	echo '</li>';
}
