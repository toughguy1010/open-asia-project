<?php

?>
<tr>
	<th><?php esc_html_e('From date', 'bookyourtravel') ?></th>
	<td>
		<span class="date_from_text"></span>
		<input type="hidden" name="selected_date_from" id="selected_date_from_2" value="" />
	</td>
</tr>
<tr>
	<th><?php esc_html_e('To date', 'bookyourtravel') ?></th>
	<td>
		<span class="date_to_text"><?php esc_html_e('Select your to date using the calendar above.', 'bookyourtravel') ?></span>
		<input type="hidden" name="selected_date_to" id="selected_date_to_2" value="" />
	</td>
</tr>
<tr class=" people_count_div" style="display:none">
	<th>
		<?php esc_html_e('People', 'bookyourtravel') ?> <span class="per_room_text" style="display:none"><?php esc_html_e('per room', 'bookyourtravel') ?></span>
	</th>
	<td>
		<span class="people_text">1</span>
	</td>
</tr>
<tr class=" room_count_div" style="display:none">
	<th>
		<?php esc_html_e('Rooms', 'bookyourtravel') ?>
	</th>
	<td>
		<span class="rooms_text">1</span>
	</td>
</tr>
<tr class=" adult_count_div">
	<th>
		<?php esc_html_e('Adults', 'bookyourtravel') ?>
	</th>
	<td>
		<span class="adults_text">1</span>
	</td>
</tr>
<tr class=" children_count_div">
	<th>
		<?php esc_html_e('Children', 'bookyourtravel') ?>
	</th>
	<td>
		<span class="children_text">0</span>
	</td>
</tr>