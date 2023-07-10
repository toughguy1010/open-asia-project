<?php
$image_id = $attr['background'];
$image_url = wp_get_attachment_image_src($image_id, 'full');
?>
<style>
    .rotate_banner{
        background-image: url('<?=  $image_url[0] ?>');
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }
</style>
<div class="rotate_banner_wrap">
    <div class="rotate_banner">
        <h2><?= $attr['title_image'] ?></h2>
    </div>
    <div class="rotate_text_fields">
        <h2><?= $attr['title_text_fields'] ?></h2>
        <p><?= $attr['content_text_fields']  ?></p>
        <a href="<?php echo home_url() ?>/contact-us">Contact us</a>
    </div>
</div>