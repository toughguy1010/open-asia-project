<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
 
if ( is_active_sidebar( 'sidebar' ) ) : ?>
	<aside id="secondary" class="widget-area  lower" role="complementary">
		<ul>
		<?php dynamic_sidebar( 'sidebar' ); ?>
		</ul>
	</aside><!-- #secondary -->
<?php endif;