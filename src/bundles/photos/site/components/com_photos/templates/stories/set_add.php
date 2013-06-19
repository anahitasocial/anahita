<?php defined('KOOWA') or die('Restricted access'); ?>

<data name="title">
	<?= sprintf(@text('COM-PHOTOS-STORY-NEW-SET'), @name($subject), @route($object->getURL()), @possessive($target)) ?>
</data>

<data name="body">
	<?php if($object->title): ?>
	<h3 class="entity-title"><?= htmlspecialchars($object->title, ENT_QUOTES) ?></h3>
	<?php endif; ?>
	
	<?php if($object->description): ?>
	<div class="entity-description"><?= htmlspecialchars($object->description, ENT_QUOTES) ?></div>
	<?php endif; ?>
	
	<?php if ( $object->hasCover() ) : ?>
	<div class="entity-portrait-medium">
		<a href="<?= @route($object->getURL()) ?>">
			<img src="<?= $object->getCoverSource('medium') ?>" />
		</a>
	</div>
	<?php endif ?>
	
	<div data-behavior="Mediabox">
		<div class="media-grid">	
			<?php $photos = $object->photos->order('photoSets.ordering'); ?>
			<?php foreach( $photos as $i=>$photo ): ?>
			<?php 
			$rel = 'lightbox[actor-set-'.$photo->owner->id.' 900 900]';
		
			$caption = htmlspecialchars($photo->title, ENT_QUOTES).
			(($photo->title && $photo->description) ? ' :: ' : '').
			@helper('text.script', $photo->description);
			?>
			<?php if ( $i > 12 ) break; ?>
			<div class="entity-portrait">
				<a rel="<?= $rel ?>" title="<?= $caption ?>" href="<?= $photo->getPortraitURL('medium') ?>">
					<img src="<?= $photo->getPortraitURL('square') ?>" />
				</a>
			</div>
		<?php endforeach; ?>
		</div>
	</div>
	
	<div class="entity-meta">
		<?= sprintf(@text('COM-PHOTOS-SET-META-PHOTOS'), $object->getPhotoCount()) ?>
	</div>
</data>
