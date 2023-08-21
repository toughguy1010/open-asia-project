<?php
global $bookyourtravel_theme_globals, $post, $first_display_tab, $default_tour_tabs, $entity_obj, $layout_class, $tab;

$tour_obj = new BookYourTravel_Tour($post);
$entity_obj = $tour_obj;
$tour_description = wpautop($tour_obj->get_short_description());

if ($customizable_itinerary = get_field('customizable_itinerary', get_the_ID())) {
?>
    <section id="customizable_itinerary" class="section">
        <div class="customizable_itinerary-wrap  nano-container">
            <div class="customizable_itinerary-left">
                <div class="customizable_itinerary-header">
                    <div class="customizable_itinerary-title">
                        <h1>
                            Customizable itinerary
                        </h1>
                        <div class="toggle-conetent toggle-desktop">
                            <div data-state = "all" class="toggle-text">
                                Show all info
                            </div>
                            <div class="toggle-icon ">
                                <div class="circle-plus closed show-all-items">
                                    <div class="circle">
                                        <div class="horizontal"></div>
                                        <div class="vertical"></div>
                                    </div>
                                </div>
                                <div class="circle-plus-two closed show-all-items">
                                    <div class="circle">
                                        <div class="horizontal"></div>
                                        <div class="vertical"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="customizable_itinerary-description">
                        <?php echo $tour_description  ?>
                    </div>
                    <div class="toggle-conetent toggle-mobile ">
                            <div data-state = "all" class="toggle-text">
                                Show all info
                            </div>
                            <div class="toggle-icon ">
                                <div class="circle-plus closed show-all-items">
                                    <div class="circle">
                                        <div class="horizontal"></div>
                                        <div class="vertical"></div>
                                    </div>
                                </div>
                                <div class="circle-plus-two closed show-all-items">
                                    <div class="circle">
                                        <div class="horizontal"></div>
                                        <div class="vertical"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                </div>
                <div class="customizable_itinerary-body">
                    <div class="customizable_itinerary-list">

                        <?php
                        foreach ($customizable_itinerary as $item) {
                        ?>
                            <div class="customizable_itinerary-item">
                                <div class="top-itinerary-item">
                                    <div class="itinerary-item-title">
                                        <?= $item['title'] ?>
                                    </div>
                                    <div class="toggle-conetent">
                                        <div class="toggle-icon">
                                            <div class="circle-plus closed">
                                                <div class="circle">
                                                    <div class="horizontal"></div>
                                                    <div class="vertical"></div>
                                                </div>
                                            </div>
                                            <div class="circle-plus-two closed">
                                                <div class="circle">
                                                    <div class="horizontal"></div>
                                                    <div class="vertical"></div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="body-itinerary-item">
                                    <?= $item['content'] ?>
                                    <?php
                                    if ($item['image']) {
                                    ?>
                                        <div class="itinerary-gallery">
                                            <?php foreach ($item['image'] as $img_src) {
                                            ?>
                                                <div class="itinerary-img">
                                                    <img src="<?= $img_src ?>" alt="">

                                                </div>
                                            <?php
                                            } ?>
                                        </div>
                                    <?php
                                    }
                                    ?>

                                </div>
                            </div>

                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="customizable_itinerary-right">
                <div class="top-plan-tour">
                    Plan Your Personalized Tour
                </div>
                <div id="plan-tour-form">
                    <?php
                    echo do_shortcode('[wpforms id="965"]')
                    ?>
                    <span class="small-text">Free service. No credit card required</span>
                </div>
                <div class="certification-tour">
                    <div class="certification-wrap">
                        <div class="certification-top">
                            <div class="list-certification-img">
                                <a href="#">
                                    <img src="<?= get_template_directory_uri() ?>/css/images/certification1.png" alt="">
                                </a>
                                <a href="#">
                                    <img src="<?= get_template_directory_uri() ?>/css/images/certification2.webp" alt="">
                                </a>
                                <a href="#">
                                    <img src="<?= get_template_directory_uri() ?>/css/images/certification3.webp" alt="">
                                </a>
                            </div>
                            <div class="certification-logo">
                                <img src="<?= get_template_directory_uri() ?>/css/images/certification4.webp" alt="">
                            </div>
                            <div class="certification-description">
                                Award Winning Service
                            </div>
                            <div class="certification-content">
                                Book with confidence. We're an ASTA member who provide a range of safety guarantee.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
}
?>