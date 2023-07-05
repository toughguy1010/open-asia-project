<?php
$members = vc_param_group_parse_atts($attr['member']);

?>

<div class="passionate_team-wrapper">
    <div class="passionate_team-title">
        <?= $attr['title'] ?>
    </div>
    <div class="passionate_team-description">
        <?= $attr['description'] ?>
    </div>
    <div class="passionate_team-member">
        <?php
            foreach($members as $member){
                $img_id = $member['avatar'];
                $img_url = wp_get_attachment_image_src($img_id, 'full');
                ?> 
                <div class="passionate_team-member-item">
                    <div class="member-item-avatar">
                        <img src="<?= $img_url[0] ?>" alt="">
                    </div>
                    <div class="member-item-name">
                        <?= $member['name'] ?>
                    </div>
                    <div class="member-item-position">
                        <?= $member['position'] ?>
                    </div>
                </div>
                <?php
            }
        ?>
    </div>
</div>