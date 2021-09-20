<?php namespace ProcessWire;

/**
 * Site Functions
 *
 * @copyright 2019 NB Communication Ltd
 *
 */

function updateSorts($pages, $now = 0) {
	foreach($pages as $p) {
		$p->of(false);
		$p->date_sort = ($now ?: $p->date_sort + (2048 * nb()->random->integer(1, 32))) - $p->id;
		pages()->___save($p, [
			"noHooks" => true,
			"quiet" => true,
		]);
	}
}

function appendSection($title, $body = "", $appendTitle = "") {
	$nb = nb();
	return $nb->wrap(
		$nb->wrap(
			$nb->wrap(
				$nb->wrap(
					renderHeading($title, 2, ["section-title", "smaller"]) . $appendTitle,
					"uk-flex uk-flex-wrap uk-flex-between"
				),
				"<div class='section-header'>"
			) .
			$body,
			"uk-container"
		),
		"uk-section uk-section-large"
	);
}

function getDays($items) {
	$sum = array_sum($items->explode("id"));
	if($sum == 10626) {
		$items = __("Every Day");
	} else if($sum >= 7585) {
		$items = $items->first->title . " - " . $items->last->title;
	} else {
		$items = $items->implode(", ", "title");
	}
	return $items;
}

function renderAttractions(PageArray $items, array $options = []) {

	$nb = nb();

	$nb->removeJsonField("url");
	$nb->addJsonFields(["address", "tel", "email", "link", "port"]);

	$out = $nb->renderJson($items, array_merge([
		"action" => "items",
		"config" => [
			"more" => __("View Website"),
		],
		"pageToArray" => [
			"type_transport" => "name",
			"address" => "Text",
		],
	], $options), function($page, $field, $type, $property) {
		$value = null;
		$v = $page->get($field);
		switch($field) {
			case "address":
				$value = str_replace("\n", ", ", $v);
				break;
			case "tel":
				$value = nbTel($v);
				break;
			case "email":
				$value = nbMailto($v);
				break;
		}
		return $value;
	});

	$nb->addJsonField("url");
	foreach(["address", "tel", "email", "link", "port"] as $f) $nb->removeJsonField($f);

	return $out;
}

function addUserData($tpl = null, $value = null) {

	$page = page();
	$session = session();
	if(is_null($tpl)) $tpl = $page->template->name;

	if($session->cruiseUser) {
		$data = json_decode($session->cruiseUser, 1);
	} else {
		$data = [
			"init" => time(),
			"landing" => $tpl,
		];
	}

	if(!array_key_exists($tpl, $data)) $data[$tpl] = [];
	$data[$tpl][time()] = isset($value) ? $value : $page->id;

	$session->cruiseUser = json_encode($data);
}

function renderSocial($str) {

	$nb = nb();

	foreach(explode("\n", $str) as $url) {

		$type = explode(".", str_replace(["www.", "/"], "", explode("//", $url)[1]))[0];
		$prefix = !in_array($type, ["facebook", "twitter", "youtube", "linkedin", "instagram"]) ? "fas" : "fab";
		$icon = $type == "facebook" ? "$type-square" : ($prefix == "fas" ? "link" : $type);

		$social[] = $nb->wrap(faIcon($icon, $prefix), [
			"href" => $url,
			"target" => "_blank",
			"class" => [
				"social-link",
				"social-$type",
			],
		], "a");
	}

	return $nb->wrap($nb->wrap($social, "div"), "uk-flex uk-flex-middle uk-margin-small-top");
}

function renderButtonLink($url, $title, $large = false) {
	return nb()->wrap(faIcon("arrow-circle-right") . $title, [
		"href" => $url,
		"class" => [
			"uk-button",
			"uk-button-default",
			"button-ghost",
			"push-icon-right" . ($large ? " uk-button-large" : ""),
		]
	], "a");
}

function renderMap($markers = [], array $options = []) {

	if($markers instanceof Page) {

		$page = $markers;
		if($page->lat && $page->lng) {

			$markers = [[
				"position" => [
					"lat" => $page->lat,
					"lng" => $page->lng,
				],
				"title" => $page->title,
			]];

			$options = [
				"zoom" => ($page->zoom ?: 10),
				"center" => [
					"lat" => $page->lat,
					"lng" => $page->lng,
				],
			];
		} else {
			return;
		}
	}

	return nb()->attr([
		"class" => "map-container",
		"id" => "map",
		"data-map" => [
			"options" => array_merge([
				"zoom" => (count($markers) > 4 ? 8 : 7),
				"center" => [
					"lat" => 60.18095961928664,
					"lng" => -1.229497460937523,
				],
			], $options),
			"marker" => $markers,
		],
	], "div", true);
}

