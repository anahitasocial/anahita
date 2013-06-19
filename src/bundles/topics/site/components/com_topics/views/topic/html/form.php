<?php defined('KOOWA') or die('Restricted access');?>

<?php $topic = empty($topic) ? @service('repos:topics.topic')->getEntity()->reset() : $topic; ?>

<form data-behavior="FormValidator" method="post" action="<?= @route($topic->getURL().'&oid='.$actor->id )?>">
	<fieldset>
		<legend><?= ($topic->persisted()) ? @text('COM-TOPICS-TOPIC-EDIT') : @text('COM-TOPICS-TOPIC-ADD') ?></legend>
		<div class="control-group">
			<label class="control-label" for="title"><?= @text('LIB-AN-MEDIUM-TITLE') ?></label>
			<div class="controls">
				<input data-validators="required" class="input-block-level" name="title" value="<?= @escape( $topic->title ) ?>" size="50" maxlength="255" tabindex="1" type="text">
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="body"><?= @text('LIB-AN-MEDIUM-BODY') ?></label>
			<div class="controls">
				<?= @editor(array('name'=>'body','content'=> $topic->body, 'html'=>array('data-validators'=>'required maxLength:5000', 'cols'=>'50','rows'=>'10', 'class'=>'input-block-level','tabindex'=>2))) ?>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" id="privacy" ><?= @text('LIB-AN-PRIVACY-FORM-LABEL') ?></label>
			<div class="controls">
				<?= @helper('ui.privacy',array('entity'=>$topic, 'auto_submit'=>false, 'options'=>$actor)) ?>
			</div>
		</div>
			
		<div class="form-actions">
			<?php $cancelURL = ($topic->persisted()) ? $topic->getURL() : 'view=topics&oid='.$actor->id ?>
			<a class="btn" href="<?= @route($cancelURL) ?>"><?= @text('LIB-AN-ACTION-CANCEL') ?></a> 
			<?php $action = ($topic->persisted()) ? 'LIB-AN-ACTION-UPDATE' : 'LIB-AN-ACTION-POST' ?>
			<button class="btn btn-primary" tabindex="3"><?= @text($action) ?></button>
		</div>
	</fieldset>
</form>

<?= @message(@text('COM-TOPICS-ALLOWED-MARKUP-INSTRUCTIONS')) ?>
