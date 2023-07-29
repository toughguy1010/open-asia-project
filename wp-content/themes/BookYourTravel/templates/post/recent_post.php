<?php
$args = array(
    'post_type' => 'post',
    'orderby'    => 'ID',
    'post_status' => 'publish',
    'order'    => 'DESC',
    'posts_per_page' => 5 // this will retrive all the post that is published 
);
$result = new WP_Query($args);
if ($result->have_posts()) : ?>
    <div class="recent-post-wrap">
        <?php while ($result->have_posts()) : $result->the_post(); ?>
            <div class="recent-post-item">
                <div class="recent-post-header">
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('thumbnail'); // You can change 'thumbnail' to other image sizes like 'medium', 'large', or custom sizes 
                        ?>
                    <?php else : ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/css/images/no_image.jpg" alt="Default Image">
                    <?php endif; ?>
                </div>
                <div class="recent-post-body">
                    <div class="recent-post-title">
                        <a href="<?php echo get_permalink() ?>">
                            <?php the_title(); // Display the post title 
                            ?>
                        </a>
                    </div>
                    <div class="recent-post-category">
                        <?php
                        $categories = get_the_category(); // Get an array of categories for the post
                        if (!empty($categories)) {
                            echo esc_html($categories[0]->name); // Display the name of the first category
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif;
wp_reset_postdata(); ?>