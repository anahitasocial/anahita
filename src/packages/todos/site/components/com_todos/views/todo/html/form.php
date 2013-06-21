<?php defined('KOOWA') or die('Restricted access'); ?>

<?php $todo = empty($todo) ? @service('repos:todos.todo')->getEntity()->reset() : $todo; ?>

<form id="entity-form" data-behavior="FormValidator" method="post" action="<?=@route($todo->getURL().'&oid='.$actor->id)?>">
	<fieldset>
		<legend><?= ($todo->persisted()) ? @text('COM-TODOS-TODO-EDIT') : @text('COM-TODOS-TODO-ADD') ?></legend>
		
		<?php if(isset($pid) && $pid): ?>
		<input name="pid" type="hidden" value="<?= $pid ?>" />
		<?php else: ?>
		<div class="control-group">
			<label class="control-label" for="todolist"><?= @text('COM-TODOS-TODOLIST-LIST') ?></label>
			<div class="controls">
				<?= @helper('todolists', $actor, empty($parent) ? $todo->parent : $parent ) ?>
			</div>
		</div>
		<?php endif; ?>
		
		<div class="control-group">
			<label class="control-label" for="title"><?= @text('COM-TODOS-MEDIUM-TITLE') ?></label>
			<div class="controls">
				<input data-validators="required" name="title" class="input-block-level" value="<?= @escape( $todo->title ) ?>" size="50" maxlength="255" tabindex="1" type="text">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="description"><?= @text('COM-TODOS-MEDIUM-DESCRIPTION') ?></label>
			<div class="controls">
				<textarea data-validators="maxLength:5000" class="input-block-level" name="description" cols="50" rows="5" tabindex="2"><?= @escape( $todo->description ) ?></textarea>
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
			<?php if($todo->persisted()): ?>
				<?php if(KRequest::type() == 'AJAX'): ?>
				<button data-trigger="Request"  type="button" class="btn"  name="cancel"  data-request-options="{method:'get',url:'<?=@route($todo->getURL().'&layout=list')?>',replace:this.getParent('form')}">
					<?= @text('LIB-AN-ACTION-CANCEL') ?>
				</button>
				<button class="btn btn-primary" data-trigger="Request" data-request-options="EditEntityOptions" >
					<?= @text('LIB-AN-ACTION-UPDATE') ?>
				</button>
				<?php else : ?>
				<a class="btn" href="<?= @route($todo->getURL()) ?>">
					<?= @text('LIB-AN-ACTION-CANCEL') ?>
				</a> 
				<button class="btn btn-primary">
					<?= @text('LIB-AN-ACTION-UPDATE') ?>
				</button>
				<?php endif;?>
			<?php else : ?>
			<button data-trigger="CancelAdd"  type="button" class="btn small"  name="cancel">
				<?= @text('LIB-AN-ACTION-CANCEL') ?>
			</button> 
			<button data-trigger="Add" class="btn btn-primary">
				<?= @text('LIB-AN-ACTION-ADD') ?>
			</button>
			<?php endif;?>
		</div>
	</fieldset>	
</form>