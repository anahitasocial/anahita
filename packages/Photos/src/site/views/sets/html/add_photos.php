<?php defined('KOOWA') or die ?>

<form id="set-select-form" data-behavior="FormValidator" method="post" action="<?= @route('view=set&oid='.$actor->id) ?>">
	<?php foreach($photos as $photo): ?>
	<input type="hidden" name="photo_id[]" value="<?= $photo->id ?>" />
	<?php endforeach; ?>
	<input type="hidden" value="addphoto" name="action" />
	
	<?= @message(@text('COM-PHOTOS-SET-SELECT-SIMPLE-INSTRUCTIONS')) ?>
		
	<?php if( $actor->sets->getTotal() ) : ?>
	<div class="clearfix">
		<label><?= @text('COM-PHOTOS-SET-SELECT-ONE') ?></label>
		<div class="input">
			<select id="set-selector" name="id" class="input-xlarge">
				<option value=""><?= @text('COM-PHOTOS-SET-SELECT-NO-SET-IS-SELECTED') ?></option>
				<?php foreach($actor->sets as $set): ?>
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
			<input data-validators="required" class="input-large" name="title" size="32" maxlength="100" type="text">
		</div>
	</div>
	<?php endif; ?>
	
	<div class="form-actions">
		<a class="btn" href="<?= @route('view=photos&oid='.$actor->id) ?>"><?= @text('COM-PHOTOS-ACTION-NO-THANK-YOU') ?></a> 
		<button data-trigger="add-photos-to-set" class="btn btn-primary"><?= @text('COM-PHOTOS-ACTION-SET-ADD-PHOTOS') ?></button>
	</div>
</form>


<script>
Delegator.register('click', {
	
	'add-photos-to-set' : function(event, el, api){
		event.stop();
		
		if(el.form.id.selectedIndex > 0)
			el.form.submit();

		else if(el.form.title && el.form.get('validator').validate())
			el.form.submit();
	}
	
});
</script>