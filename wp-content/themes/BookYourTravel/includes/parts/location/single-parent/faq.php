<?php 
$faq = get_query_var('faq');
$location_id = get_query_var('location_id');
$title = get_the_title($location_id);
if($faq){
    ?>
    <section id="faq_section" class="section">
        <div class="faq_wrapper nano-container">
            <h2><?=  $title ?> travel FAQs</h2>
            <div class="faq_list">
            <?php 
            foreach($faq as $item){
                ?>
                <div class="faq_item">
                    <div class="faq_item-header">
                        <div class="faq_item-title">
                            <?= $item['title'] ?>
                        </div>
                        <div class="faq_item-arrow"></div>
                    </div>
                    <div class="faq_item-body">
                        <div class="faq_item-content">
                            <?= $item['content'] ?>
                        </div>
                        <div class="faq_item-action">
                            <a href="<?= $item['link_button'] ?>"><?= $item['text_button'] ?></a>
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