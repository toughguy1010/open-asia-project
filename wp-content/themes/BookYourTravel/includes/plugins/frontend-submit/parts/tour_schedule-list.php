<?php
/**
 * Tour schedule frontend submit list template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $bookyourtravel_tour_helper, $bookyourtravel_theme_globals;
global $fs_table_extra_fields, $fs_table_extra_field_values, $fs_submit_url;
global $current_author_id, $booking_user_id, $is_partner_page;
$has_admin_role = current_user_can('editor') || current_user_can('administrator');

if (isset($_REQUEST['d']) && $_REQUEST['d'] == '1' && isset($_REQUEST['fesid'])) {
    $schedule_id = intval($_REQUEST['fesid']);
    $schedule = $bookyourtravel_tour_helper->get_tour_schedule($schedule_id);
    if ($schedule) {
        $tour = new BookYourTravel_Tour($schedule->tour_id);
        if ($tour) {
            $entry_author_id = $tour->get_post_author();
            if ($entry_author_id == $current_author_id || $has_admin_role) {
                $bookyourtravel_tour_helper->delete_tour_schedule($schedule_id);
            }
        }
    }
}

$submit_user_tour_schedules_url = $bookyourtravel_theme_globals->get_submit_user_tour_schedules_url();
$submit_user_tour_schedules_url_with_arg = $submit_user_tour_schedules_url;
$submit_user_tour_schedules_url_with_arg = add_query_arg( 'fesid', '', $submit_user_tour_schedules_url_with_arg);

$fs_submit_url = $submit_user_tour_schedules_url_with_arg;

$fs_table_extra_fields = $bookyourtravel_tour_helper->get_tour_schedule_fields();
$fs_table_extra_field_values = $bookyourtravel_tour_helper->list_tour_schedules(null, 0, 'Id', 'ASC', 0, 0, 0, 0, '', $current_author_id);

foreach ($fs_table_extra_field_values['results'] as $schedule) {
	$tour_id = $schedule->tour_id;
	$tour_obj = new BookYourTravel_Tour($tour_id);
	$type_is_repeated = $tour_obj->get_type_is_repeated();
	if ($type_is_repeated == 0) {
		// not repeated
		$schedule->end_date = "";
	}
}

?>
<div class="table-holder">
	<table class="fs_content_list">
	<?php get_template_part('includes/plugins/frontend-submit/parts/list-table', 'head'); ?>
	<?php get_template_part('includes/plugins/frontend-submit/parts/list-table', 'body'); ?>
	</table>
</div>
