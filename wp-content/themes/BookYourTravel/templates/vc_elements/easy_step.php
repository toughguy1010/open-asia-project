<?php
$step = vc_param_group_parse_atts($attr['step']);
$easy_title = $attr['easy_title'];
$background_id = $attr['background_image'];
$background_url = wp_get_attachment_image_src($background_id, 'full');
?>
<style>
    .easy_steps_container {
        background-image: url('<?php echo $background_url[0] ?>');
        background-position: center;
        background-repeat: no-repeat;
    }
</style>
<section id="wrap_easy_steps">
    <div class="easy_steps_container">
        <h2 class="easy-title">
            <?= $easy_title ?>
        </h2>
        <div class="step-list">
            <?php
            foreach ($step as $item) {
                $attachment_id = $item['thumbnail_step'];
                $img = wp_get_attachment_image_src($attachment_id);
            ?>
                <div class="step-item">
                    <div class="wrap-icon-guide">
                        <img src="<?= $img[0] ?>" alt="" class="step-icon">

                    </div>
                    <div class="step-text">
                        <div class="step-title">
                            <?php echo $item['step_tile'] ?>
                        </div>
                        <div class="step-content">
                            <?php echo $item['step_content'] ?>
                        </div>
                    </div>
                </div>

            <?php
            }
            ?>
        </div>
    </div>

</section>