<?php defined('KOOWA') or die ?>

<form id="photos-set-assignment" method="post" action="<?= @route('view=set&oid='.$actor->id) ?>">
	<?php foreach($photos as $photo): ?>
	<input type="hidden" name="photo_id[]" value="<?= $photo->id ?>" />
	<?php endforeach; ?>
	<input type="hidden" value="addphoto" name="action" />
	
	<?= @message(@text('COM-PHOTOS-SET-SELECT-SIMPLE-INSTRUCTIONS')) ?>
		
	<?php if( $actor->sets->getTotal() ) : ?>
	<div class="clearfix">
		<label><?= @text('COM-PHOTOS-SET-SELECT-ONE') ?></label>
		<div class="input">
			<select id="set-selector" name="id" class="input-xlarge" required>
				<option value=""><?= @text('COM-PHOTOS-SET-SELECT-NO-SET-IS-SELECTED') ?></option>
				<?php $sets = $actor->sets->order('title'); ?>
	            <?php foreach($sets as $set): ?>
				<option value="<?= $set->id ?>"><?= @escape($set->title) ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
	<?php endif; ?>
	
	<?php if($actor->authorize('action','com_photos:set:add')): ?>
	<div class="control-group">
		<label class="control-label" for="title"><?= @text('COM-PHOTOS-ACTION-OR-CREATE-A-NEW-SET') ?></label>
		<div class="controls">
			<input class="input-large" name="title" size="32" maxlength="100" type="text" required>
		</div>
	</div>
	<?php endif; ?>
	
	<div class="form-actions">
		<a class="btn" href="<?= @route('view=photos&oid='.$actor->id) ?>"><?= @text('COM-PHOTOS-ACTION-NO-THANK-YOU') ?></a> 
		<button class="btn btn-primary"><?= @text('COM-PHOTOS-ACTION-SET-ADD-PHOTOS') ?></button>
	</div>
</form>