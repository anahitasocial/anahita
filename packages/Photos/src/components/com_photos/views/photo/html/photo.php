<? defined('KOOWA') or die; ?>

<? if ($photo->authorize('edit')) : ?>
<div class="an-entity editable" data-url="<?= @route($photo->getURL()) ?>">
<? else : ?>
<div class="an-entity">
<? endif; ?>

	<div class="entity-portrait-medium">
		<? $caption = htmlspecialchars($photo->title, ENT_QUOTES); ?>
		<a data-trigger="MediaViewer" title="<?= $caption ?>" href="<?= $photo->getPortraitURL('original') ?>">
			<img alt="<?= @escape($photo->title) ?>" src="<?= $photo->getPortraitURL('medium') ?>" />
		</a>
	</div>

	<div class="entity-description-wrapper">
		<? if ($photo->title): ?>
			<h3 class="entity-title">
				<?= @escape($photo->title) ?>
			</h3>
		<? elseif ($photo->authorize('edit')) : ?>
			<h3 class="entity-title">
				<span class="muted"><?= @text('LIB-AN-EDITABLE-PLACEHOLDER') ?></span>
			</h3>
		<? endif; ?>

		<div class="entity-description">
			<?= @content(nl2br($photo->description), array('exclude' => array('gist', 'video'))) ?>
		</div>
	</div>

	<div class="entity-meta">
		<div class="an-meta" id="vote-count-wrapper-<?= $photo->id ?>">
		<?= @helper('ui.voters', $photo); ?>
		</div>
	</div>
</div>
