<?php
$img_id = $attr['background'];
$img_url = wp_get_attachment_image_src($img_id, 'full');
?>
<style>
.trip-option-wrapper{
    background-image: url(' <?php echo $img_url[0] ?>');
    width: 100%;
    height: 350px;
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
    justify-content: center;
    align-items: center;
    display: flex;
}
</style>
<div class="trip-option-wrapper">
    <div class="trip-content">
        <div class="trip-option-title">
            <?= $attr['title'] ?>
        </div>
        <div class="trip-option-btn">
            <a href="<?= $attr['link_btn'] ?>"><?= $attr['text_btn'] ?></a>
        </div>
    </div>
</div>