<?php

global $fs_table_extra_field_type, $bookyourtravel_theme_globals, $fs_table_extra_fields, $is_partner_page;

if ($fs_table_extra_fields) {

    ob_start();

    echo '<thead>';
    echo '<tr>';
    echo '<th>' . __('Id', 'bookyourtravel') . '</th>';
    foreach ($fs_table_extra_fields as $field) {
        if (isset($field['show_in_fs_list']) && $field['show_in_fs_list']) {
            echo '<th>' . (isset($field['label']) ? $field['label'] : '') . '</th>';
        }
    }

    if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && $fs_table_extra_field_type == "bookings") {
        echo '<th>' . __('Status', 'bookyourtravel') . '</th>';
    }

    if ($is_partner_page) {
        echo '<th></th>';
        echo '<th></th>';
    }
    if (!$is_partner_page && $fs_table_extra_field_type == "bookings") {
        echo '<th></th>';
    }

    echo '</tr>';
    echo '</thead>';

    echo ob_get_clean();
}
