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

global $bookyourtravel_theme_globals, $car_rental_item_args, $car_rental_list_args, $bookyourtravel_car_rental_helper;

$posts_per_page    = isset($car_rental_list_args['posts_per_page']) ? $car_rental_list_args['posts_per_page'] : 12;
$paged			   = isset($car_rental_list_args['paged']) ? $car_rental_list_args['paged'] : 1;
$sort_by           = isset($car_rental_list_args['sort_by']) ? $car_rental_list_args['sort_by'] : 'title';
$sort_order        = isset($car_rental_list_args['sort_order']) ? $car_rental_list_args['sort_order'] : 'ASC';
$parent_location_id        = isset($car_rental_list_args['parent_location_id']) ? $car_rental_list_args['parent_location_id'] : 0;

$include_private = isset($car_rental_list_args['include_private']) ? $car_rental_list_args['include_private'] : false;
$show_featured_only = isset($car_rental_list_args['show_featured_only']) ? $car_rental_list_args['show_featured_only'] : false;
$car_rental_tag_ids = isset($car_rental_list_args['car_rental_tag_ids']) ? $car_rental_list_args['car_rental_tag_ids'] : array();
$car_rental_type_ids = isset($car_rental_list_args['car_rental_type_ids']) ? $car_rental_list_args['car_rental_type_ids'] : array();
$author_id = isset($car_rental_list_args["author_id"]) ? $car_rental_list_args["author_id"] : null;

$car_rental_results = $bookyourtravel_car_rental_helper->list_car_rentals($paged, $posts_per_page, $sort_by, $sort_order, array($parent_location_id), false, $car_rental_type_ids, $car_rental_tag_ids, array(), $show_featured_only, $author_id, $include_private);

$display_mode = isset($car_rental_list_args['display_mode']) ? $car_rental_list_args['display_mode'] : 'card';

$found_post_content = isset($car_rental_list_args["found_post_content"]) ? $car_rental_list_args["found_post_content"] : false;

if ( count($car_rental_results) > 0 && $car_rental_results['total'] > 0 ) {

	if ($display_mode == 'card') {
		echo '<div class="deals' . ($found_post_content ? ' found-post-content' : '') . '">';
		echo '<div class="row">';
	} else {
		echo '<ul class="small-list' . ($found_post_content ? ' found-post-content' : '') . '">';
	}

	if (!isset($car_rental_item_args) || !is_array($car_rental_item_args)) {
		$car_rental_item_args = array();
	}

	$car_rental_item_args['hide_title'] = isset($car_rental_item_args['hide_title']) ? $car_rental_item_args['hide_title'] : false;
	$car_rental_item_args['hide_actions'] = isset($car_rental_item_args['hide_actions']) ? $car_rental_item_args['hide_actions'] : false;
	$car_rental_item_args['hide_image'] = isset($car_rental_item_args['hide_image']) ? $car_rental_item_args['hide_image'] : false;
	$car_rental_item_args['hide_description'] = isset($car_rental_item_args['hide_description']) ? $car_rental_item_args['hide_description'] : false;
	$car_rental_item_args['hide_rating'] = isset($car_rental_item_args['hide_rating']) ? $car_rental_item_args['hide_rating'] : false;
	$car_rental_item_args['hide_price'] = isset($car_rental_item_args['hide_price']) ? $car_rental_item_args['hide_price'] : false;
	$car_rental_item_args['hide_address'] = isset($car_rental_item_args['hide_address']) ? $car_rental_item_args['hide_address'] : false;
	$car_rental_item_args['car_rental_id'] = 0;

	$posts_per_row = isset($car_rental_list_args['posts_per_row']) ? (int)$car_rental_list_args['posts_per_row'] : 4;
	if (!isset($car_rental_item_args['item_class']))
		$car_rental_item_args['item_class'] = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);

	foreach ($car_rental_results['results'] as $car_rental_result) {
		global $post;
		$post = $car_rental_result;
		setup_postdata( $post );
		$car_rental_item_args['car_rental_id'] = $post->ID;
		$car_rental_item_args['post'] = $post;
		get_template_part('includes/parts/car_rental/car_rental', 'item');
	}

	if ($display_mode == 'card') {
		echo '</div><!--row-->';
		if (isset($car_rental_list_args['is_list_page']) && $car_rental_list_args['is_list_page']) {
			$total_results = $car_rental_results['total'];
			if ($total_results > $posts_per_page && $posts_per_page > 0) {
				BookYourTravel_Theme_Controls::the_pager_outer(ceil($total_results/$posts_per_page));
			}
		}
		echo '</div><!--deals-->';
	} else {
		echo '</ul>';
	}
} else {
	echo '<p>' . esc_html__('Unfortunately no car rentals were found.', 'bookyourtravel') . '</p>';
}

wp_reset_postdata();
