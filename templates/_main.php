<?php namespace ProcessWire;

/**
 * _main.php
 *
 * Please integrate common template elements here
 *
 */

include("./inc/head.php");

$textShortlist = __("Shortlist");
$textViewShortlist = sprintf(__("View your %s"), $textShortlist);
$textOn = __("On");
$textOff = __("Off");

?><body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KX3RGJG"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
	<header id='site-header' class='uk-navbar-container' data-uk-sticky='<?= json_encode([
		//"media" => 960,
		"animation" => "uk-animation-slide-top",
		"show-on-up" => true,
	]) ?>'>
		<div class='uk-container'>
			<nav data-uk-navbar class='uk-navbar uk-flex-between uk-flex-middle'>

				<a href='<?= $urls->root ?>' class='uk-navbar-item uk-logo'>
					<img src='<?= $urls->templates ?>img/logo.png' alt='<?= $nb->siteName ?> Logo'>
				</a>

				<div class='main-nav-wrapper okayNav'>
					<ul class='uk-navbar-nav'>
						<?= ukNavbar($nb->navItems->find("id!=1058|1061"), [], false) ?>
					</ul>
				</div>

				<div class='uk-visible@l'><?php
					echo $nb->wrap($nb->wrap(__("Find Activities"), "span") . "<i class='fas fa-arrow-circle-right'></i>", [
						"href" => $urlFinder,
						"class" => [
							"uk-button",
							"uk-button-primary",
							"button-ghost",
							"push-icon-right",
							"uk-hidden",
						],
						"id" => "right-nav-button",
					], "a");
				?></div>
			</nav>

			<div class='uk-position-top-right'>
				<div class='navbar-right-box uk-flex uk-flex-middle'>
					<a href='<?= $urlShetland ?>'><img src='<?= $urls->templates ?>img/shetland-pride-logo-2.jpg' alt='Shetland Pride Logo'></a>
				</div>
			</div>
		</div>

	</header>

<?php if(!$page->isHome): ?>
	<div class='page-header'>
		<div class='uk-container<?= ($isSmall ? " uk-container-xsmall" : "") ?>'>
			<?= ukBreadcrumb() ?>
			<div class='hero-content'>
				<?= $before ?>
				<h1 class='uk-heading-hero' id='title<?= $page->id ?>'><?= $page->h1 ?></h1>
				<?= $after ?>
				<?= getIntro($page, "uk-width-" . ($page->wrapContent ? "2-3" : "1-1") . "@l hero-summary") ?>
			</div>
		</div>
	</div>

	<?= $prepend ?>

<?php endif; ?>
<?php if($page->wrapContent): ?>

	<?php if(!empty($content) || !empty($sidebar)) : ?>
		<div class='page-content'>
			<?= $page->wrapContent ?>
				<div class='uk-grid' data-uk-grid>
					<div class='uk-width-<?= ($sidebar == false ? "1-1" : "2-3") ?>@l'>
						<div class='content'>
							<?= $content ?>
						</div>
					</div>
				<?php if(!empty($sidebar)): ?>
					<div class='uk-width-1-3@l'>
						<aside class='sidebar'><?= $sidebar ?></aside>
					</div>
				<?php endif; ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<?= $append ?>

<?php else: ?>
	<div id='page-<?= $page->template->name ?>'></div>
<?php endif; ?>

