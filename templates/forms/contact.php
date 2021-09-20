<?php namespace ProcessWire;

/**
 * Contact Form
 *
 * @copyright 2019 NB Communication Ltd
 *
 */

// Set the `to` email address
$to = "cruise@promoteshetland.com";

// Is reCAPTCHA to be used?
$captcha = $modules->isInstalled("MarkupGoogleRecaptcha") ? $modules->get("MarkupGoogleRecaptcha") : null;

// Create the form
$form = $nb->form([
	"class" => [
		"uk-form-stacked",
	],
	"icons" => true,
	"fields" => [
		[
			"name" => "name",
			"label" => __("Your Name"),
			"required" => true,
			"requiredLabel" => __("Please enter your name"),
			"wrapClass" => "uk-width-1-2@s",
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
			"required" => true,
			"requiredLabel" => __("Please enter a valid email address"),
			"wrapClass" => "uk-width-1-2@s",
		],
		[
			"name" => "tel",
			"label" => __("Telephone"),
			"attr" => ["type" => "tel"],
			"placeholder" => __("Optional"),
			"wrapClass" => "uk-width-1-2@s",
		],
		[
			"type" => "textarea",
			"name" => "enquiry",
			"label" => __("Your Enquiry"),
			"required" => true,
			"requiredLabel" => __("Please enter your enquiry"),
			"rows" => 9,
			"wrapClass" => "uk-width-1-1",
		],
		[
			"type" => "submit",
			"name" => "submit",
			"class" => "uk-button uk-button-primary push-icon-right",
			"value" => __("Send"),
			"icon" => "arrow-circle-right",
			"prependMarkup" => (isset($captcha) ? $nb->wrap($captcha->render() . $captcha->getScript(), "uk-margin-bottom") : ""),
			"wrapClass" => "uk-width-1-1",
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

			try {

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
						->addTags([$input->httpHostUrl(), $page->name])
						->send();
					$response = $mg->getHttpCode();
					if($sent) $message = ukAlertSuccess(__("Thank you, your message has been sent. We will be in touch soon."));
				}

			} catch(WireException $e) {

				// CSRF Exception
				$message = sprintf(__("%s Please refresh the page and try again."), $e->getMessage());
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

$form->prependMarkup = renderHeading(sprintf(__("%s Form"), $page->title));
