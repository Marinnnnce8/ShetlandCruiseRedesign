<?php namespace ProcessWire;

/**
 * Search
 *
 */

// Get selectors
$selectors = ["sort" => "-date_sort"];

$type = $input->get->array("trip_type", "pageName");
$type = $sanitizer->minArray($type);
if(count($type)) $selectors["trip_type"] = $type;

$cats = $input->get->array("cats", "pageName");
$cats = $sanitizer->minArray($cats);
if(count($cats)) $selectors["cats"] = $cats;

$duration = $input->get->text("duration");
if($duration) {
	$duration = explode("|", $duration);
	$selectors["duration>="] = (int) $duration[0];
	$selectors["duration<="] = (int) $duration[1];
}

$port = $input->get->pageName("port");
if($port) $selectors["port"] = $port;

$minPrice = round(($page->min ?: 1) - 1);
$maxPrice = round(($page->max ?: 1) + 1);

$min = $input->get->float("min");
if($min && $min !== $minPrice) $selectors["price>="] = $min;

$max = $input->get->float("max");
if($max && $max !== $maxPrice) $selectors["price<="] = $max;

// Return JSON
if($config->ajax) {

	addUserData("searched", $sanitizer->minArray([
		"trip_type" => $type,
		"cats" => $cats,
		"duration" => $duration,
		"port" => $port,
		"min" => $min,
		"max" => $max,
	]));

	$nb->returnJson($pages->get(1058)->children($selectors), [
		"noResults" => __("Sorry, no activities were found."),
		"more" => $textMore,
	], function($page, $field) {
		$value = null;
		if($field == "info_days") {

			$val = $page->get($field);
			$n = array_sum($val->explode("id"));
			if($val->count() == 7) {
				$value = __("All Week");
			} else if($n == 7585) {
				$value = __("Weekdays only");
			} else if($n == 3041) {
				$value = __("Weekends only");
			} else {
				$value = $val->implode(" / ", "title");
			}
		}
		return $value;
	});
}

// Text and default values
$textAny = __("Any");
$textFind = __("Find Activities");
$page->h1 = sprintf(__("%s for"), $textFind) . " " .
	$nb->wrap(__("Your Shetland Cruise"), "span");

$types = $pages->get(1085)->children();
foreach($types as $p) {
	if(!$pages->count("template=activity,trip_type=$p")) $types->remove($p);
}
$ports = $pages->get(1049)->children();
foreach($ports as $p) {
	if(!$pages->count("template=activity,port=$p")) $ports->remove($p);
}
$cats = $pages->get(1755)->children();
foreach($cats as $p) {
	if(!$pages->count("template=activity,cats=$p")) $cats->remove($p);
}

