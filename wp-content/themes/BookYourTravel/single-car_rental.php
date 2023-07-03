<?php
/**
 * The template for displaying single car rentals
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

	$car_rental_obj = new BookYourTravel_Car_Rental($post);
	$car_rental_use_referral_url = $car_rental_obj->use_referral_url();
	$car_rental_referral_url = $car_rental_obj->get_referral_url();
	
	if ($car_rental_use_referral_url && !empty($car_rental_referral_url)) {
		header("HTTP/1.1 301 Moved Permanently"); 
		header("Location: " . $car_rental_referral_url);
		exit();	
	}

	get_header();  
	get_template_part('byt', 'header');
	BookYourTravel_Theme_Controls::the_breadcrumbs();
	get_sidebar('under-header');

	$page_sidebar_positioning = $bookyourtravel_theme_globals->get_car_rental_single_sidebar_position();
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
			<?php do_action('bookyourtravel_before_single_car_rental_content'); ?>		
			<section class="<?php echo esc_attr($section_class); ?> section-car_rental-content">
				<?php if (empty($page_sidebar_positioning) && !$has_featured_image) { ?>
				<h1><?php the_title(); ?></h1>
				<?php } ?>
				<?php get_template_part('includes/parts/car_rental/single/single', 'content'); ?>
			</section>
			<?php do_action('bookyourtravel_after_single_car_rental_content'); ?>			
			<?php
			wp_reset_postdata();
			wp_reset_query();
			
			if ($page_sidebar_positioning == 'right' || $page_sidebar_positioning == 'both') {
				get_sidebar('right-car_rental');
			}
			?>
		</div>

<?php
endwhile; 
get_template_part('byt', 'footer');
get_footer();