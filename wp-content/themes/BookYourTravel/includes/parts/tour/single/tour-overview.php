<?php
global $bookyourtravel_theme_globals, $post, $first_display_tab, $default_tour_tabs, $entity_obj, $layout_class, $tab;

$tour_obj = new BookYourTravel_Tour($post);
$entity_obj = $tour_obj;


// get field tour content
// overview

if ($overview = get_field('overview', get_the_ID())){
	$tour_itinerary = get_field('tour_itinerary', get_the_ID());
	?>
	<section id="tour-overview" class="section">
		<div class="tour-overview-wrap nano-container">
			<h1>Overview</h1>
			<div class="overview-body">
				<div class="overview-left">
					<div class="overview-trip-duration">
						<?php
						if ($overview) {
							foreach ($overview as $item) {
						?>
								<div class="overview-item">
									<div class="overview-item-header">
										<h5 class="overview-item-title">
											<?= $item['title'] ?>
										</h5>
										<span class="overview-item-icon">
											<img src="<?= $item['icon'] ?>" alt="">
										</span>
									</div>
									<div class="overview-item-body">
										<?= $item['content'] ?>
									</div>
								</div>
						<?php
							}
						}
						?>
					</div>
				</div>
				<div class="overview-right">
					<div class="tour-map">
						<div class="featured-tour-map">
							<img src="<?= $tour_itinerary ? $tour_itinerary : get_template_directory_uri().'/css/images/no_image.jpg' ?>" alt="">
						</div>
						<div class="map-footer">
							<h4>
								This trip is not quite right for you yet?
							</h4>
							<p>
								<a href="<?= home_url() ?>/tailor-made-your-tour/Vietnam at a glance">Let's Customize It Now</a>
							</p>
							<p>
								<span>It's 100% Tailor-made. Personal Assistance 24/7</span>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php
}
