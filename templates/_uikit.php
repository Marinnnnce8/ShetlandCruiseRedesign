<?php namespace ProcessWire;

/**
 * UIkit Functions
 *
 * Based on https://github.com/processwire/processwire/blob/master/site-regular/templates/_uikit.php
 *
 */

/**
 * Render a UIkit Accordion
 *
 * https://getuikit.com/docs/accordion
 *
 * ~~~~~
 * // Display an accordion with the 4th item open and a slower animation
 * echo ukAccordion($page->items, [
 *     "active" => 3,
 *     "duration" => 512,
 * ]);
 * ~~~~~
 *
 * @param array|PageArray $items The items to display in the accordion.
 * @param array $options Options to modify behavior. The values set by default are:
 * - `active` (int): The index of the open item (default=0).
 * - `duration` (int): The open/close animation duration in milliseconds (default=256).
 * @return string
 *
 */
function ukAccordion($items, array $options = []) {

	$nb = nb();
	$hasIcons = false;

	// Set default options
	$options = array_merge([
		"active" => 0,
		"duration" => 256,
	], $options);

	// Convert to array if PageArray
	if($items instanceof PageArray) {
		$hasIcons = $items->first->getForPage()->getForPage()->id == 1056;
		$items = $items->explode("body", ["key" => "title"]);
	}

	$out = "";
	foreach($items as $title => $body) {
		$out .= $nb->wrap(
			($hasIcons ? "<i class='icon-" . (sanitizer()->pageName($title)) . "'></i>" : "") .
			$nb->wrap($nb->wrap($title, "span"), ["class" => "uk-accordion-title"], "button") .
			$nb->wrap($body, "uk-accordion-content"),
			"li"
		);
	}

	return $nb->wrap($out, [
		"class" => "tab-navigation" . ($hasIcons ? " with-icons" : ""),
		"data-uk-accordion" => $options,
		"data-uk-scrollspy" => [
			"target" => "> li",
			"delay" => 128,
			"cls" => "uk-animation-slide-bottom-medium",
		],
	], "ul");
}

/**
 * Render a UIkit Alert
 *
 * https://getuikit.com/docs/alert
 *
 * ~~~~~
 * // Display a "danger" message with a close button
 * echo ukAlert("I'm sorry Dave, I'm afraid I can't do that", "danger", [
 *     "close" => true,
 * ]);
 * ~~~~~
 *
 * @param string $msg Text/html to display in the alert box.
 * @param string $type The UIkit style: `primary | success | warning | danger`.
 * @param array|bool $options Options to modify behavior. The values set by default are:
 * - `animation` (bool|string): Fade out or use the Animation component (default=true).
 * - `close` (bool): Should a close button be displayed? (default=false).
 * - `duration` (int): Animation duration in milliseconds (default=256).
 * @return string
 *
 */
function ukAlert($msg, $type = "success", $options = []) {

	if(is_bool($options)) $options = ["close" => $options];

	// Set default options
	$options = array_merge([
		"animation" => true,
		"close" => false,
		"duration" => 256,
	], $options);

	// close is not a uk-alert option
	$close = $options["close"];
	unset($options["close"]);

	return nb()->wrap(
		($close ? "<a class='uk-alert-close' data-uk-close></a>" : "") .
		$msg,
		[
			"class" => ["uk-alert-$type"],
			"data-uk-alert" => ($close ? $options : true),
		],
		"div"
	);
}

/**
 * Render an alert with centered text
 *
 * @param string $msg
 * @param string $type
 * @param array|bool $options
 * @return string
 * @see ukAlert()
 *
 */
function ukAlertCenter($msg, $type = "success", $options = []) {
	return ukAlert(nb()->wrap($msg, "uk-text-center"), $type, $options);
}

/**
 * Render a primary alert
 *
 * Shortcut for ukAlert("message", "primary");
 *
 * @param string $msg
 * @param array|bool $options
 * @return string
 * @see ukAlert()
 *
 */
function ukAlertPrimary($msg, $options = []) {
	return ukAlert($msg, "primary", $options);
}

