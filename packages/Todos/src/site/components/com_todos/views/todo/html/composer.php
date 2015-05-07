<?php defined('KOOWA') or die('Restricted access'); ?>

<?php $todo = @service('repos:todos.todo')->getEntity()->reset() ?>

<form class="composer-form" method="post" action="<?= @route() ?>">
	<fieldset>
		<legend><?=@text('COM-TODOS-TODO-ADD')?></legend>	
		
		<div class="control-group">
			<label class="control-label" for="todo-title"><?= @text('COM-TODOS-MEDIUM-TITLE') ?></label>
			<div class="controls">
				<input id="todo-title" name="title" class="input-block-level" value="<?= @escape( $todo->title ) ?>" size="50" maxlength="255" type="text" required autofocus />
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="todo-description"><?= @text('COM-TODOS-MEDIUM-DESCRIPTION') ?></label>
			<div class="controls">
				<textarea id="todo-description" class="input-block-level" name="description" cols="5" rows="3" maxlength="5000" required><?= @escape( $todo->description ) ?></textarea>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="todo-priority"><?= @text('COM-TODOS-TODO-PRIORITY') ?></label>
			<div class="controls">
				<?= @helper('prioritylist', $todo->priority) ?>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" id="privacy" ><?= @text('LIB-AN-PRIVACY-FORM-LABEL') ?></label>
			<div class="controls">
				<?= @helper('ui.privacy', array('entity'=>$todo, 'auto_submit'=>false, 'options'=>$actor)) ?>
			</div>
		</div>
		
		<div class="form-actions">
			<button type="submit" class="btn btn-primary" data-loading-text="<?= @text('LIB-AN-MEDIUM-POSTING') ?>">
				<?= @text('LIB-AN-ACTION-ADD') ?>
			</button>			
		</div>
	</fieldset>	
</form>