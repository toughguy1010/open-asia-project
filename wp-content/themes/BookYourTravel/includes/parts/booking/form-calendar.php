<?php
/**
 * Booking calendar form part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $bookyourtravel_theme_globals, $entity_obj;

$item_type_keyword_type = $entity_obj->get_post_type();
$post_type_obj = get_post_type_object($item_type_keyword_type);
$item_type_keyword_lowercase = strtolower($post_type_obj->labels->singular_name);
$item_type_keyword_uppercase = ucfirst($item_type_keyword_lowercase);
$enable_deposit_payments = $bookyourtravel_theme_globals->enable_deposit_payments();
$force_disable_calendar = intval($entity_obj->get_custom_field($item_type_keyword_type . '_force_disable_calendar', false));

?>
<form id="booking-form-calendar" action="#" method="POST" class="<?php echo esc_attr($item_type_keyword_type); ?>-booking-form-calendar">
	<div class="redirect-notice" style="display:none;">
		<div>
			<p><?php esc_html_e('You will be redirected to cart shortly. Thank you for your patience.', 'bookyourtravel'); ?></p>
		</div>
	</div>
	<div style="display:none;">
		<div class="spinner loading"><div></div></div>
	</div>
	<div class="booking_form_controls_holder">
		<?php if (!$force_disable_calendar) { ?>
		<div class="text-wrap booking_terms">
			<div>
				<p><?php echo sprintf(esc_html__('Use the calendar below to book this %s.', 'bookyourtravel'), $item_type_keyword_lowercase); ?></p>
				<?php do_action('booking_form_calendar_' . $item_type_keyword_type . '_booking_terms'); ?>
			</div>
		</div>
		<div class="row calendar-colors">
			<div class="f-item full-width">
				<div class="today"><span></span><?php esc_html_e('Today', 'bookyourtravel') ?></div>
				<div class="selected"><span></span><?php esc_html_e('Selected', 'bookyourtravel') ?></div>
				<div class="available"><span></span><?php esc_html_e('Available', 'bookyourtravel') ?></div>
				<div class="unavailable"><span></span><?php esc_html_e('Unavailable', 'bookyourtravel') ?></div>
			</div>
		</div>
		<div class="error step1-error" style="display:none;"><div><p></p></div></div>
		<div class="row calendar">
			<div class="f-item full-width">
				<div class="booking_form_datepicker"></div>
				<input type="hidden" id="selected_date_from" name="selected_date_from" value="" />
				<input type="hidden" id="selected_date_to" name="selected_date_to" value="" />
			</div>
		</div>

		<?php do_action('booking_form_calendar_' . $item_type_keyword_type . '_after_calendar_control'); ?>

		<?php get_template_part('includes/parts/extra_item/form', 'extra-items'); ?>

		<div class="text-wrap dates_row" style="display:none;">
			<h3><?php esc_html_e('Summary', 'bookyourtravel'); ?></h3>
			<p><?php echo sprintf(esc_html__('The summary of your %s is shown below.', 'bookyourtravel'), $item_type_keyword_lowercase); ?></p>

			<table class="summary responsive">
				<tbody>
					<?php do_action('booking_form_calendar_' . $item_type_keyword_type . '_start_summary_control'); ?>
					<tr>
						<th>
							<?php echo esc_html__('Reservation total', 'bookyourtravel'); ?>
						</th>
						<td>
							<span class="reservation_total"></span>
						</td>
					</tr>
					<?php if ($bookyourtravel_theme_globals->enable_extra_items()) { ?>
					<tr class="extra_items_breakdown_row">
						<th>
							<?php esc_html_e('Extra items total', 'bookyourtravel') ?>
						</th>
						<td>
							<span class="extra_items_total"></span>
						</td>
					</tr>
					<?php } ?>
				</tbody>
				<tfoot>
					<tr>
						<th><?php esc_html_e('Total price', 'bookyourtravel') ?></th>
						<td class="total_price"></td>
					</tr>
					<?php if ($enable_deposit_payments) { ?>
					<tr>
						<th><?php esc_html_e('Deposit amount *', 'bookyourtravel') ?></th>
						<td><strong class="deposit_amount"></strong></td>
					</tr>
					<?php } ?>
				</tfoot>
			</table>
			<a href="#" class="toggle_breakdown show_breakdown"><?php esc_html_e('Show price breakdown', 'bookyourtravel') ?></a>
			<div class="row price_breakdown_row hidden" style="display:none;">
				<div class="f-item full-width">
					<label><?php echo sprintf(esc_html__('%s booking price breakdown', 'bookyourtravel'), $item_type_keyword_uppercase); ?></label>
					<table class="booking_price_breakdown tablesorter responsive">
						<thead></thead>
						<tbody></tbody>
						<tfoot></tfoot>
					</table>
				</div>
			</div>
			<?php if ($bookyourtravel_theme_globals->enable_extra_items()) { ?>
			<div class="row price_breakdown_row extra_items_breakdown_row" style="display:none;">
				<div class="f-item full-width">
					<label><?php esc_html_e('Extra items price breakdown', 'bookyourtravel') ?></label>
					<table class="extra_items_price_breakdown tablesorter responsive">
						<thead></thead>
						<tbody></tbody>
						<tfoot></tfoot>
					</table>
				</div>
			</div>
			<?php } ?>
		</div>

		<?php do_action('booking_form_calendar_' . $item_type_keyword_type . '_after_price_breakdown'); ?>		
		
		<?php } ?>
	</div>
	<div class="booking-commands">
	<?php BookYourTravel_Theme_Controls::the_link_button("#", "gradient-button book-" . $item_type_keyword_type . "-reset", "book-" . $item_type_keyword_type . "-reset", esc_html__('Reset', 'bookyourtravel')); ?>
	<?php BookYourTravel_Theme_Controls::the_link_button("#", "right gradient-button book-" . $item_type_keyword_type . "-proceed", "book-" . $item_type_keyword_type . "-proceed", esc_html__('Proceed', 'bookyourtravel')); ?>
	</div>
</form>
