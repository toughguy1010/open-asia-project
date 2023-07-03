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

global $bookyourtravel_theme_globals, $tour_item_args, $tour_list_args, $bookyourtravel_tour_helper;

$posts_per_page = isset($tour_list_args['posts_per_page']) ? $tour_list_args['posts_per_page'] : 12;
$paged = isset($tour_list_args['paged']) ? $tour_list_args['paged'] : 1;
$sort_by = isset($tour_list_args['sort_by']) ? $tour_list_args['sort_by'] : 'title';
$sort_order = isset($tour_list_args['sort_order']) ? $tour_list_args['sort_order'] : 'ASC';
$parent_location_id = isset($tour_list_args['parent_location_id']) ? $tour_list_args['parent_location_id'] : 0;

$include_private = isset($tour_list_args['include_private']) ? $tour_list_args['include_private'] : false;
$show_featured_only = isset($tour_list_args['show_featured_only']) ? $tour_list_args['show_featured_only'] : false;
$tour_tag_ids = isset($tour_list_args['tour_tag_ids']) ? $tour_list_args['tour_tag_ids'] : array();
$tour_type_ids = isset($tour_list_args['tour_type_ids']) ? $tour_list_args['tour_type_ids'] : array();
$tour_duration_ids = isset($tour_list_args['tour_duration_ids']) ? $tour_list_args['tour_duration_ids'] : array();
$author_id = isset($tour_list_args["author_id"]) ? $tour_list_args["author_id"] : null;

$tour_results = $bookyourtravel_tour_helper->list_tours($paged, $posts_per_page, $sort_by, $sort_order, array($parent_location_id), false, $tour_type_ids, $tour_duration_ids, $tour_tag_ids, array(), $show_featured_only, $author_id, $include_private);

$display_mode = strip_tags(isset($tour_list_args['display_mode']) ? $tour_list_args['display_mode'] : 'card');

$found_post_content = isset($tour_list_args["found_post_content"]) ? $tour_list_args["found_post_content"] : false;

if (count($tour_results) > 0 && $tour_results['total'] > 0) {

	if ($display_mode == 'card') {
		echo '<div class="deals' . ($found_post_content ? ' found-post-content' : '') . '">';
		echo '<div class="row">';
	} else {
		echo '<ul class="small-list' . ($found_post_content ? ' found-post-content' : '') . '">';
	}

    if (!isset($tour_item_args) || !is_array($tour_item_args)) {
        $tour_item_args = array();
    }

    $tour_item_args['hide_title'] = isset($tour_item_args['hide_title']) ? $tour_item_args['hide_title'] : false;
    $tour_item_args['hide_actions'] = isset($tour_item_args['hide_actions']) ? $tour_item_args['hide_actions'] : false;
    $tour_item_args['hide_image'] = isset($tour_item_args['hide_image']) ? $tour_item_args['hide_image'] : false;
    $tour_item_args['hide_description'] = isset($tour_item_args['hide_description']) ? $tour_item_args['hide_description'] : false;
    $tour_item_args['hide_address'] = isset($tour_item_args['hide_address']) ? $tour_item_args['hide_address'] : false;
    $tour_item_args['hide_rating'] = isset($tour_item_args['hide_rating']) ? $tour_item_args['hide_rating'] : false;
    $tour_item_args['hide_price'] = isset($tour_item_args['hide_price']) ? $tour_item_args['hide_price'] : false;
    $tour_item_args['tour_id'] = 0;

    $posts_per_row = isset($tour_list_args['posts_per_row']) ? (int) $tour_list_args['posts_per_row'] : 4;
    if (!isset($tour_item_args['item_class'])) {
        $tour_item_args['item_class'] = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);
    }

    foreach ($tour_results['results'] as $tour_result) {
        global $post;
        $post = $tour_result;
        setup_postdata($post);
        if (isset($post)) {
            $tour_item_args['tour_id'] = $post->ID;
            $tour_item_args['post'] = $post;
            get_template_part('includes/parts/tour/tour', 'item');
        }
    }

    if ($display_mode == 'card') {
        echo '</div><!--row-->';
        if (isset($tour_list_args['is_list_page']) && $tour_list_args['is_list_page']) {
            $total_results = $tour_results['total'];
            if ($total_results > $posts_per_page && $posts_per_page > 0) {
                BookYourTravel_Theme_Controls::the_pager_outer(ceil($total_results / $posts_per_page));
            }
        }
        echo '</div><!--deals-->';
    } else {
        echo '</ul>';
    }
} else {
    echo '<p>' . esc_html__('Unfortunately no tours were found.', 'bookyourtravel') . '</p>';
}

wp_reset_postdata();
