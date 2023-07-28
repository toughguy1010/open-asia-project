<?php
global $bookyourtravel_theme_globals, $bookyourtravel_theme_post_types;
$cruise_type_ids = get_query_var('cruise_type_ids');
$port_ids = get_query_var('ports');
$ports = get_term_meta($cruise_type_ids, 'port', true);
if ($ports) {
?>
    <section id="cruise_port" class="section">
        <div class="cruise_port-wrap nano-container">
            <?php
            foreach ($ports as $port_id) {
                $port = get_term_by('id', $port_id, 'cruise_tag');
                $port_title = $port->name;
                $slug = $port->slug;
                $id =  $port->term_id;
                $description = $port->description;
                $thumbnail_id = $bookyourtravel_theme_post_types->get_taxonomy_image_id($id);
                $thumbnail_obj = wp_get_attachment_image_src($thumbnail_id, "full");
                isset($thumbnail_obj) ? $thumbnail_url = $thumbnail_obj[0] : $thumbnail_url = "";
            ?>
                <div id = "<?= $slug ?>" class="cruise_port-item">
                    <h2>
                        <?=$port_title ?>
                    </h2>
                    <div class="cruise_port-row">
                        <div class="cruise_port-thumbnail">
                            <img src="<?= $thumbnail_url ?>" alt="">
                        </div>
                        <div class="cruise_port-content">
                            <div class="cruise_port-description">
                                <?= $description ?>
                            </div>
                            <p>Available Trips: </p>
                            <div class="cruise_query-list">
                                <?php
                                $cruise_args = array(
                                    'post_type' => 'cruise',          // Assuming the post type is 'cruise'
                                    'posts_per_page' => -1,            // Retrieve only one cruise
                                    'tax_query' => array(
                                        array(
                                            'taxonomy' => 'cruise_tag', // The taxonomy to filter by
                                            'field' => 'term_id',       // Use 'term_id' to filter by term ID
                                            'terms' => $port->term_id,  // The term ID to filter by
                                        ),
                                    ),
                                );

                                $cruise_query = new WP_Query($cruise_args);
                                if ($cruise_query->have_posts()) {
                                    while ($cruise_query->have_posts()) {
                                        $cruise_query->the_post();
                                ?>
                                        <a href="<?= get_permalink() ?>">
                                            <?= the_title() ?>
                                        </a>
                                <?php
                                    }
                                } else {
                                    echo 'No cruise found for this port';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php

            }
            ?>
        </div>
    </section>
<?php

}

?>