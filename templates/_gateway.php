<?php namespace ProcessWire;

/**
 * Gateway
 *
 */

$sidebar = false;
$content .= $nb->renderJson($page->children(), [
	"action" => "items",
	"pageToArray" => [
		"type_transport" => "name",
	],
]);
