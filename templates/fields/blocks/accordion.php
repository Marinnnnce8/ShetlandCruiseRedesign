<?php namespace ProcessWire;

/**
 * Accordion Block
 *
 */

if($page->hasField("items") && $page->items->count()) {
	echo nbContent(renderHeading($page->title) . ukAccordion($page->wire("page")->id == 1 ? $page->items->find("limit=5") : $page->items));
}
