<?php

$visa_location = vc_param_group_parse_atts($attr['visa_location']);
// var_dump($visa_location);

?>

<div class="visa-content-wrap">
    <p class="visa-content-description">
        <?= $attr['description'] ?>
    </p>
    <div class="visa-content-list">
        <?php

        foreach ($visa_location as $item) {
            $thumbnail_id = $item['thumbnail'];
            $thumbnail_url = wp_get_attachment_image_src($thumbnail_id, 'full');
        ?>
            <div class="visa-content-item">
                <div class="visa-item-thumb">
                    <img src="<?= $thumbnail_url[0] ?>" alt="">
                </div>
                <div class="visa-item-content">
                    <div class="visa-item-title">
                        <?= $item['title'] ?>
                    </div>
                    <div class="visa-item-caption">
                        <script>
                            var textContent = <?= json_encode($item['text_content']); ?>;
                            var formattedContent = textContent.replace(/\n/g, '<br>');
                            document.write(formattedContent);
                        </script>
                    </div>
                </div>
            </div>
        <?php
        }

        ?>
    </div>
</div>