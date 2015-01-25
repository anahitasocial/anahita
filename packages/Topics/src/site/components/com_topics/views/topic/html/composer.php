<?php defined('KOOWA') or die('Restricted access'); ?>

<?php $topic = @service('repos:topics.topic')->getEntity()->reset() ?>

<form class="composer-form" method="post" action="<?= @route() ?>">
	<fieldset>
	    <legend><?= @text('COM-TOPICS-TOPIC-ADD') ?></legend>		
		<div class="control-group">
			<label class="control-label" for="topic-title"><?= @text('COM-TOPICS-TOPIC-TITLE') ?></label>
			<div class="controls">
				<input id="topic-title" class="input-block-level" name="title" value="" size="50" maxlength="255" type="text" required autofocus />
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="topic-body"><?= @text('COM-TOPICS-TOPIC-POST-BODY') ?></label>
			<div class="controls">
				<textarea id="topic-body" maxlength="5000" class="input-block-level" name="body" cols="10" rows="5" required></textarea>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" id="privacy" ><?= @text('LIB-AN-PRIVACY-FORM-LABEL') ?></label>
			<div class="controls">
				<?= @helper('ui.privacy',array('entity'=>$topic, 'auto_submit'=>false, 'options'=>$actor)) ?>
			</div>
		</div>
			
		<div class="form-actions">
			<button type="submit" class="btn btn-primary">
			    <?= @text('LIB-AN-ACTION-POST') ?>
			</button>
		</div>
	</fieldset>
</form>