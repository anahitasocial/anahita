<?php defined('KOOWA') or die('Restricted access');?>	
	
<?php if(count($photos)) :?>
<?php foreach( $photos as $photo) : ?>
<div class="thumbnail-wrapper" mid="<?= $photo->id ?>">
	<a class="thumbnail-link" href="<?= @route($photo->getURL()) ?>" title="<?= @escape($photo->title) ?>">
		<img class="thumbnail" src="<?= $photo->getPortraitURL('square') ?>" />
	</a>
</div>
<?php endforeach; ?>
<?php else: ?>
<?= @message(@text('COM-PHOTOS-NO-PHOTOS-POSTED-YET')) ?>
<?php endif; ?>