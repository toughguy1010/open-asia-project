<?php
/**
/* Template Name: Accommodation list
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $bookyourtravel_theme_globals, $post, $accommodation_list_args, $accommodation_item_args;

get_header();
get_template_part('byt', 'header');
BookYourTravel_Theme_Controls::the_breadcrumbs();
get_sidebar('under-header');

$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id);

$parent_location_id = isset($page_custom_fields['accommodation_list_location_post_id']) ? intval($page_custom_fields['accommodation_list_location_post_id'][0]) : 0;
$sort_by = isset($page_custom_fields['accommodation_list_sort_by']) && !empty($page_custom_fields['accommodation_list_sort_by'][0]) ? $page_custom_fields['accommodation_list_sort_by'][0] : 'title';
$sort_descending = isset($page_custom_fields['accommodation_list_sort_descending']) && $page_custom_fields['accommodation_list_sort_descending'][0] == '1' ? true : false;
$sort_order = $sort_descending ? 'DESC' : 'ASC';
$show_featured_only = isset($page_custom_fields['accommodation_list_show_featured_only']) && $page_custom_fields['accommodation_list_show_featured_only'][0] == '1' ? true : false;
$posts_per_row = isset($page_custom_fields['accommodation_list_posts_per_row']) ? intval($page_custom_fields['accommodation_list_posts_per_row'][0]) : 4;
$posts_per_page = isset($page_custom_fields['accommodation_list_posts_per_page']) ? intval($page_custom_fields['accommodation_list_posts_per_page'][0]) : 12;

$hide_item_titles = isset($page_custom_fields['accommodation_list_hide_item_titles']) && $page_custom_fields['accommodation_list_hide_item_titles'][0] == '1' ? true : false;
$hide_item_images = isset($page_custom_fields['accommodation_list_hide_item_images']) && $page_custom_fields['accommodation_list_hide_item_images'][0] == '1' ? true : false;
$hide_item_descriptions = isset($page_custom_fields['accommodation_list_hide_item_descriptions']) && $page_custom_fields['accommodation_list_hide_item_descriptions'][0] == '1' ? true : false;
$hide_item_actions = isset($page_custom_fields['accommodation_list_hide_item_actions']) && $page_custom_fields['accommodation_list_hide_item_actions'][0] == '1' ? true : false;
$hide_item_stars = isset($page_custom_fields['accommodation_list_hide_item_stars']) && $page_custom_fields['accommodation_list_hide_item_stars'][0] == '1' ? true : false;
$hide_item_rating = isset($page_custom_fields['accommodation_list_hide_item_rating']) && $page_custom_fields['accommodation_list_hide_item_rating'][0] == '1' ? true : false;
$hide_item_address = isset($page_custom_fields['accommodation_list_hide_item_address']) && $page_custom_fields['accommodation_list_hide_item_address'][0] == '1' ? true : false;
$hide_item_prices = isset($page_custom_fields['accommodation_list_hide_item_prices']) && $page_custom_fields['accommodation_list_hide_item_prices'][0] == '1' ? true : false;

$accommodation_tags = wp_get_post_terms($page_id, 'acc_tag', array("fields" => "all"));
$accommodation_tag_ids = array();
if (isset($accommodation_tags) && !is_wp_error($accommodation_tags) && count($accommodation_tags) > 0) {
	foreach ($accommodation_tags as $accommodation_tag) {
		$accommodation_tag_ids[] = $accommodation_tag->term_id;
	}
}

$accommodation_types = wp_get_post_terms($page_id, 'accommodation_type', array("fields" => "all"));
$accommodation_type_ids = array();
if (!is_wp_error($accommodation_types) && count($accommodation_types) > 0) {
	foreach ($accommodation_types as $accommodation_type) {
		$accommodation_type_ids[] = $accommodation_type->term_id;
	}
}

$page_sidebar_positioning = BookYourTravel_Theme_Utils::get_page_sidebar_positioning($page_id);
$section_class = BookYourTravel_Theme_Utils::get_page_section_class($page_sidebar_positioning);
?>
		<div class="row">
		<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
				get_sidebar('left');
		?>
			<section class="<?php echo esc_attr($section_class); ?>">
				<?php  while ( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?> id="page-<?php the_ID(); ?>">
					<?php
						$has_post_thumbnail = has_post_thumbnail();
					?>
					<?php if ($has_post_thumbnail) { ?>
					<div class="page-featured-image">
						<?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'byt-featured'); ?>
						<div class="keyvisual" style="background-image:url(<?php echo esc_url($featured_img_url); ?>)"></div>
						<div class="wrap"><h1><?php the_title(); ?></h1></div>
					</div>
					<?php } else {?>
						<h1 class="entry-title"><?php the_title(); ?></h1>
					<?php } ?>
					<?php if ($has_post_thumbnail) {?>
					<div class="post-general-content">
					<?php } ?>
					<?php the_content( wp_kses(__( 'Continue reading <span class="meta-nav">&rarr;</span>', 'bookyourtravel' ), array('span' =>  array('class' => array()))) ); ?>
					<?php if ($has_post_thumbnail) {?>
					</div>
					<?php } ?>
					<?php wp_link_pages('before=<div class="pagination">&after=</div>'); ?>
				</article>
				<?php endwhile; ?>
				<?php

				$paged = 1;
				if ( get_query_var('paged-byt') ) {
					$paged = get_query_var('paged-byt');
				} else if ( get_query_var('paged') ) {
					$paged = get_query_var('paged');
				} else if ( get_query_var('page') ) {
					$paged = get_query_var('page');
				}

				$accommodation_list_args = array(
					'parent_location_id' => $parent_location_id,
					'sort_by' => $sort_by,
					'sort_order' => $sort_order,
					'show_featured_only' => $show_featured_only,
					'posts_per_page' => $posts_per_page,
					'posts_per_row' => $posts_per_row,
					'is_list_page' => true,
					'display_mode' => 'card',
					'accommodation_tag_ids' => $accommodation_tag_ids,
					'accommodation_type_ids' => $accommodation_type_ids,
					'paged' => $paged,
					'found_post_content' => $has_post_thumbnail
				);

				$accommodation_item_args = array(
					'hide_title' => $hide_item_titles,
					'hide_image' => $hide_item_images,
					'hide_description' => $hide_item_descriptions,
					'hide_actions' => $hide_item_actions,
					'hide_stars' => $hide_item_stars,
					'hide_rating' => $hide_item_rating,
					'hide_address' => $hide_item_address,
					'hide_price' => $hide_item_prices,
				);

				do_action( 'bookyourtravel_page_accommodation_list_before' );
				get_template_part('includes/parts/accommodation/accommodation', 'list');
				do_action( 'bookyourtravel_page_accommodation_list_after' );
				?>
			</section>
		<?php
			wp_reset_postdata();
			wp_reset_query();

			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right')
				get_sidebar('right');
			?>
		</div>
<?php
get_template_part('byt', 'footer');
get_footer();
