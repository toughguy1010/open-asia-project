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
$id = $post->ID;
?>

<div class="row">
	<section class="<?php echo esc_attr($section_class); ?>">
		<?php if (have_posts()) while (have_posts()) : the_post();
			global $post; ?>
			<!--post-->
			<article id="post-<?php the_ID(); ?>" <?php post_class("static-content post"); ?>>
				<?php if (has_post_thumbnail()) { ?>
					<div class="entry-featured post-bg" style="background-image: url('<?= get_template_directory_uri() ?>/css/images/bg-travel-guide.jpg');">
						<div class="post-bg-text">
							<div class="post-bg-title">
								Asia Travel Guide
							</div>
							<div class="post-bg-description">
								Asia is our homeland. We'll show you Asia, better than anyone else!
							</div>
						</div>
					</div>
				<?php } ?>
				<?php
				?>
				<div class="entry-content nano-container single-post-wrap section">
					<div class="left-entry-content">
						<div class="post_breadcrumbs ">
							<?php
							BookYourTravel_Theme_Controls::the_breadcrumbs();
							?>
						</div>
						<h1>
							<?= the_title() ?>
						</h1>
						<div class="entry-content-body">
							<?php the_content(wp_kses(__('Continue reading <span class="meta-nav">&rarr;</span>', 'bookyourtravel'), array('span' =>  array('class' => array())))); ?>
						</div>
						<div class="explore-more-post">
							<h2 class="explore-more-heading">
								Explore more
							</h2>
							<?php
							$categories = get_the_category(get_the_ID());
							$category_ids = array();

							if ($categories) {
								foreach ($categories as $category) {
									$category_ids[] = $category->term_id;
								}
							}

							$args = array(
								'post_type' => 'post',
								'orderby'    => 'ID',
								'post_status' => 'publish',
								'order'    => 'DESC',
								'posts_per_page' => 6 ,
								'category__in' => $category_ids,
							);
							$result = new WP_Query($args);
							if ($result->have_posts()) : ?>
								<div class="wrap-post-list" style="width: unset;">
									<?php while ($result->have_posts()) : $result->the_post(); ?>
										<article class="post_item one-third">
											<div class="post_item_wrap"><a href="<?php echo get_permalink() ?>" title="<?php the_title() ?>">
													<figure>
														<?php if (has_post_thumbnail()) : ?>
															<?php the_post_thumbnail('thumbnail'); // You can change 'thumbnail' to other image sizes like 'medium', 'large', or custom sizes 
															?>
														<?php else : ?>
															<img src="<?php echo get_template_directory_uri(); ?>/css/images/no_image.jpg" alt="Default Image">
														<?php endif; ?>
													</figure>
												</a>
												<div class="details ">
													<div class="tags">
														<ul>
															<?php
															$post_tags = wp_get_post_tags(get_the_ID());
															if ($post_tags) {
																foreach ($post_tags as $tag) {
																	echo '<li><a href="' . get_tag_link($tag->term_id) . '" class="post_tags">' . esc_html($tag->name) . '</a></li>';
																}
															}
															?>
														</ul>
													</div>
													<div class="item-header">
														<h3><a href="<?php echo get_permalink() ?>" title="<?php the_title() ?>" class="post_title"><?php the_title() ?></a></h3>
													</div>
													<div class="description">
														<?php the_excerpt(); ?>
													</div>
												</div>
											</div>
										</article>
									<?php endwhile; ?>
								</div>
							<?php endif;
							?>
						</div>
					</div>
					<div class="right-entry-content">
						<div class="entry-destination">
							<div class="entry-destination-header">
								<p>Destinations</p>
							</div>
							<div class="entry-destination-body">
								<?php
								get_template_part('/templates/post/destination');
								?>
							</div>

						</div>
						<div class="entry-recent-post">
							<div class="entry-recent-post-header">
								<p>Recent post</p>
							</div>
							<div class="entry-recent-post-body">
								<?php
								get_template_part('/templates/post/recent_post');
								?>
							</div>

						</div>
					</div>
				</div>
			</article>
			<!--//post-->
			<?php comments_template('', true); ?>
		<?php endwhile; ?>
	</section>
	<!--//three-fourth content-->
</div>
<?php
get_template_part('byt', 'footer');
get_footer();
