<?php namespace ProcessWire;

/**
 * head.php
 *
 * Please retain this code, integrating as necessary
 *
 */

// Meta Title
if(!$page->meta_title) {
	$page->meta_title = $sanitizer->unentities($page->isHome ?
		"$nb->siteName | {$page->get("headline|title")}" :
		"$page->title | $nb->siteName");
}

// Open Graph
$ogImage = $page->getImage([
	"height" => 0,
	"width" => 0,
	"fields" => [
		"og_image",
		"thumb",
		"images",
	],
]);

?><!doctype html>
<html lang='en-gb' class='template-<?= $page->template->name ?> section-<?= $page->rootParent->name ?> page-<?= $page->id ?>'>
<head>

<?php if(!$nb->siteLive): ?>
	<meta name='robots' content='noindex, nofollow'>
<?php endif; ?>

	<meta charset='utf-8'>
	<meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
	<meta name='format-detection' content='telephone=no'>

	<title><?= $page->meta_title ?></title>

	<meta name='description' content='<?= $sanitizer->entities1($page->get("meta_desc|getSummary"), true) ?>'>
	<meta property='og:title' content='<?= $sanitizer->entities1($page->get("og_title|meta_title"), true) ?>'>
	<meta property='og:description' content='<?= $sanitizer->entities1($page->get("og_desc|meta_desc|getSummary"), true) ?>'>
	<meta property='og:url' content='<?= $page->httpUrl ?>'>
	<meta property='og:site_name' content='<?= $nb->siteName ?>'>

<?php if($page->isArticle): ?>
	<meta property='og:type' content='article'>
	<meta property='article:published_time' content='<?= $datetime->date("Y-m-d H:i:s", $page->get("date_pub|published")) ?>'>
	<meta property='article:modified_time' content='<?= $datetime->date("Y-m-d H:i:s", $page->modified) ?>'>
<?php else: ?>
	<meta property='og:type' content='website'>
<?php endif; ?>

<?php if($ogImage->httpUrl): ?>
	<meta property='og:image' content='<?= $ogImage->httpUrl ?>'>
	<meta property='og:image:width' content='<?= $ogImage->width ?>'>
	<meta property='og:image:height' content='<?= $ogImage->height ?>'>
<?php endif; ?>

	<link rel='canonical' href='<?= $page->urlCanonical ?>'>
	<link rel='shortcut icon' href='<?= $urls->root ?>favicon.ico'>

<?php
	$pageRSS = $pages->get("template=feed-rss");
	if($pageRSS->id && !$pageRSS->isUnpublished()) {
		echo "<link href='$pageRSS->url' rel='alternate' type='application/rss+xml' title='$nb->siteName RSS Feed'>";
	}
?>

	<?= $procache->link($nb->styles->getArray()) ?>

<?php if(setting("fontawesome-version")): ?>
	<link rel='stylesheet' href='https://use.fontawesome.com/releases/v<?= setting("fontawesome-version") ?>/css/all.css' integrity='<?= setting("fontawesome-hash") ?>' crossorigin='anonymous'>
<?php endif; ?>

<?php if($nb->googleAnalytics && $nb->siteLive): ?>
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-KX3RGJG');</script>
	<!-- End Google Tag Manager -->
	<script async src='https://www.googletagmanager.com/gtag/js?id=<?= $nb->googleAnalytics ?>'></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag("js", new Date());
		gtag("config", "<?= $nb->googleAnalytics ?>"<?php if($nb->googleAnalyticsAnon): ?>, {"anonymize_ip": true}<?php endif; ?>);
	</script>
<?php endif; ?>

	<!-- Crazy Egg -->
	<script type="text/javascript" src="//script.crazyegg.com/pages/scripts/0093/6456.js" async="async"></script>
    <!-- End Crazy Egg -->

</head>
