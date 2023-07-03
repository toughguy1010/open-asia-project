<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * For example, it puts together the home page when no home.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */
get_header();
get_template_part('byt', 'header');
BookYourTravel_Theme_Controls::the_breadcrumbs();
get_sidebar('under-header');

global $bookyourtravel_theme_globals, $bookyourtravel_theme_post_types;

$posts_per_row = $bookyourtravel_theme_globals->get_taxonomy_pages_items_per_row();
$display_mode = 'card';

?>
		<div class="row">
			<section class="full-width">
				<?php if (have_posts()) { ?>
				<div class="deals">
					<div class="row">
						<?php
						while (have_posts()) {
							the_post();
							global $post;

							$template_part = '';
							global $post_item_args;
							if (!isset($post_item_args) || !is_array($post_item_args)) {
								$post_item_args = array();
							}
							$post_item_args['post_id'] = $post->ID;
							$post_item_args['post'] = $post;
							$post_item_args['item_class'] = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);
							$post_item_args['display_mode'] = $display_mode;
							$post_item_args['hide_item_descriptions'] = false;

							$template_part = 'includes/parts/post/post';

							get_template_part($template_part, 'item');
						}
						?>
					</div>
					<?php
					global $wp_query;
					if ($wp_query->max_num_pages > 1) {
					?>
					<!--bottom navigation-->
					<div class="full-width">
						<nav class="page-navigation bottom-nav">
							<div class="pager">
							<?php
								BookYourTravel_Theme_Controls::the_pager($wp_query->max_num_pages);
							?>
							</div>
						</nav>
					</div>
					<!--//bottom navigation-->
					<?php
					}
					?>
				</div>
				<?php } else { ?>
				<div class="row">
					<div class="full-width">
						<article class="static-content post">
							<header class="entry-header">
								<br /><h1 class="entry-title"><?php esc_html_e('Welcome to Book Your Travel!', 'bookyourtravel'); ?></h1>
							</header>
							<div class="entry-content">
								<p><?php esc_html_e('There is currently no content to show here, so start creating content.', 'bookyourtravel'); ?></p>
							</div>
						</article>
					</div>
				</div>
				<?php } ?>
			</section>
		</div>
<?php
get_template_part('byt', 'footer');
get_footer();
