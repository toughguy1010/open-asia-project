<?php
$cruise_type_id = get_query_var('cruise_type_ids');
$port_ids = get_query_var('ports');
$title =  get_term($cruise_type_id)->name;
?>
<section id="our_cruise" class="">
	<div class="our_cruise-wrapper nano-container">
		<div class="our_cruise-header">
			<img src="<?= get_template_directory_uri() ?>/css/images/line.png" alt="">
			<div class="our_cruise-title">
				Our <?= $title  ?>
			</div>
		</div>
		<div class="our_cruise-list">

			<?php
			global $bookyourtravel_theme_globals, $cruise_item_args, $cruise_list_args, $bookyourtravel_cruise_helper, $post, $cruise_type_obj, $cabin_obj;

			$posts_per_page    = isset($cruise_list_args['posts_per_page']) ? $cruise_list_args['posts_per_page'] : -1;
			$paged			   = isset($cruise_list_args['paged']) ? $cruise_list_args['paged'] : 1;
			$sort_by           = isset($cruise_list_args['sort_by']) ? $cruise_list_args['sort_by'] : 'title';
			$sort_order        = isset($cruise_list_args['sort_order']) ? $cruise_list_args['sort_order'] : 'ASC';
			$parent_location_id        = isset($cruise_list_args['parent_location_id']) ? $cruise_list_args['parent_location_id'] : 0;
			$include_private = isset($cruise_list_args['include_private']) ? $cruise_list_args['include_private'] : false;
			$show_featured_only = isset($cruise_list_args['show_featured_only']) ? $cruise_list_args['show_featured_only'] : false;
			$cruise_tag_ids = isset($cruise_list_args['cruise_tag_ids']) ? $cruise_list_args['cruise_tag_ids'] : array();
			$cruise_type_ids = isset($cruise_list_args['cruise_type_ids']) ? $cruise_list_args['cruise_type_ids'] : array();
			$cruise_duration_ids = isset($cruise_list_args['cruise_duration_ids']) ? $cruise_list_args['cruise_duration_ids'] : array();
			$cruise_facility_ids = isset($cruise_list_args['cruise_facility_ids']) ? $cruise_list_args['cruise_facility_ids'] : array();
			$author_id = isset($cruise_list_args["author_id"]) ? $cruise_list_args["author_id"] : null;

			$cruise_type_ids = $cruise_type_id;

			$cruise_results = $bookyourtravel_cruise_helper->list_cruises($paged, $posts_per_page, $sort_by, $sort_order, array($parent_location_id), false, $cruise_type_ids, $cruise_duration_ids, $cruise_tag_ids, $cruise_facility_ids, array(), $show_featured_only, $author_id, $include_private);

			$display_mode = isset($cruise_list_args['display_mode']) ? $cruise_list_args['display_mode'] : 'card';

			$found_post_content = isset($cruise_list_args["found_post_content"]) ? $cruise_list_args["found_post_content"] : false;

			if (count($cruise_results) > 0 && $cruise_results['total'] > 0) {

				if ($display_mode == 'card') {
					echo '<div class="deals' . ($found_post_content ? ' found-post-content' : '') . '">';
					echo '<div class="row">';
				} else {
					echo '<ul class="small-list' . ($found_post_content ? ' found-post-content' : '') . '">';
				}
				if (!isset($cruise_item_args) || !is_array($cruise_item_args)) {
					$cruise_item_args = array();
				}

				$posts_per_row = isset($cruise_list_args['posts_per_row']) ? (int)$cruise_list_args['posts_per_row'] : 4;
				if (!isset($cruise_item_args['item_class']))
					$cruise_item_args['item_class'] = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);
				foreach ($cruise_results['results'] as $cruise_result) {
					global $post;
					$post = $cruise_result;
					setup_postdata($post);
					$cruise_item_args['cruise_id'] = $post->ID;
					$cruise_item_args['post'] = $post;
					set_query_var('cruise_type_ids', $cruise_type_ids);
					get_template_part('includes/parts/cruise/cruise', 'item');
				}

				if ($display_mode == 'card') {
					echo '</div><!--row-->';
					if (isset($cruise_list_args['is_list_page']) && $cruise_list_args['is_list_page']) {
						$total_results = $cruise_results['total'];
						if ($total_results > $posts_per_page && $posts_per_page > 0) {
							BookYourTravel_Theme_Controls::the_pager_outer(ceil($total_results / $posts_per_page));
						}
					}
					echo '</div><!--deals-->';
				} else {
					echo '</ul>';
				}
			} else {
				echo '<p>' . esc_html__('Unfortunately no cruises were found.', 'bookyourtravel') . '</p>';
			}

			wp_reset_postdata();


			?>
		</div>
	</div>
</section>