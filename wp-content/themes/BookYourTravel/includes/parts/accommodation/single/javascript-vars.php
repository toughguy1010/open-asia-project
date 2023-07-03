<?php
/**
 * Javascript navs for single accommodation template
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $bookyourtravel_theme_globals, $post, $bookyourtravel_accommodation_helper;
$accommodation_obj = new BookYourTravel_Accommodation($post);
$base_id = $accommodation_obj->get_base_id();
$booking_form_fields = $bookyourtravel_theme_globals->get_booking_form_fields();
$calendar_month_rows = $bookyourtravel_theme_globals->get_calendar_month_rows();
$calendar_month_cols = $bookyourtravel_theme_globals->get_calendar_month_cols();

$date_from = isset($_GET['from']) && !empty($_GET['from'])  ? date('Y-m-d', strtotime(sanitize_text_field($_GET['from']))) : null;
$date_to = isset($_GET['to']) && !empty($_GET['to']) ? date('Y-m-d', strtotime(sanitize_text_field($_GET['to']))) : null;

$date_from = $date_from >= date("Y-m-d") ? $date_from : null;
$date_to = $date_to > date("Y-m-d H:i:s") && $date_to > $date_from ? $date_to : null;

$rooms = isset($_GET['rooms']) && !empty($_GET['rooms']) ? intval($_GET['rooms']) : 1;
$guests = isset($_GET['guests']) && !empty($_GET['guests']) ? intval($_GET['guests']) : 1;

$enable_deposit_payments = $bookyourtravel_theme_globals->enable_deposit_payments();
$deposit_percentage = (int)$accommodation_obj->get_deposit_percentage();

$accommodation_count_children_stay_free = $accommodation_obj->get_count_children_stay_free();
$accommodation_is_price_per_person = $accommodation_obj->get_is_price_per_person();
$accommodation_is_reservation_only = $accommodation_obj->get_is_reservation_only();
$accommodation_rent_type = $accommodation_obj->get_rent_type();
$accommodation_disabled_room_types = $accommodation_obj->get_disabled_room_types();	

$min_adult_count = 1;
$max_adult_count = $max_child_count = $min_child_count = 0;
if ($accommodation_disabled_room_types) {	
	$min_adult_count = $accommodation_obj->get_min_adult_count();
	$max_adult_count = $accommodation_obj->get_max_adult_count();
	$min_child_count = $accommodation_obj->get_min_child_count();
	$max_child_count = $accommodation_obj->get_max_child_count();
}

$min_days_stay = $accommodation_obj->get_min_days_stay();
$max_days_stay = $accommodation_obj->get_max_days_stay();

$checkin_week_day = $accommodation_obj->get_checkin_week_day();
$checkout_week_day = $accommodation_obj->get_checkout_week_day();

$days_of_week = BookYourTravel_Theme_Utils::get_php_days_of_week();
$checkin_week_day_text = isset($days_of_week[$checkin_week_day]) ? $days_of_week[$checkin_week_day] : '';
$checkout_week_day_text = isset($days_of_week[$checkout_week_day]) ? $days_of_week[$checkout_week_day] : '';

$total_price_label = esc_html__('Total price', 'bookyourtravel');
if ($accommodation_is_price_per_person && $accommodation_count_children_stay_free > 0) {
	$total_price_children_stay_free_label = esc_html__('Total price', 'bookyourtravel') . '<br /><em class="note">' . sprintf(esc_html__('* first %d children stay free', 'bookyourtravel') . '</em>', $accommodation_count_children_stay_free);	
}

$min_date = $bookyourtravel_accommodation_helper->get_min_future_date($post->ID);
if ($min_date) {
	$min_date = date('Y-m-d', strtotime($min_date));
}
?>
<script>
<?php if ($min_date) { ?>
	window.minAccommodationDate = new Date(<?php echo json_encode($min_date); ?>);
<?php } else { ?>
	window.minAccommodationDate = <?php echo 0; ?>;
<?php } ?>
	window.postId = <?php echo json_encode($post->ID); ?>;
	window.postTitle = <?php echo json_encode($accommodation_obj->get_title()); ?>;
	window.accommodationId = <?php echo json_encode($base_id); ?>;
	window.accommodationTitle = <?php echo json_encode($accommodation_obj->get_title()); ?>;
	window.postType = 'accommodation';
	window.pauseBetweenSlides = <?php echo json_encode($bookyourtravel_theme_globals->get_light_slider_pause_between_slides() * 1000); ?>;
	window.entityLatitude = <?php echo json_encode($accommodation_obj->get_custom_field('latitude')); ?>;
	window.entityLongitude = <?php echo json_encode($accommodation_obj->get_custom_field('longitude')); ?>;
	window.entityInfoboxText = <?php echo json_encode('<strong>' . $accommodation_obj->get_title() . '</strong><br />' . $accommodation_obj->get_custom_field('address') . '<br />' . $accommodation_obj->get_custom_field('website_address')); ?>;	
	window.calendarMonthRows = <?php echo json_encode($calendar_month_rows); ?>;
	window.calendarMonthCols = <?php echo json_encode($calendar_month_cols); ?>;
	window.requiredExtraItems = [];	
	window.accommodationRentType = <?php echo (int)$accommodation_rent_type; ?>;
	window.accommodationDisabledRoomTypes = <?php echo (int)$accommodation_disabled_room_types; ?>;
	window.accommodationIsReservationOnly = <?php echo (int)$accommodation_is_reservation_only; ?>;
	window.enableDeposits = <?php echo (int)$enable_deposit_payments; ?>;
	<?php if ($enable_deposit_payments) {?>
	window.depositPercentage = <?php echo (int)$deposit_percentage; ?>;
	<?php } ?>
	window.accommodationIsPricePerPerson = <?php echo (int)$accommodation_is_price_per_person; ?>;
	window.accommodationCountChildrenStayFree = <?php echo (int)$accommodation_count_children_stay_free; ?>;
	<?php if ($accommodation_disabled_room_types) { ?>
	window.accommodationMaxAdultCount =  <?php echo (int)$max_adult_count; ?>;
	window.accommodationMaxChildCount = <?php echo (int)$max_child_count; ?>;
	window.accommodationMinAdultCount =  <?php echo (int)$min_adult_count; ?>;
	window.accommodationMinChildCount = <?php echo (int)$min_child_count; ?>;
	<?php } ?>
	window.accommodationMinDaysStay = <?php echo (int)$min_days_stay; ?>;
	window.accommodationMaxDaysStay = <?php echo (int)$max_days_stay; ?>;
	window.accommodationCheckinWeekday = <?php echo (int)$checkin_week_day; ?>;
	window.accommodationCheckoutWeekday = <?php echo (int)$checkout_week_day; ?>;
	
	window.moreInfoText = <?php echo json_encode(esc_html__('+ more info', 'bookyourtravel')); ?>;
	window.lessInfoText = <?php echo json_encode(esc_html__('- less info', 'bookyourtravel')); ?>;	
	window.formSingleError = <?php echo json_encode(esc_html__('You failed to provide 1 required field. It has been highlighted below.', 'bookyourtravel')); ?>;
	window.formMultipleError = <?php echo json_encode(esc_html__('You failed to provide {0} required fields. They have been highlighted below.', 'bookyourtravel'));  ?>;
	window.minDaysStayError = <?php echo json_encode(wp_kses(sprintf(__('Accommodation requires <strong>%d</strong> minimum days stay!', 'bookyourtravel'), $min_days_stay), array('strong' => array()))); ?>;
	window.maxDaysStayError = <?php echo json_encode(wp_kses(sprintf(__('Accommodation allows <strong>%d</strong> maximum days stay!', 'bookyourtravel'), $max_days_stay), array('strong' => array()))); ?>;
	window.checkinWeekDayError = <?php echo json_encode(wp_kses(sprintf(__('Accommodation allows checkins only on <strong>%s</strong>!', 'bookyourtravel'), $checkin_week_day_text), array('strong' => array()))); ?>;
	window.checkinMonthlyFirstDayError = <?php echo json_encode(esc_html(sprintf(__('This accommodation is rented on a monthly basis and only allows checkins on the first day of the month!', 'bookyourtravel')))); ?>;
	window.checkoutMonthlyLastDayError = <?php echo json_encode(esc_html(sprintf(__('This accommodation is rented on a monthly basis and only allows checkouts on the last day of the month!', 'bookyourtravel')))); ?>;
	window.checkoutWeekDayError = <?php echo json_encode(wp_kses(sprintf(__('Accommodation allows checkouts only on <strong>%s</strong>!', 'bookyourtravel'), $checkout_week_day_text), array('strong' => array()))); ?>;
	window.checkoutWeeklyDayError = <?php echo json_encode(esc_html(sprintf(__('This accommodation is rented on a weekly basis and only allows checkouts 7 day multiples of your selected check-in day!', 'bookyourtravel')))); ?>;
	window.pricePerAdultLabel = <?php echo json_encode(esc_html__('Rate per adult', 'bookyourtravel')); ?>;
	window.pricePerPersonLabel = <?php echo json_encode(esc_html__('Rate per person', 'bookyourtravel')); ?>;
	window.adultCountLabel = <?php echo json_encode(esc_html__('Adults', 'bookyourtravel')); ?>;
	window.peopleCountLabel = <?php echo json_encode(esc_html__('People', 'bookyourtravel')); ?>;
	window.childCountLabel = <?php echo json_encode(esc_html__('Children', 'bookyourtravel')); ?>;
	window.pricePerChildLabel = <?php echo json_encode(esc_html__('Rate per child', 'bookyourtravel')); ?>;
	window.pricePerDayLabel = <?php echo json_encode(esc_html__('Price per day', 'bookyourtravel')); ?>;
	window.pricePerWeekLabel = <?php echo json_encode(esc_html__('Price per week', 'bookyourtravel')); ?>;
	window.pricePerMonthLabel = <?php echo json_encode(esc_html__('Price per month', 'bookyourtravel')); ?>;
	<?php if (!$accommodation_disabled_room_types) { ?>
	window.pricePerDayPerRoomLabel = <?php echo json_encode(esc_html__('Price per day per room', 'bookyourtravel')); ?>;
	window.pricePerWeekPerRoomLabel = <?php echo json_encode(esc_html__('Price per week per room', 'bookyourtravel')); ?>;
	window.pricePerMonthPerRoomLabel = <?php echo json_encode(esc_html__('Price per month per room', 'bookyourtravel')); ?>;
	window.numberOfRoomsLabel = <?php echo json_encode(esc_html__('Number of rooms', 'bookyourtravel')); ?>;
	window.perExtraItemUnitLabel = <?php echo json_encode(esc_html__(' X {0} rooms', 'bookyourtravel')); ?>;
	<?php } ?>
	window.extraItemsPriceTotalLabel = <?php echo json_encode(esc_html__('Extra items total price', 'bookyourtravel')); ?>;
	window.priceTotalLabel = <?php echo json_encode($total_price_label); ?>;
	window.priceTotalChildrenStayFreeLabel = <?php echo !empty($total_price_children_stay_free_label) ? json_encode($total_price_children_stay_free_label) : json_encode(''); ?>;
	window.dateLabel = <?php echo json_encode(esc_html__('Date', 'bookyourtravel')); ?>;
	window.itemLabel = <?php echo json_encode(esc_html__('Item', 'bookyourtravel')); ?>;
	window.priceLabel = <?php echo json_encode(esc_html__('Price', 'bookyourtravel')); ?>;
	window.pricedPerDayPerPersonLabel = <?php echo json_encode(esc_html__('priced per day, per person X {0} days X {1} people', 'bookyourtravel')); ?>;
	window.pricedPerDayLabel = <?php echo json_encode(esc_html__('priced per day X {0} days', 'bookyourtravel')); ?>;
	window.pricedPerPersonLabel = <?php echo json_encode(esc_html__('priced per person X {0} people', 'bookyourtravel')); ?>;
	window.enableExtraItems = <?php echo json_encode(intval($bookyourtravel_theme_globals->enable_extra_items())); ?>;
	window.showPriceBreakdownLabel = <?php echo json_encode(esc_html__('Show price breakdown', 'bookyourtravel')); ?>;
	window.hidePriceBreakdownLabel = <?php echo json_encode(esc_html__('Hide price breakdown', 'bookyourtravel')); ?>;
	window.depositInfo = <?php echo json_encode(wp_kses(__("* To secure this booking, a deposit of <strong>{0}%</strong> of the booking price is required to be paid immediately. You are required to pay the remaining <strong>{1}</strong> upon arrival.", 'bookyourtravel'), array("strong" => array()))); ?>;
	window.bookingRequest = {};	
<?php if ($date_from || $date_to) { ?>
	<?php if ($date_from) { ?>
	window.bookingRequest.requestedDateFrom = <?php echo json_encode($date_from); ?>;
	window.currentMonth = <?php echo json_encode(date_i18n('n', strtotime($date_from))); ?>;
	window.currentYear = <?php echo json_encode( date_i18n('Y'), strtotime($date_from)); ?>;
	window.currentDay = <?php echo json_encode( date_i18n('j'), strtotime($date_from)); ?>;
	<?php } ?>
	<?php if ($date_to) { ?>
	window.bookingRequest.requestedDateTo = <?php echo json_encode($date_to); ?>;
	<?php } ?>
<?php } else { ?>
	window.currentMonth = <?php echo json_encode(date_i18n('n')); ?>;
	window.currentYear = <?php echo json_encode( date_i18n('Y')); ?>;
	window.currentDay = <?php echo json_encode( date_i18n('j')); ?>;
<?php } ?>
<?php if ($rooms && !$accommodation_disabled_room_types) { ?>
	window.requestedRooms = <?php echo json_encode($rooms); ?>;
<?php } else { ?>
	window.requestedRooms = 1;
<?php } ?>
<?php if ($guests) { ?>
	window.requestedGuests = <?php echo json_encode($guests); ?>;
<?php } ?>
	window.bookingFormDatesError = <?php echo json_encode(esc_html__('Please select booking dates', 'bookyourtravel')); ?>;		
	window.bookingFormRequiredError = <?php echo json_encode(esc_html__('This is a required field', 'bookyourtravel')); ?>;
	window.bookingFormEmailError = <?php echo json_encode(esc_html__('You have not entered a valid email', 'bookyourtravel')); ?>;
	window.bookingFormFields = <?php echo json_encode($booking_form_fields)?>;
	window.bookingFormDateFromError = <?php echo json_encode(esc_html__('Please select a date from', 'bookyourtravel')); ?>;	
	window.bookingFormDateToError = <?php echo json_encode(esc_html__('Please select a date to', 'bookyourtravel')); ?>;	
	window.InvalidCaptchaMessage = <?php echo json_encode(esc_html__('Invalid captcha, please try again!', 'bookyourtravel')); ?>;	
</script>
