<?php

global $fs_table_extra_field_type, $bookyourtravel_theme_globals, $fs_table_extra_fields, $fs_table_extra_field_values, $fs_submit_url, $is_partner_page;

$booking_id = isset($_REQUEST["bid"]) ? intval($_REQUEST["bid"]) : 0;

if ($fs_table_extra_field_values && $fs_table_extra_fields) {

    $total_items = isset($fs_table_extra_field_values['total']) ? $fs_table_extra_field_values['total'] : 0;

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
            $result_id = (isset($result->Id) ? intval($result->Id) : '');

            if ($booking_id == $result_id) {
                echo '<tr><th>' . esc_html__("Booking Id", "bookyourtravel") . '</th><td>' . esc_html($result_id) . '</td></tr>';
                foreach ($fs_table_extra_fields as $field) {
                    echo '<tr>';
                    $field_id = isset($field['id']) ? $field['id'] : '';
                    $field_type = isset($field['type']) ? $field['type'] : 'text';
                    $field_label = (isset($field['label']) ? $field['label'] : '');
                    echo '<th>' . $field_label . '</th>';
                    echo '<td>' . format_fs_field($field_id, $field_type, $field, $result) . '</td>';
                    echo '</tr>';
                }
            }
        }
        echo '<tbody>';

        echo ob_get_clean();
    }
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
		$date_format = get_option('date_format');
		return date_i18n( $date_format, strtotime( $value ) );
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
