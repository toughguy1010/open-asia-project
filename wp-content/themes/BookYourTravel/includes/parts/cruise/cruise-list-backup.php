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

global $bookyourtravel_theme_globals, $cruise_item_args, $cruise_list_args, $bookyourtravel_cruise_helper ,$post , $cruise_type_obj ,$cabin_obj;

$posts_per_page    = isset($cruise_list_args['posts_per_page']) ? $cruise_list_args['posts_per_page'] : 12;
$paged			   = isset($cruise_list_args['paged']) ? $cruise_list_args['paged'] : 1;
$sort_by           = isset($cruise_list_args['sort_by']) ? $cruise_list_args['sort_by'] : 'title';
$sort_order        = isset($cruise_list_args['sort_order']) ? $cruise_list_args['sort_order'] : 'ASC';
$parent_location_id        = isset($cruise_list_args['parent_location_id']) ? $cruise_list_args['parent_location_id'] : 0;
$include_private = isset($cruise_list_args['include_private']) ? $cruise_list_args['include_private'] : false;
$show_featured_only = isset($cruise_list_args['show_featured_only']) ? $cruise_list_args['show_featured_only'] : false;
$cruise_tag_ids = isset($cruise_list_args['cruise_tag_ids']) ? $cruise_list_args['cruise_tag_ids'] : array();
$cruise_type_ids = isset($cruise_list_args['cruise_type_ids']) ? $cruise_list_args['cruise_type_ids'] : array();
$cruise_duration_ids = isset($cruise_list_args['cruise_duration_ids']) ? $cruise_list_args['cruise_duration_ids'] : array();
$cruise_facility_ids = isset($cruise_list_args['cruise_facility_ids']) ? $cruise_list_args['cruise_facility_ids'] : array();
$author_id = isset($cruise_list_args["author_id"]) ? $cruise_list_args["author_id"] : null;


// custom

if(isset($_GET['cruise-type'])){
	$cruise_type_slug = $_GET['cruise-type'];
	$cruise_type = get_term_by('slug', $cruise_type_slug, 'cruise_type');
	if ($cruise_type) {
        $cruise_type_id = $cruise_type->term_id;
    } else {
        echo 'Tour type không tồn tại.';
    }
}

$cruise_type_ids = $cruise_type_id;
// $ports = get_term_meta($cruise_type_ids, 'port', true);
// foreach ($ports as $port_id) {
//     $port = get_term_by('id', $port_id, 'cruise_tag');

//     if ($port) {
//         // Successfully retrieved the cruise_tag term for the current port

//         // Query a single cruise based on the cruise_tag term
//         $cruise_args = array(
//             'post_type' => 'cruise',          // Assuming the post type is 'cruise'
//             'posts_per_page' => -1,            // Retrieve only one cruise
//             'tax_query' => array(
//                 array(
//                     'taxonomy' => 'cruise_tag', // The taxonomy to filter by
//                     'field' => 'term_id',       // Use 'term_id' to filter by term ID
//                     'terms' => $port->term_id,  // The term ID to filter by
//                 ),
//             ),
//         );

//         $cruise_query = new WP_Query($cruise_args);

//         // Check if the cruise was found
//         if ($cruise_query->have_posts()) {
//             while ($cruise_query->have_posts()) {
//                 $cruise_query->the_post();
//                 the_title(); // Display the cruise title
//                 // You can access other cruise data using functions like get_field()
//             }
//         } else {
//             echo 'No cruise found for this cruise tag.';
//         }

//         // Reset the post data
//         wp_reset_postdata();
//     } else {
//         echo 'Cruise tag not found for port ID: ' . $port_id;
//     }
// }
// if(isset($cabin = )){
	
// }

// custom





$cruise_results = $bookyourtravel_cruise_helper->list_cruises($paged, $posts_per_page, $sort_by, $sort_order, array($parent_location_id), false, $cruise_type_ids, $cruise_duration_ids, $cruise_tag_ids, $cruise_facility_ids, array(), $show_featured_only, $author_id, $include_private);

$display_mode = isset($cruise_list_args['display_mode']) ? $cruise_list_args['display_mode'] : 'card';

$found_post_content = isset($cruise_list_args["found_post_content"]) ? $cruise_list_args["found_post_content"] : false;

if ( count($cruise_results) > 0 && $cruise_results['total'] > 0 ) {

	if ($display_mode == 'card') {
		echo '<div class="deals' . ($found_post_content ? ' found-post-content' : '') . '">';
		echo '<div class="row">';
	} else {
		echo '<ul class="small-list' . ($found_post_content ? ' found-post-content' : '') . '">';
	}

	if (!isset($cruise_item_args) || !is_array($cruise_item_args)) {
		$cruise_item_args = array();
	}

	$cruise_item_args['hide_title'] = isset($cruise_item_args['hide_title']) ? $cruise_item_args['hide_title'] : false;
	$cruise_item_args['hide_actions'] = isset($cruise_item_args['hide_actions']) ? $cruise_item_args['hide_actions'] : false;
	$cruise_item_args['hide_image'] = isset($cruise_item_args['hide_image']) ? $cruise_item_args['hide_image'] : false;
	$cruise_item_args['hide_description'] = isset($cruise_item_args['hide_description']) ? $cruise_item_args['hide_description'] : false;
	$cruise_item_args['hide_address'] = isset($cruise_item_args['hide_address']) ? $cruise_item_args['hide_address'] : false;
	$cruise_item_args['hide_rating'] = isset($cruise_item_args['hide_rating']) ? $cruise_item_args['hide_rating'] : false;
	$cruise_item_args['hide_price'] = isset($cruise_item_args['hide_price']) ? $cruise_item_args['hide_price'] : false;
	$cruise_item_args['cruise_id'] = 0;

	$posts_per_row = isset($cruise_list_args['posts_per_row']) ? (int)$cruise_list_args['posts_per_row'] : 4;
	if (!isset($cruise_item_args['item_class']))
		$cruise_item_args['item_class'] = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);

	foreach ($cruise_results['results'] as $cruise_result) {
		global $post;
		$post = $cruise_result;
		setup_postdata( $post );
		$cruise_item_args['cruise_id'] = $post->ID;
		$cruise_item_args['post'] = $post;
		get_template_part('includes/parts/cruise/cruise', 'item');
	}

	if ($display_mode == 'card') {
		echo '</div><!--row-->';
		if (isset($cruise_list_args['is_list_page']) && $cruise_list_args['is_list_page']) {
			$total_results = $cruise_results['total'];
			if ($total_results > $posts_per_page && $posts_per_page > 0) {
				BookYourTravel_Theme_Controls::the_pager_outer(ceil($total_results/$posts_per_page));
			}
		}
		echo '</div><!--deals-->';
	} else {
		echo '</ul>';
	}
} else {
	echo '<p>' . esc_html__('Unfortunately no cruises were found.', 'bookyourtravel') . '</p>';
}

wp_reset_postdata();
