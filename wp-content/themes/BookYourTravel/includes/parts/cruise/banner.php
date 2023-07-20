<?php

global $bookyourtravel_theme_globals, $bookyourtravel_theme_post_types;
$cruise_type_ids = get_query_var('cruise_type_ids');


$thumbnail_id = $bookyourtravel_theme_post_types->get_taxonomy_image_id($cruise_type_ids);
$thumbnail_url = wp_get_attachment_image_src($thumbnail_id, "full");
$title =  get_term($cruise_type_ids)->name;
isset($thumbnail_url) ? $background_url = $thumbnail_url[0] : $background_url = "";
?>

<style>
#cruise_type-banner{
    background-image: url("<?=  $background_url ?>");
    min-height: 650px;
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    display: flex;
    justify-content: flex-end;
    align-items: center;
}
</style>
<section id="cruise_type-banner">
    <div class="cruise_type-wrapper">
        <div class="cruise_type-title">
            <?= $title ?>
        </div>
        <div class="cruise_type-option">
            <a href="#our_option">See Our Options</a>
        </div>
    </div>
</section>