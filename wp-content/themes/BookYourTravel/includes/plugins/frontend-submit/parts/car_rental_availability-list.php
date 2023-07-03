<?php
/**
 * Car rental availability frontend submit list template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $bookyourtravel_car_rental_helper, $bookyourtravel_theme_globals;
global $fs_table_extra_fields, $fs_table_extra_field_values, $fs_submit_url;
global $current_author_id, $booking_user_id, $is_partner_page;
$has_admin_role = current_user_can('editor') || current_user_can('administrator');

if (isset($_REQUEST['d']) && $_REQUEST['d'] == '1' && isset($_REQUEST['fesid'])) {
    $availability_id = intval($_REQUEST['fesid']);
    $availability = $bookyourtravel_car_rental_helper->get_car_rental_availability($availability_id);
    if ($availability) {
        $car_rental = new BookYourTravel_Car_Rental($availability->car_rental_id);
        if ($car_rental) {
            $entry_author_id = $car_rental->get_post_author();
            if ($entry_author_id == $current_author_id || $has_admin_role) {
                $bookyourtravel_car_rental_helper->delete_car_rental_availability($availability_id);
            }
        }
    }
}

$submit_user_car_rental_availabilities_url = $bookyourtravel_theme_globals->get_submit_user_car_rental_availabilities_url();
$submit_user_car_rental_availabilities_url_with_arg = $submit_user_car_rental_availabilities_url;
$submit_user_car_rental_availabilities_url_with_arg = add_query_arg( 'fesid', '', $submit_user_car_rental_availabilities_url_with_arg);

$fs_submit_url = $submit_user_car_rental_availabilities_url_with_arg;

$fs_table_extra_fields = $bookyourtravel_car_rental_helper->get_car_rental_availability_fields();
$fs_table_extra_field_values = $bookyourtravel_car_rental_helper->list_car_rental_availabilities(0, null, 0, 'Id', 'ASC', $current_author_id);
?>
<div class="table-holder">
	<table class="fs_content_list">
	<?php get_template_part('includes/plugins/frontend-submit/parts/list-table', 'head'); ?>
	<?php get_template_part('includes/plugins/frontend-submit/parts/list-table', 'body'); ?>
	</table>
</div>