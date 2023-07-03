<?php 
/* 
 * 
 * Template Name: Register Page
 * The template for displaying the Register page.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

if (is_user_logged_in() || !get_option( 'users_can_register' )) {
	wp_redirect( home_url('/') );
}

global $bookyourtravel_theme_globals, $post;

$terms_page_url = $bookyourtravel_theme_globals->get_terms_page_url();

$page_id = $post->ID;

$user_can_frontend_submit = get_post_meta($page_id, 'user_register_can_frontend_submit', true);
$use_recaptcha = get_post_meta($page_id, 'user_register_use_recaptcha', true) && $bookyourtravel_theme_globals->is_recaptcha_usable();

$scoped_reset_password_page_url = $user_can_frontend_submit ? $bookyourtravel_theme_globals->get_partner_reset_password_page_url() : $bookyourtravel_theme_globals->get_reset_password_page_url();
$scoped_login_page_url = $user_can_frontend_submit ? $bookyourtravel_theme_globals->get_partner_login_page_url() : $bookyourtravel_theme_globals->get_login_page_url();

$recaptcha_key = $bookyourtravel_theme_globals->get_google_recaptcha_key();
$recaptcha_secret = $bookyourtravel_theme_globals->get_google_recaptcha_secret();

$errors = array();

if( isset( $_POST['user_login'] ) &&  isset( $_POST['user_email'] ) ) {
	
if (isset($_POST['_wpnonce_register']) && wp_verify_nonce( $_POST['_wpnonce_register'], 'bookyourtravel_nonce' ) ||
isset($_POST['_wpnonce_register_page']) && wp_verify_nonce( $_POST['_wpnonce_register_page'], 'bookyourtravel_nonce' )) {

		// user data array
		$register_userdata = array(
			'user_login' => sanitize_text_field( $_POST['user_login'] ),
			'user_email' => sanitize_text_field( $_POST['user_email'] )
		);
		
		$register_usermeta = array(
			'agree' =>( ( isset( $_POST['checkboxagree'] ) && !empty( $_POST['checkboxagree'] ) ) ? '1' : '0' )
		);	
		
		$register_userdata['user_pass'] = sanitize_text_field( $_POST['password'] );
		$register_userdata['confirm_pass'] = sanitize_text_field( $_POST['repeat_password'] );
		
		// validate username
		if ( trim( $register_userdata['user_login'] ) == '' ) {
			$errors['user_login'] = esc_html__( 'Username is required.', 'bookyourtravel' );
		}
		else if ( strlen( $register_userdata['user_login'] ) < 6 ) {
			$errors['user_login'] = esc_html__( 'Sorry, username must be 6 characters or more.', 'bookyourtravel' );
		}
		else if ( !validate_username( $register_userdata['user_login'] ) ) {
			$errors['user_login'] = esc_html__( 'Sorry, the username you provided is invalid.', 'bookyourtravel' );
		}
		else if ( username_exists( $register_userdata['user_login'] ) ) {
			$errors['user_login'] = esc_html__( 'Sorry, that username already exists.', 'bookyourtravel' );
		}

		// validate password
		if ( trim( $register_userdata['user_pass'] ) == '' ) {
			$errors['user_pass'] = esc_html__( 'Password is required.', 'bookyourtravel' );
		}
		else if ( strlen( $register_userdata['user_pass'] ) < 6 ) {
			$errors['user_pass'] = esc_html__( 'Sorry, password must be 6 characters or more.', 'bookyourtravel' );
		}
		else if ( $register_userdata['user_pass'] !== $register_userdata['confirm_pass'] ) {
			$errors['confirm_pass'] = esc_html__( 'Password and confirm password fields must match.', 'bookyourtravel' );
		}
		
		// validate user_email
		if ( !is_email( $register_userdata['user_email'] ) ) {
			$errors['user_email'] = esc_html__( 'You must enter a valid email address.', 'bookyourtravel' );
		}
		else if ( email_exists( $register_userdata['user_email'] ) ) {
			$errors['user_email'] = esc_html__( 'Sorry, that email address is already in use.', 'bookyourtravel' );
		}

		// validate agree
		if( $register_usermeta['agree'] == '0' ) {
			$errors['agree'] = esc_html__( 'You must agree to our terms &amp; conditions to sign up.', 'bookyourtravel' );
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
			
			// insert new user
			$new_user_id = wp_create_user( $register_userdata['user_login'], $register_userdata['user_pass'], $register_userdata['user_email'] );
			
			// notify admin and user of registration
			wp_new_user_notification( $new_user_id, null, 'admin');
			BookYourTravel_Theme_Utils::new_user_notification($new_user_id);
			
			$new_user_id = get_userdata( $new_user_id );
			$user = new WP_User($new_user_id);		

			if ($user_can_frontend_submit) {
				$user->set_role( BOOKYOURTRAVEL_FRONTEND_SUBMIT_ROLE );
			} else {
				$user->set_role( get_option('default_role') );
			};		

			// refresh
			wp_redirect( esc_url_raw (add_query_arg( array( 'action' => 'registered' ), get_permalink() ) ) );
			exit;
		}
	} else {
		die('Invalid attempt');
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
				?>
				<form id="register_form" method="post" action="<?php echo esc_url_raw ($form_action_url) ?>" class="static-content">
					<fieldset>
					<?php
					if( isset( $_GET['action'] ) && $_GET['action'] == 'registered') { 
						?>
						<p class="success">
							<?php 
							$allowed_tags = array();
							$allowed_tags['a'] = array('class' => array(), 'rel' => array(), 'style' => array(), 'id' => array(), 'href' => array(), 'title' => array());					
							echo sprintf(wp_kses(__( 'User account successfully created. Please proceed to the <a href="%s">login</a> page to login in.', 'bookyourtravel' ), $allowed_tags), $scoped_login_page_url); ?>
						</p>
					<?php
					} else {
					?>
						<h3><?php the_title(); ?></h3>
						<?php the_content( wp_kses(__( 'Continue reading <span class="meta-nav">&rarr;</span>', 'bookyourtravel' ), array('span' =>  array('class' => array()))) ); ?>
						<p>	
						<?php esc_html_e('Already a member?', 'bookyourtravel'); ?> <?php echo sprintf(__('Proceed to <a href="%s">login</a> page', 'bookyourtravel'), esc_url($scoped_login_page_url)); ?>. <?php esc_html_e('Forgotten your password?', 'bookyourtravel'); ?> <a href="<?php echo esc_url($scoped_reset_password_page_url); ?>"><?php esc_html_e('Reset it here', 'bookyourtravel'); ?></a>.
						</p>
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
								<label for="user_login"><?php esc_html_e('Username', 'bookyourtravel'); ?></label>
								<input tabindex="1" type="text" id="user_login_page" name="user_login" value="<?php echo isset($register_userdata) ? $register_userdata['user_login'] : ''; ?>" />
							</div>
							<div class="f-item one-half">
								<label for="user_email"><?php esc_html_e('Email', 'bookyourtravel'); ?></label>
								<input tabindex="2" type="email" id="user_email_page" name="user_email" value="<?php echo isset($register_userdata) ? $register_userdata['user_email'] : ''; ?>" />
							</div>
							<div class="f-item one-half">
								<label for="password_page"><?php esc_html_e('Password', 'bookyourtravel'); ?></label>
								<input tabindex="3" id="password_page" class="input" type="password" tabindex="30" size="25" value="" name="password" />
							</div>
							<div class="f-item one-half">
								<label for="repeat_password_page"><?php esc_html_e('Repeat password', 'bookyourtravel'); ?></label>
								<input tabindex="4" id="repeat_password_page" class="input" type="password" tabindex="40" size="25" value="" name="repeat_password" />
							</div>					
							<?php do_action( 'register_form' ); ?>	
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
								<input tabindex="5" type="checkbox" value="ch1" id="checkboxagree_page" name="checkboxagree" />
								<label><?php echo sprintf(__('I agree to the <a href="%s">terms &amp; conditions</a>.', 'bookyourtravel'), esc_url($terms_page_url)); ?></label>
							</div>
							<div class="f-item full-width">
								<?php wp_nonce_field( 'bookyourtravel_nonce', '_wpnonce_register_page') ?>
								<input tabindex="6" type="submit" id="register_page" name="register" value="<?php esc_attr_e('Register', 'bookyourtravel'); ?>" class="gradient-button"/>
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