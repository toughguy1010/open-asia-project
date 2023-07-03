<?php
/**
 * Sidebar right for single car rental
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $post, $current_user, $bookyourtravel_review_helper, $bookyourtravel_theme_globals, $bookyourtravel_car_rental_helper;

$car_rental_obj = new BookYourTravel_Car_Rental($post);
$car_rental_id = $car_rental_obj->get_id();
$car_rental_address = $car_rental_obj->get_formatted_address(false);

$date_from = isset($_GET['from']) && !empty($_GET['from'])  ? date('Y-m-d', strtotime(sanitize_text_field($_GET['from']))) : null;
$date_to = isset($_GET['to']) && !empty($_GET['to']) ? date('Y-m-d', strtotime(sanitize_text_field($_GET['to']))) : null;

if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
	$car_rental_price = $car_rental_obj->get_static_from_price();
} else {
    $car_rental_price = $bookyourtravel_car_rental_helper->get_min_future_price($car_rental_id, $date_from, $date_to, true);
}
$car_rental_description = wpautop($car_rental_obj->get_short_description());

$item_extra_class = '';
if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
	$item_extra_class = 'skip-ajax-call';
}
?>
<aside id="secondary" class="right-sidebar widget-area one-fourth">
	<ul>
		<li class="widget full-width">
			<article class="car_rental-details <?php echo esc_attr($item_extra_class); ?>" data-car-rental-id="<?php echo esc_attr($car_rental_id); ?>">
				<h1><?php the_title(); ?></h1>
				<?php 

				BookYourTravel_Theme_Controls::the_entity_address($car_rental_address);
                if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
                    if (is_numeric($car_rental_price)) {
                        BookYourTravel_Theme_Controls::the_entity_price($car_rental_price, esc_html__('From', 'bookyourtravel'), "");
                    }
                } else {
                    BookYourTravel_Theme_Controls::the_entity_price($car_rental_price, esc_html__('From', 'bookyourtravel'), "display:none");
                }
				BookYourTravel_Theme_Controls::the_entity_description($car_rental_description);
				BookYourTravel_Theme_Controls::the_entity_tags($car_rental_obj->get_tags(), 'car_rental_tag');
				
				if ($bookyourtravel_theme_globals->enable_reviews()) {
					$reviews_by_current_user_query = $bookyourtravel_review_helper->list_reviews($car_rental_obj->get_base_id(), $current_user->ID);	
					if ((!$reviews_by_current_user_query->have_posts() && is_user_logged_in()) || is_super_admin()) {

						$user_bookings = $bookyourtravel_car_rental_helper->list_car_rental_bookings(null, 'Id', 'ASC', null, 0, $current_user->ID);
						$car_rental_is_booked = false;
						$results = $user_bookings['results'];
						foreach ($results as $result) {
							if ($result->car_rental_id == $car_rental_obj->get_base_id()) {
								$car_rental_is_booked = true;
								break;
							}
						}

						if ($car_rental_is_booked || is_super_admin()) {
							BookYourTravel_Theme_Controls::the_link_button("#", "gradient-button right leave-review review-car_rental", "", esc_html__('Leave a review', 'bookyourtravel'));
						}
					}
				}
				
				if (!$car_rental_obj->get_custom_field('hide_inquiry_form')) {
					BookYourTravel_Theme_Controls::the_link_button("#", "gradient-button right contact-car_rental", "", esc_html__('Send inquiry', 'bookyourtravel'));
				} 
				?>				
			</article>				
		</li>
		<?php
		if ($bookyourtravel_theme_globals->enable_reviews()) {
			BookYourTravel_Theme_Controls::the_top_review($car_rental_obj->get_base_id());
		}
		
		dynamic_sidebar( 'right-car_rental' );
		?>
	</ul>
</aside><!-- #secondary -->