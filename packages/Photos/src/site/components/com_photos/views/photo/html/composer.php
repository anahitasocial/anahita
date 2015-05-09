<?php defined('KOOWA') or die('Restricted access'); ?>

<?php $photo = @service('repos:photos.photo')->getEntity()->reset() ?>

<form class="composer-form" method="post" action="<?= @route() ?>" enctype="multipart/form-data">
    <fieldset>
	    <legend><?= @text('COM-PHOTOS-PHOTO-ADD')  ?></legend>		
		
		<div class="control-group">
			<label class="control-label" for="photo-file">
			    <?= @text('COM-PHOTOS-COMPOSER-FILE-SELECT') ?>
			</label>	
			<div class="controls">
				<input accept="image/*" id="photo-file" type="file" name="file" required autofocus />
			</div>
		</div>
				
		<div class="control-group">
			<label class="control-label" for="photo-description">
			    <?= @text('COM-PHOTOS-COMPOSER-PHOTO-POST-DESCRIPTION') ?>
			</label>
			<div class="controls">
				<textarea id="photo-description" class="input-block-level" name="body" cols="5" rows="3" maxlength="5000"></textarea>
			</div>
		</div>
				
		<div class="control-group">
			<label class="control-label" for="privacy">
			    <?= @text('LIB-AN-PRIVACY-FORM-LABEL') ?>
			</label>
			<div class="controls">
				<?= @helper('ui.privacy', array('entity'=>$photo, 'auto_submit'=>false, 'options'=>$actor)) ?>
			</div>
		</div>
                		
	</fieldset>
	
    <div class="form-actions">
        <button type="submit" class="btn btn-primary" data-loading-text="<?= @text('LIB-AN-MEDIUM-POSTING') ?>">
            <?=@text('LIB-AN-ACTION-SHARE')?>
        </button>
    </div>
</form>