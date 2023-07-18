
<?php 
$location_description = get_query_var('location_description');
$thumbnail_url = get_query_var('thumbnail_url');

?>
<style>
    #destination_banner{
        background-image: url('<?= $thumbnail_url  ?>');
        height: 435px;
        background-repeat: no-repeat;
        background-size:cover;
        background-position: center;
    }
</style>
<section id="destination_banner">
    <div class="destination_banner-wrap nano-container">
        <div class="destination_banner-content">
            <h2><?= the_title()  ?></h2>
            <p>
            <?= $location_description ?>
            </p>
        </div>
    </div>
</section>