<?php
/*
 * Template Name: User Account Page
 * The template for displaying the user account page.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
global $bookyourtravel_theme_globals;

if ( !is_user_logged_in() ) {
	$login_page_url = $bookyourtravel_theme_globals->get_login_page_url();
	if (!empty($login_page_url)) {
		wp_redirect( $login_page_url );
	} else {
		wp_redirect( home_url('/') );
	}
	exit;
}

global $frontend_submit, $current_user, $item_class, $post, $current_url;

get_header();
get_template_part('byt', 'header');
BookYourTravel_Theme_Controls::the_breadcrumbs();
get_sidebar('under-header');

$current_user = wp_get_current_user();
$user_info = get_userdata($current_user->ID);

$page_id = $post->ID;
$current_url = get_permalink( $page_id );

$page_custom_fields = get_post_custom( $page_id);
$is_partner_page = false;
if (isset($page_custom_fields['user_account_is_partner_page'])) {
	$is_partner_page = $page_custom_fields['user_account_is_partner_page'][0];
}

$delete_request_id = get_user_meta($current_user->ID, "remove_personal_data_request_id", true);
$export_request_id = get_user_meta($current_user->ID, "export_personal_data_request_id", true);

$page_sidebar_positioning = BookYourTravel_Theme_Utils::get_page_sidebar_positioning($page_id);
$section_class = BookYourTravel_Theme_Utils::get_page_section_class($page_sidebar_positioning);
$item_class = BookYourTravel_Theme_Utils::get_item_class($section_class);
?>
		<div class="row">
		<?php
		if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
			get_sidebar('left');
		?>
			<section class="<?php echo esc_attr($section_class); ?>">
				<?php  while ( have_posts() ) : the_post(); ?>
				<article id="page-<?php the_ID(); ?>">
					<h1 class="entry-title"><?php the_title(); ?></h1>
					<?php the_content( wp_kses(__( 'Continue reading <span class="meta-nav">&rarr;</span>', 'bookyourtravel' ), BookYourTravel_Theme_Utils::get_allowed_content_tags_array()) ); ?>
					<?php wp_link_pages('before=<div class="pagination">&after=</div>'); ?>
				</article>
				<?php endwhile; ?>
				<?php
				if ($is_partner_page && $frontend_submit->user_has_correct_role()) {
					get_template_part('includes/parts/user/partner-account', 'menu');
				} else {
					get_template_part('includes/parts/user/user-account', 'menu');
				}
				?>
				<!--MySettings-->
				<section id="settings" class="tab-content initial">
					<script type="text/javascript">
						window.settingsFirstNameError = '<?php esc_html_e('First name is a required field!', 'bookyourtravel'); ?>';
						window.settingsLastNameError = '<?php esc_html_e('Last name is a required field!', 'bookyourtravel'); ?>';
						window.settingsEmailError = '<?php esc_html_e('Please enter valid email address!', 'bookyourtravel'); ?>';
						window.settingsPasswordError = '<?php esc_html_e('Password is a required field!', 'bookyourtravel'); ?>';
						window.settingsOldPasswordError = '<?php esc_html_e('Old password is a required field!', 'bookyourtravel'); ?>';
					</script>
					<article class="mysettings">
						<h2><?php esc_html_e('Personal details', 'bookyourtravel'); ?></h2>
						<div class="commands">
							<span style="display:none" class="request_success"><?php esc_html_e("Sent!", "bookyourtravel"); ?></span>
							<a class="gradient-button export_account_button" href="#" title="<?php esc_html_e('Submit request to admin for account data export', 'bookyourtravel'); ?>"><?php esc_html_e('Request export', 'bookyourtravel'); ?></a>
							<a class="gradient-button delete_account_button" href="#" title="<?php esc_html_e('Submit request to admin for account deletion', 'bookyourtravel'); ?>"><?php esc_html_e('Request account deletion', 'bookyourtravel'); ?></a>
						</div>
						<table>
							<tr>
								<th><?php esc_html_e('First name', 'bookyourtravel'); ?></th>
								<td><span id="span_first_name"><?php echo esc_html($user_info->user_firstname);?></span>
									<div style="display:none;" class="edit_field field_first_name">
										<form id="settings-first-name-form" method="post" action="" class="settings">
											<label for="first_name"><?php esc_html_e('First name', 'bookyourtravel'); ?>:</label>
											<input type="text" id="first_name" name="first_name" value="<?php echo esc_attr($user_info->user_firstname);?>"/>
											<input type="submit" value="<?php esc_attr_e('save', 'bookyourtravel'); ?>" class="gradient-button save_first_name"/>
											<a class="hide_edit_field" href="javascript:void(0);"><?php esc_html_e('Cancel', 'bookyourtravel'); ?></a>
										</form>
									</div>
								</td>
								<td><a class="edit_button" href="javascript:void(0);"><?php esc_html_e('Edit', 'bookyourtravel'); ?></a></td>
							</tr>
							<tr>
								<th><?php esc_html_e('Last name', 'bookyourtravel'); ?>:</th>
								<td><span id="span_last_name"><?php echo esc_html($user_info->user_lastname);?></span>
									<div style="display:none;" class="edit_field field_last_name">
										<form id="settings-last-name-form" method="post" action="" class="settings">
											<label for="last_name"><?php esc_html_e('Last name', 'bookyourtravel'); ?>:</label>
											<input type="text" id="last_name" name="last_name" value="<?php echo esc_attr($user_info->user_lastname);?>" />
											<input type="submit" value="<?php esc_attr_e('save', 'bookyourtravel'); ?>" class="gradient-button save_last_name"/>
											<a class="hide_edit_field" href="javascript:void(0);"><?php esc_html_e('Cancel', 'bookyourtravel'); ?></a>
										</form>
									</div>
								</td>
								<td><a class="edit_button" href="javascript:void(0);"><?php esc_html_e('Edit', 'bookyourtravel'); ?></a></td>
							</tr>
							<tr>
								<th><?php esc_html_e('Email address', 'bookyourtravel'); ?>:</th>
								<td><span id="span_email"><?php echo esc_html($user_info->user_email);?></span>
									<div style="display:none;" class="edit_field field_email">
										<form id="settings-email-form" method="post" action="" class="settings">
											<label for="email"><?php esc_html_e('Email', 'bookyourtravel'); ?>:</label>
											<input type="text" id="email" name="email" value="<?php echo esc_attr($user_info->user_email);?>" />
											<input type="submit" value="<?php esc_attr_e('save', 'bookyourtravel'); ?>" class="gradient-button save_email"/>
											<a class="hide_edit_field" href="javascript:void(0);"><?php esc_html_e('Cancel', 'bookyourtravel'); ?></a>
										</form>
									</div>
								</td>
								<td><a class="edit_button" href="javascript:void(0);"><?php esc_html_e('Edit', 'bookyourtravel'); ?></a></td>
							</tr>
							<tr>
								<th><?php esc_html_e('Password', 'bookyourtravel'); ?>:</th>
								<td><span id="span_email">**************</span>
									<div style="display:none;" class="edit_field field_password">
										<form id="settings-password-form" method="post" action="" class="settings">
											<label for="old_password"><?php esc_html_e('Current password', 'bookyourtravel'); ?>:</label>
											<input type="password" id="old_password" name="old_password" />
											<label for="new_password"><?php esc_html_e('New password', 'bookyourtravel'); ?>:</label>
											<input type="password" id="new_password" name="new_password" />
											<input type="submit" value="<?php esc_attr_e('save', 'bookyourtravel'); ?>" class="gradient-button save_password"/>
											<a class="hide_edit_field" href="javascript:void(0);"><?php esc_html_e('Cancel', 'bookyourtravel'); ?></a>
										</form>
									</div></td>
								<td><a class="edit_button" href="javascript:void(0);"><?php esc_html_e('Edit', 'bookyourtravel'); ?></a></td>
							</tr>
						</table>
					</article>
				</section>
				<!--//MySettings-->
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