/**
 * Render a success alert
 *
 * Shortcut for ukAlert("message", "success");
 *
 * @param string $msg
 * @param array|bool $options
 * @return string
 * @see ukAlert()
 *
 */
function ukAlertSuccess($msg, $options = []) {
	return ukAlert($msg, "success", $options);
}

/**
 * Render a warning alert
 *
 * Shortcut for ukAlert("message", "warning");
 *
 * @param string $msg
 * @param array|bool $options
 * @return string
 * @see ukAlert()
 *
 */
function ukAlertWarning($msg, $options = []) {
	return ukAlert($msg, "warning", $options);
}

/**
 * Render a danger alert
 *
 * Shortcut for ukAlert("message", "danger");
 *
 * @param string $msg
 * @param array|bool $options
 * @return string
 * @see ukAlert()
 *
 */
function ukAlertDanger($msg = "", $options = []) {
	return ukAlert($msg, "danger", $options);
}


/**
 * Render a UIkit breadcrumb list from the given Page or PageArray
 *
 * @param Page|PageArray|null $page
 * @param array $options Additional options to modify default behavior:
 *  - `attr` (array): Additional attributes to apply to the `<ul.uk-breadcrumb>`.
 *  - `appendCurrent` (bool): Append current page as non-linked item at the end? (default=true).
 * @return string
 *
 */
function ukBreadcrumb($page = null, array $options = []) {

	if(is_null($page)) $page = page();

	if($page instanceof Page) {
		$items = $page->breadCrumbs ?: $page->parents;
	} else {
		$items = $page;
		$page = $items->last;
		$items->remove($page);
	}

	$options = array_merge([
		"attr" => [],
		"appendCurrent" => true,
	], $options);

	$options["attr"] = array_merge([
		"class" => [],
	], $options["attr"]);

	$options["attr"]["class"] = array_merge($options["attr"]["class"], [
		"uk-breadcrumb",
	]);

	return nb()->wrap(
		$items->each("<li><a href='{url}'>{title}</a></li>") .
		($options["appendCurrent"] ? "<li><span>$page->title</span></li>" : ""),
		$options["attr"],
		"ul"
	);
}

/**
 * Render a UIkit card
 *
 * https://getuikit.com/docs/card
 *
 * ~~~~~
 * // An example
 * echo ukCard("<p>Test</p>");
 * ~~~~~
 *
 * @param array|string $contents
 * @param array|string $class
 * @return string
 *
 */
function ukCard($contents = [], $class = []) {

	// If string content is passed, make it the body
	if(!is_array($contents)) $contents = ["body" => $contents];

	// If string class is passed, make array
	if(!is_array($class)) $class = [$class];

	// If nothing passed, return nothing
	if(empty(implode("", $contents))) return "";

	$nb = nb();
	$out = "";

	// Set default classes
	if(!count($class)) $class = ["uk-card-default"];

	// If $header is just text, convert to uk-card-title
	if(!$nb->isTag($contents["header"])) $contents["header"] = ukCardTitle($contents["header"]);

	// Card contents
	foreach($contents as $key => $value) $out .= $nb->wrap($contents[$key], "uk-card-$key");

	return $nb->wrap($out, [
		"class" => array_merge(["uk-card"], $class),
	], "div");
}

/**
 * Render a UIkit card title
 *
 * ~~~~~
 * // An example
 * echo ukCardTitle("Title");
 * ~~~~~
 *
 * @param string $title The title
 * @param int $heading The heading number
 * @return string
 *
 */
function ukCardTitle($title, $heading = 3) {
	return renderHeading($title, $heading, [
		"id" => sanitizer()->pageName($title),
		"class" => ["uk-card-title"],
	]);
}

/**
 * Render a UIkit Dotnav
 *
 * https://getuikit.com/docs/dotnav
 *
 * ~~~~~
 * // Render a uk-dotnav
 * echo ukDotnav();
 * ~~~~~
 *
 * @param array $options
 * @return string
 * @see _ukItemnav()
 *
 */
function ukDotnav(array $options = []) {
	return _ukItemnav("dot", $options);
}

