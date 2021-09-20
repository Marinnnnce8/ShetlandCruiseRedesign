<?php namespace ProcessWire;

/**
 * Slider Block
 * 
 */

if($page->gallery->count()) {
	echo nbBlock(
		renderHeading($page->title) . 
		ukSlider($page->gallery),
		"slider"
	);
}
