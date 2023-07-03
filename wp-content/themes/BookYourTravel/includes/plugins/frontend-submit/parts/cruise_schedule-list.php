<?php
/**
 * Cruise schedule frontend submit list template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $bookyourtravel_cruise_helper, $bookyourtravel_theme_globals;
global $fs_table_extra_fields, $fs_table_extra_field_values, $fs_submit_url;
global $current_author_id, $booking_user_id, $is_partner_page;
$has_admin_role = current_user_can('editor') || current_user_can('administrator');

if (isset($_REQUEST['d']) && $_REQUEST['d'] == '1' && isset($_REQUEST['fesid'])) {
    $schedule_id = intval($_REQUEST['fesid']);
    $schedule = $bookyourtravel_cruise_helper->get_cruise_schedule($schedule_id);
    if ($schedule) {
        $cruise = new BookYourTravel_Cruise($schedule->cruise_id);
        if ($cruise) {
            $entry_author_id = $cruise->get_post_author();
            if ($entry_author_id == $current_author_id || $has_admin_role) {
                $bookyourtravel_cruise_helper->delete_cruise_schedule($schedule_id);
            }
        }
    }
}

$submit_user_cruise_schedules_url = $bookyourtravel_theme_globals->get_submit_user_cruise_schedules_url();
$submit_user_cruise_schedules_url_with_arg = $submit_user_cruise_schedules_url;
$submit_user_cruise_schedules_url_with_arg = add_query_arg( 'fesid', '', $submit_user_cruise_schedules_url_with_arg);

$fs_submit_url = $submit_user_cruise_schedules_url_with_arg;

$fs_table_extra_fields = $bookyourtravel_cruise_helper->get_cruise_schedule_fields();
$fs_table_extra_field_values = $bookyourtravel_cruise_helper->list_cruise_schedules(null, 0, 'Id', 'ASC', 0, 0, 0, 0, 0, '', $current_author_id);
foreach ($fs_table_extra_field_values['results'] as $schedule) {
	$cruise_id = $schedule->cruise_id;
	$cruise_obj = new BookYourTravel_Cruise($cruise_id);
	$type_is_repeated = $cruise_obj->get_type_is_repeated();
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