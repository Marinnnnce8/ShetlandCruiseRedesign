<?php namespace ProcessWire;

/**
 * Activity
 *
 */

// Page Title - Before
if($page->trip_type) {
	$before .= $nb->wrap(
		"<span class='category'>{$page->trip_type->title}</span>",
		"<div class='page-meta'>"
	);
}

// Page Title - After
if($page->provider->count()) {
	$after .= $nb->wrap(
		sprintf(__("Provided by %s"), $page->provider->implode(", ", function($item, $key) {
			return "'" . nb()->wrap($item->title, ["href" => $item->url], "a") . "'";
		})),
		"<div class='provider'>"
	);
}

// Facts
$facts = [];
foreach(["departure", "days", "times", "season", "prices", "level", "access"] as $f) {

	$fn = "info_$f";
	$fl = $page->getField($fn);
	$icon = str_replace("check-o", "check", str_replace("gbp", "pound-sign", $fl->icon));
	$v = $page->get($fn);

	switch($f) {
		case "days":
			$v = getDays($v);
			break;
		case "departure":
			$icon = "<i class='icon-anchor-solid'></i>";
			break;
		case "level":
			$icon = "running";
			$v = $v->title;
			break;
		case "access":
			$v = nl2br($v);
			break;
		case "times":
			$icon = "clock";
			break;
	}

	if($v) {
		$fl = $page->getField($fn);
		$facts[] = $nb->wrap(
			infoHeading(
				$fl->label,
				$icon,
				"fact-title"
			) .
			$nb->wrap($v, "<p class='info'>"),
			"<div class='fact'>"
		);
	}
}

// Details Bar
$prepend .= $nb->wrap(
	$nb->wrap(
		$nb->wrap(
			$nb->wrap(
				renderHeading(__("Tour Information"), 3, ["sub-title"]) .
				(count($facts) ? $nb->wrap($nb->wrap($facts, "div"), [
					"class" => ["uk-child-width-1-2@s", "uk-child-width-1-3@l", "uk-grid-medium"],
					"data-uk-grid" => true,
				], "div") : "") .
				$nb->wrap(
					$nb->attr([
						"data-shortlist-button-render" => $page->id,
					], "div", true),
					"uk-margin-medium-top"
				),
				"uk-width-1-1@l"
			),
			["data-uk-grid" => true],
			"div"
		) . "<small class='uk-margin-small-top uk-display-block'>* Send multiple enquiries from <a href='$urlShortlist'>my shortlist</a> page.</small>",
		"uk-container"
	),
	"uk-section uk-section-small uk-background-muted"
);

// Content
include("_default.php");

// Sidebar
if($page->provider->count()) {

	$out = "";
	$awards = [];
	foreach($page->provider as $p) {

		$out .= widgetHeading(sprintf(__("Provided by %s"), $p->title)) .
			($p->logo ? $nb->wrap(
				$nb->img($p->logo->url, ["class" => "profile-logo"]),
				"uk-padding-small uk-background-default uk-text-center"
			) : renderHeading($p->title, 5)) .
			$nb->wrap(
				$nb->wrap(faIcon("user-alt") . __("Visit provider profile page"), [
					"href" => $p->url,
					"class" => "link",
				], "a"),
				"uk-margin-xsmall"
			) . "<div><br></div>";

		$awards = array_merge($awards, getAwards($p));
	}

	$sidebar .= $nb->wrap($out, "<div class='widget widget-info'>") .
		(count($awards) ? $nb->wrap(
			widgetHeading(faIcon("award") . __("Awards / Accreditation")) .
			renderAwards($awards),
			"<div class='widget widget-awards'>"
			) : "");
}
