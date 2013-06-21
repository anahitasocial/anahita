<?php defined('KOOWA') or die('Restricted access'); ?>

<script  src="lib_anahita/js/vendors/plupload.js" />
<script  src="lib_anahita/js/uploader.js" />
<script  src="com_photos/js/upload.js" />

<module position="sidebar-b" style="none"></module>

<?php 
//check if flash uploader can work
$use_flash = ini_get('session.use_cookies') && !ini_get('session.use_only_cookies') && !ini_get('session.cookie_httponly');
?>

<div data-behavior="PhotoUploader" data-photouploader-max-file-size="<?=get_config_value('photos.uploadlimit',4)?>mb" data-photouploader-form="form" data-photouploader-plugin-url='<?=@route('view=sets&oid='.$actor->id.'&layout=add_photos')?>'>
    <form class="upload-form" action="<?= @route( 'view=photo&format=json&oid='.$actor->id ) ?>" method="post" enctype="multipart/form-data">
    	<div class="file-list">
    	    <?= @message(@text('COM-PHOTOS-UPLOAD-NO-IMAGE-FILES-SELECTED')) ?>
    	</div>
    	
    	<?php if ( $actor->authorize('administration')) : ?>
		<div id="photo-privacy-selector" class="control-group hide">
			<label class="control-label" for="privacy" id="privacy"><?= @text('LIB-AN-PRIVACY-FORM-LABEL') ?></label>
			<div class="controls">
				<?php $entity = @service('repos:photos.photo')->getEntity()->reset() ?>
				<?= @helper('ui.privacy',array('entity'=>$entity, 'auto_submit'=>false, 'options'=>$actor)) ?>
			</div>
		</div>
        <?php endif;?>	
    	
    	<div class="form-actions">
    	    <button class="select btn"><?=@text('COM-PHOTOS-UPLOAD-CHOOSE-PHOTOS')?></button>
    	    <button class="btn btn-primary"><?=@text('COM-PHOTOS-UPLOAD')?></button>
    	</div>
    </form>
</div>
		