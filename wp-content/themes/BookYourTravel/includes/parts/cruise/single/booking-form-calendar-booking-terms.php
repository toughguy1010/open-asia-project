<?php
	global $entity_obj;
	$cruise_obj = $entity_obj;
?>
<p>
<?php 					
if ($cruise_obj->get_type_is_repeated() == 1) {
	echo esc_html__('This is a daily cruise.', 'bookyourtravel'); 
} else if ($cruise_obj->get_type_is_repeated() == 2) {
	echo esc_html__('This cruise is repeated every weekday (working day).', 'bookyourtravel'); 
} else if ($cruise_obj->get_type_is_repeated() == 3) {
	echo sprintf(esc_html__('This cruise is repeated every week on a %s.', 'bookyourtravel'), $cruise_obj->get_type_day_of_week_day()); 
} else if ($cruise_obj->get_type_is_repeated() == 4) {
	echo esc_html__('This cruise is repeated every week on multiple days.', 'bookyourtravel'); 
}
?>
</p>