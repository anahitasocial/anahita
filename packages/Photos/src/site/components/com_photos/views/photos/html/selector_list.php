<?php defined('KOOWA') or die('Restricted access');?>	
	
<?php if(count($photos)) :?>
<?php foreach( $photos as $photo) : ?>
<div class="thumbnail-wrapper" photo="<?= $photo->id ?>">
	<a data-trigger="MediaViewer" class="thumbnail-link" href="<?= $photo->getPortraitURL('original') ?>" title="<?= @escape($photo->title) ?>">
		<?php $caption = htmlspecialchars($photo->title, ENT_QUOTES, 'UTF-8') ?>
		<img caption="<?= $caption ?>" class="thumbnail" src="<?= $photo->getPortraitURL('square') ?>" alt="<?= @escape($photo->title) ?>" />
	</a>
</div>
<?php endforeach; ?>
<?php else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>