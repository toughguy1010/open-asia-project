<?php
/**
 * Location list template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $found_post_content, $bookyourtravel_theme_globals, $location_list_args, $location_item_args, $bookyourtravel_location_helper;

$posts_per_page    = isset($location_list_args['posts_per_page']) ? $location_list_args['posts_per_page'] : 12;
$paged			   = isset($location_list_args['paged']) ? $location_list_args['paged'] : 1;
$sort_by           = isset($location_list_args['sort_by']) ? $location_list_args['sort_by'] : 'title';
$sort_order        = isset($location_list_args['sort_order']) ? $location_list_args['sort_order'] : 'ASC';
$exclude_descendant_locations = isset($location_list_args['exclude_descendant_locations']) ? $location_list_args['exclude_descendant_locations'] : false;
$parent_location_id  = isset($location_list_args['parent_location_id']) ? $location_list_args['parent_location_id'] : -1;

$include_private = isset($location_list_args['include_private']) ? $location_list_args['include_private'] : false;
$show_featured_only = isset($location_list_args['show_featured_only']) ? $location_list_args['show_featured_only'] : false;
$location_type_ids = isset($location_list_args['location_type_ids']) ? $location_list_args['location_type_ids'] : array();
$location_tag_ids = isset($location_list_args['location_tag_ids']) ? $location_list_args['location_tag_ids'] : array();
$author_id = isset($location_list_args["author_id"]) ? $location_list_args["author_id"] : null;

$location_results = $bookyourtravel_location_helper->list_locations($parent_location_id, $paged, $posts_per_page, $sort_by, $sort_order, $show_featured_only, $location_type_ids, $location_tag_ids, $author_id, $exclude_descendant_locations, $include_private);

$display_mode = isset($location_list_args['display_mode']) ? $location_list_args['display_mode'] : 'card';

if (!$found_post_content) {
    $found_post_content = isset($location_list_args["found_post_content"]) ? $location_list_args["found_post_content"] : false;
}

if ( count($location_results) > 0 && $location_results['total'] > 0 ) {

	if ($display_mode == 'card') {
		echo '<div class="destinations ' . ($found_post_content ? 'found-post-content' : '') . '">';
		echo '<div class="row">';
	} else {
		echo '<ul class="small-list destinations ' . ($found_post_content ? 'found-post-content' : '') . '">';
	}

	if (!isset($location_item_args) || !is_array($location_item_args)) {
		$location_item_args = array();
	}

	$location_item_args['hide_title'] = isset($location_item_args['hide_title']) ? $location_item_args['hide_title'] : false;
	$location_item_args['hide_actions'] = isset($location_item_args['hide_actions']) ? $location_item_args['hide_actions'] : false;
	$location_item_args['hide_image'] = isset($location_item_args['hide_image']) ? $location_item_args['hide_image'] : false;
	$location_item_args['hide_description'] = isset($location_item_args['hide_description']) ? $location_item_args['hide_description'] : false;
	$location_item_args['hide_counts'] = isset($location_item_args['hide_counts']) ? $location_item_args['hide_counts'] : false;
	$location_item_args['hide_ribbon'] = isset($location_item_args['hide_ribbon']) ? $location_item_args['hide_ribbon'] : false;

	$location_item_args['location_id'] = 0;

	$posts_per_row = isset($location_list_args['posts_per_row']) ? (int)$location_list_args['posts_per_row'] : 4;
	if (!isset($location_item_args['item_class']))
		$location_item_args['item_class'] = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);

	foreach ($location_results['results'] as $location_result) {
		global $post;
		$post = $location_result;
		setup_postdata( $post );
		if (isset($post)) {
			$location_item_args['location_id'] = $post->ID;
			$location_item_args['post'] = $post;
			get_template_part('includes/parts/location/location', 'item');
		}
	}

	if ($display_mode == 'card') {
		echo '</div><!--row-->';

		if (isset($location_list_args['is_list_page']) && $location_list_args['is_list_page']) {
			$total_results = $location_results['total'];
			if ($total_results > $posts_per_page && $posts_per_page > 0) {
				BookYourTravel_Theme_Controls::the_pager_outer(ceil($total_results/$posts_per_page));
			}
		}

		echo '</div><!--deals-->';
	} else {
		echo '</ul>';
	}

} else {
	echo '<p>' . esc_html__('Unfortunately no locations were found.', 'bookyourtravel') . '</p>';
}
wp_reset_postdata();