function renderPorts(PageArray $items) {

	$nb = nb();
	$grid = [
		"class" => "uk-grid-xsmall",
		"data-uk-grid" => true,
	];

	$out = "";
	$markers = [];
	$c = $items->count();
	foreach($items as $p) {

		$out .= $nb->wrap(
			$nb->wrap(
				$nb->imgBg($p->getImage(["height" => 0]), ["class" => "media uk-background-cover"]) . "</div>" .
				$nb->wrap(
					"<i class='icon-anchor large-icon'></i>" .
					renderHeading($p->title, 3, ["entry-title"]),
					"uk-overlay uk-position-left uk-position-cover"
				) .
				$nb->attr(["href" => $p->url, "class" => "read-more", "aria-label" => "Read more"], "a", true),
				"uk-light location-entry"
			),
			[
				"class" =>
				"uk-width-1-" . ($c % 2 && $p->index == $c - 1 ? "1" : "2@s"),
				"data-uk-scrollspy" => [
					"delay" => ($p->index % 2) * 128,
					"cls" => "uk-animation-slide-bottom-small",
				],
			],
			"div"
		);

		if($p->lat && $p->lng) {
			$markers[] = [
				"position" => [
					"lat" => $p->lat,
					"lng" => $p->lng,
				],
				"title" => $p->title,
			];
		}
	}

	return $nb->wrap(
		$nb->wrap($nb->wrap($out, $grid, "div"), "uk-width-2-3@l") .
		$nb->wrap(renderMap($markers), "uk-width-1-3@l"),
		$grid,
		"div"
	);
}

function getAwardImage(Pageimage $img, $size = 80, $alt = "") {
	if($img->width > $size) $img = $img->width($size);
	if($img->height > $size) $img = $img->height($size);
	return nb()->img($img->url, ["alt" => $alt, "width" => $img->width]);
}

function getAwards(Page $page, $size = 80) {

	$awards = [];

	foreach($page->awards->find("logo!=") as $p) {
		$awards[] = nb()->wrap(
			getAwardImage($p->logo, $size, $p->title),
			[
				"href" => ($p->link ?: false),
				"target" => ($p->link ? "_blank" : false),
			],
			($p->link ? "a" : "span")
		);
	}

	foreach($page->gallery as $img) {
		$awards[] = nb()->wrap(
			getAwardImage($img, $size, $img->description),
			"span"
		);
	}

	return $awards;
}

function renderAwards(array $awards) {
	return nb()->wrap(
		nb()->wrap($awards, "li"),
		[
			"class" => [
				"award-list",
				"uk-child-width-1-2",
				"uk-text-center",
				"uk-grid-small",
			],
			"data-uk-grid" => true,
		],
		"ul"
	);
}

function widgetHeading($title) {
	return renderHeading($title, 4, ["widget-title"]);
}

function widgetSubtitle($title, $icon) {
	return infoHeading($title, $icon, "widget-subtitle");
}

function infoHeading($title, $icon, $class) {
	return renderHeading((nb()->isTag($icon) ? $icon :
		faIcon(str_replace("fa-", "", $icon), "fas", ["fw"])) . $title, 4, [$class]);
}

/**
 * Render a Font Awesome icon
 *
 * A full list of available icons can be found here: https://fontawesome.com/icons
 *
 * ~~~~~
 * // Display a large (3x) user icon, fixed width
 * echo faIcon("user", "fas", [
 *     "3x",
 *     "fw",
 * ]);
 * ~~~~~
 *
 * @param string $icon The icon to be displayed.
 * @param string $prefix The icon prefix (default="fas").
 * @param array|string $classes Any Font Awesome classes that should be added to the icon (e.g. "lg", "fw", "spin").
 * @return string
 *
 */
function faIcon($icon, $prefix = "fas", $classes = []) {

	$pre = "fa-";
	$class = [
		$prefix,
		$pre . $icon,
	];

	if(!is_array($classes)) $classes = [$classes];
	foreach($classes as $cls) {
		$class[] = (strpos($cls, $pre) === false ? $pre : "") . $cls;
	}

	return nb()->attr([
		"class" => $class,
		"aria-hidden" => "true",
	], "i", true);
}

/**
 * Return a numerical value as a price
 *
 * ~~~~~
 * // 1 million dollars!
 * echo formatMoney(1000000, "$");
 * ~~~~~
 *
 * @param int|float $value The money value to be formatted.
 * @param string $prefix The currency prefix.
 * @return string
 *
 */
function formatMoney($value, $prefix = "£") {
	return htmlentities($prefix) . str_replace(".00", "", (string) number_format(($value ?: 0), 2));
}

/**
 * Return a telephone number for the href attribute
 *
 * ~~~~~
 * // Output NB Communication's telephone number as a link
 * $tel = "+44 01595 696155";
 * echo "<a href='tel:" . formatTelHref($tel) . "'>$tel</a>";
 * ~~~~~
 *
 * @param string $tel The telephone number to be formatted.
 * @return string
 *
 */
function formatTelHref($tel) {
	// If in international format, remove the bracketed zero
	if(strpos($tel, "(0)") !== false && strpos($tel, "+") !== false) {
		$tel = str_replace("(0)", "", $tel);
	}
	return trim(preg_replace("/[^0-9]/", "", $tel));
}

