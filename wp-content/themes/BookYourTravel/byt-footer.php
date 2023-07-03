<?php
/**
 * The custom part of the footer for BookYourTravel theme
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $bookyourtravel_theme_globals;
$disable_theme_footer = $bookyourtravel_theme_globals->get_disable_theme_footer();
?>
			</div><!--// .wrap -->
			<a href="#" class="scroll-to-top" title="<?php esc_attr_e('Back up', 'bookyourtravel'); ?>"><i class="material-icons">&#xE316;</i></a> 
		</div><!--// .main -->	
		<div class="page-bottom">
			<?php //get_sidebar('above-footer'); ?>	
			
			<!--footer-->
			<?php if (!$disable_theme_footer) { ?>
			<footer class="footer">
				<?php //get_sidebar('footer'); ?>
				<?php get_template_part('includes/parts/footer/footer', 'custom'); ?>
				<div class="wrap">
					<div class="row">
						<div class="full-width">
							<?php //get_template_part('includes/parts/footer/footer', 'copy'); ?>
							<?php //get_template_part('includes/parts/footer/footer', 'nav'); ?>
						</div>
					</div>
				</div>
			</footer>
			<!--//footer-->
			<?php } ?>
			<?php get_template_part('includes/parts/footer/login', 'lightbox');  ?>
			<?php get_template_part('includes/parts/footer/register', 'lightbox'); ?>
			<?php bookyourtravel_footer_status(); ?>
		</div>
	</div><!--//page-wrap-->