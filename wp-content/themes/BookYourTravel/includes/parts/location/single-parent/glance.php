<?php
$glance = get_query_var('glance');

if ($glance) {
?>
    <section id="glance" class="section">
        <div class="glance-wraper nano-container">
            <h2>Vietnam at a glance</h2>
            <div class="glance-list">
                <?php 
                foreach($glance as $item){
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
    </section>

<?php
}
?>