<?php
/* 
/*	Template Name: User Submit Content
 * The template for displaying submit forms for front-end content submission
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $bookyourtravel_theme_globals, $bookyourtravel_accommodation_helper, $bookyourtravel_review_helper, $current_user, $frontend_submit;

if ( !is_user_logged_in() || !$frontend_submit->user_has_correct_role()) {
	wp_redirect( home_url('/') );
	exit;
}

get_header();
get_template_part('byt', 'header');
BookYourTravel_Theme_Controls::the_breadcrumbs();
get_sidebar('under-header');

$enable_reviews = $bookyourtravel_theme_globals->enable_reviews();
$enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
$enable_tours = $bookyourtravel_theme_globals->enable_tours();
$enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
$enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();

$current_user = wp_get_current_user();
$user_info = get_userdata($current_user->ID);

global $post;
$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id );
$current_url = get_permalink( $page_id );

$content_type = 'accommodation';
if (isset($page_custom_fields['frontend_submit_content_type'])) {
	$content_type = $page_custom_fields['frontend_submit_content_type'][0];
}

$page_sidebar_positioning = BookYourTravel_Theme_Utils::get_page_sidebar_positioning($page_id);
$section_class = BookYourTravel_Theme_Utils::get_page_section_class($page_sidebar_positioning);

$pending_activation = false;
if ( in_array( 'pending', (array) $current_user->roles ) ) {
    $pending_activation = true;
}	
	
?>
		<div class="row">
			<?php	
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
				get_sidebar('left');
			?>
			<section class="<?php echo esc_attr($section_class); ?>">
				<?php get_template_part('includes/parts/user/partner-account', 'menu'); ?>				
				<section id="submit-frontend-content" class="tab-content initial">
					<?php  while ( have_posts() ) : the_post(); ?>
					<article id="page-<?php the_ID(); ?>">
						<h2><?php the_title(); ?></h2>
						<?php the_content( wp_kses(__( 'Continue reading <span class="meta-nav">&rarr;</span>', 'bookyourtravel' ), array('class' => array())) ); ?>
						<?php wp_link_pages('before=<div class="pagination">&after=</div>'); ?>
						<?php if ($pending_activation) { ?>
						<?php _e('You must activate your account before being able to view this page. Please click the activation link in the email we sent to your email to activate your account.', 'bookyourtravel'); ?>
						<?php } else { ?>
						<?php 
						echo $frontend_submit->render_upload_form($content_type);
						?>
						<?php } ?>
					</article>		
					<?php endwhile; ?>
				</section>
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