<?php defined('KOOWA') or die; ?>

<?php if($photo->authorize('edit')) : ?>
<div class="an-entity editable" data-url="<?= @route($photo->getURL()) ?>">
<?php else : ?>
<div class="an-entity">
<?php endif; ?>
	
	<div class="entity-portrait-medium">
		<?php $caption = htmlspecialchars($photo->title, ENT_QUOTES, 'UTF-8'); ?>
		<a data-trigger="MediaViewer" title="<?= $caption ?>" href="<?= $photo->getPortraitURL('original') ?>">			
			<img alt="<?= @escape($photo->title) ?>" src="<?= $photo->getPortraitURL('medium') ?>" />
		</a>
	</div>
	
	<div class="entity-description-wrapper">
		<h3 class="entity-title">
			<?= @escape($photo->title) ?>
		</h3>
		
		<div class="entity-description">
			<?= @content( nl2br($photo->description), array('exclude'=>array('gist','video'))) ?>
		</div>
	</div>
	
	<div class="entity-meta">
		<div class="an-meta" id="vote-count-wrapper-<?= $photo->id ?>">
		<?= @helper('ui.voters', $photo); ?>
		</div>
	</div>
</div>

