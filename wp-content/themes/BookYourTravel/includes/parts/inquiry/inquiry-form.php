<?php
	global $entity_obj, $bookyourtravel_theme_globals, $bookyourtravel_theme_of_custom, $section_class;

	$post_type = $entity_obj->get_entity_type();
	$post_id = $entity_obj->get_base_id();
	$contact_form_heading = '';

	$contact_form_heading = esc_html__('Use the form below to contact us directly.', 'bookyourtravel');
	$inquiry_form_fields = $bookyourtravel_theme_globals->get_inquiry_form_fields();

	$use_recaptcha = $bookyourtravel_theme_globals->is_recaptcha_usable() && $bookyourtravel_theme_globals->enable_inquiry_recaptcha();
	$recaptcha_key = $bookyourtravel_theme_globals->get_google_recaptcha_key();
	$recaptcha_secret = $bookyourtravel_theme_globals->get_google_recaptcha_secret();
	$inquiry_form_thank_you = $bookyourtravel_theme_globals->get_inquiry_form_thank_you();
?>
<script>
	window.InvalidCaptchaMessage = <?php echo json_encode(esc_html__('Invalid captcha, please try again!', 'bookyourtravel')); ?>;
	window.postId = <?php echo json_encode( $post_id ); ?>;
	window.inquiryFormRequiredError = <?php echo json_encode(esc_html__('This is a required field', 'bookyourtravel')); ?>;
	window.inquiryFormEmailError = <?php echo json_encode(esc_html__('You have not entered a valid email', 'bookyourtravel')); ?>;
	window.inquiryFormFields = <?php echo json_encode($inquiry_form_fields)?>;
</script>
<?php do_action( 'bookyourtravel_show_inquiry_form_before' ); ?>
<!--full-width content-->
<section class="full-width inquiry-form-thank-you modal" style="display:none;">
	<div class="static-content">
		<a href="#" class="close-btn">x</a>
		<?php echo wp_kses($inquiry_form_thank_you, BookYourTravel_Theme_Utils::get_allowed_content_tags_array()); ?>
	</div>
</section>
<section class="<?php echo esc_attr($section_class); ?> inquiry-form-section inquiry-section modal" style="display:none;">
	<div class="static-content">
		<a href="#" class="cancel-inquiry right">x</a>
		<form method="post" action="<?php echo BookYourTravel_Theme_Utils::get_current_page_url(); ?>" class="inquiry inquiry-form">
			<h2><?php echo esc_html($contact_form_heading); ?></h2>
			<div class="error error-summary" style="display:none;"><div><p></p></div></div>
			<p><?php esc_html_e('Please complete all required fields.', 'bookyourtravel'); ?></p>
			<div class="row">
				<?php
				foreach ($inquiry_form_fields as $inquiry_field) {
					$field_id = $inquiry_field['id'];
					$inquiry_field["unique_id"] = "inquiry_form_" . $inquiry_field["id"];
					
					$field_type = $inquiry_field['type'];
					$field_hidden = isset($inquiry_field['hide']) && $inquiry_field['hide'] == 1 ? true : false;
					$field_required = isset($inquiry_field['required']) && $inquiry_field['required'] == 1 ? true : false;
					$field_label = isset($inquiry_field['label']) ? $inquiry_field['label'] : '';
					$field_label = $bookyourtravel_theme_of_custom->get_translated_dynamic_string($bookyourtravel_theme_of_custom->get_option_id_context('inquiry_form_fields') . ' ' . $field_label, $field_label);

					if (!$field_hidden) {
						if ($field_type == 'email') { ?>
						<div class="f-item full-width">
							<label for="<?php echo esc_attr($inquiry_field['id']) ?>"><?php echo esc_html($field_label); ?><?php echo $field_required ? ' *' : ''; ?></label>
							<input type="email" id="<?php echo esc_attr($inquiry_field['id']) ?>" name="<?php echo esc_attr($inquiry_field['id']) ?>" />
						</div>

						<?php } else if ($field_type == 'textarea') { ?>

						<div class="f-item full-width">
							<label for="<?php echo esc_attr($inquiry_field['id']) ?>"><?php echo esc_html($field_label); ?><?php echo $field_required ? ' *' : ''; ?></label>
							<textarea name='<?php echo esc_attr($inquiry_field['id']) ?>' id='<?php echo esc_attr($inquiry_field['id']) ?>' rows="10" cols="10" ></textarea>
						</div>

						<?php } else if ($field_type == 'select' && isset($inquiry_field['options'])) {?>
						<div class="f-item full-width">
							<label for="<?php echo esc_attr($inquiry_field['id']) ?>"><?php echo esc_html($field_label); ?><?php echo $field_required ? ' *' : ''; ?></label>
						<?php
							BookYourTravel_Theme_Admin_Controls::the_dynamic_field_select_control($inquiry_field);
						?>
						</div>
						<?php } else if ($field_type == 'checkbox') {?>
						<div class="f-item full-width">
							<?php BookYourTravel_Theme_Admin_Controls::the_dynamic_field_checkbox_control($inquiry_field); ?>
							<label for="<?php echo esc_attr($inquiry_field['id']) ?>"><?php echo wp_kses($field_label, BookYourTravel_Theme_Utils::get_allowed_content_tags_array()); ?><?php echo $field_required ? ' *' : ''; ?></label>						
						</div>
						<?php
						} else { ?>
						<div class="f-item full-width">
							<label for="<?php echo esc_attr($inquiry_field['id']) ?>"><?php echo esc_html($field_label); ?><?php echo $field_required ? ' *' : ''; ?></label>
							<input type="text" name="<?php echo esc_attr($inquiry_field['id']) ?>" id="<?php echo esc_attr($inquiry_field['id']) ?>" />
						</div>
					<?php
						}
					}
				}
				?>
				<?php
				if ($use_recaptcha) {
				?>
				<div class="f-item full-width">
					<div class="g-recaptcha" data-sitekey="<?php echo esc_attr($recaptcha_key); ?>"></div>
				</div>
				<?php
				}
				?>
				<?php if ($bookyourtravel_theme_globals->enable_gdpr()) { ?>
					<div class="f-item full-width">
					<?php
					$gdpr_field = $bookyourtravel_theme_of_custom->get_gdpr_checkbox_field();
					$gdpr_field["unique_id"] = "inquiry_form_" . $gdpr_field["id"];
					?>
					<?php BookYourTravel_Theme_Admin_Controls::the_dynamic_field_checkbox_control($gdpr_field); ?>
					<label for="<?php echo esc_attr($gdpr_field['id']) ?>"><?php echo wp_kses($gdpr_field['label'], array("a" => array("href" => array(), "target" => array(), "class" => array()))); ?></label>				
					</div>
				<?php } ?>
				<div class="f-item full-width">
					<?php BookYourTravel_Theme_Controls::the_link_button("#", "gradient-button cancel-inquiry", "cancel-inquiry", esc_html__('Cancel', 'bookyourtravel')); ?>
					<?php BookYourTravel_Theme_Controls::the_submit_button("gradient-button", "submit-inquiry", esc_html__('Submit inquiry', 'bookyourtravel')); ?>
				</div>
			</div>
		</form>
	</div>
</section>
<!--//full-width content-->
<?php
do_action( 'bookyourtravel_show_inquiry_form_after' );