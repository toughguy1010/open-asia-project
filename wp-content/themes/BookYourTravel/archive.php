<?php
/**
 * The archive template file
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
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

$page_sidebar_positioning = $bookyourtravel_theme_globals->get_taxonomy_pages_sidebar_position();
$section_class = BookYourTravel_Theme_Utils::get_page_section_class($page_sidebar_positioning);
$posts_per_row = $bookyourtravel_theme_globals->get_taxonomy_pages_items_per_row();
$hide_item_titles = $bookyourtravel_theme_globals->taxonomy_pages_hide_item_titles();
$hide_item_descriptions = $bookyourtravel_theme_globals->taxonomy_pages_hide_item_descriptions();
$hide_item_images = $bookyourtravel_theme_globals->taxonomy_pages_hide_item_images();
$hide_item_actions = $bookyourtravel_theme_globals->taxonomy_pages_hide_item_actions();

$display_mode = 'card';

$obj = get_queried_object();
$taxonomy = '';
if ($obj && isset($obj->taxonomy)) {
    $taxonomy = $obj->taxonomy;
}

$term_id = get_queried_object() ? get_queried_object()->term_id : 0;
$taxonomy_featured_image_id = $term_id ? $bookyourtravel_theme_post_types->get_taxonomy_image_id($term_id) : 0;

if ($taxonomy == 'post_tag' || $taxonomy == 'category' || $taxonomy == '') {
?>
		<div class="row">
			<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
				get_sidebar('left');
			?>
			<section class="<?php echo esc_attr($section_class) ;?>">
				<?php if ($taxonomy_featured_image_id) { ?>
				<div class="page-featured-image">
					<?php $featured_img_url = wp_get_attachment_image_src($taxonomy_featured_image_id, "byt-featured")[0]; ?>
					<div class="keyvisual" style="background-image:url(<?php echo esc_url($featured_img_url); ?>)"></div>
					<div class="wrap">
						<?php
						the_archive_title( '<h1 class="entry-title">', '</h1>' );
						the_archive_description( '<div class="taxonomy-description">', '</div>' );
						?>
					</div>
				</div>
				<?php } else { ?>
				<header class="page-header">
					<?php
						the_archive_title( '<h1 class="entry-title">', '</h1>' );
						the_archive_description( '<div class="taxonomy-description">', '</div>' );
					?>
				</header><!-- .page-header -->
				<?php } ?>
				<?php if (have_posts()) { ?>
				<div class="deals">
					<div class="row">
						<?php
						while (have_posts()) {
							the_post();
							global $post;

							$template_part = '';
							if ($taxonomy == 'post_tag' || $taxonomy == 'category' || $taxonomy == '') {
								global $post_item_args;
								if (!isset($post_item_args) || !is_array($post_item_args)) {
									$post_item_args = array();
								}
								$post_item_args['post_id'] = $post->ID;
								$post_item_args['post'] = $post;
								$post_item_args['item_class'] = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);
								$post_item_args['display_mode'] = $display_mode;
								$post_item_args = array_merge($post_item_args, array(
									'hide_title' => $hide_item_titles,
									'hide_image' => $hide_item_images,
									'hide_description' => $hide_item_descriptions,
									'hide_actions' => $hide_item_actions
								));

								$template_part = 'includes/parts/post/post';
							}

							get_template_part($template_part, 'item');
						}
						?>
					</div>
					<?php
					if ($wp_query->max_num_pages > 1) {
					?>
					<!--bottom navigation-->
					<div class="full-width">
						<nav class="page-navigation bottom-nav">
							<div class="pager">
							<?php
								global $wp_query;
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
			<!--//three-fourth content-->
		<?php
		if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right')
			get_sidebar('right');
		?>
		</div>
<?php
}
get_template_part('byt', 'footer');
get_footer();
