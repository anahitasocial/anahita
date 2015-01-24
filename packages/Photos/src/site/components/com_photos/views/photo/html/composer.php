<?php $photo = @service('repos:photos.photo')->getEntity()->reset() ?>

<form class="composer-form" method="post" action="<?= @route($photo->getURL().'&oid='.$actor->id) ?>" enctype="multipart/form-data">
    <fieldset>
	    <legend><?= @text('COM-PHOTOS-PHOTO-ADD')  ?></legend>		
		
		<div class="control-group">
			<label class="control-label" for="file">
			    <?= @text('COM-PHOTOS-COMPOSER-FILE-SELECT') ?>
			</label>	
			<div class="controls">
				<input type="file" name="file" />
			</div>
		</div>
				
		<div class="control-group">
			<label class="control-label" for="body">
			    <?= @text('COM-PHOTOS-COMPOSER-PHOTO-POST-DESCRIPTION') ?>
			</label>
			<div class="controls">
				<textarea class="input-block-level" name="body" cols="10" rows="5" tabindex="2"></textarea>
			</div>
		</div>
				
		<div class="control-group">
			<label class="control-label" for="privacy" id="privacy">
			    <?= @text('LIB-AN-PRIVACY-FORM-LABEL') ?>
			</label>
			<div class="controls">
				<?= @helper('ui.privacy', array('entity'=>$photo, 'auto_submit'=>false, 'options'=>$actor)) ?>
			</div>
		</div>
                		
	</fieldset>
	
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">
            <?=@text('LIB-AN-ACTION-SHARE')?>
        </button>
    </div>
</form>