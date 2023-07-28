<?php 
$img_id = $attr['icon'];
$img_url = wp_get_attachment_image_src($img_id, 'full');

?>
<section id="responsible_quote" class="section">
    <div class="responsible_quote nano-container">
        <div class="responsible-icon">
            <img src="<?= $img_url[0] ?>" alt="">
        </div>
        <div class="responsible-title">
            <h2><?= $attr['title'] ?></h2>
        </div>
        <div class="responsible-text">
        <?= $attr['text'] ?>
        </div>
    </div>
</section>