<?php
$why_us = get_query_var('why_us');
if($why_us){
    ?> 
    <section id="why_us" class="">
        <div class="why_us-wrap nano-container">
            <div class="why_us-header">
                <div class="why-us-icon">
                    <img src="<?= $why_us['icon'] ?>" alt="">
                </div>
                <div class="why-us-title">
                    <h2><?= $why_us['title'] ?></h2>
                </div>
            </div>
            <div class="why_us-body">
                <div class="why_us-content">
                <?= $why_us['content'] ?>
                </div>
            </div>
        </div>
    </section>
    <?php
}
?>