<?php
/**
 * The sidebar containing the footer widget area.
 *
 * If no active widgets in sidebar, let's hide it completely.
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
if ( is_active_sidebar( 'footer' ) ) : ?>
	<div id="footer-sidebar" class="footer-sidebar widget-area wrap">
		<ul>
		<?php dynamic_sidebar( 'footer' ); ?>
		</ul>
	</div><!-- #secondary -->
<?php endif;