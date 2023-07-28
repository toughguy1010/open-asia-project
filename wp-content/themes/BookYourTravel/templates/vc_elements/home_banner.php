<?php

$img = $attr['background_image'];
$img_url = wp_get_attachment_image_src($img, 'full');
$disable_search = $attr['disable_search'];

?>
<style>
    #hero_wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        min-height: 555px;
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        background-image: url('<?php echo $img_url[0] ?>');
    }
</style>

<section id="hero_wrapper">
    <div class="hero_content">
        <h1 class="search-title">
            <?= $attr['search_title'] ?>
        </h1>
        <p class="search_span"><?= $attr['search_span'] ?></p>
        <?php if (!$disable_search) {
        ?>
            <div class="search_wrap">
                <input type="text" class="search-input" placeholder="Where to or what trip styles? " readonly>
                <div class="search-button">


                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.4.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                        <path d="M502.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-128-128c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L402.7 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l370.7 0-73.4 73.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l128-128z"  fill="#ffffff" />
                    </svg>
                </div>
                <div class="search-value">
                    <span class="icon-caret"></span>
                    <div class="destination">
                        <div class="destination-title">
                            Destination
                        </div>
                        <div class="destination-list">
                            <?php
                            $posts = get_posts(array(
                                'post_type' => 'location',
                                'post_parent' => 0,
                                'post_status' => 'publish',

                            ));
                            foreach ($posts as $post) {
                                setup_postdata($post);
                                $location_title = $post->post_title;
                                $location_permanlinhk = get_permalink($post->ID);
                            ?>
                                <div class="destination-item">
                                    <a href="<?php echo $location_permanlinhk ?>"><?php echo  $location_title ?></a>
                                </div>
                            <?php
                            }
                            wp_reset_postdata();

                            ?>
                        </div>
                    </div>
                    <div class="travel-style">
                        <div class="travel-style-title">
                            Travel style
                        </div>

                        <div class="travel-style-list">
                            <?php
                            $tour_type_ids = array(
                                81, // ID của Active Adventure
                                85, // ID của Beach Escapes
                                86, // ID của Culture & Heritage
                                84, // ID của Family
                                83, // ID của Honeymoon
                            );
                            foreach ($tour_type_ids as $tour_type_id) {
                                $tour_type = get_term_by('id', $tour_type_id, 'tour_type');
                                if ($tour_type) {
                            ?>
                                    <a href="<?= home_url() ?>/tour/?tour-type=<?php echo $tour_type->slug ?>">
                                        <div class="travel-style-item">
                                            <?php echo  $tour_type->name ?>
                                        </div>
                                    </a>
                            <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        } ?>

    </div>

</section>