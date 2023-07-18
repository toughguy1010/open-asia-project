<?php
global $found_post_content, $bookyourtravel_theme_globals, $location_list_args, $location_item_args, $bookyourtravel_location_helper;
$location_id = get_query_var('location_id');

$posts_per_page    = isset($location_list_args['posts_per_page']) ? $location_list_args['posts_per_page'] : 12;
$paged               = isset($location_list_args['paged']) ? $location_list_args['paged'] : 1;
$sort_by           = isset($location_list_args['sort_by']) ? $location_list_args['sort_by'] : 'date';
$sort_order        = isset($location_list_args['sort_order']) ? $location_list_args['sort_order'] : 'ASC';
$exclude_descendant_locations = isset($location_list_args['exclude_descendant_locations']) ? $location_list_args['exclude_descendant_locations'] : false;
$parent_location_id  = $location_id;

$include_private = isset($location_list_args['include_private']) ? $location_list_args['include_private'] : false;
$show_featured_only = isset($location_list_args['show_featured_only']) ? $location_list_args['show_featured_only'] : false;
$location_type_ids = isset($location_list_args['location_type_ids']) ? $location_list_args['location_type_ids'] : array();
$location_tag_ids = isset($location_list_args['location_tag_ids']) ? $location_list_args['location_tag_ids'] : array();
$author_id = isset($location_list_args["author_id"]) ? $location_list_args["author_id"] : null;

$location_results = $bookyourtravel_location_helper->list_locations($parent_location_id, $paged, $posts_per_page, $sort_by, $sort_order, $show_featured_only, $location_type_ids, $location_tag_ids, $author_id, $exclude_descendant_locations, $include_private);
if ($location_results['results']) {
?>
    <section id="list-child-location" class="section">
        <h2><?= the_title() ?> Highlight</h2>
        <div class="child-location-wrapper nano-container ">

            <?php
            foreach ($location_results['results'] as $item) {
                $item_id =  $item->ID;
                $item_title =  $item->post_title;
                $item_link =  $item->post_title;
                $thumbnail_url = get_the_post_thumbnail_url($item_id, 'full');
                $permalink = get_permalink($item_id);
            ?>
                <div class="location-inner-wrap">
                    <div class="location-inner-front" style="background-image: url('<?php echo $thumbnail_url  ?>');">
                        <h3><?= $item_title ?></h3>
                    </div>
                    <div class="location-inner-back">
                        <h3><?= $item_title ?></h3>
                        <a href="<?php echo $permalink  ?>">View</a>
                    </div>
                </div>

            <?php
            }

            ?>
        </div>
    </section>
<?php
} else {
    echo 'No locations found.';
}
?>