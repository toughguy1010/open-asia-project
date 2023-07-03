<?php
/*
/* Template Name: User Content List
 * The template for displaying the user submitted content list.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $bookyourtravel_theme_globals, $bookyourtravel_cruise_helper, $bookyourtravel_car_rental_helper, $bookyourtravel_accommodation_helper;
global $bookyourtravel_tour_helper, $bookyourtravel_room_type_helper, $bookyourtravel_review_helper, $bookyourtravel_cabin_type_helper;
global $current_user, $frontend_submit, $item_class, $bookyourtravel_location_helper, $current_author_id, $booking_user_id, $is_partner_page;

if ( !is_user_logged_in()) {
	wp_redirect( home_url('/') );
	exit;
}

get_header();
get_template_part('byt', 'header');
BookYourTravel_Theme_Controls::the_breadcrumbs();
get_sidebar('under-header');

$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id);
$current_url = get_permalink( $page_id );

$content_type = '';
if (isset($page_custom_fields['user_content_type'])) {
    $content_type = $page_custom_fields['user_content_type'][0];
}
$is_partner_page = false;
if (isset($page_custom_fields['user_content_list_is_partner_page'])) {
	$is_partner_page = $page_custom_fields['user_content_list_is_partner_page'][0];
}

$posts_per_row = isset($page_custom_fields['user_content_list_posts_per_row']) ? intval($page_custom_fields['user_content_list_posts_per_row'][0]) : 3;
$posts_per_page = isset($page_custom_fields['user_content_list_posts_per_page']) ? intval($page_custom_fields['user_content_list_posts_per_page'][0]) : 12;

$current_user = wp_get_current_user();
$user_info = get_userdata($current_user->ID);
$current_author_id = $current_user->ID;
$booking_user_id = 0;

if ((current_user_can('editor') || current_user_can('administrator'))) {
	$current_author_id = null; // we will list all items because this is a super admin. when passed to list method if author id is null, parameter is ignored so all items are returned.
}

if (!$is_partner_page) {
    $booking_user_id = $current_user->ID;
}

$enable_reviews = $bookyourtravel_theme_globals->enable_reviews();
$enable_accommodations = $bookyourtravel_theme_globals->enable_accommodations();
$enable_tours = $bookyourtravel_theme_globals->enable_tours();
$enable_cruises = $bookyourtravel_theme_globals->enable_cruises();
$enable_car_rentals = $bookyourtravel_theme_globals->enable_car_rentals();

$paged = 1;
if ( get_query_var('paged-byt') ) {
	$paged = get_query_var('paged-byt');
} else if ( get_query_var('paged') ) {
	$paged = get_query_var('paged');
} else if ( get_query_var('page') ) {
	$paged = get_query_var('page');
}

$page_sidebar_positioning = BookYourTravel_Theme_Utils::get_page_sidebar_positioning($page_id);
$section_class = BookYourTravel_Theme_Utils::get_page_section_class($page_sidebar_positioning);

$pending_activation = false;
if ( in_array( 'pending', (array) $current_user->roles ) ) {
    $pending_activation = true;
}

if ($bookyourtravel_theme_globals->enable_reviews()) {
	get_template_part('includes/parts/review/review', 'form');
}

?>
<script>
	window.feConfirmDelete = <?php echo json_encode(__("Are you sure?", "bookyourtravel")); ?>;
	window.formSingleError = <?php echo json_encode(esc_html__('You failed to provide 1 required field. It has been highlighted below.', 'bookyourtravel')); ?>;
	window.formMultipleError = <?php echo json_encode(esc_html__('You failed to provide {0} required fields. They have been highlighted below.', 'bookyourtravel'));  ?>;	
</script>
		<div class="row">
			<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
				get_sidebar('left');
			?>
			<!--three-fourth content-->
			<section class="<?php echo esc_attr($section_class); ?>">
				<?php
				if ($is_partner_page && $frontend_submit->user_has_correct_role()) {
					get_template_part('includes/parts/user/partner-account', 'menu');
				} else {
					get_template_part('includes/parts/user/user-account', 'menu');
				} ?>
				<section id="list-frontend-content" class="tab-content">
				<?php if ($pending_activation) { ?>
				<?php _e('You must activate your account before being able to view this page. Please click the activation link in the email we sent to your email to activate your account.', 'bookyourtravel'); ?>
				<?php } else { ?>
					<?php  while ( have_posts() ) : the_post(); ?>
					<article id="page-<?php the_ID(); ?>">
						<h2><?php the_title(); ?></h2>
						<?php the_content( wp_kses(__( 'Continue reading <span class="meta-nav">&rarr;</span>', 'bookyourtravel' ), array('class' => array())) ); ?>
						<?php wp_link_pages('before=<div class="pagination">&after=</div>'); ?>
						<?php

						if ($is_partner_page && $frontend_submit->user_has_correct_role() && ($content_type == 'accommodation' ||
							$content_type == 'car_rental' ||
							$content_type == 'cruise' ||
							$content_type == 'location' ||
							$content_type == 'tour' ||
							$content_type == 'room_type' ||
							$content_type == 'cabin_type')) {

							$list_args = array(
								'sort_by' => 'title',
								'sort_order' => 'ASC',
								'posts_per_page' => $posts_per_page,
								'posts_per_row' => $posts_per_row,
								'is_list_page' => true,
								'display_mode' => 'card',
								'paged' => $paged,
								'author_id' => $current_author_id,
								'include_private' => true
							);

							$item_args = array();

							global $room_type_list_args, $cabin_type_list_args, $cruise_list_args, $accommodation_list_args, $location_list_args, $tour_list_args, $car_rental_list_args;
							global $room_type_item_args, $cabin_type_item_args, $cruise_item_args, $accommodation_item_args, $location_item_args, $tour_item_args, $car_rental_item_args;

							switch ($content_type) {
								case 'accommodation':
									$accommodation_list_args = $list_args;
									$accommodation_item_args = $item_args;
									break;
								case 'car_rental':
									$car_rental_list_args = $list_args;
									$car_rental_item_args = $item_args;
									break;
								case 'cruise':
									$cruise_list_args = $list_args;
									$cruise_item_args = $item_args;
									break;
								case 'cabin_type':
									$cabin_type_list_args = $list_args;
									$cabin_type_item_args = $item_args;
									break;
								case 'location':
									$location_list_args = $list_args;
									$location_item_args = $item_args;
									break;
								case 'tour':
									$tour_list_args = $list_args;
									$tour_item_args = $item_args;
									break;
								case 'room_type':
									$room_type_list_args = $list_args;
									$room_type_item_args = $item_args;
									break;
								default:
									break;
							}

							do_action( 'bookyourtravel_frontend_submit_page_' . $content_type . '_list_before' );
							get_template_part('includes/parts/' . $content_type . '/' . $content_type . '', 'list');
							do_action( 'bookyourtravel_frontend_submit_page_' . $content_type . '_list_after' );

						} else if ($content_type == 'accommodation_booking' ||
								$content_type == 'car_rental_booking' ||
								$content_type == 'tour_booking' ||
								$content_type == 'cruise_booking') {

							global $fs_table_list_args;
							$fs_table_list_args = array(
								'sort_by' => 'title',
								'sort_order' => 'ASC',
								'posts_per_page' => $posts_per_page,
								'posts_per_row' => $posts_per_row,
								'paged' => $paged,
							);

							do_action( 'bookyourtravel_frontend_submit_page_' . $content_type . '_list_before' );
							get_template_part('includes/plugins/frontend-submit/parts/' . $content_type . '', 'list');
							do_action( 'bookyourtravel_frontend_submit_page_' . $content_type . '_list_after' );

						} else if ($is_partner_page && $frontend_submit->user_has_correct_role() &&
									($content_type == 'accommodation_vacancy' ||
									$content_type == 'car_rental_availability' ||
									$content_type == 'tour_schedule' ||
									$content_type == 'cruise_schedule')) {

							do_action( 'bookyourtravel_frontend_submit_page_' . $content_type . '_list_before' );
							get_template_part('includes/plugins/frontend-submit/parts/' . $content_type . '', 'list');
							do_action( 'bookyourtravel_frontend_submit_page_' . $content_type . '_list_after' );

						} else if ($content_type == 'review') {

							do_action( 'bookyourtravel_page_review_list_before' );
							get_template_part('includes/parts/review/review', 'list');
							do_action( 'bookyourtravel_page_review_list_after' );

						} // $content_type if
						?>
					</article>
					<?php endwhile; ?>
			<?php 	} // if ($pending_activation) { ?>
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
