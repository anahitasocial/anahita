<?php defined('KOOWA') or die('Restricted access'); ?>

<?php $todo = @service('repos:todos.todo')->getEntity()->reset() ?>

<form id="todo-form" data-behavior="FormValidator ComposerForm" method="post" action="<?=@route($todo->getURL().'&oid='.$actor->id)?>">
	<fieldset>
		<legend><?=@text('COM-TODOS-TODO-ADD')?></legend>
			
		<div class="control-group">
			<label class="control-label" for="todolist"><?= @text('COM-TODOS-TODOLIST-LIST') ?></label>
			<div class="controls">
				<?= @helper('todolists', $actor, empty($parent) ? $todo->parent : $parent ) ?>
			</div>
		</div>		
		
		<div class="control-group">
			<label class="control-label" for="title"><?= @text('COM-TODOS-MEDIUM-TITLE') ?></label>
			<div class="controls">
				<input data-validators="required" name="title" class="input-block-level" value="<?= @escape( $todo->title ) ?>" size="50" maxlength="255" tabindex="1" type="text">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="description"><?= @text('COM-TODOS-MEDIUM-DESCRIPTION') ?></label>
			<div class="controls">
				<textarea data-validators="maxLength:5000" class="input-block-level" name="description" cols="50" rows="5" tabindex="2" tabindex="2"><?= @escape( $todo->description ) ?></textarea>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="priority"><?= @text('COM-TODOS-TODO-PRIORITY') ?></label>
			<div class="controls">
				<?= @helper('prioritylist', $todo->priority)?>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" id="privacy" ><?= @text('LIB-AN-PRIVACY-FORM-LABEL') ?></label>
			<div class="controls">
				<?= @helper('ui.privacy',array('entity'=>$todo, 'auto_submit'=>false, 'options'=>$actor)) ?>
			</div>
		</div>
		
		<div class="form-actions">
			<button class="btn btn-primary">
				<?= @text('LIB-AN-ACTION-ADD') ?>
			</button>			
		</div>
	</fieldset>	
</form>