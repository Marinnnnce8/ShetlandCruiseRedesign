<?php namespace ProcessWire;

/**
 * Content Block
 * 
 */

if($page->body) {
	echo nbContent($page->body);
}
