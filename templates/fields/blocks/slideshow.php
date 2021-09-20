<?php namespace ProcessWire;

/**
 * Slideshow Block
 * 
 */

if($page->gallery->count()) {
	echo nbBlock(
		renderHeading($page->title) . 
		ukSlideshow($page->gallery),
		"slideshow"
	);
}