?><div pw-replace='page-<?= $page->template->name ?>'>

	<div class='uk-section uk-section-small uk-padding-remove-top'>
		<div class='uk-container'>

			<div class='uk-margin uk-background-muted search-form-wrapper'>
				<form class='uk-form-stacked search-form' method='post' id='search-form'>
					<div class='filters'>
						<div data-uk-grid>
							<div class='uk-width-1-2@m'>
								<div data-uk-grid>
									<div class='uk-width-1-2@s uk-width-1-2@m' data-filter>
										<h4 class='section-subtitle'><?= __("Activities") ?></h4>

										<label class='uk-form-label' for='trip_type_any'>
											<?= $nb->attr([
												"type" => "checkbox",
												"name" => "trip_type[]",
												"id" => "trip_type_any",
												"value" => "",
												"class" => "uk-checkbox",
												"checked" => true,
												"data-filter-text" => __("Any Activity"),
											], "input") . $textAny ?>
										</label>
										<?= $types->each($nb->wrap(
											$nb->attr([
												"type" => "checkbox",
												"name" => "trip_type[]",
												"id" => "trip_type_{name}",
												"value" => "{name}",
												"class" => "uk-checkbox",
												"data-filter-text" => "{title}",
											], "input") . "{title}",
											["for" => "trip_type_{name}", "class" => "uk-form-label"],
											"label"
										)) ?>
									</div>

									<?php /*<div class='uk-width-1-2@s uk-width-1-2@m' data-filter>
										<h4 class='section-subtitle'><?= __("Port of Arrival") ?></h4>
										<?= $ports->each($nb->wrap(
											$nb->attr([
												"type" => "radio",
												"name" => "port",
												"id" => "port_{name}",
												"value" => "{name}",
												"class" => "uk-radio",
												"data-filter-text" => "{title}",
											], "input") . "{title}",
											["for" => "port_{name}", "class" => "uk-form-label"],
											"label"
										)) ?>
									</div> */ ?>

									<div class='uk-width-1-2@m' data-filter>
										<h4 class='section-subtitle'><?= __("Categories") ?></h4>

										<label class='uk-form-label' for='cats_any'>
											<?= $nb->attr([
												"type" => "checkbox",
												"name" => "cats[]",
												"id" => "cats_any",
												"value" => "",
												"class" => "uk-checkbox",
												"checked" => true,
												"data-filter-text" => __("Any Category"),
											], "input") . $textAny ?>
										</label>
										<?= $cats->each($nb->wrap(
											$nb->attr([
												"type" => "checkbox",
												"name" => "cats[]",
												"id" => "cats_{name}",
												"value" => "{name}",
												"class" => "uk-checkbox",
												"data-filter-text" => "{title}",
											], "input") . "{title}",
											["for" => "cats_{name}", "class" => "uk-form-label"],
											"label"
										)) ?>
									</div>
								</div>
							</div>
							

							<div class='uk-width-1-2@m'>
								<h4 class='section-subtitle'><?= __("Activity Duration") ?></h4>

								<div class='uk-form-controls uk-flex uk-flex-wrap uk-flex-between' data-filter><?php

									echo $nb->attr([
											"type" => "radio",
											"name" => "duration",
											"id" => "duration_any",
											"value" => "",
											"class" => "c-radio-control",
											"checked" => true,
											"data-filter-text" => __("Any Duration"),
										], "input") .
										$nb->wrap($nb->wrap($textAny, "span"), [
											"class" => "custom-radio",
											"for" =>  "duration_any",
										], "label");

									foreach([
										"0 - 2h" => "0|119",
										"2h - 4h" => "120|239",
										"4h - 8h" => "240|480",
									] as $title => $value) {
										$name = $sanitizer->pageName($title);
										echo $nb->attr([
											"type" => "radio",
											"name" => "duration",
											"id" => "duration_$name",
											"value" => $value,
											"class" => "c-radio-control",
											"data-filter-text" => "$title Duration",
										], "input") .
										$nb->wrap($nb->wrap($title, "span"), [
											"class" => "custom-radio",
											"for" =>  "duration_$name",
										], "label");
									}

								?></div>

								<h4 class='section-subtitle'><?= __("Price (Per Person)") ?></h4>

								<div class='uk-form-controls range-input'>
									<input type='text' name='min' id='min' title="Minimum price" class='uk-input min' data-value='<?= $minPrice ?>' value='<?= $minPrice ?>'>
									<input type='text' name='max' id='max' title="Maximum price" class='uk-input max' data-value='<?= $maxPrice ?>' value='<?= $maxPrice ?>'>
									<div class='slider uk-margin-top'></div>
								</div>
							</div>
						</div>
					</div>

					<div class='uk-padding uk-background-grey uk-text-center'>
						<button type='submit' class='uk-button uk-button-primary push-icon-right uk-button-wide uk-button-large'><?= $textFind ?><i class='icon-search-1'></i></button>
					</div>
				</form>
			</div>

			<h4 class='results uk-margin-remove-top'><?= __("Showing results for:") ?></h4>

			<div data-uk-grid>
				<div class='uk-width-2-3@l'>
					<div class='active-filters'></div>
				</div>
				<div class='uk-width-1-3@l'>
					<div class='control-buttons'>
						<button type='button' class='uk-button uk-button-default push-icon-right uk-hidden' data-filter-clear><?= __("Clear All") ?><i class='fas fa-times'></i></button>
						<button class='uk-button uk-button-primary push-icon-right search-form-toggler' data-uk-toggle='<?= json_encode([
							"target" => ".search-form-wrapper",
							"animation" => "uk-animation-slide-top-small",
						]) ?>'>
							<span class='h'><?= __("Hide") ?></span>
							<span class='s'><?= __("Show") ?></span>
							<?= __("Search Form") ?><i class='fas fa-angle-up'></i>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class='uk-section uk-background-muted uk-padding-xlarge-bottom'>
		<div class='uk-container'>
			<div class='section-header'>
				<div class='uk-flex uk-flex-wrap uk-flex-between'>
					<p><?= $textShortlist ?></p>
				</div>
			</div>

			<?= $nb->getJson([
				"id" => "activity-results",
				"more" => __("Load More Activities"), //@todo
			]) ?>
		</div>
	</div>
</div>