/**
 * Render a UIkit icon
 *
 * A full list of available icons can be found here: https://getuikit.com/docs/icon#library
 *
 * ~~~~~
 * // Display a large (3x) user icon
 * echo ukIcon("user", 3);
 * ~~~~~
 *
 * @param string $icon The icon to be displayed.
 * @param int $ratio The size of the icon.
 * @return string
 *
 */
function ukIcon($icon, $ratio = 1) {
	return nb()->attr([
		"data-uk-icon" => [
			"icon" => $icon,
			"ratio" => $ratio,
		],
	], "span", true);
}

/**
 * Render a UIkit Nav
 *
 * https://getuikit.com/docs/nav
 *
 * ~~~~~
 * // Render the section navigation for the page
 * echo ukNav($page);
 * ~~~~~
 *
 * @param Page|PageArray $items
 * @param array $options Options to modify behavior:
 *  - `attr` (array): An array of attributes rendered on the main <ul> element.
 *  - `exclude` (array): An array of template names that should be excluded from the navigation.
 *  - `prependParent` (bool): When rendering children, should the parent be prepended? (default=false).
 * @return string
 *
 */
function ukNav($items, array $options = []) {

	// Set default options
	$options = array_merge([
		"attr" => [],
		"attrSub" => [
			"class" => [
				"uk-nav-sub",
			],
		],
		"attrSubItems" => [
			"class" => [
				"uk-nav-sub-items",
			],
		],
		"exclude" => [],
		"prependParent" => false,
	], $options);

	// Set default attributes
	$attr = array_merge([
		"class" => [
			"uk-nav",
			"uk-nav-default",
			"uk-nav-parent-icon",
		],
		"data-uk-nav" => true,
	], $options["attr"]);
	$options["attr"] = $options["attrSub"];

	if($items instanceof Page) {
		$page = $items;
		$items = $page->id == 1 ? $page->wire("pages")->find("id=1") : $page->rootParent->children;
	} else if($items instanceof PageArray && $items->count()) {
		$page = $page->wire("page");
	} else {
		return "";
	}

	// Return blank if a nav cannot or should not be rendered
	if(!$items->count() || in_array($page->template->name, $options["exclude"])) return "";

	$out = "";
	foreach($items as $item) {
		$out .= _ukNavItem($item, $page, $options);
	}

	return $page->wire("nb")->wrap($out, $attr, "ul");
}

/**
 * Render navigation items
 *
 * @param PageArray $items
 * @param array $exclude Template to exclude from dropdown rendering.
 * @param bool $dropdown
 * @return string
 *
 */
function ukNavbar(PageArray $items = null, array $exclude = [], $dropdown = true) {

	$nb = nb();

	if(is_null($items)) $items = $nb->navItems;
	$exclude = array_merge(["home", "posts"], $exclude);

	$out = "";
	foreach($items as $item) {

		$hasChildren = $item->children->count() && $dropdown && !in_array($item->template->name, $exclude);

		$attr = ["class" => []];
		if($hasChildren) $attr["class"][] = "uk-parent";
		if($item->id == $items->wire("page")->rootParent->id) $attr["class"][] = "uk-active";

		$out .= $nb->wrap(
			$nb->wrap($item->title, ["href" => $item->url], "a") .
			($hasChildren ? $nb->wrap(
				$nb->wrap(
					ukNavbar($item->children, $exclude, false),
					[
						"class" => [
							"uk-nav",
							"uk-navbar-dropdown-nav",
						]
					],
					"ul"
				),
				"uk-navbar-dropdown"
			) : ""),
			$attr,
			"li"
		);
	}

	return $out;
}

/**
 * Render a Prev/Next Page navigation
 *
 * https://getuikit.com/docs/pagination#previous-and-next
 *
 * @param Page $page
 * @param array $options
 * - `attr` (array): Array of attributes for <ul.uk-pagination>.
 * - `prevClass` (array|string): Class attributes for the previous arrow (default="uk-margin-small-right").
 * - `nextClass` (array|string): Class attributes for the next arrow (default="uk-margin-small-left").
 * @return string
 *
 */
