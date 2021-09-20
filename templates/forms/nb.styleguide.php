<?php namespace ProcessWire;

/**
 * Styleguide Form
 *
 * @copyright 2019 NB Communication Ltd
 *
 */

// Set the `to` email address
$to = "administrator@nbcommunication.com";

// Defaults to client email if the site is live
if($nb->siteLive && $nb->clientEmail && strpos($to, "@nbcommunication.com")) $to = $nb->clientEmail;

// Is reCAPTCHA to be used?
$captcha = $modules->isInstalled("MarkupGoogleRecaptcha") ? $modules->get("MarkupGoogleRecaptcha") : null;

$wrapFull = "uk-width-1-1";
$wrapHalf = "uk-width-1-2@s";
$dateFormat = [
	"d-m-Y",
	"Y-m-d",
];

// Create the form
$form = $nb->form([
	"class" => [
		"uk-form-stacked",
	],
	"fields" => [
		[
			"name" => "name",
			"label" => __("Your Name"),
			"icon" => "user-circle",
			"required" => true,
			"requiredLabel" => __("Please enter your name"),
			"description" => __("A description in **Markdown**"),
			"notes" => __("Some notes *markdown*"),
			"wrapClass" => $wrapHalf,
			"prependMarkup" => $nb->wrap(__("Some prepended text."), "<div class='uk-text-small'>"),
			"appendMarkup" => $nb->wrap(__("Some appended text."), "<div class='uk-text-small'>"),
		],
		[
			"type" => "markup",
			"name" => "spacer",
			"value" => "&nbsp;",
			"wrapClass" => "uk-width-1-2@s uk-visible@s",
		],
		[
			"type" => "email",
			"name" => "email",
			"label" => __("Email Address"),
			"icon" => "envelope",
			"required" => true,
			"requiredLabel" => __("Please enter a valid email address"),
			"wrapClass" => $wrapHalf,
		],
		[
			"name" => "tel",
			"label" => __("Telephone"),
			"icon" => "phone",
			"attr" => ["type" => "tel"],
			"placeholder" => __("Optional"),
			"wrapClass" => $wrapHalf,
		],
		[
			"type" => "textarea",
			"name" => "enquiry",
			"label" => __("Your Enquiry"),
			"icon" => "pencil-alt",
			"required" => true,
			"requiredLabel" => __("Please enter your enquiry"),
			"rows" => 9,
			"wrapClass" => $wrapFull,
		],
		[
			"name" => "datepicker",
			"label" => __("Datepicker"),
			"attr" => [
				"data-datetimepicker" => [
					"format" => $dateFormat[0],
					"minDate" => [
						"date" => $datetime->date($dateFormat[1]),
						"format" => $dateFormat[1],
					],
					"maxDate" => [
						"date" => $datetime->date($dateFormat[1], strtotime("+1 year")),
						"format" => $dateFormat[1],
					],
					"inputOutputFormat" => "U",
				],
			],
			"wrapClass" => $wrapHalf,
		],
		[
			"name" => "visit",
			"label" => __("Visit"),
			"attr" => [
				"data-visit-suggestions" => $pages->get(1383)->url,
			],
			"wrapClass" => $wrapHalf,
		],
		[
			"name" => "collapsed",
			"label" => __("A collapsed field"),
			"wrapClass" => $wrapFull,
			"collapsed" => 1,
		],
		[
			"type" => "checkbox",
			"name" => "checkbox_test",
			"label" => __("Test Checkbox"),
			"value" => 1,
			"wrapClass" => $wrapFull,
		],
		[
			"type" => "select",
			"name" => "select_test_1",
			"label" => __("Test Select"),
			"wrapClass" => $wrapHalf,
			"options" => [
				"1" => __("Option 1"),
				"2" => __("Option 2"),
				"3" => __("Option 3"),
			],
		],
		[
			"type" => "select",
			"name" => "select_test_2",
			"label" => __("Test Select 2"),
			"wrapClass" => $wrapHalf,
			"options" => [
				__("Option 1"),
				__("Option 2"),
				__("Option 3"),
			],
		],
		[
			"type" => "radios",
			"name" => "radios_test",
			"label" => __("Test Radios"),
			"notes" => __("3 column layout"),
			"options" => [
				"1" => __("Option 1"),
				"2" => __("Option 2"),
				"3" => __("Option 3"),
			],
			"optionColumns" => 3,
			"wrapClass" => $wrapHalf,
		],
		[
			"type" => "checkboxes",
			"name" => "checkboxes_test",
			"label" => __("Test Checkboxes"),
			"notes" => __("2 column layout"),
			"options" => [
				"1" => __("Option 1"),
				"2" => __("Option 2"),
				"3" => __("Option 3"),
				"4" => __("Option 4"),
			],
			"optionColumns" => 2,
			"wrapClass" => $wrapHalf,
		],
		[
			"type" => "markup",
			"name" => "markup",
			"value" => $nb->wrap($nb->wrap(__("InputfieldMarkup"), "<strong><em></em></strong>") . ", " . __("for displaying HTML markup."), "p"),
			"wrapClass" => $wrapFull,
		],
		[
			"type" => "submit",
			"name" => "submit",
			"class" => "uk-button uk-button-primary",
			"value" => __("Send"),
			"prependMarkup" => (isset($captcha) ? $captcha->render() . $captcha->getScript() : ""),
			"wrapClass" => $wrapFull,
		],
	],
]);

// Process submission
if($config->ajax) {

	$response = 400;
	$message = ukAlertDanger(__("Sorry, the message could not be sent. Please refresh the page to try again."));

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

				// Create email
				$subject = sprintf(__("%s Form Submission from"), $page->title) . " " . $input->httpHostUrl();
				$message = $nb->formEmail($form, [
					"subject" => $subject,
					"prepend" => $nb->wrap(
						sprintf(__("This is a response sent using the %s form on your website"), $page->title) . ":",
						"p"
					),
				]);

				// Send Email
				$mg = $mail->new();
				$sent = $mg->to($to)
					->replyTo($form->get("email")->attr("value"), $form->get("name")->attr("value"))
					->subject($subject)
					->bodyHTML($message)
					->setBatchMode(false)
					->setTestMode(true) // This form is for testing
					->addTags([$input->httpHostUrl(), $page->name])
					->send();
				$response = $mg->getHttpCode();
				if($sent) $message = ukAlertSuccess(__("Thank you, your message has been sent. We will be in touch soon."));
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

$form->prependMarkup = renderHeading(__("Form Styles"));
