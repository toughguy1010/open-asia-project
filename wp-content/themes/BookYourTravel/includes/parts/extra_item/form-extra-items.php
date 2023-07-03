<?php
/**
 * Extra items form part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $bookyourtravel_extra_item_helper, $bookyourtravel_theme_globals, $entity_obj;

$default_currency_symbol = $bookyourtravel_theme_globals->get_default_currency_symbol();
$show_currency_symbol_after = $bookyourtravel_theme_globals->show_currency_symbol_after();
$price_decimal_places = $bookyourtravel_theme_globals->get_price_decimal_places();

if ($bookyourtravel_theme_globals->enable_extra_items()) { 

	$post_type = $entity_obj->get_post_type();
	$tag_ids = $entity_obj->get_tag_ids();
	$type_id = $entity_obj->get_type_id();

	$extra_items = $bookyourtravel_extra_item_helper->list_extra_items_by_post_type($post_type, array($type_id), $tag_ids);
	
	if (count($extra_items) > 0) {
?>												
<div class="text-wrap price_row extra_items_row" style="display:none">
	<h3><?php esc_html_e('Extra items', 'bookyourtravel') ?></h3>
	<p><?php esc_html_e('Please select the extra items you wish to be included with your booking using the controls you see below.', 'bookyourtravel') ?></p>

	<table class="extraitems responsive">
		<thead>
			<tr>
				<th><?php esc_html_e('Item', 'bookyourtravel'); ?></th>
				<th><?php esc_html_e('Price', 'bookyourtravel'); ?></th>
				<th><?php esc_html_e('Per day?', 'bookyourtravel'); ?></th>
				<th><?php esc_html_e('Per person?', 'bookyourtravel'); ?></th>
				<th><?php esc_html_e('Quantity', 'bookyourtravel'); ?></th>
			</tr>
		</thead>
		<tbody>
			<script>
				window.requiredExtraItems = [];
			</script>								
			<?php
				foreach ($extra_items as $extra_item) {
					$extra_item_obj = new BookYourTravel_Extra_Item($extra_item);
					$item_teaser = BookYourTravel_Theme_Utils::strip_tags_and_shorten_by_words($extra_item->post_content, 20);
					$max_allowed = $extra_item_obj->get_custom_field('_extra_item_max_allowed', false);
					$item_price = $extra_item_obj->get_custom_field('_extra_item_price', false);

					if (floatval($item_price) > 0) {
						$item_price = BookYourTravel_Theme_Utils::get_price_in_current_currency($item_price);
						$item_price_per_person = intval($extra_item_obj->get_custom_field('_extra_item_price_per_person', false));
						$item_price_per_day = intval($extra_item_obj->get_custom_field('_extra_item_price_per_day', false));
						$item_is_required = intval($extra_item_obj->get_custom_field('_extra_item_is_required', false));											
						
						if ($max_allowed > 0) {
							$starting_index = $item_is_required ? 1 : 0;
							
							if ($item_is_required) {
							?>
							<script>
								window.requiredExtraItems.push(<?php echo $extra_item->ID; ?>);
							</script>							
							<?php
							}											
				?>
				<tr>
					<td>
						<span id="extra_item_title_<?php echo esc_attr($extra_item->ID); ?>"><?php echo esc_html($extra_item->post_title); ?></span>
						<?php if (!empty($item_teaser)) { ?>
						<i>
						<?php
						$allowed_tags = BookYourTravel_Theme_Utils::get_allowed_content_tags_array();
						echo wp_kses($item_teaser, $allowed_tags); 
						?>											
						</i>
						<?php } ?>
					</td>
					<td>
						<em>
							<?php if (!$show_currency_symbol_after) { ?>
							<span class="curr"><?php echo esc_html($default_currency_symbol); ?></span>
							<span class="amount"><?php echo number_format_i18n( $item_price, $price_decimal_places ); ?></span>
							<?php } else { ?>
							<span class="amount"><?php echo number_format_i18n( $item_price, $price_decimal_places ); ?></span>
							<span class="curr"><?php echo esc_html($default_currency_symbol); ?></span>
							<?php } ?>
							<input type="hidden" value="<?php echo esc_attr($item_price); ?>" name="extra_item_price_<?php echo esc_attr($extra_item->ID); ?>" id="extra_item_price_<?php echo esc_attr($extra_item->ID); ?>" />
							<input type="hidden" value="<?php echo esc_attr($item_price_per_person); ?>" name="extra_item_price_per_person_<?php echo esc_attr($extra_item->ID); ?>" id="extra_item_price_per_person_<?php echo esc_attr($extra_item->ID); ?>" />
							<input type="hidden" value="<?php echo esc_attr($item_price_per_day); ?>" name="extra_item_price_per_day_<?php echo esc_attr($extra_item->ID); ?>" id="extra_item_price_per_day_<?php echo esc_attr($extra_item->ID); ?>" />
						</em>							
					</td>
					<td><?php echo $item_price_per_day ? esc_html__('Yes', 'bookyourtravel') : esc_html__('No', 'bookyourtravel'); ?></td>
					<td><?php echo $item_price_per_person ? esc_html__('Yes', 'bookyourtravel') : esc_html__('No', 'bookyourtravel'); ?></td>
					<td>
						<select class="extra_item_quantity dynamic_control" name="extra_item_quantity_<?php echo esc_attr($extra_item->ID); ?>" id="extra_item_quantity_<?php echo esc_attr($extra_item->ID); ?>">
							<?php for ($i = $starting_index; $i <= $max_allowed; $i++) {?>
							<option value="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<?php
						}
					}
				}
			?>
		</tbody>
		<tfoot></tfoot>
	</table>
</div>							
<?php } 
}