function ukPrevNext(Page $page, array $options = []) {

	// If a single child page, return nothing
	if(!$page->prev->id && !$page->next->id) return "";

	$nb = $page->wire("nb");

	// Default options
	$options = array_merge([
		"attr" => [
			"class" => "uk-width-1-1",
		],
		"prevClass" => "uk-margin-small-right",
		"nextClass" => "uk-margin-small-left",
	], $options);

	if(!array_key_exists("class", $options["attr"])) {
		$options["attr"]["class"] = [];
	} else if(!is_array($options["attr"]["class"])) {
		$options["attr"]["class"] = [$options["attr"]["class"]];
	}

	$options["attr"]["class"][] = "uk-pagination";

	return $nb->wrap(
		$nb->wrap(($page->prev->id ? $nb->wrap(
			$nb->attr([
				"class" => $options["prevClass"],
				"data-uk-pagination-previous" => true,
			], "span", true) . $page->prev->title,
			["href" => $page->prev->url],
			"a"
		) : ""), ["class" => "uk-text-truncate"], "li") .
		$nb->wrap(($page->next->id ? $nb->wrap(
			$page->next->title . $nb->attr([
				"class" => $options["nextClass"],
				"data-uk-pagination-next" => true,
			], "span", true),
			["href" => $page->next->url, "class" => "uk-text-truncate"],
			"a"
		) : ""), ["class" => ["uk-text-truncate", "uk-margin-auto-left"]], "li"),
		$options["attr"],
		"ul"
	);
}

/**
 * Render a UIkit Slidenav
 *
 * https://getuikit.com/docs/slidenav
 *
 * ~~~~~
 * // Render a uk-slidenav
 * echo ukSlidenav();
 * ~~~~~
 *
 * @param array $options
 * - `previous` (array|bool): Attributes for the previous button. Pass `false` to disable.
 * - `next` (array|bool): Attributes for the next button. Pass `false` to disable.
 * - `large` (bool): Should uk-slidenav-large class be used? (default=false).
 * - `wrap` (string): A wrap element for the uk-slidenav.
 * @return string
 *
 */
function ukSlidenav(array $options = []) {

	$nb = nb();

	// Set default options
	$options = array_merge([
		"previous" => [],
		"next" => [],
		"large" => false,
		"wrap" => "",
	], $options);

	// Assign common classes
	if(isset($options["class"])) {

		// Make sure the common classes are an array
		$options = $nb->keyArray("class", $options);

		if(is_array($options["previous"])) {
			// Make sure the class attribute exists
			$options["previous"] = $nb->keyArray("class", $options["previous"]);
			// Add the common classes
			$options["previous"]["class"] = array_merge($options["class"], $options["previous"]["class"]);
		}
		if(is_array($options["next"])) {
			// Make sure the class attribute exists
			$options["next"] = $nb->keyArray("class", $options["next"]);
			// Add the common classes
			$options["next"]["class"] = array_merge($options["class"], $options["next"]["class"]);
		}
	}

	return $nb->wrap(
		(is_array($options["previous"]) ?
			ukSlidenavButton("previous", $options["previous"], $options["large"]) : "") .
		(is_array($options["next"]) ?
			ukSlidenavButton("next", $options["next"], $options["large"]) : ""),
		$options["wrap"]
	);
}

/**
 * Render a UIkit Slidenav button
 *
 * ~~~~~
 * // Render a 'previous' uk-slidenav button
 * echo ukSlidenavButton("previous", ["class" => "uk-position-center-left"]);
 * ~~~~~
 *
 * @param string $direction previous/next.
 * @param array $attr
 * @param bool $large
 * @return string
 *
 */
function ukSlidenavButton($direction = "previous", array $attr = [], $large = false) {

	$attr = array_merge([
		"href" => "#",
		"data-uk-slidenav-$direction" => true,
	], $attr);

	if($large) {
		$attr = nb()->keyArray("class", $attr);
		$attr["class"][] = "uk-slidenav-large";
	}

	return nb()->attr($attr, "a", true);
}

