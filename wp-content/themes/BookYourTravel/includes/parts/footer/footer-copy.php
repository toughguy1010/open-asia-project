<?php
global $bookyourtravel_theme_globals;
ob_start();
?>
<p class="copy"><?php echo wp_kses(html_entity_decode($bookyourtravel_theme_globals->get_copyright_footer()), array('a' => array('href' => array(), 'class' => array(), 'target' => array()), 'br' => array(), 'span' => array())); ?></p>				
<?php
$copy_html = ob_get_contents();
ob_end_clean();

echo apply_filters('bookyourtravel_footer_copy', $copy_html);