<?php
/**
 * Accommodation vacancy frontend submit list template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
global $bookyourtravel_accommodation_helper, $bookyourtravel_theme_globals;
global $fs_table_extra_fields, $fs_table_extra_field_values, $fs_submit_url;
global $current_author_id, $booking_user_id, $is_partner_page;
$has_admin_role = current_user_can('editor') || current_user_can('administrator');

if (isset($_REQUEST['d']) && $_REQUEST['d'] == '1' && isset($_REQUEST['fesid'])) {
    $vacancy_id = intval($_REQUEST['fesid']);
    $vacancy = $bookyourtravel_accommodation_helper->get_accommodation_vacancy($vacancy_id);
    if ($vacancy) {
        $accommodation = new BookYourTravel_Accommodation($vacancy->accommodation_id);
        if ($accommodation) {
            $entry_author_id = $accommodation->get_post_author();
            if ($entry_author_id == $current_author_id || $has_admin_role) {
                $bookyourtravel_accommodation_helper->delete_accommodation_vacancy($vacancy_id);
            }
        }
    }
}

$submit_user_accommodation_vacancies_url = $bookyourtravel_theme_globals->get_submit_user_accommodation_vacancies_url();
$submit_user_accommodation_vacancies_url_with_arg = $submit_user_accommodation_vacancies_url;
$submit_user_accommodation_vacancies_url_with_arg = add_query_arg( 'fesid', '', $submit_user_accommodation_vacancies_url_with_arg);

$fs_submit_url = $submit_user_accommodation_vacancies_url_with_arg;

$fs_table_extra_fields = $bookyourtravel_accommodation_helper->get_accommodation_vacancy_fields();
$fs_table_extra_field_values = $bookyourtravel_accommodation_helper->list_accommodation_vacancies(0, 0, 'Id', 'ASC', null, 0, $current_author_id);
?>
<div class="table-holder">
	<table class="fs_content_list">
	<?php get_template_part('includes/plugins/frontend-submit/parts/list-table', 'head'); ?>
	<?php get_template_part('includes/plugins/frontend-submit/parts/list-table', 'body'); ?>
	</table>
</div>