<!--header-->
<header class="header header2">
	<!--top-header-->
	<div class="top-header">
		<div class="wrap">
			<?php get_template_part('includes/parts/header/header', 'top-nav'); ?>
			<div class="switch-lang">
				<svg class="" width="20" height="20" fill="#fff" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" d="M8.107 1.482a8.755 8.755 0 013.786 0c.375.607.711 1.375.992 2.268h-5.77c.28-.893.617-1.66.992-2.268zm-1.693.563a8.74 8.74 0 00-2.5 1.705H5.81a14.07 14.07 0 01.604-1.705zM2.006 3.991A9.956 9.956 0 000 10c0 5.522 4.477 9.999 10 9.999a9.959 9.959 0 004.668-1.154l4.539 1.135a.638.638 0 00.747-.856l-1.507-3.77a9.956 9.956 0 001.54-5.856.623.623 0 00-.02-.327C19.544 4.037 15.243 0 10 0a9.985 9.985 0 00-7.994 3.99zM18.635 8.75A8.674 8.674 0 0017.149 5h-2.648c.246 1.156.407 2.422.47 3.75h3.664zm-2.55-5a8.74 8.74 0 00-2.5-1.705c.226.52.429 1.092.605 1.705h1.896zM18.725 10H15c0 1.308-.095 2.572-.273 3.75h3.152a8.687 8.687 0 00.845-3.75zm-1.566 5H14.5c-.233 1.097-.542 2.095-.915 2.955.233-.105.46-.22.682-.345a.638.638 0 01.468-.063l3.57.893-1.169-2.92a.638.638 0 01.02-.52zm-5.265 3.518c.54-.874 1-2.08 1.328-3.518H6.78c.328 1.437.788 2.644 1.328 3.517a8.756 8.756 0 003.786 0zm-5.479-.563c-.372-.86-.682-1.858-.915-2.955H2.851a8.758 8.758 0 003.563 2.955zM2.122 13.75h3.151A25.264 25.264 0 015 10H1.277a8.69 8.69 0 00.845 3.75zm-.756-5A8.675 8.675 0 012.85 5h2.648a23.445 23.445 0 00-.47 3.75H1.366zm12.096 5H6.538A23.848 23.848 0 016.25 10h7.5c0 1.333-.103 2.597-.288 3.75zM6.78 5a21.845 21.845 0 00-.498 3.75h7.438A21.845 21.845 0 0013.221 5H6.78z"></path>
				</svg>
				<?php
				echo do_shortcode("[language-switcher]")
				?>
			</div>
		</div>

	</div>
	<!--//top-header-->
	<div class="wrap-header wrap header-desktop">

		<?php get_template_part('includes/parts/header/header', 'logo'); ?>
		<?php // get_template_part('includes/parts/header/header', 'contact'); 
		?>
		<?php get_template_part('includes/parts/header/header', 'nav'); ?>
	</div>


	<div class=" wrap-header wrap header-mobile">
		<?php get_template_part('includes/parts/header/header', 'logo'); ?>
		<?php get_template_part('includes/parts/header/header', 'toggle-nav'); ?>

	</div>
</header>
<!--//header-->