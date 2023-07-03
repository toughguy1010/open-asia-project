<?php
/**
 * The sidebar containing the home content widget area.
 *
 * If no active widgets in sidebar, let's hide it completely.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
	if ( is_active_sidebar( 'home-content' ) ) {
?>
	<section class="home-content-sidebar hcs1 widget-area">
		<ul>
		<?php dynamic_sidebar( 'home-content' ); ?>
		</ul>
	</section><!-- #secondary -->
<?php } 