<?php
$glance = get_query_var('glance');
$location_id = get_query_var('location_id');
$youtube_link = get_query_var('youtube_link');
$title = get_the_title($location_id);

if ($glance) {
?>
    <section id="glance" class="section">
        <div class="glance-wraper nano-container">
            <h2><?= $title ?> at a glance</h2>
            <div class="glance-wraper-content">
                <!-- <iframe src="<?= $youtube_link ?>" frameborder="0" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe> -->
                <div class="glance-list">
                    <?php
                    foreach ($glance as $item) {
                    ?>
                        <div class="glance-item">
                            <div class="glance-icon">
                                <img src="<?= $item['icon'] ?>" alt="">
                            </div>
                            <div class="glance-text">
                                <div class="glance-title">
                                    <?= $item['title'] ?>
                                </div>
                                <div class="glance-description">
                                    <?= $item['description'] ?>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>

        </div>
    </section>

<?php
}
?>