<?php
$partners_images = vc_param_group_parse_atts($attr['partners_images']);
?>
<div class="partners-wrap">
    <div class="partners-list">
        <?php
        foreach ($partners_images as $item) {
            $img_id = $item['image'];
            $img_url = wp_get_attachment_image_src($img_id, 'full');
        ?>
            <div class="partner-item">
                <img src="<?= $img_url[0]  ?>" alt="">
            </div>
        <?php
        }
        ?>
    </div>
</div>