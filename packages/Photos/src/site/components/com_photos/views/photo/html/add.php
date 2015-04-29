<?php defined('KOOWA') or die('Restricted access'); ?>

<?php if(defined('JDEBUG') && JDEBUG ) : ?>
<script  src="com_photos/js/upload.js" />
<?php else: ?>
<script  src="com_photos/js/min/upload.min.js" />
<?php endif; ?>

<div class="row">
	<div class="span8">   
		<?= @helper('ui.header', array()) ?>
	    
	    <div id="photos-add">
	    	<div class="dropzone"></div>
	    	
	    	<form>
        	    <?php if ( $actor->authorize('administration')) : ?>
        		<div id="photo-privacy-selector" class="control-group">
        			<label class="control-label" for="privacy" id="privacy"><?= @text('LIB-AN-PRIVACY-FORM-LABEL') ?></label>
        			<div class="controls">
        				<?php $entity = @service('repos:photos.photo')->getEntity()->reset() ?>
        				<?= @helper('ui.privacy',array('entity'=>$entity, 'auto_submit'=>false, 'options'=>$actor)) ?>
        			</div>
        		</div>
                <?php endif;?>
            
            	<div class="form-actions">
            	    <button class="btn" data-trigger="RemovePhotos"><?= @text('LIB-AN-ACTION-CANCEL') ?></button>
            	    <button class="btn btn-primary" data-trigger="UploadPhotos"><?= @text('LIB-AN-ACTION-UPLOAD')?></button>
            	</div>
            </form>
        </div>

	</div>        		
</div>   

<script>
$('#photos-add').photoUpload({
	filedrop : '.dropzone',
	url : "<?= @route( 'view=photo&format=json&oid='.$actor->id ) ?>",
	setsUrl : "<?= @route('view=sets&oid='.$actor->id.'&layout=add_photos') ?>",
	parallelUploads : 2,
	maxFilesize : <?= get_config_value('photos.uploadlimit', 4) ?>,
	maxFiles : 10,
	addRemoveLinks : true,
	autoQueue: false,
	acceptedFiles : 'image/jpeg,image/jpg,image/png,image/gif',
	dictDefaultMessage : "<?= @text('COM-PHOTOS-UPLOAD-DROP-FILES-TO-UPLOAD') ?>",
	dictInvalidFileType : "<?= @text('COM-PHOTOS-UPLOAD-INVALID-FILE-TYPE') ?>",
	dictFileTooBig : "<?= sprintf(@text('COM-PHOTOS-UPLOAD-FILE-TOO-BIG'), get_config_value('photos.uploadlimit', 4)) ?>",
	dictRemoveFile : "<?= @text('LIB-AN-ACTION-REMOVE') ?>"
});
</script>     		