<?php namespace ProcessWire;

/**
 * Gallery Block
 *
 */

$c = $page->gallery->count();
if($c) {

	$out = "";
	$options = [];
	$wrapContent = $page->getForPage()->wrapContent;

	if($c < 4) $options["perRow"] = 2;

	// If a caption is specified and there is only one image
	// Set the image description to the caption
	if($page->headline && $c == 1) $options["desc"] = $page->headline;

	// Add a title if specified
	if($page->title) $out .= $nb->wrap(renderHeading($page->title, 3, ["uk-margin-remove-top"]), $wrapContent);

	// Render the gallery
	$out .= nbGallery($page->gallery, $options);

	// Add a caption if specified
	if($page->headline) {
		$out .= $nb->wrap(
			$nb->wrap(renderMarkdown($page->headline), "nb-gallery-caption uk-text-center uk-margin-small-top"),
			$wrapContent
		);
	}

	// Wrap in a block
	$out = nbBlock($out, "gallery");

	if($page->checkbox) {
		echo "</div>" . // Close container
			$nb->wrap($out, [
				"class" => [
					"uk-container" . 
						($page->gallery->first()->width > $nb->width ? "-expand" : ""),
				],
			], "div") .
			$wrapContent; // Open container
	} else {
		echo $out;
	}
}
