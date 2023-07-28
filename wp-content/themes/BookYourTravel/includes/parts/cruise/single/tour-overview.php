<?php
global $bookyourtravel_theme_globals, $post, $first_display_tab, $default_tour_tabs, $entity_obj, $layout_class, $tab;

$cruise_obj = new BookYourTravel_Cruise($post);
$entity_obj = $cruise_obj;


// get field tour content
// overview

if ($overview = get_field('overview', get_the_ID())) {
    $cruise_contact = get_field('cruise_contact', get_the_ID());
?>
    <section id="tour-overview" class="section">
        <div class="tour-overview-wrap nano-container">
            <h1>Overview</h1>
            <div class="overview-body">
                <div class="overview-left">
                    <div class="overview-trip-duration">
                        <?php
                        if ($overview) {
                            foreach ($overview as $item) {
                        ?>
                                <div class="overview-item">
                                    <div class="overview-item-header">
                                        <h5 class="overview-item-title">
                                            <?= $item['title'] ?>
                                        </h5>
                                        <span class="overview-item-icon">
                                            <img src="<?= $item['icon'] ?>" alt="">
                                        </span>
                                    </div>
                                    <div class="overview-item-body">
                                        <?= $item['content'] ?>
                                    </div>
                                </div>
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="overview-right">
                    <div class="cruise-contact" style="background-image: url('<?= $cruise_contact['background'] ?>');">
                        <div class="cruise-contact-content">
                            <p>
                                We know you are unique, so we're here to create unique experiences for you!
                            </p>
                            <p style="margin-bottom: 0px;">
                                Share your ideas with us:
                            </p>
                            <div class="cruise-contact-information">
                                <p class="cruise-contact-email">
                                    <?= $cruise_contact['mail'] ?>
                                </p>
                                <p class="cruise-contact-phone">
                                    <?= $cruise_contact['phone'] ?>
                                </p>
                            </div>
                            <a href="<?php echo home_url() ?>/tailor-made-your-tour/">
                                Make an enquiry
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
}