<?php 
/* 

$pageSignup = $pages->get(1119);
$imgSignup = $pageSignup->banner->getRandom();

echo $nb->imgBg($imgSignup, [
	"class" => [
		"uk-section",
		"uk-section-xlarge",
		"section-signup",
		"uk-position-relative",
		"has-image-background",
	],
]);

	if($imgSignup->description): ?>
		<div class='uk-light image-description'>
			<small><i class='fas fa-image'></i><?= $imgSignup->description ?></small>
		</div>
	<?php endif; ?>
		<div class='uk-container'>
			<div class='uk-child-width-1-2@l uk-flex uk-flex-middle' data-uk-grid>
				<div>
					<blockquote class='uk-light uk-text-center'>
						Fair Isle is one of the best places to watch seabirds at close range, especially Puffins which will walk right up to you if you sit quietly.
					</blockquote>
				</div>
				<div>
					<div class='join-us-form uk-padding-medium uk-background-default'>

						<h3><?= $pageSignup->headline ?></h3>

						<?= getIntro($pageSignup, "p") ?>

						<?= $nb->form([
							"action" => $pageSignup->url,
							"id" => "footer-signup",
							"protectCSRF" => false,
							"classes" => [
								"list" => "uk-grid-medium",
								"item_label" => "uk-hidden",
							],
							"fields" => [
								[
									"name" => "name",
									"label" => __("Your Name"),
									"required" => true,
									"requiredLabel" => __("Please enter your name"),
									"placeholder" => "My name is...",
									"class" => ["uk-form-large"],
									"wrapClass" => "uk-width-1-1",
								],
								[
									"type" => "email",
									"name" => "email",
									"label" => __("Email Address"),
									"required" => true,
									"requiredLabel" => __("Please enter a valid email address"),
									"placeholder" => "My email address is...",
									"class" => ["uk-form-large"],
									"wrapClass" => "uk-width-1-1",
								],
								[
									"type" => "submit",
									"name" => "submit",
									"class" => "uk-button uk-button-primary push-icon-right uk-display-block",
									"value" => __("SUBSCRIBE"),
									"icon" => "arrow-circle-right",
									"wrapClass" => "uk-width-1-1",
								],
							],
						])->render() ?>
					</div>
				</div>
			</div>
		</div>
	</div>
*/ 
?>

	

	<footer id='site-footer' class='footer'>

		<div class='uk-container uk-position-relative'>

			<div class='footer-inner'>

				<div class='uk-flex uk-flex-column uk-flex-center'>
					<a href='<?= $urlShetland ?>' class='shetland-box uk-text-center uk-light' data-uk-scrollspy='<?= json_encode([
						"delay" => 64,
						"offsetTop" => -180,
						"cls" => "uk-animation-slide-bottom-small",
					]) ?>'>
						<h3>Visit Shetland.org</h3>
						<p class='font-family-alt'>Savour the wildlife, the birdlife and the warm community spirit.</p>
					</a>

					<div class='uk-text-center' data-uk-scrollspy='<?= json_encode([
						"delay" => 128,
						"offsetTop" => -180,
						"cls" => "uk-animation-slide-bottom-small",
					]) ?>'>

						<h4>Let's Connect</h4>

						<ul class='uk-flex uk-flex-center uk-flex remove-bullets'><?=
							$nb->clientData("Social")->each($nb->wrap($nb->wrap("<i class='fab fa-{key}'></i>", [
								"href" => "{value}",
								"aria-label" => "{key}",
								"class" => "social-link social-{key}",
								"target" => "_blank",
							],"a"), "li"))
						?></ul>

						<ul class='secondary-nav uk-flex uk-flex-center uk-flex-wrap'>
							<?= $pages->find([
								"template" => "legal",
								"include" => "hidden",
							])->prepend($pages->get(1040))->prepend($pages->get(1500))->each("<li><a href={url}>{title}</a></li>") ?>
						</ul>

						<div class='copyright uk-text-center'>
							<?= nbCopyright() ?><br>
							<?= nbWatermark() ?>
						</div>
					</div>
				</div>

				<div class='shetland-pride-white'>
					<a href='<?= $urlShetland ?>'>
						<img src='<?= $urls->templates ?>img/shetland-pride-white.png' alt='Shetland Pride Logo'>
					</a>
				</div>
			</div>
		</div>
	</footer>

	<div class='floating-tool uk-visible@s'>
		<ul class='social-sharer'><?php

			$url = urlencode($page->httpURL);

			foreach([
				"facebook" => [
					"href" => "https://www.facebook.com/sharer.php?u=$url",
				],
				"twitter" => [
					"href" => "https://twitter.com/share?url=$url&text=" . urlencode($page->meta_title),
				],
			] as $icon => $attr) {
				echo $nb->wrap(
					$nb->wrap(faIcon(($icon == "facebook" ? "$icon-square" : $icon), "fab"), array_merge([
						"target" => "_blank",
						"class" => [
							"social-link",
							"social-$icon",
						],
						"aria-label" => $icon
					], $attr), "a"),
					"li"
				);
			}

			?><li><span class='verical-label'>SHARE PAGE ON</span></li>
		</ul>
	</div>

	<div class='floating-tool stick-to-right'>
		<ul>
			<li><a href='<?= $urlShortlist ?>' id='shortlisted' data-uk-tooltip='<?= json_encode([
				"title" => $textViewShortlist,
				"pos" => "left",
			]) ?>' data-shortlist-config='<?= json_encode([
				"name" => $modules->get("CruiseEnquiry")::name,
				"off" => $textShortlist,
				"offAdd" => __("Add to My Shortlist"),
				"on" => __("Shortlisted"),
				"icon" => "<i class=icon-clipboards-4></i>",
				"url" => $urlShortlist,
				"view" => $textViewShortlist,
				"add" => sprintf(__("Add to your %s"), $textShortlist),
				"added" => sprintf(__('%1$s <br>added to your %2$s'), "{name}", $textShortlist),
				"remove" => sprintf(__("Remove from your %s"), $textShortlist),
				"removed" => sprintf(__('%1$s <br>removed from your %2$s'), "{name}", $textShortlist),
				"removeConfirm" => sprintf(__("Are you sure you want to remove this activity from your %s?"), $textShortlist),
				"emailsOn" => sprintf(__("Are you sure you want to turn %s email notifications?"), strtolower($textOn)),
				"emailsOff" => sprintf(__("Are you sure you want to turn %s email notifications?"), strtolower($textOff)),
				"textOn" => $textOn,
				"textOff" => $textOff,
				"textTurn" => __("Turn"),
				/*"msgSelect" => $nb->wrap(__("Are you sure you want to select this provider?"), "p") .
					$nb->wrap(__("This will mark your enquiry as complete and you won&apos;t receive any further responses."), "p") .
					$nb->wrap(
						__("Click OK only if you have confirmed your booking with this provider."),
						"<p class=uk-text-warning>"
					),*/
			]) ?>'><i class='icon-clipboards-4'></i><span class='num'>0</span><span class='verical-label'><?= $textShortlist ?></span></a></li>
		</ul>
	</div><?php

	include("./inc/foot.php");

	?></body>
</html>
