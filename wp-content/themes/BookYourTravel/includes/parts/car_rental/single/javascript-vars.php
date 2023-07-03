<?php
/**
 * Javascript navs for single car rental template
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $bookyourtravel_theme_globals, $post, $bookyourtravel_car_rental_helper;
$car_rental_obj = new BookYourTravel_Car_Rental($post);
$base_id = $car_rental_obj->get_base_id();
$booking_form_fields = $bookyourtravel_theme_globals->get_booking_form_fields();

$calendar_month_rows = $bookyourtravel_theme_globals->get_calendar_month_rows();
$calendar_month_cols = $bookyourtravel_theme_globals->get_calendar_month_cols();

$date_from = isset($_GET['from']) && !empty($_GET['from'])  ? date('Y-m-d', strtotime(sanitize_text_field($_GET['from']))) : null;
$date_to = isset($_GET['to']) && !empty($_GET['to']) ? date('Y-m-d', strtotime(sanitize_text_field($_GET['to']))) : null;

$date_from = $date_from >= date("Y-m-d") ? $date_from : null;
$date_to = $date_to > date("Y-m-d") && $date_to > $date_from ? $date_to : null;

$car_rental_is_reservation_only = (int)$car_rental_obj->get_is_reservation_only();

$enable_deposit_payments = $bookyourtravel_theme_globals->enable_deposit_payments();
$deposit_percentage = (int)$car_rental_obj->get_deposit_percentage();

$min_date = $bookyourtravel_car_rental_helper->get_min_future_date($post->ID);
if ($min_date) {
	$min_date = date('Y-m-d', strtotime($min_date));
}

$min_booking_days = $car_rental_obj->get_min_booking_days();
$max_booking_days = $car_rental_obj->get_max_booking_days();
?>
<script>
<?php if ($min_date) { ?>
	window.minCarRentalDate = new Date(<?php echo json_encode($min_date); ?>);
<?php } else { ?>
	window.minCarRentalDate = <?php echo 0; ?>;
<?php } ?>
	window.postId = <?php echo json_encode($post->ID); ?>;
	window.postTitle = <?php echo json_encode($car_rental_obj->get_title()); ?>;
	window.carRentalId = <?php echo json_encode($base_id); ?>;
	window.postType = 'car_rental';
	window.pauseBetweenSlides = <?php echo json_encode($bookyourtravel_theme_globals->get_light_slider_pause_between_slides() * 1000); ?>;
	window.entityInfoboxText = <?php echo json_encode('<strong>' . $car_rental_obj->get_title() . '</strong><br />' . $car_rental_obj->get_custom_field('address') . '<br />' . $car_rental_obj->get_custom_field('website_address')); ?>;
	window.calendarMonthRows = <?php echo json_encode($calendar_month_rows); ?>;
	window.calendarMonthCols = <?php echo json_encode($calendar_month_cols); ?>;
	window.formSingleError = <?php echo json_encode(esc_html__('You failed to provide 1 required field. It has been highlighted below.', 'bookyourtravel')); ?>;
	window.formMultipleError = <?php echo json_encode(esc_html__('You failed to provide {0} required fields. They have been highlighted below.', 'bookyourtravel'));  ?>;
	window.carRentalTitle = <?php echo json_encode($car_rental_obj->get_title()); ?>;
	window.carRentalCarType = <?php echo json_encode($car_rental_obj->get_type_name()); ?>;
	window.carRentalMinBookingDays = <?php echo (int)$min_booking_days; ?>;
	window.carRentalMaxBookingDays = <?php echo (int)$max_booking_days; ?>;

	window.bookingRequest = {};
<?php
	if ($date_from || $date_to) { ?>
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
<?php
    }
    $request_values = BookYourTravel_Theme_Utils::retrieve_array_of_values_from_query_string('l', true);
    if (count($request_values) > 0) { ?>
        window.bookingRequest.requestedLocationFrom = <?php echo json_encode($request_values[0]); ?>;
    <?php }
    if (count($request_values) > 1) { ?>
        window.bookingRequest.requestedLocationTo = <?php echo json_encode($request_values[1]); ?>;
    <?php }
?>
	window.carRentalIsReservationOnly = <?php echo json_encode($car_rental_is_reservation_only); ?>;
	window.enableDeposits = <?php echo (int)$enable_deposit_payments; ?>;
	<?php if ($enable_deposit_payments) {?>
	window.depositPercentage = <?php echo (int)$deposit_percentage; ?>;	
	<?php } ?>	
	window.enableExtraItems = <?php echo json_encode(intval($bookyourtravel_theme_globals->enable_extra_items())); ?>;
	window.showPriceBreakdownLabel = <?php echo json_encode(esc_html__('Show price breakdown', 'bookyourtravel')); ?>;
	window.hidePriceBreakdownLabel = <?php echo json_encode(esc_html__('Hide price breakdown', 'bookyourtravel')); ?>;
	window.dateLabel = <?php echo json_encode(esc_html__('Date', 'bookyourtravel')); ?>;
	window.itemLabel = <?php echo json_encode(esc_html__('Item', 'bookyourtravel')); ?>;
	window.priceLabel = <?php echo json_encode(esc_html__('Price', 'bookyourtravel')); ?>;
	window.pricedPerDayPerPersonLabel = <?php echo json_encode(esc_html__('priced per day, per person X {0} days X {1} people', 'bookyourtravel')); ?>;
	window.pricedPerDayLabel = <?php echo json_encode(esc_html__('priced per day X {0} days', 'bookyourtravel')); ?>;
	window.pricedPerPersonLabel = <?php echo json_encode(esc_html__('priced per person X {0} people', 'bookyourtravel')); ?>;
	window.pricePerDayLabel = <?php echo json_encode(esc_html__('Price per day', 'bookyourtravel')); ?>;
	window.extraItemsPriceTotalLabel = <?php echo json_encode(esc_html__('Extra items total price', 'bookyourtravel')); ?>;
	window.priceTotalLabel = <?php echo json_encode(esc_html__('Total price', 'bookyourtravel')); ?>;
	window.bookingFormDatesError = <?php echo json_encode(esc_html__('Please select booking dates', 'bookyourtravel')); ?>;
	window.bookingFormRequiredError = <?php echo json_encode(esc_html__('This is a required field', 'bookyourtravel')); ?>;
	window.bookingFormEmailError = <?php echo json_encode(esc_html__('You have not entered a valid email', 'bookyourtravel')); ?>;
	window.bookingFormFields = <?php echo json_encode($booking_form_fields)?>;
	window.bookingFormDateFromError = <?php echo json_encode(esc_html__('Please select a date from', 'bookyourtravel')); ?>;
	window.bookingFormDateToError = <?php echo json_encode(esc_html__('Please select a date to', 'bookyourtravel')); ?>;
	window.InvalidCaptchaMessage = <?php echo json_encode(esc_html__('Invalid captcha, please try again!', 'bookyourtravel')); ?>;
	window.depositInfo = <?php echo json_encode(wp_kses(__("* To secure this booking, a deposit of <strong>{0}%</strong> of the booking price is required to be paid immediately. You are required to pay the remaining <strong>{1}</strong> upon arrival.", 'bookyourtravel'), array("strong" => array()))); ?>;
	window.minBookingDaysError = <?php echo json_encode(wp_kses(sprintf(__('Car rental requires <strong>%d</strong> minimum days booking!', 'bookyourtravel'), $min_booking_days), array('strong' => array()))); ?>;
	window.maxBookingDaysError = <?php echo json_encode(wp_kses(sprintf(__('Car rental allows <strong>%d</strong> maximum days booking!', 'bookyourtravel'), $max_booking_days), array('strong' => array()))); ?>;
</script>
