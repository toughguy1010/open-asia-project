<?php
/*
 * Template Name: Contact
 * The template for displaying the contact page.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $bookyourtravel_theme_globals, $post, $bookyourtravel_theme_of_custom;

get_header();
get_template_part('byt', 'header');
BookYourTravel_Theme_Controls::the_breadcrumbs();
get_sidebar('under-header');

$page_id = $post->ID;

$business_contact_email = $bookyourtravel_theme_globals->get_contact_email();
$business_address_longitude = $bookyourtravel_theme_globals->get_business_address_longitude();
$business_address_latitude = $bookyourtravel_theme_globals->get_business_address_latitude();

$use_recaptcha = get_post_meta($page_id, 'contact_page_use_recaptcha', true) && $bookyourtravel_theme_globals->is_recaptcha_usable();
$contact_phone_number = get_post_meta($page_id, 'contact_page_phone_number', true);

$recaptcha_key = $bookyourtravel_theme_globals->get_google_recaptcha_key();
$recaptcha_secret = $bookyourtravel_theme_globals->get_google_recaptcha_secret();

$enable_gdpr = $bookyourtravel_theme_globals->enable_gdpr();

$form_submitted = false;

$contact_message = '';
$contact_email = '';
$contact_name = '';

$errors = array();

if(isset($_POST['contact_submit'])) {

	if ( empty($_POST) || !wp_verify_nonce($_POST['_wpnonce'], 'bookyourtravel_nonce') )
	{
	   // failed to verify nonce so exit.
	   exit;
	}
	else
	{
		$form_submitted = true;
		$contact_message = sanitize_text_field($_POST['contact_message']);
		$contact_email = sanitize_text_field($_POST['contact_email']);
		$contact_name = sanitize_text_field($_POST['contact_name']);

		if ($use_recaptcha) {
			if (isset($_POST['g-recaptcha-response'])) {

				$captcha = $_POST['g-recaptcha-response'];

				$json_uri = "https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptcha_secret . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR'];
				$request = wp_remote_get($json_uri);
				$response = wp_remote_retrieve_body( $request );
				$response = json_decode($response, true);

				if (!$response['success']) {
					$errors['captcha'] = esc_html__('You must complete the captcha field correctly.', 'bookyourtravel');
				}
			} else {
				$errors['captcha'] = esc_html__('You must complete the captcha field.', 'bookyourtravel');
			}
		}

		if (empty($errors)) {

			if (!empty($contact_name) && !empty($contact_email) && !empty($contact_message)) {

				$admin_email = get_option('admin_email');

				$subject = sprintf(esc_html__('Contact form submission from %s', 'bookyourtravel'), $contact_name);

				$message = sprintf(__('Name: %s', 'bookyourtravel'), $contact_name) . "\r\n";
				$message .= sprintf(__('Email: %s', 'bookyourtravel'), $contact_email) . "\r\n";
				$message .= sprintf(__('Message: %s', 'bookyourtravel'), $contact_message) . "\r\n";

				$headers = "Content-Type: text/plain; charset=utf-8\r\n";
				$headers .= "From: " . $admin_email . " <" . $admin_email . ">\r\n";

				if (!empty($contact_email)) {
					$headers .= "Reply-To: " . $contact_email . " <" . $contact_email . ">\r\n";
				} else {
					$headers .= "Reply-To: " . $admin_email . " <" . $admin_email . ">\r\n";
				}

				$message = apply_filters('bookyourtravel_contact_form_submission_message', $message);
				$subject = apply_filters('bookyourtravel_contact_form_submission_subject', $subject);
				$headers = apply_filters('bookyourtravel_contact_form_submission_headers', $headers, $contact_email, $admin_email);

				wp_mail($admin_email, $subject, $message, $headers);

				$contact_name = '';
				$contact_email = '';
				$contact_message = '';
			} else {
				$errors['contact'] = esc_html__('Please fill in all contact form fields to proceed.', 'bookyourtravel');
			}
		}
	}
}
?>
		<div class="row">
			<script type="text/javascript">
				window.contactNameError = '<?php esc_html_e('Name is a required field!', 'bookyourtravel'); ?>';
				window.contactEmailError = '<?php esc_html_e('Please enter a valid email address!', 'bookyourtravel'); ?>';
				window.contactMessageError = '<?php esc_html_e('Message is a required field!', 'bookyourtravel'); ?>';
			</script>
		<?php
			if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
			<div class="full-width">
				<h1 class="entry-title"><?php the_title(); ?></h1>
				<?php the_content( wp_kses(__( 'Continue reading <span class="meta-nav">&rarr;</span>', 'bookyourtravel' ), array('span' =>  array('class' => array()))) ); ?>
			</div>
			<!--three-fourth content-->
			<section class="three-fourth">
				<?php
				$google_maps_key = $bookyourtravel_theme_globals->get_google_maps_key();
				$allowed_tags = BookYourTravel_Theme_Utils::get_allowed_content_tags_array();

				if (!empty($google_maps_key)) {
					if (!empty($business_address_longitude) && !empty($business_address_latitude)) { ?>
				<!--map-->
				<div class="map-wrap">
					<div class="gmap" id="map_canvas"></div>
				</div>
				<!--//map-->
				<?php }
				} else {?>
				<p><?php echo wp_kses(__('Before using google maps you must go to <a href="https://developers.google.com/maps/documentation/javascript/get-api-key">Google maps api console</a> and get an api key. After you do, please proceed to Appearance -> Theme options -> Configuration settings and enter your key in the field labeled "Google maps api key"', 'bookyourtravel'), $allowed_tags); ?></p>
				<?php } ?>
			</section>
			<!--three-fourth content-->
			<!--sidebar-->
			<aside class="right-sidebar one-fourth">
				<!--contact form-->
				<div class="widget">
					<h4><?php esc_html_e('Send us a message', 'bookyourtravel'); ?></h4>
					<?php
					if ($form_submitted) { ?>
					<p>
					<?php
						if (count($errors) > 0) {
							?>
							<div class="error">
								<p><?php esc_html_e( 'Errors were encountered when processing your request.', 'bookyourtravel' ) ?></p>
								<?php foreach ($errors as $error) {
									echo '<p>' . $error . '</p>';
								} ?>
							</div>
							<?php
						} else {
							esc_html_e('Thank you for contacting us. We will get back to you as soon as we can.', 'bookyourtravel');
						} ?>
					</p>
					<?php
					}
					?>
					<?php
					$form_action_url = get_permalink();
					if (!$bookyourtravel_theme_globals->permalinks_enabled()) {
						$form_action_url = add_query_arg( array( 'page_id' => $page_id ), $form_action_url );
					}
					?>
					<form action="<?php echo esc_url_raw($form_action_url); ?>" id="contact-form" method="post">
						<fieldset class="row">
							<div class="f-item full-width">
								<label for="contact_name"><?php esc_html_e('Your name', 'bookyourtravel'); ?></label>
								<input type="text" id="contact_name" name="contact_name" value="<?php echo esc_attr($contact_name); ?>" />
							</div>
							<div class="f-item full-width">
								<label for="contact_email"><?php esc_html_e('Your e-mail', 'bookyourtravel'); ?></label>
								<input type="email" id="contact_email" name="contact_email" value="<?php echo esc_attr($contact_email); ?>" />
							</div>
							<div class="f-item full-width">
								<label for="contact_message"><?php esc_html_e('Your message', 'bookyourtravel'); ?></label>
								<textarea name="contact_message" id="contact_message" rows="10" cols="10" ><?php echo esc_attr($contact_message); ?></textarea>
							</div>
							<?php if ($use_recaptcha) { ?>
							<div class="f-item full-width">
								<div class="g-recaptcha" data-sitekey="<?php echo esc_attr($recaptcha_key); ?>"></div>
							</div>
							<?php } ?>
							<?php wp_nonce_field('bookyourtravel_nonce'); ?>
							<?php if ($enable_gdpr) {
								$gdpr_field = $bookyourtravel_theme_of_custom->get_gdpr_checkbox_field();
								$gdpr_field["unique_id"] = "contact_form_" . $gdpr_field["id"];
								$gdpr_field_label = $gdpr_field["label"];
								$gdpr_field_label = $bookyourtravel_theme_of_custom->get_translated_dynamic_string($bookyourtravel_theme_of_custom->get_option_id_context($gdpr_field["id"]) . ' ' . $gdpr_field_label, $gdpr_field_label);
							?>
							<div class="f-item full-width">
								<?php BookYourTravel_Theme_Admin_Controls::the_dynamic_field_checkbox_control($gdpr_field); ?>
								<label for="<?php echo esc_attr($gdpr_field['id']) ?>"><?php echo wp_kses($gdpr_field_label, array("a" => array("href" => array(), "class" => array(), "title" => array()))); ?></label>	
							</div>
							<?php } ?>
							<div class="f-item full-width"><input type="submit" value="<?php esc_attr_e('Send', 'bookyourtravel'); ?>" id="contact_submit" name="contact_submit" class="gradient-button" /></div>
						</fieldset>
					</form>
				</div>
				<!--//contact form-->
				<?php if (!empty($contact_phone_number) || !empty($business_contact_email)) { ?>
				<!--contact info-->
				<div class="widget">
					<h4><?php esc_html_e('Or contact us directly', 'bookyourtravel'); ?></h4>
					<?php if (!empty($contact_phone_number)) {?><p class="ico ico-phone"><?php echo esc_html($contact_phone_number); ?></p><?php } ?>
					<?php if (!empty($business_contact_email)) {?><p class="ico ico-email"><a href="mailto:<?php echo esc_attr($business_contact_email); ?>"><?php echo esc_html($business_contact_email); ?></a></p><?php } ?>
				</div>
				<!--//contact info-->
				<?php } ?>
			</aside>
			<!--//sidebar-->
			<?php
			endwhile; ?>
		</div>
<?php
get_template_part('byt', 'footer');
get_footer();