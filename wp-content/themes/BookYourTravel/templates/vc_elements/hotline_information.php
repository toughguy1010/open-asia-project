<div class="hotline-infomation-wrap">
    <div class="hotline-title">
        Don't like filling our the form?
    </div>
    <div class="hotline-number-wrap">
        <div class="hotline-number-title">
            <img src="<?php echo get_template_directory_uri() ?>/css/images/icon-headset-white.png" alt="">
            <p>Contact us, <strong><?= $attr['name'] ?></strong> </p>
        </div>
        <div class="hotline-number">
            <?=  $attr['phone_number']  ?>
        </div>
    </div>
    <div class="hotline-email-wrap">
        <div class="hotline-email-title">
            <img src="<?php echo get_template_directory_uri() ?>/css/images/icon-envelope-white.png" alt="">
            <p>Email us</p>
        </div>
        <div class="hotline-email">
            <?=  $attr['email']  ?>
        </div>
    </div>
    <div class="woking-time">
        (<?php echo $attr['working_time'] ?>)
    </div>
</div>