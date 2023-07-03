<?php
/**
 * The template for displaying single posts
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

global $post;


$page_sidebar_positioning = BookYourTravel_Theme_Utils::get_page_sidebar_positioning($post->ID);
$section_class = BookYourTravel_Theme_Utils::get_page_section_class($page_sidebar_positioning);
?>
		<div class="row">
		<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
				get_sidebar('left');
		?>
			<section class="<?php echo esc_attr($section_class); ?>">
			<?php if ( have_posts() ) while ( have_posts() ) : the_post(); global $post; ?>
				<!--post-->
				<article id="post-<?php the_ID(); ?>" <?php post_class("static-content post"); ?>>
					<?php if ( has_post_thumbnail() ) { ?>
					<div class="entry-featured">
						<figure>
							<?php
							$thumbnail_id = get_post_thumbnail_id($post->ID);
							$attachment = get_post($thumbnail_id);
							if ($attachment) {
								$image_title = $attachment->post_title; //The Title
								the_post_thumbnail('byt-featured', array('title' => $image_title));
							}
							?>
						</figure>
					</div>
					<?php } ?>
					<?php
					?>
					<header class="entry-header">
						<h1 class="entry-title"><?php the_title(); ?></h1>
						<p class="entry-meta">
							<span class="date"><?php esc_html_e('Date', 'bookyourtravel');?>: <?php the_time(get_option('date_format')); ?></span>
							<span class="author"><?php esc_html_e('By ', 'bookyourtravel'); the_author_posts_link(); ?></span>
							<span class="categories"><?php esc_html_e('Categories', 'bookyourtravel'); ?>: <?php the_category(' ') ?></span>
							<span class="tags"><?php the_tags(); ?></span>
							<span class="comments">
								<a href="<?php esc_url(get_comments_link()); ?>" rel="nofollow">
									<?php comments_number(esc_html__('No comments', 'bookyourtravel'), esc_html__('1 Comment', 'bookyourtravel'), esc_html__('% Comments', 'bookyourtravel')); ?>
								</a>
							</span>
						</p>
					</header>

					<div class="entry-content">
						<?php the_content( wp_kses(__( 'Continue reading <span class="meta-nav">&rarr;</span>', 'bookyourtravel' ), array('span' =>  array('class' => array()))) ); ?>
						<?php wp_link_pages('before=<div class="pagination">&after=</div>'); ?>
					</div>
				</article>
				<!--//post-->
				<?php comments_template( '', true ); ?>
				<?php endwhile; ?>
			</section>
			<!--//three-fourth content-->
			<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right')
				get_sidebar('right');
			?>
		</div>
<?php 
get_template_part('byt', 'footer');
get_footer();