<?php

global $fs_table_extra_field_type, $bookyourtravel_theme_globals, $fs_table_extra_fields, $fs_table_extra_field_values, $fs_submit_url, $is_partner_page;

if ($fs_table_extra_field_values && $fs_table_extra_fields) {

    $total_items = isset($fs_table_extra_field_values['total']) ? $fs_table_extra_field_values['total'] : 0;
    $current_url = BookYourTravel_Theme_Utils::get_current_page_url_no_query();

    if ($total_items > 0 && isset($fs_table_extra_field_values['results'])) {

        $results = $fs_table_extra_field_values['results'];

		$status_array = array (
			'pending' => esc_html__('Pending', 'bookyourtravel'),
			'on-hold' => esc_html__('On hold', 'bookyourtravel'),
			'completed' => esc_html__('Completed', 'bookyourtravel'),
			'processing' => esc_html__('Processing', 'bookyourtravel'),
			'cancelled' => esc_html__('Cancelled', 'bookyourtravel'),
			'initiated' => esc_html__('Initiated', 'bookyourtravel'),
		);

        ob_start();

        echo '<tbody>';
        foreach ($results as $result) {
            $result_id = (isset($result->Id) ? $result->Id : '');
            echo '<tr>';
            echo '<td>' . $result_id . '</td>';
            foreach ($fs_table_extra_fields as $field) {
                $show_in_list = isset($field['show_in_fs_list']) ? $field['show_in_fs_list'] : false;
                $field_id = isset($field['id']) ? $field['id'] : '';
                $field_type = isset($field['type']) ? $field['type'] : 'text';
                if ($show_in_list) {
                    echo '<td>' . format_fs_field($field_id, $field_type, $field, $result) . '</td>';
                }
            }

            if ($bookyourtravel_theme_globals->use_woocommerce_for_checkout() && $fs_table_extra_field_type == "bookings") {
                echo '<td>' . (isset($result->woo_status) && isset($status_array[$result->woo_status]) ? $status_array[$result->woo_status] : "") . '</td>';
            }

            if ($is_partner_page) {
                echo '<td>' . format_edit_button($result_id, $fs_submit_url) . '</td>';
                echo '<td>' . format_delete_button($result_id, $current_url) . '</td>';
            }

            if (!$is_partner_page && $fs_table_extra_field_type == "bookings") {
                echo '<td>' . format_view_button($result_id, $current_url) . '</td>';
            }

            echo '</tr>';
        }
        echo '</tbody>';

        echo ob_get_clean();
    }
}

function format_delete_button($result_id, $current_url) {
    if (!empty($result_id)) {
        $current_url = add_query_arg('fesid', $result_id, $current_url);
        $current_url = add_query_arg('d', '1', $current_url);
        return sprintf("<a href='%s'>", $current_url) . __("Delete", "bookyourtravel") . "</a>";
    }
    return "";
}

function format_edit_button($result_id, $submit_url) {
    if (!empty($result_id)) {
        return sprintf("<a href='%s=%s'>", $submit_url, $result_id) . __("Edit", "bookyourtravel") . "</a>";
    }
    return "";
}

function format_view_button($result_id, $list_url) {
    if (!empty($result_id)) {
        return sprintf("<a href='%s?bid=%s'>", $list_url, $result_id) . __("View", "bookyourtravel") . "</a>";
    }
    return "";
}

function format_fs_field($field_id, $field_type, $field, $result) {
	$value = (isset($result->{$field_id}) ? $result->{$field_id} : '');

	if ($field_type == 'text' || $field_type == 'textarea' || $field_type == 'editor') {
		return $value;
	} else if ($field_type == 'number') {
		if (strpos($field_id, 'price') !== false) {
			return format_fs_price($value);
		}
		return $value;
	} else if ($field_type == 'post_select') {
        if ($value > 0) {
		    $post = get_post($value);
            return isset($post) && isset($post->post_title) ? $post->post_title : '';
        } else {
            return '';
        }
	} else if ($field_type == 'datepicker') {
		if (isset($result->{$field_id}) && $result->{$field_id}) {
			$date_format = get_option('date_format');
		    return date_i18n( $date_format, strtotime( $value ) );
        } else {
            return "";
        }
	}

	return $value;
}

function format_fs_price($price) {
	global $bookyourtravel_theme_globals;
	$default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
	$show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();
    $price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();
    
    $price = BookYourTravel_Theme_Utils::get_price_in_current_currency($price);

	if (!$show_currency_symbol_after) {
		return $default_currency_symbol . '' . number_format_i18n( $price, $price_decimal_places );
	} else {
		return number_format_i18n( $price, $price_decimal_places ) . '' . $default_currency_symbol;
	}
}
