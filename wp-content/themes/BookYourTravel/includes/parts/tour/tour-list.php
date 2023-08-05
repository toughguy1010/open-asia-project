<?php

/**
 * Accommodation list template part
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage BookYourTravel
 * @since 1.0
 * @version 8.00
 */

global $bookyourtravel_theme_globals, $tour_item_args, $tour_list_args, $bookyourtravel_tour_helper;



// get term id by slug
if (isset($_GET['tour-type'])) {
    $tour_type_slug = $_GET['tour-type'];

    $tour_type = get_term_by('slug', $tour_type_slug, 'tour_type');

    if ($tour_type) {
        $tour_type_id = $tour_type->term_id;
    } else {
        echo 'Tour type không tồn tại.';
    }
}
$view ='';
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
$tour_type_ids =  $tour_type_id;


if (isset($_GET['view'])) {
    $view = $_GET['view'];
    if($view == 'grid'){
        ?> 
        <style>
            .tour_grid-list{
                display: flex;
                flex-wrap: wrap;
            }
        </style>
        <?php
    }
}

$sort_by = 'duration';
// lấy ra tất cả các tour
$tour_results = $bookyourtravel_tour_helper->list_tours($paged, -1, $sort_by, $sort_order, array($parent_location_id), false, $tour_type_ids, $tour_duration_ids, $tour_tag_ids, array(), $show_featured_only, $author_id, $include_private);
// số tour hiển thị trên 1 trang
$current_page = max(1, $paged); // Đảm bảo số trang ít nhất là 1
$posts_per_page = 3; // Cập nhật số tour hiển thị trên mỗi trang thành 3
if (isset($_GET['post_per_page'])) {
    $posts_per_page = $_GET['post_per_page'];
}
$start_index = ($current_page - 1) * $posts_per_page;
$end_index = $start_index + $posts_per_page;

// lấy ra từng duration của các tour hiện có và sắp xếp rồi cho vào 1 mảng mới
$newArrayTour=[];
foreach ($tour_results['results'] as $item){
    if (is_object($item)) {
        $item = (array) $item;
    }
 $duration =   wp_get_post_terms( $item['ID'], 'tour_duration', array( "fields" => "all" ) );
 if(!isset($item['duration'])){
    $item['duration']=$duration[0]->term_id??null;
 }
$newArrayTour[]=$item;
}
// sắp xếp mảng mới theo duration
$duration = array_column($newArrayTour, 'duration');

// Sắp xếp mảng $duration dựa trên mảng $newArrayTour
if (isset($_GET['orderby']) && ($_GET['orderby'] == 'DESC')) {
    array_multisort($duration, SORT_DESC, $newArrayTour);
} else {
    array_multisort($duration, SORT_ASC, $newArrayTour);
}

// gán mảng mới sắp xếp vào mảng tour
$tour_results['results'] =json_decode(json_encode( $newArrayTour));
// Lấy tập con của các tour để hiển thị trên trang hiện tại
$tour_results_subset = array_slice($tour_results['results'], $start_index, $posts_per_page);


