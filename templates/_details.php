<?php namespace ProcessWire;

/**
 * Details
 *
 */

// Contact Details
$contact = [];
if($page->tel) $contact[] = nbTel($page->tel, ["icon" => "phone"]);
if($page->email) $contact[] = nbMailto($page->email, ["icon" => "envelope"]);
if($page->link) $contact[] = faIcon("globe") . " " . $nb->wrap(__("Visit Website"), ["href" => $page->link], "a");
if($page->link_ta) $contact[] = faIcon("tripadvisor", "fab") . " " . $nb->wrap(__("Tripadvisor"), ["href" => $page->link_ta], "a");

// Awards
$awards = getAwards($page);

// Details Bar
$prepend .= $nb->wrap(
	$nb->wrap(
		$nb->wrap(
			($page->logo ? $nb->wrap(
				$nb->wrap(
					$nb->img($page->logo->url, ["class" => "profile-logo"]),
					"uk-padding-small uk-background-default uk-text-center"
				),
				"div"
			) : "") .
			($page->address || count($contact) ? $nb->wrap(
				widgetSubtitle(__("Contact Details"), "map-marker-alt") .
				($page->address ? $nb->wrap(
					nl2br($page->address),
					"<div class='address'>"
				) : "") .
				(count($contact) ? $nb->wrap($nb->wrap($contact, "li"), "<ul class='uk-list contact-list'>") : ""),
				"div"
			) : "") .
			($page->social ? $nb->wrap(
				widgetSubtitle( __("Let's Connect on:"), "share") .
				renderSocial($page->social),
				"div"
			) : "") .
			(count($awards) ? $nb->wrap(
				widgetSubtitle(__("Awards / Accreditation"), "award") .
				renderAwards($awards),
				"div"
			) : ""),
			[
				"class" => [
					"sidebar-horizontal",
					"uk-child-width-1-4@l",
					"uk-child-width-1-2@s",
				],
				"data-uk-grid" => true,
			],
			"div"
		),
		"uk-container"
	),
	"uk-section uk-section-small uk-background-muted"
);
