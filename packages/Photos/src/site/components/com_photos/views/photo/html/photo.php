<?php defined('KOOWA') or die; ?>

<?php @commands('toolbar') ?>

<div id="an-photos-photo" class="an-entity">	
	<div class="entity-portrait-medium">
		<img alt="<?= @escape($photo->title) ?>" src="<?= $photo->getPortraitURL('medium') ?>" />
	</div>

	<div class="entity-title-wrapper">
		<h3 data-behavior="<?= $photo->authorize('edit') ? 'Editable' : ''; ?>" class="entity-title <?= $photo->authorize('edit') ? 'editable' : '' ?>" data-editable-options="{'url':'<?= @route($photo->getURL()) ?>','name':'title', 'prompt':'<?= @text('COM-PHOTOS-MEDIUM-TITLE-PROMPT') ?>'}">
			<?= @escape($photo->title) ?>
		</h3>
	</div>

	<div class="entity-description-wrapper">
		<div data-behavior="<?= $photo->authorize('edit') ? 'Editable' : ''; ?>" class="entity-description <?= ($photo->authorize('edit')) ? 'editable' : '' ?>" data-editable-options="{'url':'<?= @route($photo->getURL()) ?>','name':'description', 'input-type':'textarea', 'prompt':'<?= @text('COM-PHOTOS-MEDIUM-DESCRIPTION-PROMPT') ?>'}">
			<?= @content($photo->description) ?>
		</div>
	</div>
	
	<div class="entity-meta">
		<div class="an-meta" id="vote-count-wrapper-<?= $photo->id ?>">
		<?= @helper('ui.voters', $photo); ?>
		</div>
	</div>
</div>

