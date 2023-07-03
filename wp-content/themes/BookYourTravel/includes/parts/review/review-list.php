<?php
	global $bookyourtravel_review_helper, $current_user, $bookyourtravel_theme_of_custom;

	$paged = 1;
	if ( get_query_var('paged-byt') ) {
		$paged = get_query_var('paged-byt');
	} else if ( get_query_var('paged') ) {
		$paged = get_query_var('paged');
	} else if ( get_query_var('page') ) {
		$paged = get_query_var('page');
	}
	
	$posts_per_page = get_option('posts_per_page');	
	
	$user_review_results = $bookyourtravel_review_helper->list_user_reviews($current_user->ID, $paged, $posts_per_page);
	
	if ( count($user_review_results) > 0 && $user_review_results['total'] > 0 ) {

		foreach ($user_review_results['results'] as $user_review_result) {
			global $post;
			$post = $user_review_result;
			setup_postdata( $post );
			
			$review = $post;
			$review_id = $review->ID;
			$review_custom_fields = get_post_custom($review_id);
			$reviewed_post_id = 0;

			if (isset($review_custom_fields['review_post_id'])) {
				$reviewed_post_id = $review_custom_fields['review_post_id'][0];
				
				if ($reviewed_post_id > 0) {
					$reviewed_item = get_post($reviewed_post_id);
					$reviewed_post_id = BookYourTravel_Theme_Utils::get_default_language_post_id($reviewed_post_id, $reviewed_item->post_type );
					$reviews_total = $bookyourtravel_review_helper->get_reviews_count($reviewed_post_id);
					
					$review_fields = $bookyourtravel_review_helper->list_review_fields($reviewed_item->post_type);			
					$total_review_fields = count($review_fields);						

					$reviews_score = 0;
					$reviews_possible_score = 10 * $total_review_fields;
					$reviews_score = $bookyourtravel_review_helper->sum_user_review_meta_values($review_id, $current_user->ID, $reviewed_item->post_type);
					$score_out_of_10 = 0;
					if ($reviews_possible_score > 0) {
						$score_out_of_10 = round(($reviews_score / $reviews_possible_score) * 10);
					}

					$likes = $review_custom_fields['review_likes'][0];
					$dislikes = $review_custom_fields['review_dislikes'][0]; 
					
					$context_option_id = '';
					
					if ($reviewed_item->post_type == 'accommodation') {
						$review_item_title = esc_html__('Accommodation review scores and score breakdown', 'bookyourtravel');
						$context_option_id = 'accommodation_review_fields';
					} else if ($reviewed_item->post_type == 'tour') {
						$review_item_title = esc_html__('Tour review scores and score breakdown', 'bookyourtravel');
						$context_option_id = 'tour_review_fields';
					} else if ($reviewed_item->post_type == 'cruise') {
						$review_item_title = esc_html__('Cruise review scores and score breakdown', 'bookyourtravel');	
						$context_option_id = 'cruise_review_fields';
					} else if ($reviewed_item->post_type == 'car_rental') {
						$review_item_title = esc_html__('Car rental review scores and score breakdown', 'bookyourtravel');
						$context_option_id = 'car_rental_review_fields';
					}			
					
					?>				

					<article class="myreviews static-content">	
						<p><strong><?php echo sprintf(esc_html__('Your review of %s %s', 'bookyourtravel'), str_replace("_", " ", $reviewed_item->post_type), $reviewed_item ? $reviewed_item->post_title : ''); ?></strong></p>
						<div class="score">
							<span class="achieved"><?php echo esc_html($score_out_of_10); ?></span>
							<span> / 10</span>
						</div>
						<dl class="chart">
							<?php 
							$total_possible = $reviews_total * 10;	
							
							$review_fields = $bookyourtravel_review_helper->list_review_fields($reviewed_item->post_type, true);
							foreach ($review_fields as $review_field) {
								$field_id = $review_field['id'];
								$field_value = round($total_possible > 0 ? ($bookyourtravel_review_helper->sum_review_meta_values($reviewed_post_id, $field_id) / $total_possible) * 10 : 0);
								
								$field_label = isset($review_field['label']) ? $review_field['label'] : '';
								$field_label = $bookyourtravel_theme_of_custom->get_translated_dynamic_string($bookyourtravel_theme_of_custom->get_option_id_context($context_option_id) . ' ' . $field_label, $field_label);			
							?>
							<dt><?php echo esc_html($field_label); ?></dt>
							<dd><span style="width:<?php echo esc_html($field_value * 10); ?>%;"><?php echo esc_html($field_value); ?>&nbsp;&nbsp;&nbsp;</span></dd>
							<?php
							}
							?>
						</dl>
						<?php if (!empty($likes) || !empty($dislikes)) { ?>
						<div class="reviews">
							<?php if (!empty($likes)) { ?>
							<div class="rev pro"><p><?php echo esc_html($likes); ?></p></div>
							<?php } ?>
							<?php if (!empty($dislikes)) { ?>
							<div class="rev con"><p><?php echo esc_html($dislikes); ?></p></div>
							<?php } ?>							
						</div>
						<?php } ?>
					</article>						
<?php			
				}
			}
		} // foreach

		$total_results = $user_review_results['total'];
		if ($total_results > $posts_per_page && $posts_per_page > 0) {
			BookYourTravel_Theme_Controls::the_pager_outer(ceil($total_results/$posts_per_page));
		}

	}