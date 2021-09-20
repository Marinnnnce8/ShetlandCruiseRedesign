<?php namespace ProcessWire;

/**
 * Submission Form
 *
 * @copyright 2019 NB Communication Ltd
 *
 */

// Set the `to` email address
$to = "melissastewartfreelance@gmail.com";
$to = "cruise@promoteshetland.com";

$nbUpload = $modules->get("NbWireUpload");
$nbUpload->setConfig([
	"list-items" => false,
	"description" => true,
]);

// Is reCAPTCHA to be used?
$captcha = $modules->isInstalled("MarkupGoogleRecaptcha") ? $modules->get("MarkupGoogleRecaptcha") : null;

$wrapFull = "uk-width-1-1";
$wrapHalf = "uk-width-1-2@s";

$providers = ["" => "New Provider"];
foreach($pages->get(1061)->children("include=all") as $p) $providers[$p->id] = $p->title;

$thumbText = __("The first image will be used as the thumbnail image on search listings.");

// Create the form
$form = $nb->form([
	"class" => [
		"uk-form-stacked",
	],
	"fields" => [
		[
			"type" => "markup",
			"name" => "heading_provider",
			"value" => renderHeading(__("Tour Provider"), 4, ["uk-margin-remove-bottom"]) .
				$nb->wrap(
					__("Please provide the following information about your business (this should be the business offering the trips/tours/workshops, not specific information about the tour itself)."),
					"uk-text-small"
				),
			"attr" => ["data-provider-field" => true, "title" => "Optional"],
			"wrapClass" => $wrapFull,
		],
		[
			"type" => "select",
			"name" => "select_provider",
			"label" => __("Provider"),
			"wrapClass" => $wrapFull,
			"notes" => __("Please select a provider name above or enter new provider details below."),
			"options" => $providers,
		],
		[
			"name" => "provider_title",
			"label" => __("Name of your business"),
			"requiredLabel" => __("Please enter the business name."),
			"attr" => ["data-provider-field" => true],
			"wrapClass" => $wrapFull,
		],
		[
			"field" => "logo",
			"attr" => ["data-provider-field" => true, "title" => "Optional"],
			"wrapClass" => "uk-width-1-1",
		],
		[
			"type" => "textarea",
			"name" => "provider_intro",
			"label" => __("Describe what service your business provides"),
			"requiredLabel" => __("Please enter a business description."),
			"rows" => 2,
			"attr" => ["data-provider-field" => true],
			"wrapClass" => $wrapFull,
		],
		[
			"field" => "images",
			"attr" => ["data-provider-field" => true, "title" => "Optional"],
			"notes" => __("Please supply good quality landscape images - a maximum of 4 - to illustrate your business.") . 
				"\n$thumbText",
			"wrapClass" => "uk-width-1-1",
		],
		[
			"type" => "textarea",
			"name" => "provider_address",
			"label" => __("Address"),
			"requiredLabel" => __("Please enter the provider's address."),
			"rows" => 3,
			"attr" => ["data-provider-field" => true],
			"wrapClass" => $wrapFull,
		],
		[
			"name" => "provider_tel",
			"label" => __("Telephone"),
			"attr" => ["type" => "tel", "data-provider-field" => true, "title" => "Optional"],
			"wrapClass" => $wrapHalf,
		],
		[
			"type" => "email",
			"name" => "provider_email",
			"label" => __("Email Address"),
			"requiredLabel" => __("Please enter a valid email address."),
			"attr" => ["data-provider-field" => true],
			"wrapClass" => $wrapHalf,
		],
		[
			"name" => "provider_link",
			"label" => __("Website"),
			"attr" => ["data-provider-field" => true, "title" => "Optional"],
			"wrapClass" => $wrapHalf,
		],
		[
			"name" => "provider_link_ta",
			"label" => __("Tripadvisor Link"),
			"attr" => ["data-provider-field" => true, "title" => "Optional"],
			"wrapClass" => $wrapHalf,
		],
		[
			"type" => "textarea",
			"name" => "provider_social",
			"label" => __("Social Links"),
			"notes" => __("Please enter each link on a new line."),
			"rows" => 2,
			"attr" => ["data-provider-field" => true, "title" => "Optional"],
			"wrapClass" => $wrapFull,
		],
		[
			"type" => "checkboxes",
			"name" => "provider_awards",
			"label" => __("Awards and accreditations"),
			"wrapClass" => $wrapFull,
			"prependMarkup" => $nb->attr(["data-provider-field" => true, "title" => "Optional"], "span", true),
			"options" => $pages->get(1120)->children()->explode("title", ["key" => "id"]),
			"optionColumns" => 1,
		],
		[
			"field" => "gallery",
			"label" => __("Additional Award Images"),
			"attr" => ["data-provider-field" => true, "title" => "Optional"],
			"wrapClass" => "uk-width-1-1",
		],
		// Tour Details
		[
			"type" => "markup",
			"name" => "heading_activity",
			"value" => "<hr>" . renderHeading(__("Tour Details"), 4, ["uk-margin-remove-bottom"]),
			"wrapClass" => $wrapFull,
		],
		[
			"name" => "activity_title",
			"label" => __("Name of your tour/excursion"),
			"required" => true,
			"requiredLabel" => __("Please enter the name of the activity."),
			"wrapClass" => "uk-width-2-3@s",
		],
		[
			"type" => "select",
			"name" => "activity_trip_type",
			"label" => __("Type"),
			"required" => true,
			"requiredLabel" => __("Please select the type of the activity."),
			"wrapClass" => "uk-width-1-3@s",
			"options" => $pages->get(1085)->children()->explode("title", ["key" => "id"]),
		],
		[
			"name" => "activity_info_departure",
			"label" => __("Departure Point"),
			"required" => true,
			"requiredLabel" => __("Please enter the departure point."),
			"placeholder" => "e.g. Lerwick Tourist Office",
			"wrapClass" => $wrapHalf,
		],
		[
			"type" => "select",
			"name" => "activity_port",
			"label" => __("Closest Port"),
			"required" => true,
			"requiredLabel" => __("Please select the closest port for this activity."),
			"wrapClass" => $wrapHalf,
			"options" => $pages->get(1049)->children()->explode("title", ["key" => "id"]),
		],
		[
			"name" => "activity_info_times",
			"label" => __("Times"),
			"required" => true,
			"requiredLabel" => __("Please enter tour times"),
			"placeholder" => "e.g. 9am to 3:30pm",
			"wrapClass" => $wrapHalf,
		],
		[
			"name" => "activity_duration",
			"label" => __("Duration (Minutes)"),
			"placeholder" => "e.g. 360",
			"wrapClass" => $wrapHalf,
		],
		[
			"type" => "checkboxes",
			"name" => "activity_info_days",
			"label" => __("Days of the week you operate"),
			"required" => true,
			"requiredLabel" => __("Please select the days of the week you operate."),
			"wrapClass" => $wrapFull,
			"options" => $pages->get(1514)->children()->explode("title", ["key" => "id"]),
		],
		[
			"name" => "activity_info_season",
			"label" => __("Season (to/from dates) "),
			"required" => true,
			"requiredLabel" => __("Please enter the season dates you operate within."),
			"placeholder" => "e.g. 1 April - 30 September",
			"wrapClass" => $wrapFull,
		],
		[
			"name" => "activity_info_prices",
			"label" => __("Price"),
			"placeholder" => "e.g. From £55 per person",
			"required" => true,
			"requiredLabel" => __("Please enter price details."),
			"wrapClass" => $wrapHalf,
		],
		[
			"type" => "select",
			"name" => "activity_info_level",
			"label" => __("Activity Level"),
			"required" => true,
			"requiredLabel" => __("Please select the activity level."),
			"wrapClass" => $wrapHalf,
			"options" => $pages->get(1510)->children()->explode("title", ["key" => "id"]),
		],
		[
			"type" => "checkboxes",
			"name" => "activity_cats",
			"label" => __("Categories"),
			"wrapClass" => $wrapFull,
			"options" => $pages->get(1755)->children()->explode("title", ["key" => "id"]),
		],
		[
			"type" => "textarea",
			"name" => "activity_info_access",
			"label" => __("Accessibility"),
			"required" => true,
			"requiredLabel" => __("Please enter accessibility information."),
			"rows" => 2,
			"wrapClass" => $wrapFull,
			"notes" => __("Please detail any restrictions, or state that it is fully accessible."),
		],
		[
			"type" => "textarea",
			"name" => "activity_intro",
			"label" => __("Introduction"),
			"rows" => 2,
			"wrapClass" => $wrapFull,
			"notes" => __("A short introductory paragraph that describes your activity."),
		],
		[
			"type" => "textarea",
			"name" => "activity_content",
			"label" => __("A full description of your tour/excursion/activity"),
			"required" => true,
			"requiredLabel" => __("Please enter a description"),
			"rows" => 3,
			"attr" => ["data-ckeditor" => true],
			"wrapClass" => $wrapFull,
		],
		[
			"field" => "images_up",
			"label" => __("Image Gallery"),
			"notes" => __("Please supply good quality landscape images - a maximum of 4 - to illustrate your tour/excursion/activity.") . 
				"\n$thumbText",
			"wrapClass" => "uk-width-1-1",
		],
		[
			"type" => "submit",
			"name" => "submit",
			"class" => "uk-button uk-button-primary uk-button-large",
			"value" => __("Submit"),
			"prependMarkup" => (isset($captcha) ? $nb->wrap($captcha->render(), "uk-margin-bottom") . $captcha->getScript() : ""),
			"wrapClass" => "uk-width-1-1",
			"protectCSRF" => false,
		],
	],
]);

