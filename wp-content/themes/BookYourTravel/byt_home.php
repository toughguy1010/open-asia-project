<?php
/* Template Name: Byt Home page
 * The Front Page template file.
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $bookyourtravel_theme_globals, $post, $item_class;

$page_id = $post->ID;
$page_custom_fields = get_post_custom( $page_id);

$page_sidebar_positioning = BookYourTravel_Theme_Utils::get_page_sidebar_positioning($page_id);
$section_class = BookYourTravel_Theme_Utils::get_page_section_class($page_sidebar_positioning);
$item_class = BookYourTravel_Theme_Utils::get_item_class($section_class);

get_header();
get_template_part('byt', 'header');
get_sidebar('under-header');

$allowed_tags = array();
$allowed_tags['span'] = array('class' => array());
?>
		<div class="row">
			<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'left')
				get_sidebar('left');
			?>
			<section class="<?php echo esc_attr($section_class); ?>">
			<?php
			if (have_posts()) { ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<article <?php post_class(); ?> id="page-<?php the_ID(); ?>">
						<?php the_content( wp_kses(__( 'Continue reading <span class="meta-nav">&rarr;</span>', 'bookyourtravel' ), $allowed_tags) ); ?>
						<?php wp_link_pages('before=<div class="pagination">&after=</div>'); ?>
					</article>
				<?php endwhile;
			}
			get_sidebar('home-content');
			get_sidebar('home-footer');
			?>
			</section>
			<?php
			if ($page_sidebar_positioning == 'both' || $page_sidebar_positioning == 'right')
				get_sidebar('right');
		?>
		</div>
<?php
get_template_part('byt', 'footer');
get_footer();