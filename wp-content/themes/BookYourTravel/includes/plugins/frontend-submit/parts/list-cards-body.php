<?php

global $fs_table_extra_field_type, $bookyourtravel_theme_globals, $fs_table_extra_fields, $fs_table_extra_field_values, $fs_submit_url, $is_partner_page;

global $fs_table_list_args;

$posts_per_page = isset($fs_table_list_args['posts_per_page']) ? $fs_table_list_args['posts_per_page'] : 12;

if ($fs_table_extra_field_values && $fs_table_extra_fields) {

    $total_items = isset($fs_table_extra_field_values['total']) ? $fs_table_extra_field_values['total'] : 0;
    $current_url = BookYourTravel_Theme_Utils::get_current_page_url_no_query();

    if ($total_items > 0 && isset($fs_table_extra_field_values['results'])) {

        $results = $fs_table_extra_field_values['results'];
        $total_results = $fs_table_extra_field_values['total'];

        ob_start();

        echo '<div class="row">';

        foreach ($results as $result) {
            global $booked_item_args;
            $booked_item_args = array();

            $booked_item_args['booking'] = $result;
            $booked_item_args['woo_status'] = $result->woo_status;
            $booked_item_args['created'] = $result->created;
            $booked_item_args['total_price'] = $result->total_price;
            $booked_item_args['result'] = $result;

            if ($result->entry_type == 'accommodation_booking') {
                $booked_item_args['post_id'] = $result->accommodation_id;
            } else if ($result->entry_type == 'car_rental_booking') {
                $booked_item_args['post_id'] = $result->car_rental_id;
            } else if ($result->entry_type == 'cruise_booking') {
                $booked_item_args['post_id'] = $result->cruise_id;
            } else if ($result->entry_type == 'tour_booking') {
                $booked_item_args['post_id'] = $result->tour_id;
            }

            get_template_part('includes/plugins/frontend-submit/parts/booked-item', 'card');
        }

        echo '</div>';

        // if ($total_results > $posts_per_page && $posts_per_page > 0) {
        //     BookYourTravel_Theme_Controls::the_pager_outer(ceil($total_results / $posts_per_page));
        // }

        echo ob_get_clean();
    }
}
