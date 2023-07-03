<?php
	global $entity_obj;
	$tour_obj = $entity_obj;
?>
<p>
<?php 					
if ($tour_obj->get_type_is_repeated() == 1) {
	echo esc_html__('This is a daily tour.', 'bookyourtravel'); 
} else if ($tour_obj->get_type_is_repeated() == 2) {
	echo esc_html__('This tour is repeated every weekday (working day).', 'bookyourtravel'); 
} else if ($tour_obj->get_type_is_repeated() == 3) {
	echo sprintf(esc_html__('This tour is repeated every week on a %s.', 'bookyourtravel'), $tour_obj->get_type_day_of_week_day()); 
} else if ($tour_obj->get_type_is_repeated() == 4) {
	echo esc_html__('This tour is repeated every week on multiple days.', 'bookyourtravel'); 
}
?>
</p>