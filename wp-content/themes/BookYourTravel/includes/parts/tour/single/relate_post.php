<?php
$args = array(
    'post_type' => 'post',
    'posts_per_page' => 3,
    'post_status' => 'publish',
);

$query = new WP_Query($args);

?>


<section id="relate_post" class="section">
    <div class="relate_post-wrapper nano-container">
        <div class="top-relate_post">
            <div class="relate_post-header">
                Asia Travel Tips, Best Practices and Updates
            </div>
            <div class="relate_post-description">
                Check latest articles from our blog. All about News, Tips & Guides, Infographics
            </div>
        </div>
        <div class="body-relate_post">
            <div class="list-relate_post">
                <?php
                if ($query->have_posts()) {
                    while ($query->have_posts()) {
                        $query->the_post();
                ?>
                        <div class="item-relate_post">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink() ?>" class="relate_post-thumbnail">
                                    <?php the_post_thumbnail('thumbnail'); ?>
                                </a>
                            <?php endif; ?>
                            <div class="content-relate_post">
                                <div class="relate_post-date">
                                    <img src="<?= get_template_directory_uri() ?>/css/images/calendar-regular.svg" alt="">
                                    <span class="relate_post-date"><?php echo get_the_date(); ?></span>

                                </div>
                                <a  href="<?php the_permalink() ?>" class="relate_post-title"><?php the_title(); ?></a>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo 'Không có bài viết nào được tìm thấy';
                }
                ?>
            </div>
        </div>
    </div>
</section>