/**
 * Render a UIkit Slider
 *
 * https://getuikit.com/docs/slider
 *
 * ~~~~~
 * // Render a slider, resize images to 512px in width
 * echo ukSlider($page->images, [
 *     "width" => 512,
 * ]);
 * ~~~~~
 *
 * @param Pageimages|array $items Images to render and display or an array of items to display.
 * @param array $options Options to modify behavior:
 * - `uk-slider` (array): UIkit Slider options https://getuikit.com/docs/slider#component-options.
 * - `class` (array): Classes for the slider wrapper.
 * - `uk-lightbox` (array|bool): UIkit Lightbox options https://getuikit.com/docs/lightbox#component-options.
 * - `uk-scrollspy` (array|false): UIkit Scrollspy options: https://getuikit.com/docs/scrollspy#component-options.
 * - `uk-slidenav` (array|bool): UIkit Slidenav options. Pass `false` to disable.
 * - `caption` (array|bool): An array of attributes for captions. Pass `false` to disable captions.
 * - `width` (int): The width of the thumbnail image (default=`$nb->width`).
 * - `height` (int): The height of the thumbnail image (default=`$nb->height`).
 * @return string
 *
 */
function ukSlider($items, array $options = []) {

	$nb = nb();
	$sanitizer = sanitizer();

	// Set default options
	$options = array_merge([
		"uk-slider" => [],
		"class" => [
			"uk-grid",
			"uk-grid-small",
			"uk-child-width-1-2",
			"uk-child-width-1-3@s",
		],
		"uk-lightbox" => [],
		"uk-scrollspy" => [
			"cls" => "uk-animation-fade",
			"delay" => 128,
		],
		"uk-slidenav" => [],
		"width" => ($nb->width ?: 910),
		"height" => ($nb->height ?: 512),
		"wrap" => "uk-position-relative uk-visible-toggle uk-light",
	], $options);

	// Set default uk-slider options
	$ukSlider = array_merge([
		"sets" => true,
	], $options["uk-slider"]);

	// Lightbox only used for Pageimages
	$ukLightbox = false;

	// Set default uk-slidenav options
	$ukSlidenav = is_array($options["uk-slidenav"]) ? array_merge([
		"class" => [
			"uk-position-small",
			"uk-hidden-hover",
		],
		"previous" => [
			"data-uk-slider-item" => "previous",
			"class" => "uk-position-center-left",
		],
		"next" => [
			"data-uk-slider-item" => "next",
			"class" => "uk-position-center-right",
		],
	], $options["uk-slidenav"]) : false;

	// If uk-dotnav should be used
	$ukDotnav = false;
	if(array_key_exists("uk-dotnav", $options) && $options["uk-dotnav"] !== false) {
		// Set default uk-dotnav options
		if(!is_array($options["uk-dotnav"])) $options["uk-dotnav"] = [];
		$ukDotnav = array_merge([
			"class" => [
				"uk-slider-nav",
				"uk-flex-center",
				"uk-margin",
			],
		], $options["uk-dotnav"]);
	}

	if($items instanceof Pageimages) {

		$images = $items;
		$items = [];

		// Set default uk-lightbox options
		$ukLightbox = is_array($options["uk-lightbox"]) ? array_merge([
			"animation" => "fade",
		], $options["uk-lightbox"]) : $options["uk-lightbox"];

		// Automatically assign wrap ID if not passed
		if(!array_key_exists("id", $options)) {
			$options["id"] = implode("_", [
				"uk-slider",
				$images->getField()->name,
				$images->getPage()->id,
			]);
		}

		// Render the Pageimages
		foreach($images as $image) {

			// Convert caption markdown to HTML
			$desc = $sanitizer->entitiesMarkdown($image->description);

			// Remove tags for alt text
			$alt = $sanitizer->markupToText($desc);

			$items[] = $nb->wrap(
				$nb->img(
					$image->size($options["width"], $options["height"])->url,
					["alt" => $alt],
					["uk-img" => ["target" => "#$options[id]"]]
				),
				($ukLightbox ? $nb->attr([
					"href" => $image->url,
					"data-alt" => $alt,
					"data-caption" => (!empty($desc) ? $nb->wrap($desc, "div") : ""),
				], "a") : "")
			);
		}

	} else if(!is_array($items)) {

		// No renderable items passed, return nothing
		return "";
	}

	return $nb->wrap(
		$nb->wrap(
			$nb->wrap(
				$nb->wrap(
					$nb->wrap($items, "li"),
					[
						"class" => array_merge(["uk-slider-items"], $options["class"]),
						"data-uk-lightbox" => $ukLightbox,
						"data-uk-scrollspy" => $options["uk-scrollspy"],
					],
					"ul"
				),
				"uk-slider-container"
			) .
			($ukSlidenav ? ukSlidenav($ukSlidenav) : "") .
			($ukDotnav ? ukDotnav($ukDotnav) : ""),
			$options["wrap"]
		),
		[
			"data-uk-slider" => $ukSlider,
			"id" => (isset($options["id"]) ? $options["id"] : "uk-slider"),
		],
		"div"
	);
}

