<?php 
$travel_guide = get_query_var('travel_guide');
if($travel_guide){
    ?> 
    <section id="travel_guide" class="section">
        <div class="travel_guide-wrap nano-container">
            <h2>Travel Guides</h2>
            <div class="travel_guide-content">
                <?=  $travel_guide ?>
            </div>
        </div>
    </section>
    <?php
}

?>