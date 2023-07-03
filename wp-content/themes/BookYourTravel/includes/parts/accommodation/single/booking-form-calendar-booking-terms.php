<?php
	global $entity_obj;
	
	$accommodation_obj = $entity_obj;
	$accommodation_rent_type = $accommodation_obj->get_rent_type();
	$min_days_stay = intval($accommodation_obj->get_min_days_stay());
	$max_days_stay = intval($accommodation_obj->get_max_days_stay());
	$checkin_week_day = $accommodation_obj->get_checkin_week_day();
	$checkout_week_day = $accommodation_obj->get_checkout_week_day();
	
	$days_of_week = BookYourTravel_Theme_Utils::get_php_days_of_week();
	$checkin_week_day_text = isset($days_of_week[$checkin_week_day]) ? $days_of_week[$checkin_week_day] : '';
	$checkout_week_day_text = isset($days_of_week[$checkout_week_day]) ? $days_of_week[$checkout_week_day] : '';
?>
<?php if ($accommodation_rent_type == 1 || $accommodation_rent_type == 2) { ?>
<p>
<?php if ($accommodation_rent_type == 1) { ?>
	<span class="rent_type"><?php _e('This accommodation is rented on a weekly basis', 'bookyourtravel'); ?></span>
<?php } else { ?>
	<span class="rent_type"><?php _e('This accommodation is rented on a monthly basis', 'bookyourtravel'); ?></span>
<?php } ?>
</p>
<?php } ?>			
<?php if ($min_days_stay > 1 || $max_days_stay > 0 || $checkin_week_day > -1 || $checkout_week_day > -1) { ?>
<p>
<?php if ($min_days_stay > 0) { ?>
	<span class="min"><?php echo wp_kses(sprintf(__('Minimum days stay <strong>%d</strong>', 'bookyourtravel'), $min_days_stay), array('strong' => array())); ?></span>
<?php } ?>
<?php if ($max_days_stay > 0) { ?>
	<span class="max"><?php echo wp_kses(sprintf(__('Maximum days stay <strong>%d</strong>', 'bookyourtravel'), $max_days_stay), array('strong' => array())); ?></span>
<?php } ?>
<?php if ($checkin_week_day > -1) { ?>
	<span class="checkin_week_day"><?php echo wp_kses(sprintf(__('Check-ins allowed only on <strong>%s</strong>', 'bookyourtravel'), $checkin_week_day_text), array('strong' => array())); ?></span>
<?php } ?>
<?php if ($checkout_week_day > -1) { ?>
	<span class="checkout_week_day"><?php echo wp_kses(sprintf(__('Check-outs allowed only on <strong>%s</strong>', 'bookyourtravel'), $checkout_week_day_text), array('strong' => array())); ?></span>
<?php } ?>		
</p>
<?php } ?>