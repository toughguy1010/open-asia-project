<?php
$logo_element = vc_param_group_parse_atts($attr['logo_element']);


?>
<div class="client-slider-wrap">
    <div class="slider-element-list">
        <?php
        foreach ($logo_element as $element) {
            $icon_id = $element['icon'];
            $icon_url = wp_get_attachment_image_src($icon_id, 'full');
        ?>
            <a href="<?= $element['link_icon'] ?>" class="slider-element-item">
                <div class="slider-item-img">
                    <img src="<?= $icon_url[0] ?>" alt="">
                </div>
                <div class="slider-item-title">
                    <?= $element['title'] ?>
                </div>
            </a>
        <?php
        }
        ?>
    </div>

</div>