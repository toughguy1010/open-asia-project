<?php
/* Template Name: Blank Page
 * Blank Page with Header and Footer
 *
 * @link  http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

get_header();
get_template_part('byt', 'header');

global $post, $bookyourtravel_theme_globals;
$page_id = $post->ID;
?>
		<section>
			<?php  while ( have_posts() ) : the_post(); ?>
				<article>
					<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'bookyourtravel' ) ); ?>
					<?php wp_link_pages('before=<div class="pagination">&after=</div>'); ?>
				</article>
			<?php endwhile; ?>
		</section>
<?php
get_template_part('byt', 'footer');
get_footer();