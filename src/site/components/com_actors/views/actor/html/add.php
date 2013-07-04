<?php defined('KOOWA') or die ?>

<module position="sidebar-b" style="none"></module>
<?php $entity = empty($entity) ?@controller($this->getView()->getName())->getRepository()->getEntity()->reset() : $entity; ?>
<form data-behavior="FormValidator" action="<?= @route($entity->getURL()) ?>" method="post" enctype="multipart/form-data">		

	<div class="control-group">
		<label class="label-group"  for="actor-name">
			<?= @text('COM-ACTORS-NAME') ?>
		</label>
		<div class="controls">
			<input data-validators="required" class="input-block-level" size="30" maxlength="100" name="name" value="<?=$entity->name?>" type="text" />
		</div>
	</div>
	
	<div class="control-group">
		<label class="label-group"  for="actor-body">
			<?= @text('COM-ACTORS-BODY') ?>
		</label>
		<div class="controls">
			<textarea data-validators="required maxLength:1000" class="input-block-level" name="body" rows="5"><?= $entity->body?></textarea>
		</div>
	</div>

	<?php if ( $entity->isEnableable() ) : ?>
	<div class="control-group">
		<label class="label-group"  for="actor-enabled">
			<?= @text('COM-ACTORS-ENABLED') ?>
		</label>
		<div class="controls">
			<?= @html('select','enabled', array('options'=>array(@text('LIB-AN-NO'), @text('LIB-AN-YES')), 'selected'=>$entity->enabled))->class('input-small') ?>
		</div>
	</div>
	<?php endif;?>
	
	<?php if ( $entity->isPrivatable() ) : ?>
	<div class="control-group">
		<label class="label-group"  for="actor-privacy">
			<?= @text('COM-ACTORS-PRIVACY') ?>
		</label>
		<div class="controls">
			<?= @helper('ui.privacy',array('auto_submit'=>false, 'entity'=>$entity))?>
		</div>
	</div>	
	<?php endif;?>
	
	<div class="form-actions">
		<a href="javascript:history.go(-1)" class="btn"><?= @text('LIB-AN-ACTION-CANCEL') ?></a>			
		<button type="submit" class="btn btn-primary"><?= @text('LIB-AN-ACTION-SAVE') ?></button>
	</div>
</form>