<?php namespace ProcessWire;

/**
 * Contact
 *
 */

include("forms/$page->name.php");
include("_default.php");

if($page->checkbox) {

	$address = WireArray::new();
	if($nb->clientName) $address->add($nb->wrap($nb->clientName, "strong"));
	if($nb->clientAddress) $address->add(nl2br($nb->clientAddress));

	$contact = WireArray::new();
	// Email
	if($nb->clientEmail) $contact->add($nb->wrap(__("Email"), "strong") . ": " . nbMailto($nb->clientEmail));
	if($nb->clientEmails) {
		$contact->add($nb->clientData("Emails")->implode("<br>", function($value, $key) {
			return nb()->wrap($key, "strong") . ": " . nbMailto($value);
		}));
	}

	// Telephone
	if($nb->clientTel) $contact->add($nb->wrap(__("Telephone"), "strong") . ": " . nbTel($nb->clientTel));
	if($nb->clientTels) {
		$contact->add($nb->clientData("Tels")->implode("<br>", function($value, $key) {
			return nb()->wrap($key, "strong") . ": " . nbTel($value);
		}));
	}

	// Social
	if($nb->clientSocial) {
		$contact->add(renderSocial($nb->clientData("Social")->implode("\n")));
	}

	$content .= nbContent($nb->wrap(
		//$nb->wrap($nb->wrap($address->implode("<br>"), "p"), "div") .
		$nb->wrap($nb->wrap($contact->implode("<br>"), "p"), "div"),
		[
			"class" => [
				"uk-grid-small",
				"uk-child-width-1-2@s",
			],
			"data-uk-grid" => true,
		],
		"div"
	));
}

$content .= nbBlock($form->render(), "form");
