<?php
/**
 * Review form template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $entity_obj, $bookyourtravel_review_helper, $bookyourtravel_theme_globals, $bookyourtravel_theme_of_custom, $section_class;

if (is_user_logged_in()) {
	$review_form_thank_you = $bookyourtravel_theme_globals->get_review_form_thank_you();
?>
<script>
	window.reviewFormLikesError = <?php echo json_encode(esc_html__('Please enter your likes', 'bookyourtravel')); ?>;
	window.reviewFormDislikesError = <?php echo json_encode(esc_html__('Please enter your dislikes', 'bookyourtravel')); ?>;
	window.reviewFormPostTitleString = <?php echo json_encode(esc_html__('We would like to know your opinion about %s', 'bookyourtravel')); ?>;
</script>
<?php do_action( 'bookyourtravel_show_review_form_before' ); ?>
<section class="full-width review-form-thank-you modal" style="display:none;">
	<div class="static-content">
		<a href="#" class="close-btn">x</a>
		<?php echo wp_kses($review_form_thank_you, BookYourTravel_Theme_Utils::get_allowed_content_tags_array()); ?>
	</div>
</section>
<section class="<?php echo esc_attr($section_class); ?> review-form-section review-section modal" style="display:none;">
	<article class="static-content">
		<a href="#" class="cancel-review right">x</a>
		<form id="review-form" method="post" action="<?php echo BookYourTravel_Theme_Utils::get_current_page_url(); ?>" class="review-form">
			<h2 class="post-title"><?php echo esc_html__('We would like to know your opinion about %s', 'bookyourtravel'); ?></h2>
			<div class="error error-summary" style="display:none;"><p></p></div>
			<p><?php esc_html_e('Please score the following:', 'bookyourtravel'); ?></p>
			<table class="review-fields">
				<thead>
					<tr>
						<th><?php esc_html_e('Element', 'bookyourtravel'); ?></th>
						<?php for ( $i = 1; $i <= 10; $i++ ) {
							echo '<th>' . $i . '</th>';
						} ?>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
			<h3 class="post-type-likes"><?php echo esc_html__('What did you like about the %s?', 'bookyourtravel'); ?></h3>
			<div class="row"><div class="f-item full-width">
				<textarea id="likes" name='likes' rows="10" cols="10" ></textarea>
			</div></div>
			<h3 class="post-type-dislikes"><?php echo esc_html__('What did you not like about the %s?', 'bookyourtravel'); ?></h3>
			<div class="row">
				<div class="f-item full-width">
					<textarea id="dislikes" name='dislikes' rows="10" cols="10" ></textarea>
				</div>
			</div>
			<?php if ($bookyourtravel_theme_globals->enable_gdpr()) { ?>
			<div class="row">
				<div class="f-item full-width">
					<?php
					$gdpr_field = $bookyourtravel_theme_of_custom->get_gdpr_checkbox_field();
					$gdpr_field["unique_id"] = "review_form_" . $gdpr_field["id"];
					?>
					<?php BookYourTravel_Theme_Admin_Controls::the_dynamic_field_checkbox_control($gdpr_field); ?>
					<label for="<?php echo esc_attr($gdpr_field['id']) ?>"><?php echo wp_kses($gdpr_field['label'], array("a" => array("href" => array(), "target" => array(),  "class" => array()))); ?></label>				
				</div>
			</div>
			<?php } ?>
			<?php BookYourTravel_Theme_Controls::the_link_button("#", "gradient-button cancel-review", "", esc_html__('Cancel', 'bookyourtravel')); ?>
			<?php BookYourTravel_Theme_Controls::the_submit_button("gradient-button submit-review", "submit-review", esc_html__('Submit review', 'bookyourtravel')); ?>
		</form>
	</article>
</section>
<!--// full-width content-->
<?php do_action( 'bookyourtravel_show_review_form_after' ); ?>
<?php
}