<?php

$img = $attr['background_image'];
$img_url = wp_get_attachment_image_src($img, 'full');

?>
<style>
    #hero_wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        min-height: 650px;
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
        <div class="search_wrap">
            <input type="text" class="search-input" readonly>
            <div class="search-button">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0,0,256,256" width="50px" height="50px" fill-rule="nonzero">
                    <g fill="#ffffff" fill-rule="nonzero" stroke="none" stroke-width="1" stroke-linecap="butt" stroke-linejoin="miter" stroke-miterlimit="10" stroke-dasharray="" stroke-dashoffset="0" font-family="none" font-weight="none" font-size="none" text-anchor="none" style="mix-blend-mode: normal">
                        <g transform="scale(5.12,5.12)">
                            <path d="M21,3c-9.37891,0 -17,7.62109 -17,17c0,9.37891 7.62109,17 17,17c3.71094,0 7.14063,-1.19531 9.9375,-3.21875l13.15625,13.125l2.8125,-2.8125l-13,-13.03125c2.55469,-2.97656 4.09375,-6.83984 4.09375,-11.0625c0,-9.37891 -7.62109,-17 -17,-17zM21,5c8.29688,0 15,6.70313 15,15c0,8.29688 -6.70312,15 -15,15c-8.29687,0 -15,-6.70312 -15,-15c0,-8.29687 6.70313,-15 15,-15z"></path>
                        </g>
                    </g>
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
                            49, // ID của Active Adventure
                            52, // ID của Beach Escapes
                            53, // ID của Culture & Heritage
                            54, // ID của Family
                            55, // ID của Honeymoon
                            56, // ID của Luxury Travel
                            57  // ID của Off the Beaten Path
                        );                        
                        foreach ($tour_type_ids as $tour_type_id) {
                            $tour_type = get_term_by('id', $tour_type_id, 'tour_type');
                            if ($tour_type) {
                        ?>
                        <div class ="travel-style-col">
                             <a href="<?php echo $tour_type->slug ?>">
                                    <div class="travel-style-item">
                                        <?php echo  $tour_type->name ?>
                                    </div>
                                </a>
                        </div>
                               
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>