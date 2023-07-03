<?php

global $bookyourtravel_theme_globals;

$frontpage_show_slider = $bookyourtravel_theme_globals->frontpage_show_slider();

get_sidebar('home-above-slider');
	
if (class_exists ('RevSlider') && function_exists('putRevSlider')) {

	$homepage_slider = $bookyourtravel_theme_globals->get_homepage_slider();
	$homepage_slider_alias = '';
	
	if ($homepage_slider >= 0) {
		$sliders_array = array();
		try {
			$sliders_array = array();
			$revs = new RevSlider();
			if (method_exists($revs, "get_sliders")) {
				$sa = $revs->get_sliders();

				foreach($sa as $slider){
					$sliders_array[] = $slider->title;
				}			
			}
			$homepage_slider_alias = isset($sliders_array[$homepage_slider]) ? $sliders_array[$homepage_slider] : '';
		} catch(Exception $e) {
		
		}				
	}
	if (!empty($homepage_slider_alias) && $frontpage_show_slider) {
		putRevSlider($homepage_slider_alias);
	}
}