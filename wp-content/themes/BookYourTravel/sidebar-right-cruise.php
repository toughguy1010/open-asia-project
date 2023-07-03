<?php
/**
 * Sidebar right for single cruise
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $post, $current_user, $bookyourtravel_review_helper, $bookyourtravel_theme_globals, $bookyourtravel_cruise_helper;

$cruise_obj = new BookYourTravel_Cruise($post);
$cruise_id = $cruise_obj->get_id();
$cruise_address = $cruise_obj->get_formatted_address(false);

$date_from = isset($_GET['from']) && !empty($_GET['from'])  ? date('Y-m-d', strtotime(sanitize_text_field($_GET['from']))) : null;
$date_to = isset($_GET['to']) && !empty($_GET['to']) ? date('Y-m-d', strtotime(sanitize_text_field($_GET['to']))) : null;
$cruise_description = wpautop($cruise_obj->get_short_description());

if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
	$cruise_price = $cruise_obj->get_static_from_price();
} else {
    $cruise_price = $bookyourtravel_cruise_helper->get_min_future_price($cruise_id, 0, $date_from, $date_to, true);
}

$item_extra_class = '';
if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
	$item_extra_class = 'skip-ajax-call';
}
?>
<aside id="secondary" class="right-sidebar widget-area one-fourth">
	<ul>
		<li class="widget full-width">
			<article class="cruise-details <?php echo esc_attr($item_extra_class); ?>" data-cruise-id="<?php echo esc_attr($cruise_id); ?>">
				<h1><?php the_title(); ?></h1>
				<?php 

				BookYourTravel_Theme_Controls::the_entity_address($cruise_address);
                if ($bookyourtravel_theme_globals->show_static_prices_in_grids()) {
                    if (is_numeric($cruise_price)) {
                        BookYourTravel_Theme_Controls::the_entity_price($cruise_price, esc_html__('From', 'bookyourtravel'), "");
                    }
                } else {
                    BookYourTravel_Theme_Controls::the_entity_price($cruise_price, esc_html__('From', 'bookyourtravel'), "display:none");
                }
				BookYourTravel_Theme_Controls::the_entity_description($cruise_description);
				BookYourTravel_Theme_Controls::the_entity_tags($cruise_obj->get_tags(), 'cruise_tag');
				
				if ($bookyourtravel_theme_globals->enable_reviews()) {
					$reviews_by_current_user_query = $bookyourtravel_review_helper->list_reviews($cruise_obj->get_base_id(), $current_user->ID);	
					if ((!$reviews_by_current_user_query->have_posts() && is_user_logged_in()) || is_super_admin()) {

						$user_bookings = $bookyourtravel_cruise_helper->list_cruise_bookings(null, 0, 'Id', 'ASC', null, $current_user->ID);
						$cruise_is_booked = false;
						$results = $user_bookings['results'];
						foreach ($results as $result) {
							if ($result->cruise_id == $cruise_obj->get_base_id()) {
								$cruise_is_booked = true;
								break;
							}
						}

						if ($cruise_is_booked || is_super_admin()) {
							BookYourTravel_Theme_Controls::the_link_button("#", "gradient-button right leave-review review-cruise", "", esc_html__('Leave a review', 'bookyourtravel'));
						}
					}
				}
				
				if (!$cruise_obj->get_custom_field('hide_inquiry_form')) {
					BookYourTravel_Theme_Controls::the_link_button("#", "gradient-button right contact-cruise", "", esc_html__('Send inquiry', 'bookyourtravel'));
				} 
				?>				
			</article>				
		</li>
		<?php
		if ($bookyourtravel_theme_globals->enable_reviews()) {
			BookYourTravel_Theme_Controls::the_top_review($cruise_obj->get_base_id());
		}
		dynamic_sidebar( 'right-cruise' );
		?>
	</ul>
</aside><!-- #secondary -->