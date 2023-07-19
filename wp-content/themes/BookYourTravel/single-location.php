<?php

/**
 * The template for displaying single locations
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $bookyourtravel_theme_globals;

get_header();
get_template_part('byt', 'header');
BookYourTravel_Theme_Controls::the_breadcrumbs();
get_sidebar('under-header');

$page_sidebar_positioning = $bookyourtravel_theme_globals->get_location_single_sidebar_position();
$section_class = BookYourTravel_Theme_Utils::get_page_section_class($page_sidebar_positioning);

if (have_posts()) while (have_posts()) : the_post();
	global $post;
	$has_featured_image = false;
	$displayed_featured_element = get_post_meta($post->ID, $post->post_type . '_displayed_featured_element', true);
	
?>
	<div class="row">
		<section class="destination-list <?php echo esc_attr($section_class); ?> section-location-content">
			<?php if (empty($page_sidebar_positioning) && !$has_featured_image) { ?>
				<h1><?php the_title(); ?></h1>
			<?php } ?>
			<?php do_action('bookyourtravel_before_single_location_content'); ?>
			<?php
			$post_id = $post->ID;
			$parent_id = wp_get_post_parent_id($post_id);
			if ($parent_id) {
				get_template_part('includes/parts/location/single-child/single-content');
			} else {
				get_template_part('includes/parts/location/single-parent/single-content');
			}
			?>
			<?php do_action('bookyourtravel_after_single_location_content'); ?>
		</section>
		<?php
		wp_reset_postdata();
		wp_reset_query();
		?>
	</div>
<?php
endwhile;
get_template_part('byt', 'footer');
get_footer();