/**
 * Render a UIkit Slideshow
 *
 * https://getuikit.com/docs/slideshow
 *
 * ~~~~~
 * // Render a slideshow, resize images to 512px in width
 * echo ukSlideshow($page->images, [
 *     "width" => 512,
 * ]);
 * ~~~~~
 *
 * @param Pageimages|array $items Images to render and display or an array of items to display.
 * @param array $options Options to modify behavior:
 * - `uk-slideshow` (array): UIkit Slideshow options https://getuikit.com/docs/slideshow#component-options.
 * - `class` (array): Classes for the slideshow wrapper.
 * - `uk-lightbox` (array|bool): UIkit Lightbox options https://getuikit.com/docs/lightbox#component-options.
 * - `uk-scrollspy` (array|false): UIkit Scrollspy options: https://getuikit.com/docs/scrollspy#component-options.
 * - `uk-slidenav` (array|bool): UIkit Slidenav options. Pass `false` to disable.
 * - `caption` (array|bool): An array of attributes for captions. Pass `false` to disable captions.
 * - `width` (int): The width of the thumbnail image (default=`$nb->width`).
 * @return string
 *
 */
function ukSlideshow($items, array $options = []) {

	$nb = nb();
	$sanitizer = sanitizer();

	// Set default options
	$options = array_merge([
		"uk-slideshow" => [],
		"class" => [
			"uk-position-relative",
			"uk-visible-toggle",
			"uk-light",
		],
		"uk-lightbox" => [],
		"uk-scrollspy" => [
			"cls" => "uk-animation-slide-bottom-small",
		],
		"uk-slidenav" => [],
		"caption" => [
			"class" => [
				"uk-overlay",
				"uk-overlay-primary",
				"uk-position-bottom",
				"uk-padding-small",
				"uk-text-center",
				"uk-transition-slide-bottom",
			],
		],
		"width" => ($nb->width ?: 910),
		"height" => ($nb->height ?: 512),
	], $options);

	// Set default uk-slideshow options
	$ukSlideshow = array_merge([
		"animation" => "fade",
		"autoplay" => true,
		"autoplay-interval" => 4096,
		"ratio" => "16:9",
	], $options["uk-slideshow"]);

	// Set height from slideshow ratio
	if($ukSlideshow["ratio"] && strpos($ukSlideshow["ratio"], ":") !== false) {
		$ratio = explode(":", $ukSlideshow["ratio"]);
		$options["height"] = ($ratio[1] / $ratio[0]) * $options["width"];
	}

	// Set default uk-slidenav options
	$ukSlidenav = is_array($options["uk-slidenav"]) ? array_merge([
		"class" => [
			"uk-position-small",
			"uk-hidden-hover",
		],
		"previous" => [
			"data-uk-slideshow-item" => "previous",
			"class" => "uk-position-center-left",
		],
		"next" => [
			"data-uk-slideshow-item" => "next",
			"class" => "uk-position-center-right",
		],
	], $options["uk-slidenav"]) : false;

	// Lightbox only used for Pageimages
	$ukLightbox = false;

	// Default itemnav options
	$ukItemnav = false;
	$ukItemnavOptions = [
		"class" => [
			"uk-slideshow-nav",
			"uk-flex-center",
			"uk-margin",
		],
	];

	// If uk-dotnav should be used
	if(array_key_exists("uk-dotnav", $options) && $options["uk-dotnav"] !== false) {
		// Set default uk-dotnav options
		$ukItemnav = "uk-dotnav";
		if(!is_array($options["uk-dotnav"])) $options["uk-dotnav"] = [];
		$options["uk-dotnav"] = array_merge($ukItemnavOptions, $options["uk-dotnav"]);
	}

	if($items instanceof Pageimages) {

		$images = $items;
		$items = [];

		// Set default uk-lightbox options
		$ukLightbox = is_array($options["uk-lightbox"]) ? array_merge([
			"animation" => "fade",
		], $options["uk-lightbox"]) : $options["uk-lightbox"];

		// If uk-thumbnav should be used
		if(array_key_exists("uk-thumbnav", $options) && $options["uk-thumbnav"] !== false) {

			$ukItemnav = "uk-thumbnav";

			// Set default uk-thumbnav options
			if(!is_array($options["uk-thumbnav"])) $options["uk-thumbnav"] = [];
			$options["uk-thumbnav"] = array_merge($ukItemnavOptions, $options["uk-thumbnav"]);
			$options["uk-thumbnav"]["items"] = "";

			// Set height for uk-thumbnav images
			if(!array_key_exists("height", $options["uk-thumbnav"])) $options["uk-thumbnav"]["height"] = 72;
		}

		// Automatically assign wrap ID if not passed
		if(!array_key_exists("id", $options)) {
			$options["id"] = implode("_", [
				"uk-slideshow",
				$images->getField()->name,
				$images->getPage()->id,
			]);
		}

		// Render the Pageimages
		for($i = 0; $i < $images->count(); $i++) {

			$image = $images->eq($i);

			// Convert caption markdown to HTML
			$desc = $sanitizer->entitiesMarkdown($image->description);

			// Remove tags for alt text
			$alt = $sanitizer->markupToText($desc);

			$items[] = $nb->wrap(

				$nb->img(
					$image->size($options["width"], $options["height"])->url,
					["alt" => $alt],
					["uk-img" => ["target" => "#$options[id]"]]
				),

				($ukLightbox ? $nb->attr([
					"href" => $image->url,
					"class" => ["uk-position-center"],
					"data-alt" => $alt,
					"data-caption" => (!empty($desc) ? $nb->wrap($desc, "div") : ""), // UIkit caption needs a div wrap to work
				], "a") : "")
			) .

			($image->description && $options["caption"] ? $nb->wrap(
				$desc,
				$options["caption"],
				"div"
			) : "");

			if($ukItemnav == "uk-thumbnav") {

				$options["uk-thumbnav"]["items"] .= $nb->wrap(
					$nb->wrap(
						$nb->img(
							$image->height($options["uk-thumbnav"]["height"])->url,
							[
								"alt" => $alt,
								"width" => false,
								"height" => $options["uk-thumbnav"]["height"],
							],
							["uk-img" => ["target" => "#$options[id]"]]
						),
						["href" => "#"],
						"a"
					),
					["uk-slideshow-item" => "$i"],
					"li"
				);
			}
		}

	} else if(!is_array($items)) {

		// No renderable items passed, return nothing
		return "";
	}

	return $nb->wrap(
		$nb->wrap(
			$nb->wrap(
				$nb->wrap($items, "li"),
				[
					"class" => ["uk-slideshow-items"],
					"data-uk-lightbox" => $ukLightbox,
					"data-uk-scrollspy" => $options["uk-scrollspy"],
				],
				"ul"
			) .
			($ukSlidenav ? ukSlidenav($ukSlidenav) : "") .
			($ukItemnav ? _ukItemnav($ukItemnav, $options[$ukItemnav]) : ""),
			["class" => $options["class"]],
			"div"
		),
		[
			"data-uk-slideshow" => $ukSlideshow,
			"id" => (isset($options["id"]) ? $options["id"] : "uk-slideshow"),
		],
		"div"
	);
}

