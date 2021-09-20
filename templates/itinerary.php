<?php namespace ProcessWire;

/**
 * Itinerary
 *
 */

// Page Title - Before
$meta = $page->port->explode(function($item) {
	return "<i class='icon-anchor-solid'></i> $item->title";
});
if($page->type_transport->count()) {
	$meta = array_merge($meta, $page->type_transport->explode(function($item) {
		return "<i class='icon-$item->name'></i>";
	}));
}
if(count($meta)) {
	$before .= $nb->wrap(
		$nb->wrap($meta, "<span class='category'>"),
		"<div class='page-meta'>"
	);
}

// Page Title - After
if($page->duration) {
	$h = floor($page->duration / 60);
	$m = $page->duration - ($h * 60);
	$after .= $nb->wrap(
		faIcon("clock") . " {$h}h" . ($m ? " {$m}min" : ""),
		"<div class='date-time'>"
	);
}

include("_default.php");
