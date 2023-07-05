<?php
$certification_img = vc_param_group_parse_atts($attr['list_certification_image']);
// var_dump($certification_img);
$logo_id = $attr['logo'];
$logo = wp_get_attachment_image_src($logo_id, "full");
$brand_id = $attr['brand_image'];
$brand = wp_get_attachment_image_src($brand_id, "full");
$list_traveler_review =  vc_param_group_parse_atts($attr['list_traveler_review']);
?>
<div class="certification-wrap">
    <div class="certification-top">
        <div class="list-certification-img">
            <?php
            foreach ($certification_img as $item) {
                $attachment_id = $item['image'];
                $img_item = wp_get_attachment_image_src($attachment_id);
            ?>
                <a href="#">
                    <img src="<?= $img_item[0] ?>" alt="">
                </a>
            <?php
            }
            ?>
        </div>
        <div class="certification-logo">
            <img src="<?= $logo[0] ?>" alt="">
        </div>
        <div class="certification-description">
            <?= $attr['description'] ?>
        </div>
        <div class="certification-content">
            <?= $attr['span'] ?>
        </div>
    </div>
    <div class="certification-bottom">
        <div class="certification-brand">
            <img src="<?= $brand[0] ?>" alt="">
        </div>
        <div class="certification-title">
            Open Asia Travel
        </div>
        <div class="certification-ratting">
            <div class="ratting-title">
                <?= $attr['rating_title']  ?>
            </div>
            <div class="ratting-number-wrap">
                <div class="ratting-star">
                    <i class="star"></i>
                    <i class="star"></i>
                    <i class="star"></i>
                    <i class="star"></i>
                    <i class="star"></i>
                </div>
                <div class="ratting-number">
                    <?= $attr['rating_number'] ?>
                </div>
            </div>
        </div>
        <div class="certification-ranking">
            <div class="ranking-title">
                <?= $attr['ranking_title']  ?>
            </div>
            <div class="ranking">
                # <?= $attr['ranking']  ?>
            </div>
        </div>
        <div class="certification-reviews-wrap">
            <div class="certification-reviews-title">
                Recent Traveler Reviews
            </div>
            <div class="certification-reviews-list">
                <?php
                foreach ($list_traveler_review as $reivew) {
                ?>
                    <div class="certification-reviews-item">
                        <?= $reivew['traveler_reviews'] ?>
                    </div>
                <?php
                }
                ?>
            </div>
            <div class="certification-reviews-action">
                <div class="certification-reviews-read">
                    Read review
                </div>
                <div class="certification-reviews-write">
                    Write review
                </div>
            </div>
        </div>
    </div>
</div>