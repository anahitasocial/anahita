<?php defined('KOOWA') or die('Restricted access');?>

<?php if ( is_array($object) ) : ?>
<data name="title">
	<?= sprintf(@text('COM-PHOTOS-STORY-NEW-PHOTOS'), @name($subject)) ?>
</data>
<?php else: ?>
<data name="title">
	<?= sprintf(@text('COM-PHOTOS-STORY-NEW-PHOTO'), @name($subject), @route($object->getURL()) ); ?>
</data>
<?php endif;?>

<?php if ( $type != 'notification') : ?>
<data name="body">
	<?php if ( !is_array($object) ) : ?>
		<?php $caption = htmlspecialchars($object->title, ENT_QUOTES, 'UTF-8') ?>
		<?php if( !empty($object->title) ): ?>
		<h4 class="entity-title">
    		<a href="<?= @route($object->getURL()) ?>">
    			<?= $object->title ?>
    		</a>
    	</h4>
		<?php endif; ?>
		
		<?php if ( $story->body ) : ?>
		<div class="entity-description">
			<?= @content( nl2br( $story->body ), array('exclude'=>'gist') ) ?>
		</div>
		<?php endif;?>
		
		<div class="entity-portrait-medium">
			<a data-rel="story-<?= $story->id ?>" data-trigger="MediaViewer" title="<?= $caption ?>" href="<?= $object->getPortraitURL('original'); ?>">
				<img src="<?= $object->getPortraitURL('medium') ?>" />
			</a>
		</div>
	<?php else : ?>
	<div class="media-grid">	
		<?php foreach($object as $i=>$photo) : ?>
		<?php if($i > 12) break; ?>
		<?php $caption = htmlspecialchars($photo->title, ENT_QUOTES, 'UTF-8'); ?>
		<div class="entity-portrait">
			<a data-rel="story-<?= $story->id ?>" data-trigger="MediaViewer" title="<?= $caption ?>" href="<?= $photo->getPortraitURL('original') ?>">
				<img src="<?= $photo->getPortraitURL('square') ?>" />
			</a>
		</div>
		<?php endforeach; ?>
	</div>	
	<?php endif; ?>
</data>
<?php else : ?>
<data name="body">
	<div>
		<a href="<?= @route($object->getURL())?>">
			<img src="<?= $object->getPortraitURL('medium') ?>" />
		</a>
	</div>
</data>
<?php $commands->insert('viewpost', array('label'=>@text('COM-PHOTOS-PHOTO-VIEW')))->href($object->getURL())?>
<?php endif;?>