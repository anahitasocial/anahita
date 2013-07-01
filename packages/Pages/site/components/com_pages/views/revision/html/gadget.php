<?php defined('KOOWA') or die; ?>

<div class="an-entity an-record">
	<h4 class="entity-title">
		<a href="<?= @route($revision->getURL()) ?>">
			<?= sprintf(@text('COM-PAGES-PAGE-REVISION-META-NUMBER'), $revision->revisionNum) ?>
		</a>
	</h4>
	
	<div class="entity-meta">
		<?= sprintf( @text('COM-PAGES-PAGE-REVISION-LIST-ITEM'), @date($revision->creationTime, @date($revision->creationTime, '%B %d %Y - %l:%M %p')), @name($revision->editor) ) ?>
	</div>
</div>