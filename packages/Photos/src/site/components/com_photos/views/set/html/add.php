<?php defined('KOOWA') or die('Restricted access');?>

<?php if(defined('JDEBUG') && JDEBUG ) : ?>
<script src="com_photos/js/organizer.js" />
<?php else: ?>
<script src="com_photos/js/min/organizer.min.js" />
<?php endif; ?>

<div class="row">
	<div class="span8">
    
    <?= @helper('ui.header', array()) ?>
    
    <form id="set-form" method="post" action="<?= @route('view=set&oid='.$actor->id) ?>">
    	<input type="hidden" value="addphoto" name="action" />
    	
    	<fieldset>
    		<legend><?= @text('COM-PHOTOS-SET-ADD') ?></legend>
    		<div class="control-group">
    			<label class="control-label" for="set-title">
    			    <?= @text('LIB-AN-MEDIUM-TITLE') ?>
    			</label>
    			
    			<div class="controls">
    				<input class="input-block-level" id="set-title" name="title" size="50" maxlength="255" type="text" required>
    			</div>
    		</div>
    		
    		<div class="control-group">
    			<label class="control-label" for="set-description">
    			    <?= @text('LIB-AN-MEDIUM-DESCRIPTION') ?>
    			</label>
    			
    			<div class="controls">
    				<textarea maxlength="5000" id="set-description" class="input-block-level" name="description" cols="50" rows="5"></textarea>
    			</div>
    		</div>
    
    		<div id="photo-selector">
    			
    		</div>
    		
    		<div id="set-photos" class="an-entities">
    			<div class="media-grid"></div>
    		</div>
    			
    		<div class="form-actions">
    			<a class="btn" href="<?= @route(array('view'=>'sets', 'oid'=>$actor->id)) ?>">
    			    <?= @text('LIB-AN-ACTION-CANCEL') ?>
    			</a> 
    			<button type="submit" class="btn btn-primary">
    			    <?= @text('LIB-AN-ACTION-ADD') ?>
    			</button>
    		</div>
    	</fieldset>
    </form>

	</div>
</div>

<script>
<?php $url = 'option=com_photos&view=photos&layout=selector&oid='.$actor->id; ?>
$('#photo-selector').setOrganizer( 'open', '<?= @route($url) ?>');		
</script>