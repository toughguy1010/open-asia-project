<?php
/* 
 * Template Name: Reset Password Page
 * The template for displaying the reset password page.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
 
if (is_user_logged_in()) {
	wp_redirect( home_url('/') );
}

global $bookyourtravel_theme_globals, $item_class, $post;

$page_id = $post->ID;

$user_can_frontend_submit = get_post_meta($page_id, 'user_forgot_password_can_frontend_submit', true);
$use_recaptcha = get_post_meta($page_id, 'user_forgot_password_use_recaptcha', true) && $bookyourtravel_theme_globals->is_recaptcha_usable();

$scoped_register_page_url = $user_can_frontend_submit ? $bookyourtravel_theme_globals->get_partner_register_page_url() : $bookyourtravel_theme_globals->get_register_page_url();

$recaptcha_key = $bookyourtravel_theme_globals->get_google_recaptcha_key();
$recaptcha_secret = $bookyourtravel_theme_globals->get_google_recaptcha_secret();

$errors = array();

// Process reset password attempt on submit
if (isset($_POST['_wpnonce'])) {
	if( wp_verify_nonce( $_POST['_wpnonce'], 'bookyourtravel_nonce' ) ) {

		// user data array
		$resetpassword_userdata = array(
			'user_email' => sanitize_text_field( $_POST['user_email'] )
		);

		// custom user meta array
		$resetpassword_usermeta = array(
			'user_resetpassword_key' => wp_generate_password( 20, false ),
			'user_resetpassword_datetime' => date('Y-m-d H:i:s', time() )
		);	

		// validate email
		if ( !is_email( $resetpassword_userdata['user_email'] ) ) {
			$user = get_user_by('login', $resetpassword_userdata['user_email']);
			if (!$user)
				$errors['user_email'] = esc_html__( 'You must enter a valid and existing email address or username.', 'bookyourtravel' );
		} else if ( !email_exists( $resetpassword_userdata['user_email'] ) ) {
			$errors['user_email'] = esc_html__( 'You must enter a valid and existing email address or username.', 'bookyourtravel' );
		}
		
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

			$user = get_user_by( 'email', $resetpassword_userdata['user_email'] );
			
			if (!$user)
				$user = get_user_by( 'login', $resetpassword_userdata['user_email'] );
				
			if ($user) {

				// update custom user meta
				foreach ( $resetpassword_usermeta as $key => $value ) {
					update_user_meta( $user->ID, $key, $value );
				}

				BookYourTravel_Theme_Utils::reset_password_notification( $user->ID );
			}
				
			// refresh
			wp_redirect( esc_url_raw( add_query_arg( array( 'action' => 'resetpasswordnotification' ), get_permalink() ) ) );
			
			exit;
		}
	} 
}

get_header();
get_template_part('byt', 'header');
BookYourTravel_Theme_Controls::the_breadcrumbs();
get_sidebar('under-header');

$page_id = $post->ID;
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
				?>
				<form id="reset_password_form" method="post" action="<?php echo esc_url_raw ($form_action_url) ?>" class="static-content">
					<fieldset>
						<h3><?php the_title(); ?></h3>
						<?php the_content( wp_kses(__( 'Continue reading <span class="meta-nav">&rarr;</span>', 'bookyourtravel' ), array('span' =>  array('class' => array()))) ); ?>
						<?php 				
						if( isset( $_GET['action'] ) && $_GET['action'] == 'resetpasswordnotification' ) { 
						?>
						<p class="success">
							<?php esc_html_e( 'Please confirm the request to reset your password by clicking the link sent to your email address.', 'bookyourtravel' ) ?>
						</p>
						<?php
						} else if( isset( $_GET['action'] ) && $_GET['action'] == 'resetpassword' && isset( $_GET['user_id'] ) && isset( $_GET['resetpassword_key'] ) ) { 

							$user_id = intval( $_GET['user_id'] );
							$resetpassword_key = sanitize_text_field( $_GET['resetpassword_key'] );
							$new_password = BookYourTravel_Theme_Utils::reset_password( $user_id, $resetpassword_key );

							BookYourTravel_Theme_Utils::new_password_notification( $user_id, $new_password );
							
							if( $new_password ) { ?>
								<p class="success">
									<?php esc_html_e( 'Your password was successfully reset. We have sent the new password to your email address.', 'bookyourtravel' ) ?>
								</p>
							<?php } else { ?>
								<p class="error">
									<?php esc_html_e( 'We encountered an error when attempting to reset your password. Please try again later.', 'bookyourtravel' ) ?>
								</p>
							<?php }
						} else { ?>
							<?php if ( !empty( $scoped_register_page_url && get_option( 'users_can_register' ) ) ) { ?>	
							<p>
							<?php esc_html_e("Don't have an account yet?", 'bookyourtravel'); ?> <a href="<?php echo esc_url($scoped_register_page_url); ?>"><?php esc_html_e('Sign up', 'bookyourtravel'); ?></a>.
							</p>
							<?php } ?>
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
								<div class="f-item full-width">
									<label for="user_email"><?php esc_html_e('Username or email address', 'bookyourtravel'); ?></label>
									<input type="text" name="user_email" id="user_email" value="" />
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
								<div class="f-item full-width">
									<?php wp_nonce_field( 'bookyourtravel_nonce' ) ?>
									<input type="submit" id="reset" name="reset" value="<?php esc_attr_e('Reset password', 'bookyourtravel'); ?>" class="gradient-button"/>
								</div>
							</div>
					<?php } ?>
					</fieldset>
				</form>
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