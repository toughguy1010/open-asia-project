<?php
/**
 * Tour booking frontend submit list template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $bookyourtravel_tour_helper, $bookyourtravel_theme_globals;
global $fs_table_extra_fields, $fs_table_extra_field_values, $fs_submit_url, $fs_table_extra_field_type, $fs_table_list_args;
global $current_author_id, $booking_user_id, $is_partner_page;
$has_admin_role = current_user_can('editor') || current_user_can('administrator');

if (isset($_REQUEST['d']) && $_REQUEST['d'] == '1' && isset($_REQUEST['fesid'])) {
    $booking_id = intval($_REQUEST['fesid']);
    $booking = $bookyourtravel_tour_helper->get_tour_booking($booking_id);
    if ($booking) {
        $tour = new BookYourTravel_Tour($booking->tour_id);
        if ($tour) {
            $entry_author_id = $tour->get_post_author();
            if ($entry_author_id == $current_author_id || $has_admin_role) {
                $bookyourtravel_tour_helper->delete_tour_booking($booking_id);
            }
        }
    }
}

$submit_user_tour_bookings_url = $bookyourtravel_theme_globals->get_submit_user_tour_bookings_url();
$submit_user_tour_bookings_url_with_arg = $submit_user_tour_bookings_url;
$submit_user_tour_bookings_url_with_arg = add_query_arg( 'fesid', '', $submit_user_tour_bookings_url_with_arg);

$fs_submit_url = $submit_user_tour_bookings_url_with_arg;

$posts_per_page = isset($fs_table_list_args['posts_per_page']) ? $fs_table_list_args['posts_per_page'] : 12;
$paged = isset($fs_table_list_args['paged']) ? $fs_table_list_args['paged'] : 1;

$fs_table_extra_fields = $bookyourtravel_tour_helper->get_tour_booking_fields();
$fs_table_extra_field_values = $bookyourtravel_tour_helper->list_tour_bookings($paged, $posts_per_page, 'Id', 'DESC', null, $booking_user_id, $is_partner_page ? $current_author_id : null);
$fs_table_extra_field_type = "bookings";

$current_url = BookYourTravel_Theme_Utils::get_current_page_url_no_query();
$booking_id = isset($_REQUEST["bid"]) ? intval($_REQUEST["bid"]) : 0;
$holder_class = 'cards-holder';
if ($is_partner_page) {
    $holder_class = 'table-holder';
}
?>
<div class="<?php echo esc_attr($holder_class); ?>">
    <?php if ($booking_id == 0) { ?>
        <?php if ($is_partner_page) { ?>
            <table class="fs_content_list">
            <?php
                get_template_part('includes/plugins/frontend-submit/parts/list-table', 'head');
                get_template_part('includes/plugins/frontend-submit/parts/list-table', 'body');
            ?>
            </table>
        <?php
        } else {
        ?>
        <div class="deals">
           <?php get_template_part('includes/plugins/frontend-submit/parts/list-cards', 'body'); ?>
        </div>
        <?php }
			$total_results = $fs_table_extra_field_values['total'];
			if ($total_results > $posts_per_page && $posts_per_page > 0) {
				BookYourTravel_Theme_Controls::the_pager_outer(ceil($total_results/$posts_per_page));
			}
        ?>
    <?php } else { ?>
    <table class="fs_content_single">
        <?php get_template_part('includes/plugins/frontend-submit/parts/single-table', 'body'); ?>
    </table>
    <a class="list-link gradient-button" href="<?php echo esc_url($current_url); ?>"><?php echo esc_html__("&laquo; Back to list", "bookyourtravel"); ?></a>
    <?php } ?>
</div>
