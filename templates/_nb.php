<?php namespace ProcessWire;

/**
 * NB Functions
 *
 * @copyright 2019 NB Communication Ltd
 *
 */

/**
 * Wrap a string in a nb-block
 *
 * ~~~~~
 * // Output the $page content wrapped in a block
 * echo nbBlock($page->body);
 * ~~~~~
 *
 * @param string $str The string to be wrapped.
 * @param string $type The block type (default="content").
 * @param array $attr An array of attributes for the block.
 * @return string
 *
 */
function nbBlock($str = "", $type = "content", array $attr = []) {

	// Set default attributes
	$attr = array_merge([
		"class" => [],
		"data-nb-block" => $type,
	], $attr);

	// Set default classes
	$attr["class"] = array_merge([
		"nb-block",
		"nb-$type",
	], $attr["class"]);

	return nb()->wrap($str, $attr, "div");
}

/**
 * Render the Page content
 *
 * ~~~~~
 * // Render the page content
 * echo nbContent($page->body);
 * ~~~~~
 *
 * @param string $content
 * @return string
 *
 */
function nbContent($content) {
	return nbBlock($content, "content");
}

/**
 * Render a gallery
 *
 * This function evalues the number of images and the perRow number
 * specified, to return the 'best fit'. For example, if there are
 * 4 images, and perRow is set to 3, the first row will be a single image,
 * resized by width. The next row will be the remaining 3, resized by
 * height, allowing them to be displayed side-by-side without unnecessary whitespace.
 *
 * ~~~~~
 * // Display a gallery of square images, four per row
 * echo nbGallery($page->gallery, [
 *     "height" => 480,
 *     "width" => 480,
 *     "perRow" => 4,
 * ]);
 * ~~~~~
 *
 * @param Pageimages $images The images to be rendered.
 * @param array $options Options to modify behavior:
 * - `perRow` (int): Number of images per row (default=3).
 * - `width` (int): Crop width (default=`$nb->width`).
 * - `height` (int): Crop height (default=`$nb->height`).
 * - `uk-lightbox` (array|true): UIkit Lightbox options: https://getuikit.com/docs/lightbox#component-options.
 * - `uk-scrollspy` (array|false): UIkit Scrollspy options: https://getuikit.com/docs/scrollspy#component-options.
 * - `block` (bool): Wrap with `nbBlock()`? (default=false).
 * @return string
 *
 */
function nbGallery(Pageimages $images, array $options = []) {

	$nb = $images->wire("nb");
	$sanitizer = sanitizer();

	// Set default options
	$options = array_merge([
		"perRow" => 3,
		"width" => $nb->width,
		"height" => $nb->height,
		"uk-lightbox" => [],
		"uk-scrollspy" => [
			"cls" => "uk-animation-fade",
			"delay" => 128,
			"target" => "> .nb-gallery-row > .nb-gallery-image",
		],
		"block" => false,
	], $options);

	$c = $images->count();
	$out = "";
	if($c) {

		// Get our increment value
		$remainder = $c % $options["perRow"];
		$increment = $remainder ? $remainder : $options["perRow"];

		// Cycle through images and create our gallery rows
		for($y = 0; $y < $c; $y += $increment) {

			if($y == $increment) $increment = $options["perRow"];

			$items = "";
			for($x = 0; $x < $increment; $x++) {

				$index = $x + $y;
				$image = $images->eq($index);

				if(isset($image)) {

					// Get thumbnail
					// Resize by width if the first/single image else height
					$thumb = $remainder == 1 && $y == 0 ? $image->width($options["width"]) : $image->height($options["height"]);

					// Convert caption markdown to HTML
					$desc = $sanitizer->entitiesMarkdown($image->description);

					// If a single image and a description has been specified
					// If the image doesn't already have a description
					// Set the image description to the specified description
					if(array_key_exists("desc", $options) && ($y + $x + $c) == 1 && empty($desc)) {
						$desc = $options["desc"];
					}

					// Remove tags for alt text
					$alt = $sanitizer->markupToText($desc);

					$items .= $nb->wrap(
						$nb->img($thumb->url, [
							"alt" => $alt,
							"width" => $thumb->width,
							"height" => $thumb->height,
						]),
						[
							"href" => $image->url,
							"class" => "nb-gallery-image",
							"data-alt" => $alt,
							"data-caption" => (!empty($desc) ? $nb->wrap($desc, "div") : ""), // UIkit caption needs a div wrap to work
						],
						"a"
					);
				}
			}

			$out .= $nb->wrap($items, "nb-gallery-row");
		}

		// Render the gallery
		$out = $nb->wrap($out, [
			"class" => "nb-gallery-images",
			"data-uk-lightbox" => (is_array($options["uk-lightbox"]) ? array_merge([
				"animation" => "fade",
			], $options["uk-lightbox"]) : true),
			"data-uk-scrollspy" => $options["uk-scrollspy"],
		], "div");
	}

	return $options["block"] ? nbBlock($out, "gallery") : $out;
}

