<?php defined('KOOWA') or die('Restricted access'); ?>

<?php $milestone = empty($milestone) ? @service('repos:todos.milestone')->getEntity()->reset() : $milestone; ?>
			
<form data-behavior="FormValidator" method="post" action="<?=@route($milestone->getURL().'&oid='.$actor->id)?>">	
	<fieldset>
		<legend><?= ($milestone->persisted()) ? @text('COM-TODOS-MILESTONE-EDIT') : @text('COM-TODOS-MILESTONE-ADD') ?></legend>
		
		<div class="control-group">
			<label class="control-label" for="title"><?= @text('COM-TODOS-MEDIUM-TITLE') ?></label>
			<div class="controls">
				<input data-validators="required" class="input-block-level" name="title" value="<?= @escape( $milestone->title ) ?>" size="50" maxlength="255" type="text">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="body"><?= @text('COM-TODOS-MEDIUM-DESCRIPTION') ?></label>
			<div class="controls">
				<textarea data-validators="required maxLength:5000" class="input-block-level" name="description" cols="5" rows="3"><?= @escape( $milestone->description ) ?></textarea>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="endDate"><?= @text('COM-TODOS-MILESTONE-END-DATE') ?></label>
			<div class="controls">
				<?php $milestone->endDate->addHours( get_viewer()->timezone ); ?>
				<?= @helper('date.picker', 'endDate', array('date'=>$milestone->endDate)) ?>
			</div>
		</div>
		
		<div class="actions">
			<?php $cancelURL = ($milestone->persisted()) ? $milestone->getURL() : 'view=milestones&oid='.$actor->id; ?>
			<a href="<?= @route($cancelURL) ?>" class="btn"><?= @text('LIB-AN-ACTION-CANCEL') ?></a> 
			<?php $saveButton = ($milestone->persisted()) ? 'LIB-AN-ACTION-UPDATE' : 'LIB-AN-ACTION-SAVE'; ?>
			<button class="btn btn-primary"><?= @text($saveButton) ?></button>
		</div>
	</fieldset>
</form>

<?= @message(@text('COM-TODOS-MEDIUM-ALLOWED-MARKUP-INSTRUCTIONS')) ?>