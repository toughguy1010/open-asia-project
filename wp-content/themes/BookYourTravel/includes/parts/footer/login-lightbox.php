<?php 

global $bookyourtravel_theme_globals;

$register_page_url = $bookyourtravel_theme_globals->get_register_page_url();
$reset_password_page_url = $bookyourtravel_theme_globals->get_reset_password_page_url();
$login_page_url = $bookyourtravel_theme_globals->get_login_page_url();
$login_page_id = $bookyourtravel_theme_globals->get_login_page_id();
$terms_page_url = $bookyourtravel_theme_globals->get_terms_page_url();

$use_recaptcha = get_post_meta($login_page_id, 'user_login_use_recaptcha', true) && $bookyourtravel_theme_globals->is_recaptcha_usable();
$recaptcha_key = $bookyourtravel_theme_globals->get_google_recaptcha_key();
?>	
<div class="lightbox" style="display:none;" id="login_lightbox">
	<div class="lb-wrap">
		<a href="javascript:void(0);" class="close toggle_lightbox login_lightbox">x</a>
		<div class="lb-content">
			<form action="<?php echo esc_url( $login_page_url ); ?>" method="post">
				<h2><?php esc_html_e('Log in', 'bookyourtravel'); ?></h2>
				<div class="row">
					<div class="f-item full-width">
						<label for="log"><?php esc_html_e('Username', 'bookyourtravel'); ?></label>
						<input type="text" name="log" id="log" value="" />
					</div>
					<div class="f-item full-width">
						<label for="login_pwd"><?php esc_html_e('Password', 'bookyourtravel'); ?></label>
						<input type="password" id="login_pwd" name="pwd" />
					</div>
					<?php if ($use_recaptcha) { ?>
					<div class="f-item full-width">
						<div class="g-recaptcha" data-sitekey="<?php echo esc_attr($recaptcha_key); ?>"></div>
					</div>
					<?php } ?>
					<div class="f-item checkbox full-width">
						<input type="checkbox" id="rememberme" name="rememberme" checked="checked" value="forever" />
						<label for="rememberme"><?php esc_html_e('Remember me next time', 'bookyourtravel'); ?></label>
					</div>
				</div>
				<p><a href="<?php echo esc_url($reset_password_page_url); ?>" title="<?php esc_attr_e('Forgot your password?', 'bookyourtravel'); ?>"><?php esc_html_e('Forgot your password?', 'bookyourtravel'); ?></a><br />
				<?php if ( !empty( $register_page_url && get_option( 'users_can_register' ) ) ) { ?>
				<?php esc_html_e("Don't have an account yet?", 'bookyourtravel'); ?> <a  href="<?php echo esc_url($register_page_url); ?>" title="<?php esc_attr_e('Sign up', 'bookyourtravel'); ?>"><?php esc_html_e('Sign up', 'bookyourtravel'); ?>.</a></p>
				<?php } ?>
				<?php wp_nonce_field( 'bookyourtravel_nonce', '_wpnonce' ) ?>
				<input type="hidden" name="redirect_to" value="<?php echo esc_attr($_SERVER['REQUEST_URI']); ?>" />
				<input type="submit" id="login" name="login" value="<?php esc_attr_e('Login', 'bookyourtravel'); ?>" class="gradient-button"/>
			</form>
		</div>
	</div>
</div>