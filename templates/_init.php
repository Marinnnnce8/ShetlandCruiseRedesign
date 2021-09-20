<?php namespace ProcessWire;

/**
 * Initialize
 *
 * Set up site functions, defaults, styles and scripts
 *
 */

/**
 * Site Settings
 *
 * https://processwire.com/api/ref/functions/setting/
 *
 * The majority of site settings are handled by `NbWire`,
 * but the following makes editing/updating other settings easier.
 * You can add your own settings here too.
 *
 */

setting([
	"fontawesome-version" => "5.8.2",
	"fontawesome-hash" => "sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay",
	"jquery-version" => "3.3.1",
	"jquery-hash" => "sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=",
	"cookieconsent-url" => "https://cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/",
	"cookieconsent-hash-css" => "sha256-ebN46PPB/s45oUcqLn2SCrgOtYgVJaFiLZ26qVSqI8M=",
	"cookieconsent-hash-js" => "sha256-y0EpKQP2vZljM73+b7xY4dvbYQkHRQXuPqRjc7sjvnA=",
	"cookieconsent-settings" => [
		"palette" => [
			"popup" => [
				"background" => "#0098d1",
				"text" => "#fff",
			],
			"button" => [
				"background" => "#fff",
				"text" => "#0098d1",
			],
		],
		"position" => "bottom-right",
		"content" => [
			"href" => $pages->get(1038)->url,
		],
	],
]);

/**
 * Site functions
 *
 */

include_once("./_nb.php");
include_once("./_uikit.php");
include_once("./_func.php");

/**
 * Add stylesheets/scripts
 *
 */

$urlUIkit = urls("AdminThemeUikit") . "uikit/dist";

$nb->styles->import([
	"$urlUIkit/css/uikit.min.css",
	"css/datetimepicker.base.css",
	"css/datetimepicker.css",
	"css/datetimepicker.themes.css",
	"css/nb.scss",
	"css/site.css",
	"css/print.scss",
	"css/ie11.scss",
]);

$nb->scripts->import([
	"$urlUIkit/js/uikit.min.js",
	"$urlUIkit/js/uikit-icons.min.js",
	urls("NbWire") . "nb.js",
	"js/nb.js",
	"js/jquery.okayNav-min.js",
	"js/jquery.waypoints.min.js",
	"js/nouislider.min.js",
	"js/gmaps-popup.js",
	"js/datetimepicker.min.js",
	"js/cookie.js",
	"js/theme.js",
]);

/**
 * Homepage
 *
 */

$pageHome = $pages->get(1);

/**
 * Page
 *
 */

// Is this?
$page->set("isHome", $page->id == $pageHome->id);
$page->set("isArticle", in_array($page->template->name, ["post"]));

// Page Title
$page->set("h1", $page->get("headline|title"));

$isSmall = in_array($page->template->name, [
	"submission",
	"contact",
	"itinerary",
	"enquiries",
	"port",
]);

// Wrap the content? Add template name to array to exclude.
$page->set("wrapContent", in_array($page->template->name, [
	"home",
	"search",
	"enquiries",
]) ? "" : "<div class='uk-container" . ($isSmall ? " uk-container-xsmall" : "") . "'>");

/**
 * Variables
 *
 */

// The page content variables
$before = ""; // Before title
$after = ""; // After title
$prepend = ""; // Before content
$content = ""; // Page content
$sidebar = ""; // Sidebar
$append = ""; // After content

$urlFinder = $pages->get(1053)->url;
$urlShortlist = $pages->get(1060)->url;
$urlShetland = "https://www.shetland.org/";

$nb->addJsonFields([
	"provider",
	"trip_type",
	"port",
	"info_departure",
	"info_season",
	"info_days",
	"duration",
	"info_prices",
	"type_transport",
]);

$textShortlist = __("You can shortlist multiple activities.") . " " .
	__("Simply look for the Shortlist icon.") . "<em><i class='icon-clipboards-4'></i></em><br>" . 
	__("Once shortlisted you can contact all providers in one go") . " - " . 
	"<a href='$urlShortlist/about'>" . 
	__("Click here to find out more") . 
	"</a>.";

$textMore = __("More");

if(!$session->cruiseUser) {
	updateSorts($pages->find([
		"template" => "activity",
		"date_sort<" => time() - (2048 * 64),
	]));
}
if(!$config->ajax) addUserData();
