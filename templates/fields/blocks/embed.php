<?php namespace ProcessWire;

/**
 * Embed Block
 *
 */

if($page->html) {
	echo nbBlock(
		renderHeading($page->title) .
		getIntro($page) .
		$page->html,
		"embed"
	);
}