$nbUpload->processUpload($form);

// Process submission
if($config->ajax) {

	$response = 400;
	$message = ukAlertDanger(__("Sorry, the submission could not be sent. Please refresh the page to try again."));

	// If the form has been submitted
	if($input->post()->count()) {

		// Check reCAPTCHA
		if(isset($captcha) && $captcha->verifyResponse() !== true) {
			$response = 401; // Unauthorized
			$message = ukAlertWarning(__("Please ensure the reCAPTCHA is checked."));
		}

		if($response !== 401) {

			// Check form
			$form->processInput($input->post);
			$errors = $form->getErrors();

			if(count($errors)) {

				// Return errors
				$response = 412; // Precondition failed
				$message = ukAlertWarning(implode("<br>", $errors));

			} else {

				$editLinks = [];

				$provider = $sanitizer->int($form->get("select_provider")->value);
				$newProvider = true;
				if($provider) {
					$provider = $pages->get($provider);
					$newProvider = false;
					$form->get("select_provider")->attr("value", $provider->title);
				} else {
					$data = [];
					foreach($form->find("name^=provider_") as $f) {
						$data[str_replace("provider_", "", $f->name)] = $f->value;
					}
					$provider = $pages->add("provider", 1061, $data);
					$provider->addStatus(Page::statusUnpublished);
					$provider->save();
					foreach(["logo", "images", "gallery"] as $f) {
						$nbUpload->moveFiles($f, $form->get($f)->value, $provider, $page);
						$provider->of(false);
						if(!$provider->get($f)->count()) {
							$form->remove($form->get($f));
						} 
						$provider->of(true);
					}
					if($provider->images->count()) $provider->setAndSave("thumb", $provider->images->first());
					$form->remove($form->get("select_provider"));
				}

				$fromEmail = $provider->email;
				$fromName = $provider->title;

				if($fromEmail && $fromName) {

					// Add activity
					$data = [];
					$blocks = [];
					foreach($form->find("name^=activity_") as $f) {
						$key = str_replace("activity_", "", $f->name);
						$value = $f->value;
						if($key == "content") {
							$blocks["body"] = [1, $value];
						} else {
							if($key == "info_prices" && $value) {
								$data["price"] = $sanitizer->float($value);
							}
							$data[$key] = $value;
						}
					}

					$images = $form->get("images_up")->value;
					if($images->count()) $blocks["gallery"] = [2, false];

					$activity = $pages->add("activity", 1058, $data);
					foreach($blocks as $key => $value) {
						$block = $activity->blocks->getNew();
						$block->repeater_matrix_type = $value[0];
						if($value[1]) $block->set($key, $value[1]);
						$block->save();
						$activity->blocks->add($block);
					}
					$activity->provider->add($provider);
					$activity->addStatus(Page::statusUnpublished);
					$activity->save();

					$block = $activity->blocks->get("repeater_matrix_type=2");
					if($block) {
						$nbUpload->moveFiles("gallery|images_up", $images, $block, $page);
						$gallery = $block->gallery;
						if($gallery->count()) {
							$activity->setAndSave("thumb", $gallery->first());
							$form->get("images_up")->label = "Image Gallery - Activity";
						} else {
							$form->remove($form->get("images_up"));
						}
					} else {
						$form->remove($form->get("images_up"));
					}
					
					// Update values for email
					if($newProvider) {

						$editUrl = $provider->editUrl(true);
						$editLinks[] =  "Edit the provider: <a href='$editUrl'>$editUrl</a>";

						if($provider->social) {
							$form->get("provider_social")->attr("value", nl2br($provider->social));
						} else {
							$form->remove($form->get("provider_social"));
						}
						if($provider->awards->count()) {
							$form->get("provider_awards")->attr("value", $provider->awards->explode("title"));
						} else {
							$form->remove($form->get("provider_awards"));
						}

						foreach($form->find("name^=provider_") as $f) if(!$f->attr("value")) $form->remove($f);

					} else {
						
						foreach(["logo", "images", "gallery"] as $f) $form->remove($form->get($f));
						foreach($form->find("name^=provider_") as $f) $form->remove($f);
					}

					$editUrl = $activity->editUrl(true);
					$editLinks[] =  "Edit the activity: <a href='$editUrl'>$editUrl</a>";
					
					$form->get("activity_trip_type")->attr("value", $activity->trip_type ? $activity->trip_type->title : "");
					$form->get("activity_port")->attr("value", $activity->port->count() ? $activity->port->implode(", ", "title") : "");
					$form->get("activity_info_days")->attr("value", $activity->info_days->count() ? $activity->info_days->explode("title") : "");
					$form->get("activity_info_level")->attr("value", $activity->info_level ? $activity->info_level->title : "");
					$form->get("activity_cats")->attr("value", $activity->cats->count() ? $activity->cats->explode("title") : "");
					foreach($form->find("name^=activity_") as $f) if(!$f->attr("value")) $form->remove($f);

					// Create email
					$subject = sprintf(__("%s Form Submission from"), $page->title) . " " . $input->httpHostUrl();
					$message = $nb->formEmail($form, [
						"subject" => $subject,
						"prepend" => $nb->wrap(
							sprintf(__("This is a response sent using the %s form on your website"), $page->title) . ":",
							"p"
						),
						"append" => $nb->wrap(implode("<br>", $editLinks), "p"),
					]);

					// Send Email
					$mg = $mail->new();
					$sent = $mg->to($to)
						->replyTo($fromEmail, $fromName)
						->subject($subject)
						->bodyHTML($message)
						->setBatchMode(false)
						->setTrackOpens(false)
						->send();
					$response = $mg->getHttpCode();
					if($sent) $message = ukAlertSuccess(__("Thank you, your submission has been received. We will be in touch soon.") . "<br>" .
						__("If you would like to submit another tour/trip/activity, please refresh the page."));
				}
			}
		}
	}

	// Return response
	$nb->outputJSON([
		"action" => $page->name,
		"response" => $response,
		"message" => $message,
	]);
}

//$form->prependMarkup = renderHeading(sprintf(__("%s Form"), $page->title));
