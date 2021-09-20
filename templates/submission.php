<?php namespace ProcessWire;

/**
 * Submit an Activity Listing
 *
 */

include("forms/$page->name.php");
include("_default.php");

$content .= nbBlock($form->render(), "form");
