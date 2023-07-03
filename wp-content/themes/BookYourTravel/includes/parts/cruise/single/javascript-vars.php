<?php
/**
 * Javascript navs for single cruise template
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $bookyourtravel_theme_globals, $post, $bookyourtravel_cruise_helper;

$cruise_obj = new BookYourTravel_Cruise($post);
$base_id = $cruise_obj->get_base_id();
$booking_form_fields = $bookyourtravel_theme_globals->get_booking_form_fields();

$calendar_month_rows = $bookyourtravel_theme_globals->get_calendar_month_rows();
$calendar_month_cols = $bookyourtravel_theme_globals->get_calendar_month_cols();

$date_from = isset($_GET['from']) && !empty($_GET['from'])  ? date('Y-m-d', strtotime(sanitize_text_field($_GET['from']))) : null;

$date_from = $date_from >= date("Y-m-d") ? $date_from : null;

$cabins = isset($_GET['cabins']) && !empty($_GET['cabins']) ? intval($_GET['cabins']) : 1;
$guests = isset($_GET['guests']) && !empty($_GET['guests']) ? intval($_GET['guests']) : 1;

$enable_deposit_payments = $bookyourtravel_theme_globals->enable_deposit_payments();
$deposit_percentage = (int)$cruise_obj->get_deposit_percentage();

$cruise_is_reservation_only = (int)$cruise_obj->get_is_reservation_only();
$cruise_is_price_per_person = (int)$cruise_obj->get_is_price_per_person();
$cruise_count_children_stay_free = (int)$cruise_obj->get_count_children_stay_free();
$cruise_duration_days = (int)$cruise_obj->get_duration_days();

$min_date = $bookyourtravel_cruise_helper->get_min_future_date($post->ID);
if ($min_date) {
	$min_date = date('Y-m-d', strtotime($min_date));
}
?>
<script>
<?php if ($min_date) { ?>
	window.minCruiseDate = new Date(<?php echo json_encode($min_date); ?>);
<?php } else { ?>
	window.minCruiseDate = <?php echo 0; ?>;
<?php } ?>
	window.postId = <?php echo json_encode($post->ID); ?>;
	window.postTitle = <?php echo json_encode($cruise_obj->get_title()); ?>;
	window.postType = 'cruise';
	window.cruiseTitle = <?php echo json_encode($cruise_obj->get_title()); ?>;
	window.cruiseId = <?php echo json_encode($base_id); ?>;
	window.pauseBetweenSlides = <?php echo json_encode($bookyourtravel_theme_globals->get_light_slider_pause_between_slides() * 1000); ?>;
	window.entityInfoboxText = <?php echo json_encode('<strong>' . $cruise_obj->get_title() . '</strong><br />' . $cruise_obj->get_custom_field('address') . '<br />' . $cruise_obj->get_custom_field('website_address')); ?>;	
	window.calendarMonthRows = <?php echo json_encode($calendar_month_rows); ?>;
	window.calendarMonthCols = <?php echo json_encode($calendar_month_cols); ?>;
	window.cruiseCountChildrenStayFree = <?php echo (int)$cruise_count_children_stay_free; ?>;
	window.cruiseIsPricePerPerson = <?php echo (int)$cruise_is_price_per_person; ?>;
	window.cruiseIsReservationOnly = <?php echo (int)$cruise_is_reservation_only; ?>;
	window.enableDeposits = <?php echo (int)$enable_deposit_payments; ?>;
	<?php if ($enable_deposit_payments) {?>
	window.depositPercentage = <?php echo (int)$deposit_percentage; ?>;	
	<?php } ?>	
	window.enableExtraItems = <?php echo json_encode(intval($bookyourtravel_theme_globals->enable_extra_items())); ?>;
	window.requiredExtraItems = [];

	window.formSingleError = <?php echo json_encode(esc_html__('You failed to provide 1 required field. It has been highlighted below.', 'bookyourtravel')); ?>;
	window.formMultipleError = <?php echo json_encode(esc_html__('You failed to provide {0} required fields. They have been highlighted below.', 'bookyourtravel'));  ?>;
	window.showPriceBreakdownLabel = <?php echo json_encode(esc_html__('Show price breakdown', 'bookyourtravel')); ?>;
	window.hidePriceBreakdownLabel = <?php echo json_encode(esc_html__('Hide price breakdown', 'bookyourtravel')); ?>;
	window.dateLabel = <?php echo json_encode(esc_html__('Date', 'bookyourtravel')); ?>;
	window.itemLabel = <?php echo json_encode(esc_html__('Item', 'bookyourtravel')); ?>;
	window.priceLabel = <?php echo json_encode(esc_html__('Price', 'bookyourtravel')); ?>;
	window.pricedPerDayPerPersonLabel = <?php echo json_encode(esc_html__('priced per day, per person X {0} days X {1} people', 'bookyourtravel')); ?>;
	window.pricedPerDayLabel = <?php echo json_encode(esc_html__('priced per day X {0} days', 'bookyourtravel')); ?>;
	window.pricedPerPersonLabel = <?php echo json_encode(esc_html__('priced per person X {0} people', 'bookyourtravel')); ?>;
	window.pricePerAdultLabel = <?php echo json_encode(esc_html__('Price per adult', 'bookyourtravel')); ?>;
	window.pricePerPersonLabel = <?php echo json_encode(esc_html__('Price per person', 'bookyourtravel')); ?>;
	window.adultCountLabel = <?php echo json_encode(esc_html__('Adults', 'bookyourtravel')); ?>;
	window.childCountLabel = <?php echo json_encode(esc_html__('Children', 'bookyourtravel')); ?>;
	window.pricePerChildLabel = <?php echo json_encode(esc_html__('Price per child', 'bookyourtravel')); ?>;
	window.pricePerDayPerCabinLabel = <?php echo json_encode(esc_html__('Price per cabin', 'bookyourtravel')); ?>;
	window.pricePerDayLabel = <?php echo json_encode(esc_html__('Price', 'bookyourtravel')); ?>;
	window.extraItemsPriceTotalLabel = <?php echo json_encode(esc_html__('Extra items total price', 'bookyourtravel')); ?>;
	window.priceTotalLabel = <?php echo json_encode(esc_html__('Total price', 'bookyourtravel')); ?>;
	window.moreInfoText = <?php echo json_encode(esc_html__('+ more info', 'bookyourtravel')); ?>;
	window.lessInfoText = <?php echo json_encode(esc_html__('- less info', 'bookyourtravel')); ?>;	
	window.cruiseDurationDays = <?php echo json_encode($cruise_duration_days); ?>;
	window.perExtraItemUnitLabel = <?php echo json_encode(esc_html__(' X {0} cabins', 'bookyourtravel')); ?>;	
	window.bookingRequest = {};
<?php
	if ($date_from) { ?>
	window.bookingRequest.requestedDateFrom = <?php echo json_encode($date_from); ?>;
	window.currentMonth = <?php echo json_encode(date_i18n('n', strtotime($date_from))); ?>;
	window.currentYear = <?php echo json_encode( date_i18n('Y'), strtotime($date_from)); ?>;
	window.currentDay = <?php echo json_encode( date_i18n('j'), strtotime($date_from)); ?>;
<?php } else { ?>
	window.currentMonth = <?php echo json_encode(date_i18n('n')); ?>;
	window.currentYear = <?php echo json_encode( date_i18n('Y')); ?>;
	window.currentDay = <?php echo json_encode( date_i18n('j')); ?>;
<?php } ?>
<?php if ($cabins) { ?>
	window.requestedCabins = <?php echo json_encode($cabins); ?>;
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
	window.depositInfo = <?php echo json_encode(wp_kses(__("* To secure this booking, a deposit of <strong>{0}%</strong> of the booking price is required to be paid immediately. You are required to pay the remaining <strong>{1}</strong> upon arrival.", 'bookyourtravel'), array("strong" => array()))); ?>;
</script>
