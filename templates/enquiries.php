<?php namespace ProcessWire;

/**
 * Shortlist
 *
 */

$out = $cruise->execute();

?><div pw-replace='page-<?= $page->template->name ?>'>
	<div class='uk-section uk-section-large uk-background-muted'>
		<div class='uk-container uk-container-xsmall'><?= $out ?></div>
	</div>
</div>
