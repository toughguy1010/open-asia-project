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


				<div class="entry-content nano-container section">
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
					</div>
					<div class="right-entry-content">
						<div class="entry-destination">
							
							<div class="entry-destination-header">
								<p>Destination</p>
							</div>
							<div class="entry-destination-body">
								<?php
								$categories = get_categories();
								foreach ($categories as $category) {
									echo '<div class="col-md-4"><a href="' . get_category_link($category->term_id) . '">' . $category->name . '</a></div>';
								}
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