/**
 * Render the email address as a jQuery obfuscated nb-mailto link
 *
 * ~~~~~
 * // Output a mailto link, with a Font Awesome icon
 * echo nbMailto("tester@nbcommunication.com", [
 *     "icon" => "fa-envelope",
 * );
 * ~~~~~
 *
 * @param string $email The email address.
 * @param array $data Other data to be processed by javascript.
 * @param array $attr Other attributes to be rendered.
 * @return string
 *
 */
function nbMailto($email = "", array $data = [], array $attr = []) {
	$e = explode("@", $email);
	return count($e) == 2 ? nb()->attr(array_merge($attr, [
		"data-nb-mailto" => array_merge($data, [
			"id" => $e[0],
			"domain" => $e[1],
		]),
	]), "a", true) : "";
}

/**
 * Render a phone number as a nb-tel link
 *
 * It doesn't actually return a 'href' link, but a data-nb-tel one, which
 * the `$nb` javascript turns into a `href='tel:{tel}'` link. This is to prevent
 * scrapers from harvesting numbers from tel: href values.
 *
 * ~~~~~
 * // Output the client's telephone number as a mailto link, with an icon
 * echo nbTel($nb->clientTel, [
 *     "icon" => fa-phone",
 * ]);
 * ~~~~~
 *
 * @param string $tel The telephone number.
 * @param array $data Other data to be processed by javascript.
 * @param array $attr Other attributes to be rendered.
 * @return string
 *
 */
function nbTel($tel = "", array $data = [], array $attr = []) {
	return !empty($tel) ? nb()->attr(array_merge($attr, [
		"data-nb-tel" => array_merge($data, [
			"tel" => $tel,
			"href" => formatTelHref($tel),
		]),
	]), "a", true) : "";
}

/**
 * Return a url with or without the protocol
 *
 * This is primarily for returning urls without http:// or https://.
 * If protocol is set to true, then the url is only run through $sanitizer->url().
 *
 * ~~~~~
 * // Output NB Communication's URL without the protocol
 * echo nbUrl("https://www.nbcommunication.com/");
 * ~~~~~
 *
 * @param string $url The URL to be processed.
 * @param bool $protocol Should the protocol be displayed.
 * @return string
 *
 */
function nbUrl($url = "", $protocol = false) {

	$url = sanitizer()->url($url);
	return $protocol ? $url : trim(str_replace([
		"https",
		"http",
	], "", str_replace("://", "", $url)), "/");
}

/**
 * Render the site copyright message
 *
 * A launch date should be set in Setup > Site
 *
 * @return string
 *
 */
function nbCopyright() {

	$nb = nb();
	$thisYear = date("Y");
	$year = $nb->launchDate ? date("Y", $nb->launchDate) : $thisYear;

	return implode(" ", [
		__("Copyright") . " &copy;",
		(date("Y") !== $year ? "$year - " : "") . $thisYear,
		$nb->clientName . ".",
		__("All rights reserved.")
	]);
}

/**
 * Render the NB watermark
 *
 * @param string $image The image to be used.
 * @return string
 *
 */
function nbWatermark($image = "nb.png") {
	$nb = nb();
	return $nb->wrap(
		__("Website by") . " " .
		$nb->wrap(
			"NB " .
			$nb->img("https://brand.nbcommunication.com/logo/web/{$image}", [
				"alt" => "NB Communication Ltd Logo",
				"width" => 0,
				"height" => 0,
			]),
			$nb->attr([
				"href" => "https://www.nbcommunication.com/",
				"title" => "NB Communication - Digital Marketing Agency",
			], "a")
		),
		["class" => "nb-credit"],
		"div"
	);
}
