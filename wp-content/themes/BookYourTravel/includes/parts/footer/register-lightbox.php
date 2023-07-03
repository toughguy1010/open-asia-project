<?php
global $bookyourtravel_theme_globals;

$register_page_url = $bookyourtravel_theme_globals->get_register_page_url();
$register_page_id = $bookyourtravel_theme_globals->get_register_page_id();
$reset_password_page_url = $bookyourtravel_theme_globals->get_reset_password_page_url();
$login_page_url = $bookyourtravel_theme_globals->get_login_page_url();
$terms_page_url = $bookyourtravel_theme_globals->get_terms_page_url();

$use_recaptcha = get_post_meta($register_page_id, 'user_register_use_recaptcha', true) && $bookyourtravel_theme_globals->is_recaptcha_usable();
$recaptcha_key = $bookyourtravel_theme_globals->get_google_recaptcha_key();
?>
<div class="lightbox" style="display:none;" id="register_lightbox">
	<div class="lb-wrap">
		<a href="javascript:void(0);" class="close register_lightbox toggle_lightbox">x</a>
		<div class="lb-content">
			<form action="<?php echo esc_url($register_page_url); ?>" method="post">
				<h2><?php esc_html_e('Register', 'bookyourtravel'); ?></h2>
				<div class="row">
					<div class="f-item full-width">
						<label for="user_login"><?php esc_html_e('Username', 'bookyourtravel'); ?></label>
						<input type="text" id="user_login" name="user_login" tabindex="1" />
					</div>
					<div class="f-item full-width">
						<label for="user_email"><?php esc_html_e('Email', 'bookyourtravel'); ?></label>
						<input type="email" id="user_email" name="user_email" tabindex="2" />
					</div>
					<?php if (strpos($register_page_url, 'wp-login.php') == false) { ?>
					<div class="f-item one-half">
						<label for="password"><?php esc_html_e('Password', 'bookyourtravel'); ?></label>
						<input id="user_password" class="input" type="password" tabindex="3" size="25" value="" name="password" />
					</div>
					<div class="f-item one-half">
						<label for="repeat_password"><?php esc_html_e('Repeat password', 'bookyourtravel'); ?></label>
						<input id="repeat_password" class="input" type="password" size="25" tabindex="4" value="" name="repeat_password" />
					</div>
					<?php } ?>
					<?php if ($use_recaptcha) { ?>
					<div class="f-item full-width">
						<div class="g-recaptcha" data-sitekey="<?php echo esc_attr($recaptcha_key); ?>"></div>
					</div>
					<?php } ?>
					<?php do_action( 'register_form' ); ?>	
					<div class="f-item checkbox full-width">
						<input type="checkbox" value="ch1" id="checkboxagree" name="checkboxagree" tabindex="5">
						<label><?php echo sprintf(__('I agree to the <a href="%s">terms &amp; conditions</a>.', 'bookyourtravel'), $terms_page_url); ?></label>
						<?php if( isset( $errors['agree'] ) ) { ?>
							<div class="error"><p><?php echo esc_html($errors['agree']); ?></p></div>
						<?php } ?>
					</div>
				</div>
				<?php wp_nonce_field( 'bookyourtravel_nonce', '_wpnonce_register' ) ?>
				<input type="submit" id="register" name="register" tabindex="5" value="<?php esc_attr_e('Create account', 'bookyourtravel'); ?>" class="gradient-button"/>
			</form>
		</div>
	</div>
</div>