/**
 * Render UIkit Tabs
 *
 * https://getuikit.com/docs/tab
 *
 * ~~~~~
 * // Display tabs with the 5th item active and a quick animation
 * echo ukTabs($page->items, [
 *     "active" => 5,
 *     "duration" => 128
 * ]);
 * ~~~~~
 *
 * @param array|PageArray $items The items to display in tabs.
 * @param array $options Options to modify behavior. The values set by default are:
 * - `active` (int): The index of the open item (default=0).
 * - `animation` (string): The type of animation used (default="uk-animation-fade").
 * - `duration` (int): The open/close animation duration in milliseconds (default=256).
 * @return string
 *
 */
function ukTabs($items, array $options = []) {

	$nb = nb();

	// Set default options
	$options = array_merge([
		"active" => 0,
		"animation" => "uk-animation-fade",
		"duration" => 256,
	], $options);

	// Convert to array if PageArray
	if($items instanceof PageArray) {
		$items = $items->explode("body", ["key" => "title"]);
	}

	$tabs = "";
	$contents = "";
	foreach($items as $title => $body) {
		$tabs .= $nb->wrap("<a href='#'>$title</a>", "li");
		$contents .= $nb->wrap($body, "li");
	}

	return $nb->wrap($tabs, ["data-uk-tab" => $options], "ul") .
		$nb->wrap($contents, ["class" => "uk-switcher"], "ul");
}

