<?php $photo = @service('repos:photos.photo')->getEntity()->reset() ?>

<form id="photo-form" data-behavior="FormValidator ComposerForm" method="post" action="<?=@route($photo->getURL().'&oid='.$actor->id)?>" enctype="multipart/form-data">
    <fieldset>
	    <legend><?= @text('COM-PHOTOS-PHOTO-ADD')  ?></legend>		
		
		<div class="control-group">
			<label class="control-label" for="file"><?= @text('COM-PHOTOS-COMPOSER-FILE-SELECT') ?></label>	
			<div class="controls">
				<input data-validators="required" type="file" name="file" />
			</div>
		</div>
				
		<div class="control-group">
			<label class="control-label" for="body"><?= @text('COM-PHOTOS-COMPOSER-PHOTO-POST-DESCRIPTION') ?></label>
			<div class="controls">
				<textarea class="input-block-level" name="body" cols="10" rows="5" tabindex="2"></textarea>
			</div>
		</div>
				
		<div class="control-group">
			<label class="control-label" for="privacy" id="privacy"><?= @text('LIB-AN-PRIVACY-FORM-LABEL') ?></label>
			<div class="controls">
				<?= @helper('ui.privacy',array('entity'=>$photo, 'auto_submit'=>false, 'options'=>$actor)) ?>
			</div>
		</div>
                		
	</fieldset>
	
    <div class="form-actions">
        <button class="btn btn-primary"><?=@text('LIB-AN-ACTION-POST')?></button>
    </div>
</form>