<?php 
global $entity_obj, $bookyourtravel_theme_globals, $booking_form_fields, $enable_extra_items, $bookyourtravel_theme_of_custom, $section_class;

$booking_form_fields = $bookyourtravel_theme_globals->get_booking_form_fields();

$item_type_keyword_type = $entity_obj->get_post_type();
$post_type_obj = get_post_type_object($item_type_keyword_type);
$item_type_keyword_lowercase = strtolower($post_type_obj->labels->singular_name);
$item_type_keyword_uppercase = ucfirst($item_type_keyword_lowercase);
$booking_form_thank_you = $bookyourtravel_theme_globals->get_booking_form_thank_you();

do_action( 'bookyourtravel_show_' . $item_type_keyword_type . '_confirm_form_before' ); ?>
<section class="<?php echo esc_attr($section_class); ?> <?php echo esc_attr($item_type_keyword_type); ?>-confirmation-section confirmation-section modal" style="display:none;">
	<div class="static-content">
		<a href="#" class="close-btn">x</a>
		<form id="<?php echo esc_attr($item_type_keyword_type); ?>-confirmation-form" method="post" action="<?php echo BookYourTravel_Theme_Utils::get_current_page_url(); ?>">
			<fieldset>
				<h3><?php esc_html_e('Confirmation', 'bookyourtravel') ?></h3>
				<div class="text-wrap">
					<?php echo wp_kses($booking_form_thank_you, BookYourTravel_Theme_Utils::get_allowed_content_tags_array()); ?>
				</div>				
				<h3><?php esc_html_e('Booking info', 'bookyourtravel') ?></h3>
				<div class="output">
					<div class="row">
						<?php do_action('booking_form_confirmation_' . $item_type_keyword_type . '_core_fields'); ?>		
						<?php 	
						foreach ($booking_form_fields as $booking_field) {
							$field_hidden = isset($booking_field['hide']) && $booking_field['hide'] == 1 ? true : false;
							$field_id = $booking_field['id'];
							$field_label = isset($booking_field['label']) ? $booking_field['label'] : '';
							$field_label = $bookyourtravel_theme_of_custom->get_translated_dynamic_string($bookyourtravel_theme_of_custom->get_option_id_context('booking_form_fields') . ' ' . $field_label, $field_label);			
				
							if (!$field_hidden) {
							?>
							<div class="one-half">
								<p><?php echo esc_html($field_label); ?>: 
									<strong class="confirm_<?php echo esc_attr($field_id); ?>_p"></strong>
								</p>
							</div>
						<?php 
							}	 
						}
						?>
					</div>				
				</div>				
				<div class="totals">
					<div class="row">
						<?php if ($enable_extra_items) { ?>
						<div class="full-width">
							<p><?php esc_html_e('Reservation total', 'bookyourtravel') ?>: 
								<strong class="confirm_reservation_total_p"></strong>
							</p>
						</div>
						<div class="full-width">
							<p><?php esc_html_e('Extra items total', 'bookyourtravel') ?>: 
								<strong class="confirm_extra_items_total_p"></strong>
							</p>
						</div>
						<?php } ?>
						<div class="full-width">
							<p><?php esc_html_e('Total price', 'bookyourtravel') ?>: 
								<strong class="confirm_total_price_p"></strong>
							</p>
						</div>
					</div>
				</div>			
				<div class="text-wrap">
					<p><?php echo sprintf(__('<strong>We wish you a pleasant trip</strong><br /><i>your %s team</i>', 'bookyourtravel'), of_get_option('contact_company_name', 'BookYourTravel')) ?></p>
				</div>
				<a href="#" class="close-btn gradient-button">Back to website</a>
			</fieldset>
		</form>
	</div>
</section>
<?php do_action( 'bookyourtravel_show_' . $item_type_keyword_type . '_confirm_form_after' );