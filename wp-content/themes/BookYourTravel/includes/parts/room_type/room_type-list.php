<?php
/**
 * Room type list template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
 
global $bookyourtravel_theme_globals, $room_type_item_args, $room_type_list_args, $bookyourtravel_room_type_helper;

$author_id = isset($room_type_list_args["author_id"]) ? $room_type_list_args["author_id"] : null;

$room_type_query = $bookyourtravel_room_type_helper->list_room_types($author_id, true);

$display_mode = isset($room_type_list_args['display_mode']) ? $room_type_list_args['display_mode'] : 'card';

if ($room_type_query->have_posts()) {

	if ($display_mode == 'card') {
		echo '<div class="deals">';
		echo '<div class="row">';
	} else {
		echo '<ul class="small-list">';
	}
	
	if (!isset($room_type_item_args) || !is_array($room_type_item_args)) {
		$room_type_item_args = array();
	}	
	
	$room_type_item_args['hide_title'] = isset($room_type_item_args['hide_title']) ? $room_type_item_args['hide_title'] : false;
	$room_type_item_args['hide_actions'] = isset($room_type_item_args['hide_actions']) ? $room_type_item_args['hide_actions'] : false;
	$room_type_item_args['hide_image'] = isset($room_type_item_args['hide_image']) ? $room_type_item_args['hide_image'] : false;
	$room_type_item_args['hide_description'] = isset($room_type_item_args['hide_description']) ? $room_type_item_args['hide_description'] : false;
	$room_type_item_args['room_type_id'] = 0;
	
	$posts_per_row = isset($room_type_list_args['posts_per_row']) ? (int)$room_type_list_args['posts_per_row'] : 1;
	if (!isset($room_type_item_args['item_class']))
		$room_type_item_args['item_class'] = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);
	
	while ($room_type_query->have_posts()) {
		$room_type_query->the_post();
		global $post;
		
		$room_type_item_args['room_type_id'] = $post->ID;
		$room_type_item_args['post'] = $post;

		get_template_part('includes/parts/room_type/room_type', 'item');		
	}
	
	wp_reset_postdata();
	
	if ($display_mode == 'card') {
		echo '</div><!--row-->';	
		echo '</div><!--deals-->';
	} else {
		echo '</ul>';
	}
} else {
	echo '<p>' . esc_html__('Unfortunately no room types were found.', 'bookyourtravel') . '</p>';
}