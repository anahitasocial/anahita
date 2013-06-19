<?php defined('KOOWA') or die ?>

<div id="sets" class="an-entities">
<?php if(count($sets)): ?>
	<?php foreach($sets as $set): ?>
	<div class="an-entity an-record">
		<?php if($set->hasCover()): ?>
		<div class="entity-portrait-square">
			<a href="<?= @route($set->getURL()) ?>">
				<img src="<?= $set->getCoverSource('square') ?>" alt="<?= $set->alias ?>" />
			</a>
		</div>
		<?php endif; ?>
		
		<div class="entity-container">
			<h4 class="entity-title">
				<a href="<?= @route($set->getURL()) ?>">
					<?= @helper('text.truncate',  @escape($set->title), array('length'=>25, 'omission'=>'...') ) ?>
				</a>
			</h4>
			
			<div class="entity-meta">
				<?= sprintf(@text('COM-PHOTOS-SET-META-PHOTOS'), $set->getPhotoCount()) ?>
			</div>
		</div>
	</div>
	<?php endforeach; ?>
<?php else: ?>
<?= @message(@text('COM-PHOTOS-PHOTO-NO-RELATED-SETS')) ?>
<?php endif; ?>
</div>
