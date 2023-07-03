<?php
/**
 * Sidebar right for single tour
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $post, $current_user, $bookyourtravel_review_helper, $bookyourtravel_theme_globals, $bookyourtravel_tour_helper;

$tour_obj = new BookYourTravel_Tour($post);
$tour_id = $tour_obj->get_id();
$tour_address = $tour_obj->get_formatted_address(false);

$date_from = isset($_GET['from']) && !empty($_GET['from'])  ? date('Y-m-d', strtotime(sanitize_text_field($_GET['from']))) : null;
$date_to = isset($_GET['to']) && !empty($_GET['to']) ? date('Y-m-d', strtotime(sanitize_text_field($_GET['to']))) : null;
$tour_description = wpautop($tour_obj->get_short_description());

if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
	$tour_price = $tour_obj->get_static_from_price();
} else {
    $tour_price = $bookyourtravel_tour_helper->get_min_future_price($tour_id, $date_from, $date_to, true);
}

$item_extra_class = '';
if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
	$item_extra_class = 'skip-ajax-call';
}
?>
<aside id="secondary" class="right-sidebar widget-area one-fourth">
	<ul>
		<li class="widget full-width">
			<article class="tour-details <?php echo esc_attr($item_extra_class); ?>" data-tour-id="<?php echo esc_attr($tour_id); ?>">
				<h1><?php the_title(); ?></h1>
				<?php 

				BookYourTravel_Theme_Controls::the_entity_address($tour_address);
                if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
                    if (is_numeric($tour_price)) {
                        BookYourTravel_Theme_Controls::the_entity_price($tour_price, esc_html__('From', 'bookyourtravel'), "");
                    }
                } else {
                    BookYourTravel_Theme_Controls::the_entity_price($tour_price, esc_html__('From', 'bookyourtravel'), "display:none");
                }
				BookYourTravel_Theme_Controls::the_entity_description($tour_description);
				BookYourTravel_Theme_Controls::the_entity_tags($tour_obj->get_tags(), 'tour_tag');
				
				if ($bookyourtravel_theme_globals->enable_reviews()) {
					$reviews_by_current_user_query = $bookyourtravel_review_helper->list_reviews($tour_obj->get_base_id(), $current_user->ID);	
					if ((!$reviews_by_current_user_query->have_posts() && is_user_logged_in()) || is_super_admin()) {

						$user_bookings =  $bookyourtravel_tour_helper->list_tour_bookings(null, 0, 'Id', 'ASC', null, $current_user->ID);
						$tour_is_booked = false;
						$results = $user_bookings['results'];
						foreach ($results as $result) {
							if ($result->tour_id == $tour_obj->get_base_id()) {
								$tour_is_booked = true;
								break;
							}
						}

						if ($tour_is_booked || is_super_admin()) {
							BookYourTravel_Theme_Controls::the_link_button("#", "gradient-button right leave-review review-tour", "", esc_html__('Leave a review', 'bookyourtravel'));
						}
					}
				}
				
				if (!$tour_obj->get_custom_field('hide_inquiry_form')) {
					BookYourTravel_Theme_Controls::the_link_button("#", "gradient-button right contact-tour", "", esc_html__('Send inquiry', 'bookyourtravel'));
				} 
				?>				
			</article>				
		</li>
		<?php 
		if ($bookyourtravel_theme_globals->enable_reviews()) {
			BookYourTravel_Theme_Controls::the_top_review($tour_obj->get_base_id());
		}
		
		dynamic_sidebar( 'right-tour' );
		?>
	</ul>
</aside><!-- #secondary -->