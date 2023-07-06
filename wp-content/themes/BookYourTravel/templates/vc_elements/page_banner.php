<?php
$background_id = $attr['background'];
$background_url = wp_get_attachment_image_src($background_id, 'full');
$steps = vc_param_group_parse_atts($attr['step_of_plan']);
?>
<style>
    .page-banner-wrap {
        background-image: url('<?= $background_url[0] ?>');
        min-height: 650px;
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
<div class="page-banner-wrap">
    <div class="page-banner-container">
        <h1 class="page-banner-title"><?= $attr['title'] ?></h1>
        <div class="plan-banner-wrap">
            <h3><?= $attr['title_plan']  ?></h3>
            <div class="step-plan-list">
                <?php
                foreach ($steps as $step) {
                    $step_icon_id = $step['step_icon'];
                    $step_icon_url = wp_get_attachment_image_src($step_icon_id, 'full');
                ?>
                    <div class="step-plan-item">
                        <div class="step-img">
                            <img src="<?php echo $step_icon_url[0] ?>" alt="">
                        </div>
                        <div class="step-plan-body">
                            <div class="step-plan-title">
                                <?= $step['step_title'] ?>
                            </div>
                            <div class="step-plan-content">
                                <?= $step['step_content'] ?>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
            <div class="step-plan-btn">
                <a href=""><?= $attr['text_btn'] ?></a>
            </div>
            <span style="text-align: center; display: block;">
                Free service. No credit card required
            </span>
        </div>
    </div>
</div>