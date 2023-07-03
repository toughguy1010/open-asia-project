<!--header-->
<header class="header header2">
		<!--top-header-->
	<div class="top-header">
		<div class="wrap">	
			<?php //get_template_part('includes/parts/header/header', 'top-nav-left'); ?>			
			<?php get_template_part('includes/parts/header/header', 'top-nav'); ?>
			<?php //get_template_part('includes/parts/header/header', 'contact'); ?>
		</div>
	</div>
	<!--//top-header-->
	<div class="wrap-header wrap header-desktop">

		<?php get_template_part('includes/parts/header/header', 'logo'); ?>
		<?php // get_template_part('includes/parts/header/header', 'contact'); ?>
		<?php get_template_part('includes/parts/header/header', 'nav'); ?>
		</div>
		

	<div class=" wrap-header wrap header-mobile">
		<?php get_template_part('includes/parts/header/header', 'logo'); ?>
		<?php get_template_part('includes/parts/header/header', 'toggle-nav'); ?>

		</div>
</header>
<!--//header-->