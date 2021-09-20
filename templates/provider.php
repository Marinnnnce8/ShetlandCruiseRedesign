<?php namespace ProcessWire;

/**
 * Provider
 *
 */

// Page title
$before .= $nb->wrap(renderHeading(__("Activity Provider"), 2), "<div class='page-meta'>");

include("_details.php");

// Activities List
$activities = $pages->get(1058)->children([
	"template" => "activity",
	"provider" => $page,
]);
if($activities->count()) {
	$append .= appendSection(
		__("Activities"),
		$nb->renderJson($activities, "items"),
		$nb->wrap($textShortlist, "<p class='uk-text-right@m'>")
	);
}

if($page->images->count()) {
	$append .= appendSection(__("Gallery"), nbBlock(nbGallery($page->images), "gallery"));
}
