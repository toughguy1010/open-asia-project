<?php

$list_member = vc_param_group_parse_atts($attr['list_member']);
$list_quote = vc_param_group_parse_atts($attr['list_quote']);
$quote_content =$attr['quote_content'];
// var_dump($list_member);
?>

<section id="quote_wrapper">
    <div class="list-member-avatar">
        <?php
        foreach ($list_member as $member) {
            $attachment_id = $member['member_avatar']; // Giả sử 'image' là khóa chứa ID của hình ảnh thành viên
            $img = wp_get_attachment_image_src($attachment_id,'full');
            // var_dump($img);
        ?>
            <div class="member-item">
                <img src="<?= $img[0] ?>" alt="">
            </div>
        <?php
        }
        ?>
    </div>
    <div class="quote-content">
        <?= $quote_content ?>
    </div>
    <div class="list-quote ">
        <?php
        foreach($list_quote as $quote){
            $quote_img = $quote['thumbnail_quote_item'];
            $img_url = wp_get_attachment_image_src($quote_img);
            ?>
            <div class="quote-item">
                    <img class="quote-img" src="<?= $img_url[0] ?>" alt="">
                <div class="quote-item-content">
                    <div class="quote-title">
                        <?= $quote['title_quote_item'] ?>
                    </div>
                    <div class="quote-desc">
                        <?= $quote['content_quote_item' ]?>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>

</section>
<script>
    
</script>