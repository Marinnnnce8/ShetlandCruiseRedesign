<?php namespace ProcessWire;

/**
 * Signup
 *
 */

// TODO: AWeber signup to be replace with Mailchimp

/*
$aw = $modules->get("AweberSignup");

if($config->ajax) {

	$list = $aw->findList("visit-shetland");
	$response = $aw->addSubscriber([
		"name" => $input->post->text("name"),
		"email" => $input->post->email("email"),
	], $list->id) ? 200 : 400;

	$nb->outputJson([
		"action" => $page->template->name,
		"response" => $response,
		"message" => ($response == 200 ? 
			ukAlertSuccess(__("Thank you for signing up.")) : 
			ukAlertDanger(__("Sorry, there was a problem signing you up. Please refresh the page and try again."))),
	]);
}

*/