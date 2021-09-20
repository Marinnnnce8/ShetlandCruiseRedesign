<?php namespace ProcessWire;

/**
 * Sitemap
 *
 */

$content .= nbContent(ukNav($pageHome, [
	"attr" => [
		"class" => ["nb-sitemap"],
		"data-uk-nav" => false,
	],
]));
