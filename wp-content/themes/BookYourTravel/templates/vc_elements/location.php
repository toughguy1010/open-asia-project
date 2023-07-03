<?php
$location_list = vc_param_group_parse_atts($attr['list_location']);

?>
<section id="location_wrap">
    <div class="location-header">
        <div class="location-title">
            <?= $attr['title'] ?>
        </div>
        <div class="location-description">
            <?= $attr['description'] ?>
        </div>
    </div>
    <div class="location-body">
        <?php
        foreach ($location_list as $location) {
            $id = $location['id'];
            $img_id =  $location['background_img'];
            $img_url = wp_get_attachment_image_src($img_id, 'full');
            $posts = get_posts(array(
                'post_type' => 'location',
                'post_parent' => 0,
                'post_status' => 'publish',
                'include' => array($id)
            ));
            foreach ($posts as $post) {
                setup_postdata($post);
                $location_title = $post->post_title;
                $location_permanlinhk = get_permalink($post->ID);
        ?>
                <div class="location-item">
                    <div>
                    <img src="<?= $img_url[0] ?>" alt="" class="location-img">
                        
                    </div>
                    <div class="location-content">
                        <div class="location-title-item">
                            <h2><?= $location_title ?></h2>
                        </div>
                        <div class="location-item-content">
                                <?= $location['content'] ?>    
                        </div>
                        <div class="location-btn">
                            <a href="<?= $location_permanlinhk ?>">Discover Now</a>

                        </div>
                    </div>
                </div>
        <?php
            }
            wp_reset_postdata();
        }
        ?>
        
    </div>
</section>