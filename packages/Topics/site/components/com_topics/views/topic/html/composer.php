<?php defined('KOOWA') or die('Restricted access');?>

<?php $topic = @service('repos:topics.topic')->getEntity()->reset() ?>

<form data-behavior="FormValidator ComposerForm" method="post" action="<?= @route($topic->getURL().'&oid='.$actor->id )?>">
	<fieldset>
	    <legend><?= @text('COM-TOPICS-TOPIC-ADD')  ?></legend>		
		<div class="control-group">
			<label class="control-label" for="title"><?= @text('COM-TOPICS-TOPIC-TITLE') ?></label>
			<div class="controls">
				<input data-validators="required" class="input-block-level" name="title" value="" size="50" maxlength="255" tabindex="1" type="text">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="body"><?= @text('COM-TOPICS-TOPIC-POST-BODY') ?></label>
			<div class="controls">
				<textarea data-validators="required maxLength:5000" class="input-block-level" name="body" cols="10" rows="5" tabindex="2"></textarea>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" id="privacy" ><?= @text('LIB-AN-PRIVACY-FORM-LABEL') ?></label>
			<div class="controls">
				<?= @helper('ui.privacy',array('entity'=>$topic, 'auto_submit'=>false, 'options'=>$actor)) ?>
			</div>
		</div>
			
		<div class="form-actions">
			<button class="btn btn-primary" tabindex="3"><?= @text('LIB-AN-ACTION-POST') ?></button>
		</div>
	</fieldset>
</form>