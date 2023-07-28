<?php
global  $bookyourtravel_theme_globals, $post, $first_display_tab, $default_tour_tabs, $entity_obj, $layout_class, $tab, $tour_item_args, $tour_list_args, $bookyourtravel_tour_helper;
global $bookyourtravel_theme_globals, $cruise_item_args, $cruise_list_args, $bookyourtravel_cruise_helper, $post, $cruise_type_obj, $cabin_obj;
$cruise_type_id = get_query_var('cruise_type_id');

?>

<section id="relate_tour" class="section">
    <div class="nano-container">
        <h2>You may also like...</h2>
        <?php
        // get term id by slug
       
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
        
        $posts_per_page = 3;
        $cruise_results = $bookyourtravel_cruise_helper->list_cruises($paged, $posts_per_page, $sort_by, $sort_order, array($parent_location_id), false, $cruise_type_ids, $cruise_duration_ids, $cruise_tag_ids, $cruise_facility_ids, array(), $show_featured_only, $author_id, $include_private);
        echo '<div class ="tour_grid-list" >';
        foreach ($cruise_results['results'] as $cruise_result) {
            global $post;
            $post = $cruise_result;
            setup_postdata($post);
            if (isset($post)) {
                $cruise_item_args['tour_id'] = $post->ID;
                $cruise_item_args['post'] = $post;
                get_template_part('includes/parts/cruise/single/cruise-grid');
            }
        }
        echo '</div>';
        ?>
    </div>
</section>