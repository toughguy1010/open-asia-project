<?php
/**
 * Sidebar right for single accommodation
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $post, $current_user, $bookyourtravel_review_helper, $bookyourtravel_theme_globals, $bookyourtravel_accommodation_helper;

$accommodation_obj = new BookYourTravel_Accommodation($post);
$accommodation_id = $accommodation_obj->get_id();

$accommodation_rent_type_str = $accommodation_obj->get_formatted_rent_type();
$accommodation_address = $accommodation_obj->get_formatted_address(false);

$date_from = isset($_GET['from']) && !empty($_GET['from'])  ? date('Y-m-d', strtotime(sanitize_text_field($_GET['from']))) : null;
$date_to = isset($_GET['to']) && !empty($_GET['to']) ? date('Y-m-d', strtotime(sanitize_text_field($_GET['to']))) : null;
$accommodation_description = wpautop($accommodation_obj->get_short_description());

if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
	$accommodation_price = $accommodation_obj->get_static_from_price();
} else {
    $accommodation_price = $bookyourtravel_accommodation_helper->get_min_future_price($accommodation_id, 0, $date_from, $date_to, true);
}

$item_extra_class = '';
if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
	$item_extra_class = 'skip-ajax-call';
}
?>
<aside id="secondary" class="right-sidebar widget-area one-fourth">
	<ul>
		<li class="widget full-width">
			<article class="accommodation-details hotel-details <?php echo esc_attr($item_extra_class); ?>" data-accommodation-id="<?php echo esc_attr($accommodation_id); ?>">
				<h1>
					<?php the_title(); ?>
					<?php BookYourTravel_Theme_Controls::the_entity_stars($accommodation_obj->get_custom_field('star_count')); ?>
				</h1>
				<?php 

				BookYourTravel_Theme_Controls::the_entity_address($accommodation_address);
				if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
                    if (is_numeric($accommodation_price)) {
                        BookYourTravel_Theme_Controls::the_entity_price($accommodation_price, esc_html__('From', 'bookyourtravel'), "");
                    }
                } else {
					BookYourTravel_Theme_Controls::the_entity_price($accommodation_price, esc_html__('From', 'bookyourtravel'), "display:none");
				}
				BookYourTravel_Theme_Controls::the_entity_description($accommodation_description);
				BookYourTravel_Theme_Controls::the_entity_tags($accommodation_obj->get_tags(), 'acc_tag');
				
				if ($bookyourtravel_theme_globals->enable_reviews()) {
					$reviews_by_current_user_query = $bookyourtravel_review_helper->list_reviews($accommodation_obj->get_base_id(), $current_user->ID);	
					if ((!$reviews_by_current_user_query->have_posts() && is_user_logged_in()) || is_super_admin()) {

						$user_bookings = $bookyourtravel_accommodation_helper->list_accommodation_bookings(null, 0, 'Id', 'ASC', null, $current_user->ID);
						$accommodation_is_booked = false;
						$results = $user_bookings['results'];
						foreach ($results as $result) {
							if ($result->accommodation_id == $accommodation_obj->get_base_id()) {
								$accommodation_is_booked = true;
								break;
							}
						}

						if ($accommodation_is_booked || is_super_admin()) {
							BookYourTravel_Theme_Controls::the_link_button("#", "gradient-button right leave-review review-accommodation", "", esc_html__('Leave a review', 'bookyourtravel'));
						}
					}
				}
				
				if (!$accommodation_obj->get_custom_field('hide_inquiry_form')) {
					BookYourTravel_Theme_Controls::the_link_button("#", "gradient-button right contact-accommodation", "", esc_html__('Send inquiry', 'bookyourtravel'));
				} 
				?>				
			</article>				
		</li>
		<?php 
		if ($bookyourtravel_theme_globals->enable_reviews()) {
			BookYourTravel_Theme_Controls::the_top_review($accommodation_obj->get_base_id());
		}		
		dynamic_sidebar( 'right-accommodation' );
		?>
	</ul>
</aside><!-- #secondary -->