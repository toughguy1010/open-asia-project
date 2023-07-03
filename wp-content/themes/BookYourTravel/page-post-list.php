<?php
/**
/* Template Name: Post list
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

global $bookyourtravel_theme_globals, $post;
$page_id = $post->ID;

$page_sidebar_positioning = BookYourTravel_Theme_Utils::get_page_sidebar_positioning($page_id);
$section_class = BookYourTravel_Theme_Utils::get_page_section_class($page_sidebar_positioning);

$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id);

$sort_by = isset($page_custom_fields['post_list_sort_by']) && !empty($page_custom_fields['post_list_sort_by'][0]) ? $page_custom_fields['post_list_sort_by'][0] : 'title';
$sort_descending = isset($page_custom_fields['post_list_sort_descending']) && $page_custom_fields['post_list_sort_descending'][0] == '1' ? true : false;
$sort_order = $sort_descending ? 'DESC' : 'ASC';
$posts_per_page = isset($page_custom_fields['post_list_posts_per_page']) ? intval($page_custom_fields['post_list_posts_per_page'][0]) : 12;
$posts_per_row = isset($page_custom_fields['post_list_posts_per_row']) ? intval($page_custom_fields['post_list_posts_per_row'][0]) : 4;

$hide_item_titles = isset($page_custom_fields['post_list_hide_item_titles']) && $page_custom_fields['post_list_hide_item_titles'][0] == '1' ? true : false;
$hide_item_images = isset($page_custom_fields['post_list_hide_item_images']) && $page_custom_fields['post_list_hide_item_images'][0] == '1' ? true : false;
$hide_item_descriptions = isset($page_custom_fields['post_list_hide_item_descriptions']) && $page_custom_fields['post_list_hide_item_descriptions'][0] == '1' ? true : false;
$hide_item_actions = isset($page_custom_fields['post_list_hide_item_actions']) && $page_custom_fields['post_list_hide_item_actions'][0] == '1' ? true : false;

$page = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
	'paged'			   => $page,
	'orderby'          => $sort_by,
	'order'            => $sort_order,
	'posts_per_page'   => $posts_per_page,
	'post_type'        => 'post',
	'post_status'      => 'publish');

$query = new WP_Query($args);
$display_mode = 'card';
$item_class = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);
?>
		<div class="row">
			<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
				get_sidebar('left');
			?>
			<section class="<?php echo esc_attr($section_class); ?>">
				<?php  while ( have_posts() ) : the_post(); ?>
				<article <?php post_class(); ?> id="page-<?php the_ID(); ?>">
					<?php
						$has_post_thumbnail = has_post_thumbnail();
					?>
					<?php if ($has_post_thumbnail) { ?>
					<div class="page-featured-image">
						<?php $featured_img_url = get_the_post_thumbnail_url(get_the_ID(), "byt-featured"); ?>
						<div class="keyvisual" style="background-image:url(<?php echo esc_url($featured_img_url); ?>)"></div>
						<div class="wrap"><h1><?php the_title(); ?></h1></div>
					</div>
					<?php } else {?>
						<h1 class="entry-title"><?php the_title(); ?></h1>
					<?php } ?>
					<?php if ($has_post_thumbnail) {?>
					<div class="post-general-content">
					<?php } ?>
					<?php the_content( wp_kses(__( 'Continue reading <span class="meta-nav">&rarr;</span>', 'bookyourtravel' ), array('span' =>  array('class' => array()))) ); ?>
					<?php if ($has_post_thumbnail) {?>
					</div>
					<?php } ?>
					<?php wp_link_pages('before=<div class="pagination">&after=</div>'); ?>
				</article>
				<?php endwhile; ?>
				
				<div class="offers">
					<div class="row">
					<?php if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); global $post; ?>
					<!--post-->
					<?php
					$template_part = '';
					global $post_item_args;
					if (!isset($post_item_args) || !is_array($post_item_args)) {
						$post_item_args = array();
					}
					$post_item_args['post_id'] = $post->ID;
					$post_item_args['post'] = $post;
					$post_item_args['item_class'] = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);
					$post_item_args['display_mode'] = $display_mode;
					$post_item_args['hide_actions'] = $hide_item_actions;
					$post_item_args['hide_image'] = $hide_item_images;
					$post_item_args['hide_title'] = $hide_item_titles;
					$post_item_args['hide_description'] = $hide_item_descriptions;

					$template_part = 'includes/parts/post/post';

					get_template_part($template_part, 'item');

					?>
					<!--//post-->
					<?php endwhile; endif; ?>
					</div>
				<?php
				if ($query->max_num_pages > 1) {
				?>
				<!--bottom navigation-->
				<nav class="page-navigation bottom-nav">
					<div class="pager">
					<?php BookYourTravel_Theme_Controls::the_pager($query->max_num_pages); ?>
					</div>
				</nav>
				<!--//bottom navigation-->
				<?php
				}
				?>
				</div>
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