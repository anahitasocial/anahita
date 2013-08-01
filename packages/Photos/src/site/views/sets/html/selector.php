<?php defined('KOOWA') or die ?>

<form id="set-form" data-behavior="FormValidator" method="post" action="<?= @route('view=set&oid='.$actor->id) ?>">
	<input type="hidden" name="action" value="addphoto" />
	<input type="hidden" name="photo_id" value="<?= $photo->top()->id ?>" />
	
	<fieldset>
		<legend><?= @text('COM-PHOTOS-SET-ADD') ?></legend>
		<div class="control-group">
			<label class="control-label" for="title"><?= @text('LIB-AN-MEDIUM-TITLE') ?></label>
			<div class="controls">
				<input data-validators="required" name="title" class="input-large" size="50" maxlength="255" tabindex="1" type="text">
			</div>
		</div>
			
		<div class="form-actions">
			<a data-trigger="CloseSelector" class="btn" href="<?= @route(array('view'=>'sets', 'oid'=>$actor->id)) ?>"><?= @text('LIB-AN-ACTION-CLOSE') ?></a> 
			<button data-trigger="Add" class="btn btn-primary">
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
<?= @message(@text('COM-PHOTOS-SETS-NO-SETS-CREATED')) ?>
<?php endif; ?>
</div>


