<?php defined('KOOWA') or die('Restricted access'); ?>

<?php $todolist = empty($todolist) ? @service('repos:todos.todolist')->getEntity()->reset() : $todolist; ?>

<form data-behavior="FormValidator" method="post" action="<?=@route($todolist->getURL().'&oid='.$actor->id) ?>">
	<fieldset>	
		<legend><?= ($todolist->persisted()) ? @text('COM-TODOS-TODOLIST-EDIT') : @text('COM-TODOS-TODOLIST-ADD') ?></legend>
	
		<div class="control-group">
			<label class="control-label" class="control-label" for="title"><?= @text('COM-TODOS-MEDIUM-TITLE') ?></label>
			<div class="controls">
				<input data-validators="required" class="input-block-level" name="title" value="<?= @escape( $todolist->title ) ?>" size="50" maxlength="255" tabindex="1" type="text">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="body"><?= @text('COM-TODOS-MEDIUM-DESCRIPTION') ?></label>
			<div class="controls">
				<textarea data-validators="required maxLength:5000" class="input-block-level" name="description" cols="5" rows="5" id="COM-TODOS-todolist-description" tabindex="2"><?= @escape( $todolist->description ) ?></textarea>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="tags"><?= @text('COM-TODOS-MILESTONES-LIST') ?></label>
			<div class="controls">
				<?= @helper('milestones', $actor, empty($parent) ? $todolist->parent : $parent) ?>
			</div>
		</div>
			
		<div class="form-actions">
			<?php $cancelURL = ($todolist->persisted()) ? $todolist->getURL() : 'view=todolists&oid='.$actor->id; ?>
			<a href="<?= @route($cancelURL) ?>" class="btn"><?= @text('LIB-AN-ACTION-CANCEL') ?></a> 
			<?php $saveButton = ($todolist->persisted()) ? 'LIB-AN-ACTION-UPDATE' : 'LIB-AN-ACTION-SAVE'; ?>
			<button class="btn btn-primary" tabindex="4"><?= @text($saveButton) ?></button>
		</div>
	</fieldset>
</form>

<?= @message(@text('COM-TODOS-MEDIUM-ALLOWED-MARKUP-INSTRUCTIONS')) ?>