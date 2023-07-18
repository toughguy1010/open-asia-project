<?php
global  $bookyourtravel_theme_globals, $post, $first_display_tab, $default_tour_tabs, $entity_obj, $layout_class, $tab, $tour_item_args, $tour_list_args, $bookyourtravel_tour_helper;
$location_id = get_query_var('location_id');
?>

<section id="relate_tour" class="section">
    <div class="nano-container">
        <h2>You may also like...</h2>
        <?php
        // get term id by slug
       
        $posts_per_page = isset($tour_list_args['posts_per_page']) ? $tour_list_args['posts_per_page'] : 12;
        $paged = isset($tour_list_args['paged']) ? $tour_list_args['paged'] : 1;
        $sort_by = isset($tour_list_args['sort_by']) ? $tour_list_args['sort_by'] : 'title';
        $sort_order = isset($tour_list_args['sort_order']) ? $tour_list_args['sort_order'] : 'ASC';
        $parent_location_id = isset($tour_list_args['parent_location_id']) ? $tour_list_args['parent_location_id'] : 0;

        $include_private = isset($tour_list_args['include_private']) ? $tour_list_args['include_private'] : false;
        $show_featured_only = isset($tour_list_args['show_featured_only']) ? $tour_list_args['show_featured_only'] : false;
        $tour_tag_ids = isset($tour_list_args['tour_tag_ids']) ? $tour_list_args['tour_tag_ids'] : array();
        $tour_type_ids = isset($tour_list_args['tour_type_ids']) ? $tour_list_args['tour_type_ids'] : array();
        $tour_duration_ids = isset($tour_list_args['tour_duration_ids']) ? $tour_list_args['tour_duration_ids'] : array();
        $author_id = isset($tour_list_args["author_id"]) ? $tour_list_args["author_id"] : null;
        $parent_location_id = $location_id;
        $posts_per_page = 3;
        $tour_results = $bookyourtravel_tour_helper->list_tours($paged, $posts_per_page, $sort_by, $sort_order, array($parent_location_id), false, $tour_type_ids, $tour_duration_ids, $tour_tag_ids, array(), $show_featured_only, $author_id, $include_private);
        echo '<div class ="tour_grid-list" >';
        foreach ($tour_results['results'] as $tour_result) {
            global $post;
            $post = $tour_result;
            setup_postdata($post);
            if (isset($post)) {
                $tour_item_args['tour_id'] = $post->ID;
                $tour_item_args['post'] = $post;
                get_template_part('includes/parts/tour/tour', 'item-grid');
            }
        }
        echo '</div>';
        ?>
    </div>
</section>