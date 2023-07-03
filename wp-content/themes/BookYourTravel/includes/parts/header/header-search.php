<?php 
ob_start();
?>
<!--search-->
<div class="search">
	<form id="sf" method="get" action="<?php echo esc_url( home_url('/') ); ?>">
		<input type="search" placeholder="<?php esc_attr_e('Search entire site here', 'bookyourtravel'); ?>" name="s" id="search" /> 
		<input type="submit" id="ss" value="" name="searchsubmit"/>
	</form>
</div>
<!--//search-->	
<?php
$search_html = ob_get_contents();
ob_end_clean();
echo apply_filters('bookyourtravel_header_search', $search_html);