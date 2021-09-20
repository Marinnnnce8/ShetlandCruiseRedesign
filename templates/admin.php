<?php namespace ProcessWire;

/**
 * Admin template just loads the admin application controller, 
 * and admin is just an application built on top of ProcessWire. 
 *
 * This demonstrates how you can use ProcessWire as a front-end 
 * to another application. 
 *
 * Feel free to hook admin-specific functionality from this file, 
 * but remember to leave the require() statement below at the end.
 * 
 */

$pages->addHook("saveReady", function(HookEvent $event) {

	$page = $event->arguments("page");

	if(!$page->date_sort) $page->date_sort = time();

	// While site is in development
	// Update page name if the title changes
	if($page->isChanged("title") && $page->rootParent->id !== 2 && !nb()->siteLive) {
		$name = sanitizer()->pageName($page->title);
		// Do not update if name has been changed
		// Or if a sibling already has that name
		if(!$page->siblings("name=$name")->count() && !$page->isChanged("name")) {
			$page->name = $name;
		}
	}
});

require($config->paths->adminTemplates . 'controller.php'); 
