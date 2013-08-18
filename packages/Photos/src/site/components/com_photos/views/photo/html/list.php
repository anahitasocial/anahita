<?php defined('KOOWA') or die; ?>

<div class="an-entity an-entity-portraiable an-record an-removable">
	<div class="entity-portrait-medium">
		<a title="<?= @escape($photo->title) ?>" href="<?= @route($photo->getURL()) ?>">			
			<img alt="<?= @escape($photo->title) ?>" src="<?= $photo->getPortraitURL('medium') ?>" />
		</a>
	</div>
	
	<div class="entity-title-wrapper">
		<h3 data-behavior="<?= $photo->authorize('edit') ? 'Editable' : ''; ?>" class="entity-title <?= $photo->authorize('edit') ? 'editable' : '' ?>" data-editable-options="{'url':'<?= @route($photo->getURL()) ?>','name':'title', 'prompt':'<?= @text('COM-PHOTOS-MEDIUM-TITLE-PROMPT') ?>'}">
		<?= @escape($photo->title) ?>
        </h3>		
	</div>
	
	<div class="entity-description-wrapper <?= $photo->authorize('edit') ? 'editable' : '' ?>">
		<div data-behavior="<?= $photo->authorize('edit') ? 'Editable' : ''; ?>" class="entity-description <?= $photo->authorize('edit') ? 'editable' : '' ?>" data-editable-options="{'url':'<?= @route($photo->getURL()) ?>','name':'description', 'input-type':'textarea', 'prompt':'<?= @text('COM-PHOTOS-MEDIUM-DESCRIPTION-PROMPT') ?>'}">
		<?= @escape($photo->description) ?>
		</div>
	</div>
	
	<div class="entity-meta">
		<?php if($filter == 'leaders'): ?>
		<div class="an-meta">
		<?= sprintf(@text('LIB-AN-MEDIUM-OWNER'), @name($photo->owner)) ?>
		</div>
		<?php endif; ?>
		
		<ul class="an-meta inline">
			<li><?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $photo->numOfComments) ?></li>
			<?php if($photo->lastCommenter): ?>
			<li><?= sprintf(@text('LIB-AN-MEDIUM-LAST-COMMENT-BY-X'), @name($photo->lastCommenter), @date($photo->lastCommentTime)) ?></li>
			<?php endif; ?>
		</ul>
		
		<div class="vote-count-wrapper an-meta" id="vote-count-wrapper-<?= $photo->id ?>">
			<?= @helper('ui.voters', $photo); ?>
		</div>
	</div>
	
	<div class="entity-actions">
		<?= @helper('ui.commands', @commands('list')) ?>
	</div>
</div>
