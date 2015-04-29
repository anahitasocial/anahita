<?php defined('KOOWA') or die ?>

<form id="set-form" method="post" action="<?= @route('option=com_photos&view=set&oid='.$actor->id.'&layout=selector_list&reset=1') ?>">
	<input type="hidden" name="action" value="addphoto" />
	<input type="hidden" name="photo_id" value="<?= $photo->top()->id ?>" />
	
	<fieldset>
		<legend><?= @text('COM-PHOTOS-SET-ADD') ?></legend>
		<div class="control-group">
			<label class="control-label" for="title"><?= @text('LIB-AN-MEDIUM-TITLE') ?></label>
			<div class="controls">
				<input name="title" class="input-large" size="50" maxlength="255" type="text" required>
			</div>
		</div>
			
		<div class="form-actions">
			<button data-action="CloseSetSelector" class="btn">
			    <?= @text('LIB-AN-ACTION-CLOSE') ?>
			</button> 
			<button type="submit" class="btn btn-primary">
				<i class="icon-plus-sign icon-white"></i>
				<?= @text('LIB-AN-ACTION-NEW') ?>
			</button>
		</div>
	</fieldset>
</form>

<h4><?= @text('COM-PHOTOS-SET-SELECT') ?></h4>

<?php 
$assigned_sets = array();
if(count($photo->top()->sets))
	foreach($photo->top()->sets as $set)
		$assigned_sets[] = $set->id;
?>

<div id="sets" class="an-entities">
<?php if(count($sets)): ?>
	<?php foreach($sets as $set): ?>
	<?= @view('set')->layout('selector_list')->set('set', $set)->assignedSets($assigned_sets); ?>
	<?php endforeach; ?>
<?php else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>
</div>


