<?php
global $bookyourtravel_theme_globals, $post, $first_display_tab, $default_tour_tabs, $entity_obj, $layout_class, $tab;

$cruise_obj = new BookYourTravel_Cruise($post);

if ($user_information = $cruise_obj->get_custom_field('user_information')) {
?>
    <section id="useful_information" class="section nano-container">
        <div class="useful_information-title">
            Useful Information
        </div>
        <div class="useful-information-content">
            <?= $user_information ?>
        </div>
        <div class="useful-information-action">
            <a href="<?= home_url() ?>/tailor-made-your-tour/">Request a free quote</a>
            <span>Free service. No credit card required</span>
        </div>
    </section>
<?php
}
?>