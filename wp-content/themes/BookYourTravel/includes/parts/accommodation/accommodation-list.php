<?php
/**
 * Accommodation list template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $bookyourtravel_theme_globals, $accommodation_item_args, $accommodation_list_args, $bookyourtravel_accommodation_helper;

$posts_per_page    = isset($accommodation_list_args['posts_per_page']) ? $accommodation_list_args['posts_per_page'] : 12;
$paged			   = isset($accommodation_list_args['paged']) ? $accommodation_list_args['paged'] : 1;
$sort_by           = isset($accommodation_list_args['sort_by']) ? $accommodation_list_args['sort_by'] : 'title';
$sort_order        = isset($accommodation_list_args['sort_order']) ? $accommodation_list_args['sort_order'] : 'ASC';
$parent_location_id        = isset($accommodation_list_args['parent_location_id']) ? $accommodation_list_args['parent_location_id'] : 0;

$include_private = isset($accommodation_list_args['include_private']) ? $accommodation_list_args['include_private'] : false;
$show_featured_only = isset($accommodation_list_args['show_featured_only']) ? $accommodation_list_args['show_featured_only'] : false;
$accommodation_tag_ids = isset($accommodation_list_args['accommodation_tag_ids']) ? $accommodation_list_args['accommodation_tag_ids'] : array();
$accommodation_type_ids = isset($accommodation_list_args['accommodation_type_ids']) ? $accommodation_list_args['accommodation_type_ids'] : array();
$accommodation_facilities = isset($accommodation_list_args['accommodation_facilities']) ? $accommodation_list_args['accommodation_facilities'] : array();
$author_id = isset($accommodation_list_args["author_id"]) ? $accommodation_list_args["author_id"] : null;
$accommodation_results = $bookyourtravel_accommodation_helper->list_accommodations($paged, $posts_per_page, $sort_by, $sort_order, array($parent_location_id), $accommodation_type_ids, $accommodation_tag_ids, $accommodation_facilities, array(), $show_featured_only, null, $author_id, $include_private);

$display_mode = isset($accommodation_list_args['display_mode']) ? $accommodation_list_args['display_mode'] : 'card';

$found_post_content = isset($accommodation_list_args["found_post_content"]) ? $accommodation_list_args["found_post_content"] : false;

if ( count($accommodation_results) > 0 && $accommodation_results['total'] > 0 ) {

	if ($display_mode == 'card') {
		echo '<div class="deals' . ($found_post_content ? ' found-post-content' : '') . '">';
		echo '<div class="row">';
	} else {
		echo '<ul class="small-list' . ($found_post_content ? ' found-post-content' : '') . '">';
	}

	if (!isset($accommodation_item_args) || !is_array($accommodation_item_args)) {
		$accommodation_item_args = array();
	}

	$accommodation_item_args['hide_title'] = isset($accommodation_item_args['hide_title']) ? $accommodation_item_args['hide_title'] : false;
	$accommodation_item_args['hide_actions'] = isset($accommodation_item_args['hide_actions']) ? $accommodation_item_args['hide_actions'] : false;
	$accommodation_item_args['hide_image'] = isset($accommodation_item_args['hide_image']) ? $accommodation_item_args['hide_image'] : false;
	$accommodation_item_args['hide_description'] = isset($accommodation_item_args['hide_description']) ? $accommodation_item_args['hide_description'] : true;
	$accommodation_item_args['hide_address'] = isset($accommodation_item_args['hide_address']) ? $accommodation_item_args['hide_address'] : false;
	$accommodation_item_args['hide_stars'] = isset($accommodation_item_args['hide_stars']) ? $accommodation_item_args['hide_stars'] : false;
	$accommodation_item_args['hide_rating'] = isset($accommodation_item_args['hide_rating']) ? $accommodation_item_args['hide_rating'] : false;
	$accommodation_item_args['hide_price'] = isset($accommodation_item_args['hide_price']) ? $accommodation_item_args['hide_price'] : false;
	$accommodation_item_args['accommodation_id'] = 0;

	$posts_per_row = isset($accommodation_list_args['posts_per_row']) ? (int)$accommodation_list_args['posts_per_row'] : 4;
	if (!isset($accommodation_item_args['item_class']))
		$accommodation_item_args['item_class'] = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);

	foreach ($accommodation_results['results'] as $accommodation_result) {
		global $post;
		$post = $accommodation_result;
		setup_postdata( $post );
		$accommodation_item_args['accommodation_id'] = $post->ID;
		$accommodation_item_args['post'] = $post;
		get_template_part('includes/parts/accommodation/accommodation', 'item');
	}

	if ($display_mode == 'card') {
		echo '</div><!--row-->';
		if (isset($accommodation_list_args['is_list_page']) && $accommodation_list_args['is_list_page']) {
			$total_results = $accommodation_results['total'];
			if ($total_results > $posts_per_page && $posts_per_page > 0) {
				BookYourTravel_Theme_Controls::the_pager_outer(ceil($total_results/$posts_per_page));
			}
		}
		echo '</div><!--deals-->';
	} else {
		echo '</ul>';
	}
} else {
	echo '<p>' . esc_html__('Unfortunately no accommodations were found.', 'bookyourtravel') . '</p>';
}

wp_reset_postdata();
