<?php 
$img = $attr['avatar_image'];
$img_url = wp_get_attachment_image_src($img, 'full');
?>

<div class="introduction-content">
    <div class="introduction-header">
        <img src="<?php echo $img_url[0] ?>" alt="">
    </div>
    <div class="introduction-body">
        <div class="introduction-title">
            <?= $attr['title'] ?>
        </div>
        <div class="introduction-description">
            <?= $attr['description'] ?>
        </div>
    </div>
</div>