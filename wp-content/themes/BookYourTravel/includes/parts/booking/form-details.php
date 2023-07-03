<?php
global $entity_obj, $bookyourtravel_theme_globals, $booking_form_fields, $enable_extra_items, $bookyourtravel_theme_of_custom, $section_class;

$booking_form_fields = $bookyourtravel_theme_globals->get_booking_form_fields();

$enable_deposit_payments = $bookyourtravel_theme_globals->enable_deposit_payments();
$item_type_keyword_type = $entity_obj->get_post_type();
$post_type_obj = get_post_type_object($item_type_keyword_type);
$item_type_keyword_lowercase = strtolower($post_type_obj->labels->singular_name);
$item_type_keyword_uppercase = ucfirst($item_type_keyword_lowercase);

do_action( 'bookyourtravel_show_' . $item_type_keyword_type . '_booking_form_before' ); ?>
<section class="<?php echo esc_attr($section_class); ?> <?php echo esc_attr($item_type_keyword_type); ?>-booking-section booking-section modal" style="display:none">
	<div class="static-content">
		<form id="<?php echo esc_attr($item_type_keyword_type); ?>-booking-form" method="post" action="<?php echo BookYourTravel_Theme_Utils::get_current_page_url(); ?>">
			<fieldset>
				<h3><?php esc_html_e('Booking details', 'bookyourtravel') ?></h3>

				<div class="output">
					<div class="row">
						<?php do_action('booking_form_details_' . $item_type_keyword_type . '_core_fields'); ?>
						<div class="totals">
							<?php if ($enable_extra_items) { ?>
							<div class="output full-width">
								<p><?php esc_html_e('Reservation total', 'bookyourtravel') ?>:
									<strong class="booking_form_reservation_total_p"></strong>
								</p>
							</div>
							<div class="output full-width">
								<p><?php esc_html_e('Extra items total', 'bookyourtravel') ?>:
									<strong class="booking_form_extra_items_total_p"></strong>
								</p>
							</div>
							<?php } ?>
							<div class="output full-width">
								<p><?php esc_html_e('Total', 'bookyourtravel') ?>:
									<strong class="booking_form_total_p"></strong>
								</p>
							</div>
						</div>
					</div>
				</div>

				<h3><?php esc_html_e('Submit booking', 'bookyourtravel') ?></h3>
				<div class="error-summary error" style="display:none;"><div><p></p></div></div>
				<div class="row">
				<?php
				foreach ($booking_form_fields as $booking_field) {

					$field_type = $booking_field['type'];
					$field_hidden = isset($booking_field['hide']) && $booking_field['hide'] == 1 ? true : false;
					$field_required = isset($booking_field['required']) && $booking_field['required'] == 1 ? true : false;
					$field_id = $booking_field['id'];
					$booking_field["unique_id"] = "booking_form_" . $booking_field["id"];

					$field_label = isset($booking_field['label']) ? $booking_field['label'] : '';
					$field_label = $bookyourtravel_theme_of_custom->get_translated_dynamic_string($bookyourtravel_theme_of_custom->get_option_id_context('booking_form_fields') . ' ' . $field_label, $field_label);

					$field_value = isset($user_info->{$field_id}) ? $user_info->{$field_id} : '';
					if (empty($field_value)) {
						$field_value = isset($user_info->{'billing_' . $field_id}) ? $user_info->{'billing_' . $field_id} : '';
					}
					if (empty($field_value)) {
						if ($field_id == 'zip') {
							$field_value = isset($user_info->{'billing_postcode'}) ? $user_info->{'billing_postcode'} : '';
						} elseif ($field_id == 'town') {
							$field_value = isset($user_info->{'billing_city'}) ? $user_info->{'billing_city'} : '';
						} elseif ($field_id == 'address') {
							$field_value = isset($user_info->{'billing_address_1'}) ? $user_info->{'billing_address_1'} : '';
						}
					}

					if (!$field_hidden) {
						if ($field_type == 'email') { ?>
						<div class="f-item one-half">
							<label for="<?php echo esc_attr($field_id) ?>"><?php echo esc_html($field_label); ?><?php echo $field_required ? ' *' : ''; ?></label>
							<input value="<?php echo esc_attr($field_value); ?>" <?php echo isset($booking_field['required']) && $booking_field['required'] == '1' ? 'data-required' : ''; ?> type="email" id="<?php echo esc_attr($field_id) ?>" name="<?php echo esc_attr($field_id) ?>" />
						</div>
					<?php } else if ($field_type == 'textarea') { ?>
						<div class="f-item full-width">
							<label><?php echo esc_html($field_label); ?><?php echo $field_required ? ' *' : ''; ?></label>
							<textarea <?php echo isset($booking_field['required']) && $booking_field['required'] == '1' ? 'data-required' : ''; ?> name='<?php echo esc_attr($field_id) ?>' id='<?php echo esc_attr($field_id) ?>' rows="10" cols="10" ><?php echo esc_html($field_value); ?></textarea>
						</div>
					<?php } else if ($field_type == 'checkbox') { ?>
						<div class="f-item full-width">
							<?php BookYourTravel_Theme_Admin_Controls::the_dynamic_field_checkbox_control($booking_field); ?>
							<label for="<?php echo esc_attr($booking_field['id']) ?>"><?php echo wp_kses($field_label, array("a" => array('href', 'class'))); ?></label>
						</div>
					<?php } else if ($field_type == 'select') { ?>
						<div class="f-item full-width">
							<label for="<?php echo esc_attr($booking_field['id']) ?>"><?php echo wp_kses($field_label, array("a" => array('href', 'class'))); ?></label>
							<?php BookYourTravel_Theme_Admin_Controls::the_dynamic_field_select_control($booking_field); ?>
						</div>
					<?php } else { ?>
						<div class="f-item one-half">
							<label for="<?php echo esc_attr($field_id) ?>"><?php echo esc_html($field_label); ?><?php echo $field_required ? ' *' : ''; ?></label>
							<input value="<?php echo esc_attr($field_value); ?>" <?php echo isset($booking_field['required']) && $booking_field['required'] == '1' ? 'data-required' : ''; ?> type="text" name="<?php echo esc_attr($field_id) ?>" id="<?php echo esc_attr($field_id) ?>" />
						</div>
					<?php
						}
					}
				}
				?>
				</div>
				<?php if ($bookyourtravel_theme_globals->enable_gdpr()) { ?>
				<div class="row">
					<div class="f-item full-width">
					<?php
					$gdpr_field = $bookyourtravel_theme_of_custom->get_gdpr_checkbox_field();
					$gdpr_field["unique_id"] = "booking_form_" . $gdpr_field["id"];
					?>
					<?php BookYourTravel_Theme_Admin_Controls::the_dynamic_field_checkbox_control($gdpr_field); ?>
					<label for="<?php echo esc_attr($gdpr_field['id']) ?>"><?php echo wp_kses($gdpr_field['label'], array("a" => array("href" => array(), "target" => array(), "class" => array()))); ?></label>				
					</div>
				</div>
				<?php } ?>
				<input type="hidden" name="<?php echo esc_attr($item_type_keyword_type); ?>_id" id="<?php echo esc_attr($item_type_keyword_type); ?>_id" />
				<div class="booking-commands">
					<?php BookYourTravel_Theme_Controls::the_link_button("#", "gradient-button right", "submit-" . $item_type_keyword_type . "-booking", esc_html__('Submit booking', 'bookyourtravel')); ?>
					<?php BookYourTravel_Theme_Controls::the_link_button("#", "gradient-button cancel-" . $item_type_keyword_type . "-booking", "cancel-" . $item_type_keyword_type . "-booking", esc_html__('Go Back', 'bookyourtravel')); ?>
				</div>
			</fieldset>
		</form>
	</div>
</section>
<?php
do_action( 'bookyourtravel_show_' . $item_type_keyword_type . '_booking_form_after' );