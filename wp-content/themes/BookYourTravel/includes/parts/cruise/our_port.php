<?php
global $bookyourtravel_theme_globals, $bookyourtravel_theme_post_types;
$cruise_type_ids = get_query_var('cruise_type_ids');
$port_ids = get_query_var('ports');
$caption = get_query_var('caption');
?>
<section id="our_port" class="section">
    <div class="our_port-wrapper nano-container">
        <div class="our_port-header">
            <div class="our_port-title">
                We Pick Up From These Ports
            </div>
            <div class="our_port-description">
                Click your arrival port
            </div>
        </div>
        <div class="our_port-body">
            <div class="our_port-list">
                <?php
                foreach ($port_ids as $port_id) {
                    $port = get_term_by('id', $port_id, 'cruise_tag');
                    $id =  $port->term_id;
                    $thumbnail_id = $bookyourtravel_theme_post_types->get_taxonomy_image_id($id);
                    $thumbnail_obj = wp_get_attachment_image_src($thumbnail_id, "full");
                    isset($thumbnail_obj) ? $thumbnail_url = $thumbnail_obj[0] : $thumbnail_url = "";
                    ?> 
                    <a href="#" class="our_port-item">
                        <img src="<?=  $thumbnail_url?>" alt="">
                    </a>
                    <?php
                }
                ?>
            </div>
            <div class="our_port-caption">
                <?=   nl2br($caption) ?>
            </div>
        </div>
    </div> 
</section>