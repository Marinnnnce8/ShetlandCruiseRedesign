<?php namespace ProcessWire;

/**
 * foot.php
 *
 * Please retain this code, integrating as necessary
 *
 */

if(setting("jquery-version")): ?>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/<?= setting("jquery-version") ?>/jquery.min.js' integrity='<?= setting("jquery-hash") ?>' crossorigin='anonymous'></script>
<?php endif; ?>

<?php if(setting("cookieconsent-url")): ?>
<link rel='stylesheet' href='<?= setting("cookieconsent-url") ?>cookieconsent.min.css' integrity='<?= setting("cookieconsent-hash-css") ?>' crossorigin='anonymous' />
<script src='<?= setting("cookieconsent-url") ?>cookieconsent.min.js' integrity='<?= setting("cookieconsent-hash-js") ?>' crossorigin='anonymous'></script>
<script>
	window.addEventListener("load", function() {
		window.cookieconsent.initialise(<?= wireEncodeJSON(setting("cookieconsent-settings")) ?>)
	});
</script>
<?php endif;

echo $procache->script($nb->scripts->getArray());

if($nb->googleApiKey): ?>
<script src='https://maps.googleapis.com/maps/api/js?key=<?= $nb->googleApiKey ?>&callback=theme.map' async defer></script>
<?php endif;

if($page->template->name == "submission"): ?>
<script src='https://cdn.ckeditor.com/4.11.2/basic/ckeditor.js'></script>
<?php endif;

