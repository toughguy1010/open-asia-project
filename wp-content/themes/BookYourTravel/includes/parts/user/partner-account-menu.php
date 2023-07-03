<?php
/**
 * The sidebar containing the partner account widget area.
 *
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since Book Your Travel 1.0
 */

global $bookyourtravel_theme_globals, $post, $current_url;

$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id);
?>
<?php
 if (has_nav_menu( 'partner-account-menu' )) {
	wp_nav_menu( array( 
		'theme_location' => 'partner-account-menu', 
		'container' => 'nav',
		'container_class' => 'inner-nav'
	) ); 
} else { 

	$partner_account_page_url = $bookyourtravel_theme_globals->get_partner_account_page_url();

	if (empty($partner_account_page_url)) {
		echo "<p class='error'>" . esc_html__('You have not created a user account page for your partners. Please go to Admin -> Pages and create a user account page and once published, please make sure the appropriate checkbox is checked below the main content editor.', 'bookyourtravel') . "</p>";
	}
?>
	<!--inner navigation-->
	<nav class="inner-nav">
		<ul>
			<?php if (!empty($partner_account_page_url)) { ?>
			<li <?php echo $current_url == $partner_account_page_url ? 'class="active"' : ''; ?>><a href="<?php echo esc_url($partner_account_page_url); ?>" title="<?php esc_attr_e('Settings', 'bookyourtravel'); ?>"><?php esc_html_e('Settings', 'bookyourtravel'); ?></a></li>
			<?php } ?>
		</ul>
	</nav>
	<!--// inner navigation-->
<?php 
}