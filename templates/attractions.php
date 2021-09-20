<?php namespace ProcessWire;

/**
 * Attractions
 *
 */

$sidebar = false;
$content .= renderAttractions($page->children("include=hidden,sort=random"));