/**
 * Return the Page content
 *
 * This method assumes that a Page will have either
 * a "blocks" or "body" field present. It isn't looking anywhere else.
 *
 * ~~~~~
 * // Output the page content
 * echo getContent($page);
 * ~~~~~
 *
 * @param Page $page The `Page` to be queried.
 * @return string
 *
 */
function getContent(Page $page) {

	$out = "";
	if($page->hasField("blocks") && $page->blocks->count()) {
		$out .= $page->render->blocks;
	} else if($page->hasField("body") && $page->body) {
		$out .= nbContent($page->body);
	}

	return $out;
}

/**
 * Return a formatted Page introduction
 *
 * ~~~~~
 * // Output the Page intro
 * echo getIntro($page);
 * ~~~~~
 *
 * @param Page $page The `Page` to be queried
 * @param array|string $options Options to modify behaviour:
 * - `field` (string): The name of the intro field (default="intro").
 * - `nl2br` (bool): Should newlines be converted to <br> tags? (default=true).
 * - `wrap` (bool|string): HTML wrapper.
 * @return string
 *
 */
function getIntro(Page $page, $options = []) {

	// Shortcut
	if(!is_array($options)) $options = ["wrap" => $options];

	// Set default options
	$options = array_merge([
		"field" => "intro",
		"nl2br" => true,
		"wrap" => "<p class='uk-text-lead'>",
	], $options);

	$value = $page->get($options["field"]);
	if($value) {
		if($options["nl2br"]) $value = nl2br($value);
		if($options["wrap"]) $value = $page->wire("nb")->wrap($value, $options["wrap"]);
	}

	return $value;
}

/**
 * Get related pages (children/siblings)
 *
 * @param Page $page
 * @return PageArray
 *
 */
function getRelatedPages(Page $page) {
	return $page->children->count() ? $page->children : $page->siblings("id!=$page->id");
}

/**
 * Render a list of Pagefiles
 *
 * ~~~~~
 * // Render the files, but without the filesize string
 * echo renderFiles($page->files, [
 *     "size" => false,
 * ]);
 * ~~~~~
 *
 * @param Pagefiles $files The files to be rendered.
 * @param array $options Options to modify behaviour:
 * - `attributes` (array): An array of attributes to be added to the `<ul>` element.
 * - `download` (bool): Force download of the files.
 * - `size` (bool): Append the filesize.
 * @return string
 *
 */
function renderFiles(Pagefiles $files, array $options = []) {

	$nb = $files->wire("nb");

	// Set default options
	$options = array_merge([
		"attr" => [],
		"download" => true,
		"size" => true,
	], $options);

	$items = [];
	foreach($files as $file) {
		$items[] = $nb->wrap(
			($file->description ? $file->description : $file->basename) .
			($options["size"] ? " " . $nb->wrap("($file->filesizeStr)", "small") : ""),
			[
				"href" => $file->url,
				"download" => $options["download"],
				"target" => ($options["download"] ? false : "_blank"),
			],
			"a"
		);
	}

	return count($items) ? $nb->wrap(
		$nb->wrap($items, "li"),
		$options["attr"],
		"ul"
	) : "";
}

/**
 * Render an HTML heading
 *
 * ~~~~~
 * // Output `<h3>Heading</h3>`
 * echo renderHeading("Heading"); // h3 is the default
 * ~~~~~
 *
 * @param string $title
 * @param int $heading
 * @param array $attr
 * @return string
 *
 */
function renderHeading($title, $heading = 3, array $attr = []) {
	if(count($attr) && nb()->isSeq($attr)) $attr = ["class" => $attr];
	return $title ? nb()->wrap($title, $attr, "h$heading") : "";
}

/**
 * Render pages or related pages (children/siblings)
 *
 * @param Page|PageArray $page
 * @param array $options The JSON options.
 * @return string
 * @see NbWire::renderJson()
 *
 */
function renderItems($page, array $options = []) {

	// Set default options
	$options = array_merge([
		"action" => "items",
		"fields" => [
			"image" => "url",
		],
	], $options);

	$pages = $page instanceof PageArray ? $page : getRelatedPages($page);

	return $pages->count() ? $page->wire("nb")->renderJson($pages, $options) : "";
}

/**
 * Convert markdown to html
 *
 * ~~~~~
 * // Example
 * echo renderMarkdown("Testing **1 2 3**");
 * // Returns `Testing <strong>1 2 3</strong>`
 * ~~~~~
 *
 * @param string $str The markdown string to convert.
 * @param bool $textVersion If true, an array is returned with both html and text versions (default=false).
 * @return array|string
 *
 */
function renderMarkdown($str, $textVersion = false) {
	$html = sanitizer()->entitiesMarkdown($str);
	return ($textVersion ? [
		$html,
		sanitizer()->markupToText($html), // Remove tags for alt text
	] : $html);
}