/**
 * Render a UIkit Thumbnav
 *
 * https://getuikit.com/docs/thumbnav
 *
 * ~~~~~
 * // Render a uk-thumbnav
 * echo ukThumbnav();
 * ~~~~~
 *
 * @param array $options
 * @return string
 * @see _ukItemnav()
 *
 */
function ukThumbnav(array $options = []) {
	return _ukItemnav("thumb", $options);
}

// Internal

/**
 * Render a UIkit item nav (uk-dotnav/uk-thumbnav)
 *
 * @param string $type dot/thumb.
 * @param array $options
 * - `items` (string): List items to populate.
 * - `class` (array): An array of classes.
 * - `vertical` (bool): Display vertically.
 * - `wrap` (string): A wrap element.
 * @return string
 *
 */
function _ukItemnav($type = "dot", array $options = []) {

	// Set default options
	$options = array_merge([
		"items" => "",
		"class" => [],
		"vertical" => false,
		"wrap" => "",
	], $options);

	$type = str_replace(["uk-", "nav"], "", $type);
	$options["class"][] = "uk-{$type}nav";
	if($options["vertical"]) $options["class"][] = "uk-{$type}nav-vertical";

	return nb()->wrap(
		nb()->wrap(
			$options["items"],
			["class" => $options["class"]],
			"ul"
		),
		$options["wrap"]
	);
}

/**
 * Renders a UIkit Nav item
 *
 * @param Page $item The child item being rendered.
 * @param Page $page The page the nav is being rendered on.
 * @param array $options Options to modify behavior.
 * @param bool $children Should the child pages be rendered?
 * @return string
 * @see ukNav()
 *
 */
function _ukNavItem(Page $item, Page $page, array $options = [], $children = true) {

	$nb = $page->wire("nb");

	$isActive = $item->id == $page->id || ($page->parents->has($item) && $item->id !== 1);

	$attr = array_merge([
		"class" => [],
	], $options["attr"]);

	if($isActive) $attr["class"][] = "uk-active";

	$out = $nb->wrap($item->title, ["href" => $item->url], "a");
	if($item->children->count() && !in_array($item->template->name, $options["exclude"]) && $children) {

		$attr["class"][] = "uk-parent";
		if($isActive) $attr["class"][] = "uk-open";

		$subOptions = $options;
		$subOptions["attr"] = $options["attrSubItems"];

		$o = $options["prependParent"] ? _ukNavItem($item, $page, $subOptions, false) : "";

		foreach($item->children as $child) {
			$o .= _ukNavItem($child, $page, $subOptions);
		}

		$out .= $nb->wrap($o, $attr, "ul");
	}

	return $nb->wrap($out, $attr, "li");
}
