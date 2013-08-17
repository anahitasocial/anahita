<?php defined('KOOWA') or die('Restricted access'); ?>

<data name="title">
	<?= sprintf(@text('COM-PHOTOS-STORY-NEW-SET'),  @name($subject), @route($object->getURL())) ?>
</data>

<data name="body">
	<?php if( !empty($object->title) ): ?>
	<h4 class="entity-title">
    	<a href="<?= @route($object->getURL()) ?>">
    		<?= $object->title ?>
    	</a>
    </h4>
	<?php endif; ?>

	<?php if($object->description): ?>
	<div class="entity-description"><?= @content($object->description) ?></div>
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
			<?php $photos = $object->photos->order('photoSets.ordering')->limit(15)->fetchSet(); ?>
			<?php foreach( $photos as $i=>$photo ): ?>
			<?php 
			$rel = 'lightbox[actor-set-'.$photo->owner->id.' 900 900]';
		
			$caption = htmlspecialchars($photo->title, ENT_QUOTES).
			(($photo->title && $photo->description) ? ' :: ' : '').
			@helper('text.script', $photo->description);
			?>
			<?php if ( $i > 12 ) break; ?>
			<div class="entity-portrait">
				<a rel="<?= $rel ?>" title="<?= $caption ?>" href="<?= @route($photo->getPortraitURL('medium')) ?>">
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
