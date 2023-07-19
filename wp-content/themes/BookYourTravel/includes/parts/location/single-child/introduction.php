<?php
$introduction = get_query_var('introduction');
$map = get_query_var('map');
$location_id = get_query_var('location_id');
$title = get_the_title($location_id);
if ($introduction) {

?>
    <section id="introduction" class="section">
        <div class="introduction-wrapper nano-container">
            <div class="introduction-left">
                <h2><?= $title ?></h2>
                <p>
                    <?= $introduction ?>
                </p>
            </div>
            <div class="introduction-right">
                <iframe src="<?= $map ?>" width="500" height="500" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>
<?php
}
?>