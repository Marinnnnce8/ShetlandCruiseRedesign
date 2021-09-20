<?php namespace ProcessWire;

/**
 * Homepage
 *
 */

$body = explode("</p>", $page->body, 2);
$pagePorts = $pages->get(1049);
$pageActivities = $pages->get(1051);
$pageItineraries = $pages->get(1054);
$pageInfo = $pages->get(1056);
$pageFaqs = $pages->get(1041);
$moreText = __("FIND OUT MORE");

$optionsJson = [
	"action" => "items",
	"config" => [
		"grid" => [
			"uk-child-width-1-3@l",
			"pull-up",
			"uk-grid",
			"uk-grid-xsmall@l",
		],
	],
	"pageToArray" => [
		"type_transport" => "name",
	],
	"more" => $textMore,
];

?><div pw-replace='page-<?= $page->template->name ?>'>
	<?= $nb->imgBg($page->banner->getRandom(), [
		"class" => [
			"hero-section",
			"hero-full-screen",
			"uk-background-cover",
		],
	]) ?>
		<div class='hero-inner uk-light'>
			<div class='uk-position-bottom'>
				<div class='uk-container'>
					<div class='hero-content'>
						<h1 class='uk-heading-hero'><?= $page->h1 ?></h1>
						<?= getIntro($page, "<div class='hero-summary'>") ?>
						<div class='uk-margin-small-top'>
							<a href='#intro' class='goto-next-section hero-button' aria-label="Go to next section" data-uk-scroll><i class='icon-arrow-down-circle'></i></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class='uk-section uk-section-large section-intro' id='intro'>
		<div class='uk-container'>
			<div class='uk-child-width-1-2@l' data-uk-grid>
				<div>
					<div class='uk-text-lead'><?= str_replace("<p>", "", $body[0]) ?></div>
					<div class="uk-text-center"><img src="/cruise/site/templates/img/cruise-critic-2019-c.png" alt="Cruisers' Choice cruise critic 2019 award" width="500" height="373" style="width: 250px; height: auto;" class="uk-margin-large-top uk-margin-large-right uk-margin-small-bottom"></div>
				</div>
				<div>
					<div class='text-box'><?= $body[1] ?></div>
				</div>
			</div>
		</div>
	</div>

	<div class='uk-section uk-section-large section-locations'>
		<div class='uk-container'>

			<div class='section-header uk-flex uk-flex-between uk-flex-wrap'>
				<h2 class='section-title'>Shetland <span><?= $pagePorts->get("headline|title") ?></span></h2>
				<div class='uk-visible@l'>
					<?= renderButtonLink($pagePorts->url, $moreText) ?>
				</div>
			</div>

			<?= renderPorts($pagePorts->children()) ?>

			<div class='uk-margin-medium-top uk-text-center uk-hidden@l'>
				<?= renderButtonLink($pagePorts->url, $moreText, true) ?>
			</div>
		</div>
	</div>


	<div class='uk-section section-attractions section-with-header-bg'>

		<?= $nb->imgBg($pageActivities->banner->getRandom(), ["class" => "section-header uk-light has-image-background"]) ?>
			<div class='uk-container'>
				<h2 class='section-title'>Shetland <span><?= $pageActivities->title ?></span></h2>
				<div class='section-summary'><?= $pageActivities->getSummary() ?></div>
			</div>
		</div>

		<div class='uk-container'>
			<?= renderAttractions($pageActivities->children("include=hidden,limit=3,sort=random"), array_merge($optionsJson, [
				"config" => [
					"grid" => [
						"uk-child-width-1-3@l",
						"pull-up",
						"uk-grid",
						"uk-grid-xsmall@l",
					],
					"more" => __("View Website"),
				],
				"pageToArray" => [
					"type_transport" => "name",
					"address" => "Text",
				],
				"more" => $textMore,
			])) ?>
			<div class='uk-margin-medium-top uk-text-center'>
				<?= renderButtonLink($pages->get(1051)->url, __("VIEW ALL ATTRACTIONS"), true) ?>
			</div>
		</div>
	</div>


	<div class='uk-section uk-section-large'>
		<div class='uk-container'>
			<div class='section-header uk-text-center'>
				<h2 class='section-title'><a href='<?= $urlFinder ?>'>Find Your <span>Shetland Activity</span></a></h2>
				<div class='section-summary'>Go to the Activity Finder page to search for Shetland activities. Make your visit memorable.</div>
			</div>

			<img class='uk-width-1-1' src='<?= $urls->templates ?>img/finder.jpg' alt='Find Shetland Activity'>

			<div class='uk-margin-top uk-text-center'>

				<?= renderButtonLink($urlFinder, __("GO TO ACTIVITY FINDER"), true) ?>
			</div>
		</div>
	</div>


	<div class='uk-section section-with-header-bg'>

		<?= $nb->imgBg($pageItineraries->banner->getRandom(), ["class" => "section-header uk-light has-image-background"]) ?>
			<div class='uk-container'>
				<h2 class='section-title'>Shetland <span><?= $pageItineraries->title ?></span></h2>
				<div class='section-summary'><?= $pageItineraries->getSummary() ?></div>
			</div>
		</div>

		<div class='uk-container'>
			<?= $nb->renderJson($pageItineraries->children->getRandom(3), $optionsJson) ?>

			<div class='uk-margin-medium-top uk-text-center'>
				<?= renderButtonLink($pageItineraries->url, __("VIEW ALL ITINERARIES"), true) ?>
			</div>
		</div>
	</div>


	<div class='uk-section uk-section-large uk-background-muted'>
		<div class='uk-container'>
			<div class='uk-child-width-1-2@l' data-uk-grid>
				<div>
					<h2 class='section-title small'>Information <span>For Crew Members</span></h2>
					<?= $pageInfo->blocks->get("repeater_matrix_type=5")->render() ?>
					<div class='uk-margin-top'>
						<?= renderButtonLink($pageInfo->url, __("View a full list")) ?>
					</div>
				</div>

				<div>
					<h2 class='section-title small'>Frequently <span>Asked Questions</span></h2>
					<?= $pageFaqs->blocks->get("repeater_matrix_type=5")->render() ?>
					<div class='uk-margin-top'>
						<?= renderButtonLink($pageFaqs->url, __("View all questions")) ?>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class='uk-section uk-section-large uk-padding-remove-bottom uk-background-default section-about'>

		<div class='uk-container'>
			<div class='section-header'>
				<h2 class='section-title'>Learn More <span>About Shetland</span></h2>
			</div>
		</div><?php

			echo $nb->imgBg("{$urls->templates}img/bg-about-large.jpg", ["class" => "section-inner has-image-background"]) .
				$nb->attr(["uk-container"], "div") .
				$nb->attr([
					"class" => [
						"uk-grid-collapse",
						"uk-grid",
						"uk-grid-match",
						"uk-flex",
						"uk-child-width-1-3@l",
						"uk-child-width-1-2@s",
					],
					"data-uk-grid" => true,
					"data-uk-scrollspy" => [
						"target" => "> div > .about-link",
						"delay" => 128,
						"cls" => "uk-animation-slide-bottom-small",
					],
				], "div");

				$spacer = "<div></div>";
				$i = 0;
				foreach($page->links->find("limit=4,link!=,thumb!=") as $p) {

					echo $nb->wrap(
						$nb->wrap(
							$nb->wrap(
								renderHeading($p->title),
								"uk-overlay uk-light uk-position-bottom uk-light"
							),
							$nb->imgBg($p->thumb->getRandom(), [
								"href" => $p->link,
								"class" => ["uk-cover-container", "about-link"],
								"aria-label" => "Find out more",
								"target" => "_blank",
							], ["tag" => "a"])
						),
						"div"
					);

					if($i == 1) echo str_repeat($spacer, 2);
					if($i == 3) echo $spacer;
					$i++;
				}

					?><div>
						<div class='about-etc about-link uk-background-primary uk-light'>
							<div class='uk-overlay'>
								<p>... and much more about life in Shetland on the shetland.org website.</p>
								<img class='shetland-pride-logo uk-position-bottom-right' src='<?= $urls->templates ?>img/shetland-pride-white.png' alt=''>
							</div>
						</div>
					</div>

					<div>
						<div class='about-link'>
							<div class='uk-flex uk-height-1-1 uk-flex-bottom uk-flex-right uk-light'>
								<a href='<?= $urlShetland ?>' class='visit-shetland'><span class='uk-text-muted'>Visit</span> Shetland<small>.org</small></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class='uk-section section-cta uk-section-large'>
		<div class='uk-container'>
			<div class='uk-flex-between uk-flex uk-flex-wrap'>
				<div>
					<h2 class='section-title small'>Connect <span>With Shetland.org on:</span></h2>
				</div>
				<div><?php

					foreach($nb->clientData("Social") as $key => $value) {
						$key = str_replace("-f", "", $key);
						echo $nb->wrap("<i class='fab fa-$key'></i>" . ucfirst($key), [
							"href" => $value,
							"class" => [
								"uk-button",
								"push-icon-left",
								"button-$key",
							],
							"target" => "_blank",
						],"a");
					}

				?></div>
			</div>
		</div>
	</div>
</div>
