<?php

$about_us_content = vc_param_group_parse_atts($attr['about_us_content']);
?>

<div class="about_us_list aaa">
    <?php
    foreach ($about_us_content as $content) {
        $img_id = $content['iamge'];
        $img_url = wp_get_attachment_image_src($img_id, 'full');

        $position = isset($content['position']) ? $content['position'] : '';

        $about_us_item_class = 'about-us-item';
        if ($position == 'rtl') {
            $about_us_item_class .= ' rtl';
        }
    ?>
        <div class="<?php echo $about_us_item_class; ?>">
            <div class="about-thumbnail-item">
                <img src="<?php echo $img_url[0]; ?>" alt="">
            </div>
            <div class="about-content">
                <div class="about-content-title">
                    <?php echo $content['title']; ?>
                </div>
                <div class="about-content-body">
                    <?php echo $content['body']; ?>

                </div>
                <div class="view-btn">
                    View more
                </div>
            </div>

        </div>
    <?php
    }
    ?>
</div>