$display_mode = strip_tags(isset($tour_list_args['display_mode']) ? $tour_list_args['display_mode'] : 'card');
$found_post_content = isset($tour_list_args["found_post_content"]) ? $tour_list_args["found_post_content"] : false;
if (count($tour_results) > 0 && $tour_results['total'] > 0) {

    if ($display_mode == 'card') {
        // echo '<div class="deals' . ($found_post_content ? ' found-post-content' : '') . '">';
        echo '<div class="row">';
    } else {
        echo '<ul class="small-list' . ($found_post_content ? ' found-post-content' : '') . '">';
    }

    if (!isset($tour_item_args) || !is_array($tour_item_args)) {
        $tour_item_args = array();
    }
?>
    <div class="tours-list-form">
        <form action="<?php echo home_url('/tours/'); ?>" method="GET" class="form-inline">
            <input type="hidden" name="tour-type" value="<?= $tour_type_slug ?>">
            <div class="form-row">
                <div class="left-form">
                    <div class="total-results">
                        <?= $tour_type->count ?> results found
                    </div>
                    <span class="sep"></span>
                    <div class="view-per-page">
                        View
                        <div class="select-dropdown">
                            <select name="post_per_page" id="postPerPageSelect">
                                <option value="3" <?php if (isset($_GET['post_per_page']) && $_GET['post_per_page'] === '3') echo 'selected'; ?>>3</option>
                                <option value="6" <?php if (isset($_GET['post_per_page']) && $_GET['post_per_page'] === '6') echo 'selected'; ?>>6</option>
                                <option value="9" <?php if (isset($_GET['post_per_page']) && $_GET['post_per_page'] === '9') echo 'selected'; ?>>9</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="right-form">
                    <div class="sortting-form">
                        <select name="orderby" id="orderby">
                            <option value="Default_sorting" <?php if (isset($_GET['orderby']) && $_GET['orderby'] === 'Default_sorting') echo 'selected'; ?>>
                                Default sorting
                            </option>
                            <option value="ASC" <?php if (isset($_GET['orderby']) && $_GET['orderby'] === 'ASC') echo 'selected'; ?>>By duration ASC</option>
                            <option value="DESC" <?php if (isset($_GET['orderby']) && $_GET['orderby'] === 'DESC') echo 'selected'; ?>>By duration DESC</option>
                        </select>
                    </div>
                    <span class="sep"></span>
                    <div class="view-mode">
                        <div class="mode-list" id="listMode" data-mode="list">
                            <img src="<?php echo get_template_directory_uri() ?>/css/images/list-solid.svg" alt="">
                        </div>
                        <div class="mode-list" id="gridMode" data-mode="grid">
                            <img src="<?php echo get_template_directory_uri() ?>/css/images/grip-vertical-solid.svg" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>

<?php

    $tour_item_args['hide_title'] = isset($tour_item_args['hide_title']) ? $tour_item_args['hide_title'] : false;
    $tour_item_args['hide_actions'] = isset($tour_item_args['hide_actions']) ? $tour_item_args['hide_actions'] : false;
    $tour_item_args['hide_image'] = isset($tour_item_args['hide_image']) ? $tour_item_args['hide_image'] : false;
    $tour_item_args['hide_description'] = isset($tour_item_args['hide_description']) ? $tour_item_args['hide_description'] : false;
    $tour_item_args['hide_address'] = isset($tour_item_args['hide_address']) ? $tour_item_args['hide_address'] : false;
    $tour_item_args['hide_rating'] = isset($tour_item_args['hide_rating']) ? $tour_item_args['hide_rating'] : false;
    $tour_item_args['hide_price'] = isset($tour_item_args['hide_price']) ? $tour_item_args['hide_price'] : false;
    $tour_item_args['tour_id'] = 0;

    $posts_per_row = isset($tour_list_args['posts_per_row']) ? (int) $tour_list_args['posts_per_row'] : 4;
    if (!isset($tour_item_args['item_class'])) {
        $tour_item_args['item_class'] = BookYourTravel_Theme_Utils::get_item_class_by_row_posts($posts_per_row);
    }
    echo '<div class ="tour_grid-list" >';
    foreach ($tour_results_subset as $tour_result) {
        global $post;
        $post = $tour_result;
        setup_postdata($post);
        if (isset($post)) {
            $tour_item_args['tour_id'] = $post->ID;
            $tour_item_args['post'] = $post;
            if ($view !== 'grid') {

                get_template_part('includes/parts/tour/tour', 'item-list');
            } else {

                get_template_part('includes/parts/tour/tour', 'item-grid');
            }
        }
    }
    echo '</div>';

    if ($display_mode == 'card') {
        echo '</><!--row-->';
        if (isset($tour_list_args['is_list_page']) && $tour_list_args['is_list_page']) {
            $total_results = $tour_results['total'];
            if ($total_results > $posts_per_page && $posts_per_page > 0) {
                BookYourTravel_Theme_Controls::the_pager_outer(ceil($total_results / $posts_per_page));
            }
        }
        // echo '</div><!--deals-->';
    } else {
        echo '</ul>';
    }
} else {
    echo '<p>' . esc_html__('Unfortunately no tours were found.', 'bookyourtravel') . '</p>';
}

wp_reset_postdata();


?>

<script>
    document.getElementById('postPerPageSelect').addEventListener('change', function() {
        updateUrl('test');
    });

    document.getElementById('orderby').addEventListener('change', function() {
        updateUrl('test');
    });
    document.getElementById('listMode').addEventListener('click', function() {
        updateUrl('list');
    });
    document.getElementById('gridMode').addEventListener('click', function() {
        updateUrl('grid');
    });

    function updateUrl(mode) {
        var selectedPostPerPage = document.getElementById('postPerPageSelect').value;
        var selectedOrderby = document.getElementById('orderby').value;
        var tourType = '<?php echo $tour_type_slug; ?>';
        var baseUrl = '<?php echo home_url('/tours/'); ?>';
        var urlParams = new URLSearchParams(window.location.search);

        var existingView = urlParams.get('view');

        if (selectedPostPerPage && selectedPostPerPage !== '3') {
            urlParams.set('post_per_page', selectedPostPerPage);
        } else {
            urlParams.delete('post_per_page');
        }

        if (selectedOrderby && selectedOrderby !== 'Default_sorting') {
            urlParams.set('orderby', selectedOrderby);
        } else {
            urlParams.delete('orderby');
        }

        urlParams.set('tour-type', tourType);

        if (mode === 'list') {
            urlParams.set('view', 'list');
        } else if (mode === 'grid') {
            urlParams.set('view', 'grid');
        } else if (existingView) {
            urlParams.set('view', existingView);
        }

        var newUrl = baseUrl + '?' + urlParams.toString();

        window.location.href = newUrl;
    }
</script>