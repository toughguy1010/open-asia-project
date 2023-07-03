<?php
/**
 * Cabin type list template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
 
global $bookyourtravel_theme_globals, $cabin_type_item_args, $cabin_type_list_args, $bookyourtravel_cabin_type_helper;

$author_id = isset($cabin_type_list_args["author_id"]) ? $cabin_type_list_args["author_id"] : null;

$cabin_type_query = $bookyourtravel_cabin_type_helper->list_cabin_types($author_id, true, 0);

$display_mode = isset($cabin_type_list_args['display_mode']) ? $cabin_type_list_args['display_mode'] : 'card';

if ($cabin_type_query->have_posts()) {

	if ($display_mode == 'card') {
		echo '<div class="deals">';
		echo '<div class="row">';
	} else {
		echo '<ul class="small-list">';
	}
	
	if (!isset($cabin_type_item_args) || !is_array($cabin_type_item_args)) {
		$cabin_type_item_args = array();
	}	
	
	$cabin_type_item_args['hide_title'] = isset($cabin_type_item_args['hide_title']) ? $cabin_type_item_args['hide_title'] : false;
	$cabin_type_item_args['hide_actions'] = isset($cabin_type_item_args['hide_actions']) ? $cabin_type_item_args['hide_actions'] : false;
	$cabin_type_item_args['hide_image'] = isset($cabin_type_item_args['hide_image']) ? $cabin_type_item_args['hide_image'] : false;
	$cabin_type_item_args['hide_description'] = isset($cabin_type_item_args['hide_description']) ? $cabin_type_item_args['hide_description'] : false;
	$cabin_type_item_args['cabin_type_id'] = 0;
	
	$posts_per_row = isset($cabin_type_list_args['posts_per_row']) ? (int)$cabin_type_list_args['posts_per_row'] : 1;
	if (!isset($cabin_type_item_args['item_class']))
		$cabin_type_item_args['item_class'] = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);
	
	while ($cabin_type_query->have_posts()) {
		$cabin_type_query->the_post();
		global $post;
		
		$cabin_type_item_args['cabin_type_id'] = $post->ID;
		$cabin_type_item_args['post'] = $post;

		get_template_part('includes/parts/cabin_type/cabin_type', 'item');		
	}
	
	wp_reset_postdata();
	
	if ($display_mode == 'card') {
		echo '</div><!--row-->';	
		echo '</div><!--deals-->';
	} else {
		echo '</ul>';
	}
} else {
	echo '<p>' . esc_html__('Unfortunately no cabin types were found.', 'bookyourtravel') . '</p>';
}