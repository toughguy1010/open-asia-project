<?php 
global $cart_page_url, $my_account_page_url, $my_partner_account_page_url, $bookyourtravel_theme_globals;

$login_page_url	= $bookyourtravel_theme_globals->get_login_page_url();
$register_page_url = $bookyourtravel_theme_globals->get_register_page_url();
$my_account_page_url = $bookyourtravel_theme_globals->get_my_account_page_url();

if ( has_nav_menu( 'header-ribbon-menu' ) ) { ?>
<div class="ribbon">
<?php
	wp_nav_menu( array( 
		'theme_location' => 'header-ribbon-menu', 
		'container' => 'nav',
		'menu_class' => 'profile-nav',
		'menu_id' => '',
		'container_class' => ''
	) ); ?>
</div>
<?php
} else {
	ob_start();	
?>
<!--ribbon-->
<div class="ribbon ">
	<nav>
		<ul class="profile-nav">
		<?php if ( !is_user_logged_in() ) { ?>
			<li class="fn-opener"><a href="javascript:void(0);" title="<?php esc_attr_e('My Account', 'bookyourtravel'); ?>"><?php esc_html_e('My Account', 'bookyourtravel'); ?></a></li>
			<?php if (!empty($login_page_url)) { ?>
			<li class="login_lightbox"><a href="javascript:void(0);" title="<?php esc_attr_e('Login', 'bookyourtravel'); ?>"><?php esc_html_e('Login', 'bookyourtravel'); ?></a></li>
			<?php } ?>
			<?php if ( !empty( $register_page_url && get_option( 'users_can_register' ) ) ) { ?>
			<li class="register_lightbox"><a href="javascript:void(0);" title="<?php esc_attr_e('Register', 'bookyourtravel'); ?>"><?php esc_html_e('Register', 'bookyourtravel'); ?></a></li>
			<?php } ?>
		<?php } else { ?>
			<li class="fn-opener"><a href="javascript:void(0);" title="<?php esc_attr_e('My Account', 'bookyourtravel'); ?>"><?php esc_html_e('My Account', 'bookyourtravel'); ?></a></li>
			<?php if ( !empty( $my_account_page_url ) || !empty( $cart_page_url ) ) { ?>
			<li><a class="" href="<?php echo esc_url( $my_account_page_url ); ?>" title="<?php esc_attr_e('Dashboard', 'bookyourtravel'); ?>"><?php esc_html_e('Dashboard', 'bookyourtravel'); ?></a></li>
				<?php if ( !empty( $cart_page_url ) && BookYourTravel_Theme_Utils::is_woocommerce_active() ) { ?>
			<li><a class="" href="<?php echo esc_url($cart_page_url); ?>"><?php esc_html_e('Cart', 'bookyourtravel'); ?></a></li>	
				<?php } ?>
			<?php } // (!empty($my_account_page_url) || !empty($cart_page_url)) ?>
			<li><a class="" href="<?php echo wp_logout_url(home_url('/')); ?>"><?php esc_html_e('Logout', 'bookyourtravel'); ?></a></li>
		<?php } ?>
		</ul>
		<?php get_sidebar('header');?>
	</nav>
</div>
<!--//ribbon-->
<?php 
	$ribbon_html = ob_get_contents();
	ob_end_clean();
	echo apply_filters('bookyourtravel_header_ribbon', $ribbon_html);
}