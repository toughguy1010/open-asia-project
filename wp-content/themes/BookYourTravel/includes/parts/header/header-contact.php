<?php 
global $bookyourtravel_theme_globals;
$contact_message = trim($bookyourtravel_theme_globals->get_header_contact_message());
$contact_phone = trim($bookyourtravel_theme_globals->get_contact_phone_number());
if ( !empty($contact_phone) || !empty($contact_message) ) {
	ob_start();
?>
<!--contact-->
<div class="contact wrap">
	<?php if (!empty($contact_message)) { ?>
	<span class="message"><?php echo esc_html($contact_message); ?></span>
	<?php } ?>
	<?php if (!empty($contact_phone)) { ?>	
	<span class="number"><?php echo esc_html($contact_phone); ?></span>
	<?php } ?>
</div>
<!--//contact-->
<?php 
	$contact_html = ob_get_contents();
	ob_end_clean();
	echo apply_filters('bookyourtravel_header_contact', $contact_html);
}