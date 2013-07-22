<?php defined('KOOWA') or die; ?>

<h3><?= @text('COM-ACTORS-PROFILE-EDIT-PERMISSIONS') ?></h3>
<form action="<?=@route($item->getURL())?>" method="post">
<div class="control-group">
	<label class="control-label"  for="actor-privacy">
		<?= @text('COM-ACTORS-PRIVACY') ?>
	</label>
	<div class="controls">
		<?= @helper('ui.privacy',array('auto_submit'=>false, 'entity'=>$item))?>
        <?php if ( $item->isFollowable() ) : ?>
        <label class="checkbox">
            <input type="checkbox" name="allowFollowRequest" value="1" <?= $item->allowFollowRequest ? 'checked' : ''?> >
            <?= @text('COM-ACTORS-PERMISSION-CAN-SEND-FOLLOW-REQUEST') ?>
        </label>
        <script data-inline>
            (function(){
                var toggle = function() {
                    var select = document.getElement('select[name="access"]');
                    var allowRequest = document.getElement('input[name="allowFollowRequest"] !label');
                    if ( select.value == 'followers' ) {
                        allowRequest.show();
                    } else {
                        allowRequest.hide();
                    }
                };
                'select[name="access"]'.addEvent('change', toggle);
                toggle();
            })();
        </script>
        <?php endif; ?>
	</div>
</div>
<?php foreach($apps_resources as $app_name => $resources) : ?>
	<input type="hidden" name="action"  value="setprivacy" />
	<fieldset>
		<legend><?= $app_name ?></legend>
		<?php foreach($resources as $label => $operation) : ?>
			<div class="control-group">
				<label class="control-label" ><?= $label ?></label>
				<div class="controls">
					<?= @helper('ui.privacy',array('entity'=>$item, 'name'=>$operation,'auto_submit'=>false))?>
				</div>
			</div>
		<?php endforeach;?>
	</fieldset>
<?php endforeach;?>
<div class="form-actions">			
	<input type="submit" data-trigger="Request" class="btn" value="<?= @text('LIB-AN-ACTION-SAVE') ?>" />
</div>
</form>