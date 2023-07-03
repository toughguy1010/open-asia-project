<?php
/**
 * The template for displaying single tours
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $bookyourtravel_theme_globals, $section_class, $post;
if ( have_posts() ) while ( have_posts() ) : the_post();

	$tour_obj = new BookYourTravel_Tour($post);
	$tour_use_referral_url = $tour_obj->use_referral_url();
	$tour_referral_url = $tour_obj->get_referral_url();
	
	if ($tour_use_referral_url && !empty($tour_referral_url)) {
		header("HTTP/1.1 301 Moved Permanently"); 
		header("Location: " . $tour_referral_url);
		exit();	
	}

	get_header();  
	get_template_part('byt', 'header');
	BookYourTravel_Theme_Controls::the_breadcrumbs();
	get_sidebar('under-header');

	$page_sidebar_positioning = $bookyourtravel_theme_globals->get_tour_single_sidebar_position();
	$section_class = BookYourTravel_Theme_Utils::get_page_section_class($page_sidebar_positioning);

	$has_featured_image = false;
	$displayed_featured_element = get_post_meta($post->ID, $post->post_type . '_displayed_featured_element', true);
	if ($displayed_featured_element == 'image' && has_post_thumbnail($post->ID)) {
		$has_featured_image = true;
	}
?>
		<div class="row">
			<?php
			if ($page_sidebar_positioning == 'left' || $page_sidebar_positioning == 'both') {
				get_sidebar('left');
			}
			?>
			<?php do_action('bookyourtravel_before_single_tour_content'); ?>		
			<section class="<?php echo esc_attr($section_class); ?> section-tour-content">
				<?php if (empty($page_sidebar_positioning) && !$has_featured_image) { ?>
				<h1><?php the_title(); ?></h1>
				<?php } ?>
				<?php get_template_part('includes/parts/tour/single/single', 'content'); ?>
			</section>
			<?php do_action('bookyourtravel_after_single_tour_content'); ?>			
			<?php
			wp_reset_postdata();
			wp_reset_query();

			if ($page_sidebar_positioning == 'right' || $page_sidebar_positioning == 'both') {
				get_sidebar('right-tour');
			}
			?>
		</div>
<?php
endwhile; 
get_template_part('byt', 'footer');
get_footer();