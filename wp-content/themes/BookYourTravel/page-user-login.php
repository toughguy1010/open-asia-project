<?php 
/* 
 * Template Name: Login Page
 * The template for displaying the Login page.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if (is_user_logged_in()) {
	wp_redirect( home_url('/') );
}

global $bookyourtravel_theme_globals, $post;

$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id);

$user_can_frontend_submit = get_post_meta($page_id, 'user_login_can_frontend_submit', true);
$use_recaptcha = get_post_meta($page_id, 'user_login_use_recaptcha', true) && $bookyourtravel_theme_globals->is_recaptcha_usable();

$scoped_reset_password_page_url = $user_can_frontend_submit ? $bookyourtravel_theme_globals->get_partner_reset_password_page_url() : $bookyourtravel_theme_globals->get_reset_password_page_url();
$scoped_register_page_url = $user_can_frontend_submit ? $bookyourtravel_theme_globals->get_partner_register_page_url() : $bookyourtravel_theme_globals->get_register_page_url();

$recaptcha_key = $bookyourtravel_theme_globals->get_google_recaptcha_key();
$recaptcha_secret = $bookyourtravel_theme_globals->get_google_recaptcha_secret();

$redirect_to_after_login_url = home_url('/');
if (isset($page_custom_fields['user_login_redirect_to_after_login'])) {
	$user_login_redirect_to_after_login_id = $page_custom_fields['user_login_redirect_to_after_login'][0];
	$user_login_redirect_to_after_login_id = empty($user_login_redirect_to_after_login_id) ? 0 : (int)$user_login_redirect_to_after_login_id;
	
	if ($user_login_redirect_to_after_login_id > 0) {
		$user_login_redirect_to_after_login_id = BookYourTravel_Theme_Utils::get_current_language_page_id( $user_login_redirect_to_after_login_id );
		if ($user_login_redirect_to_after_login_id > 0) {
			$redirect_to_after_login_url = get_permalink($user_login_redirect_to_after_login_id);
		}
	}
}

$login = null;
$errors = array();

nocache_headers();

if( isset( $_POST['log'] ) && isset($_POST['_wpnonce']) && wp_verify_nonce( $_POST['_wpnonce'], 'bookyourtravel_nonce' ) ) {

	$is_ssl = is_ssl();
	
	if ($use_recaptcha && isset($_POST['g-recaptcha-response'])) {
		$captcha = $_POST['g-recaptcha-response'];

		$json_uri = "https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptcha_secret . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR'];
		$request = wp_remote_get($json_uri);
		$response = wp_remote_retrieve_body( $request );
		$response = json_decode($response, true);			
		
		if (!$response['success']) {
			$errors['captcha'] = esc_html__('You must fill in the captcha field correctly.', 'bookyourtravel');
		}
	}	

	if( empty( $errors ) ) {	
		$login = wp_signon(
			array(
				'user_login' => $_POST['log'],
				'user_password' => $_POST['pwd'],
				'remember' =>( ( isset( $_POST['rememberme'] ) && $_POST['rememberme'] ) ? true : false )
			),
			$is_ssl
		);
		
		if ( !is_wp_error( $login ) ) {
			wp_redirect( $redirect_to_after_login_url );
			exit;
		} else {
			wp_clear_auth_cookie();
		}
	}
}

get_header();
get_template_part('byt', 'header');

BookYourTravel_Theme_Controls::the_breadcrumbs();
get_sidebar('under-header');

$page_sidebar_positioning = BookYourTravel_Theme_Utils::get_page_sidebar_positioning($page_id);
$section_class = BookYourTravel_Theme_Utils::get_page_section_class($page_sidebar_positioning);
?>
		<div class="row">
			<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
				get_sidebar('left');
			?>
			<section class="<?php echo esc_attr($section_class); ?>">
				<?php 
				$form_action_url = get_permalink();
				if (!$bookyourtravel_theme_globals->permalinks_enabled()) {
					$form_action_url = add_query_arg( array( 'page_id' => $page_id ), $form_action_url );
				} 

				if (is_wp_error( $login ) && isset($login->errors['too_many_retries'])) {
					echo '<div class="error message-box">' . esc_html__('Too many login attempts. Please try again later.', 'bookyourtravel') . '</div>';
				} else {
				?>
				<form id="login_form" method="post" action="<?php echo esc_url_raw ( $form_action_url ) ?>" class="static-content">
					<fieldset>
						<h3><?php the_title(); ?></h3>
						<?php the_content( wp_kses(__( 'Continue reading <span class="meta-nav">&rarr;</span>', 'bookyourtravel' ), array('span' =>  array('class' => array()))) ); ?>
						<p class="">
							<?php if ( !empty( $scoped_register_page_url && get_option( 'users_can_register' ) ) ) { ?><?php esc_html_e("Don't have an account yet?", 'bookyourtravel'); ?> <a href="<?php echo esc_url($scoped_register_page_url); ?>"><?php esc_html_e('Sign up', 'bookyourtravel'); ?></a>.<?php } ?> <?php esc_html_e('Forgotten your password?', 'bookyourtravel'); ?> <a href="<?php echo esc_url($scoped_reset_password_page_url); ?>"><?php esc_html_e('Reset it here', 'bookyourtravel'); ?></a>.
						</p>
						<?php if( is_wp_error( $login ) ) { 
							echo '<div class="error message-box">' . esc_html__('Incorrect username or password. Please try again.', 'bookyourtravel') . '</div>';
						} 
						?>
						<?php
						if (count($errors) > 0) {
							?>
							<div class="error message-box">
								<p><?php esc_html_e( 'Errors were encountered when processing your request.', 'bookyourtravel' ) ?></p>
								<?php foreach ($errors as $error) {
									echo '<p>' . $error . '</p>';
								} ?>
							</div>
							<?php
						}
						?>				
						<div class="row">
							<div class="f-item one-half">
								<label for="log"><?php esc_html_e('Username', 'bookyourtravel'); ?></label>
								<input type="text" name="log" id="log" value="" />
							</div>
							<div class="f-item one-half">
								<label for="pwd"><?php esc_html_e('Password', 'bookyourtravel'); ?></label>
								<input type="password" name="pwd" id="pwd" value="" />
							</div>
							<?php
							if ($use_recaptcha) {
							?>
							<div class="f-item one-half">
								<div class="g-recaptcha" data-sitekey="<?php echo esc_attr($recaptcha_key); ?>"></div>
							</div>
							<?php
							}
							?>					
							<div class="f-item full-width checkbox">
								<input type="checkbox" name="rememberme" name="rememberme">
								<label for="rememberme"><?php esc_html_e( 'Remember Me', 'bookyourtravel' ); ?> </label>
							</div>
							<div class="f-item full-width">
								<?php wp_nonce_field( 'bookyourtravel_nonce' ) ?>
								<input type="hidden" name="redirect_to" value="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" />
								<input type="submit" id="login" name="login" value="<?php esc_attr_e('Login', 'bookyourtravel'); ?>" class="gradient-button"/>
							</div>
						</div>
					</fieldset>
				</form>
				<?php } ?>
			</section>	
			<?php
			wp_reset_postdata();
			wp_reset_query();
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right')
				get_sidebar('right');
			?>
		</div>
<?php
get_template_part('byt', 'footer');
get_footer();