<?php
$image_id = $attr['behind_image'];
$image_url = wp_get_attachment_image_src($image_id, 'full');
global $bookyourtravel_theme_globals;
$logo_title = get_bloginfo('name') . ' | ' . (is_home() || is_front_page() ? get_bloginfo('description') : wp_title('', false));
?>
<div class="contact-banner-wrap">
    <div class="text_contact">
        <div class="logo">
            <a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr($logo_title); ?>">
                <div class="logo-container">
                    <h2 class="text-logo">open</h2>
                    <h2 class="text-logo">asia travel</h2>
                </div>
            </a>
        </div>
        <div class="text_contact-caption">
            <?= $attr['title'] ?>
        </div>
        <div class="text_contact-information">
            <p class="contact_information-item">
                <strong>Tell:</strong> <?= $attr['phone'] ?>
            </p>
            <p class="contact_information-item">
                <strong>Email:</strong> <?= $attr['email'] ?>
            </p>
            <p class="contact_information-item">
                <strong>Address: </strong> <?= $attr['address'] ?>
            </p>
        </div>
    </div>
    <div class="contact_banner">
        <img src="<?= $image_url[0] ?>" alt="">
    </div>
</div>