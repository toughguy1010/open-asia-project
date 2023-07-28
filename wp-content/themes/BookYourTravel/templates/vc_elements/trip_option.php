<?php
$img_id = $attr['background'];
$img_url = wp_get_attachment_image_src($img_id, 'full');
if (!$img_url) {
    $img_url[0] = get_template_directory_uri() . '/css/images/plan-your-trip-bg.jpg';
}
$attr['text_btn'] = $attr['text_btn'] ?? "PLAN YOUR TRIP NOW";
$attr['title'] = $attr['title'] ?? "Personalised Asia Tours to the highest standards, especially for you!";
$attr['link_btn'] = $attr['link_btn'] ?? home_url() . '/tailor-made-your-tour/';
$attr['description'] = $attr['description'] ?? "Free service. No credit card required";
?>
<style>
    .trip-option-wrapper {
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
        <div class="trip-option-description">
            <p ><?= $attr['description'] ?></p>
        </div>
    </div>
</div>