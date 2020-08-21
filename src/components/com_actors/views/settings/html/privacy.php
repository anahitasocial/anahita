<? defined('ANAHITA') or die; ?>

<? if (defined('ANDEBUG') && ANDEBUG) : ?>
<script src="com_actors/js/privacy.js" />
<? else: ?>
<script src="com_actors/js/min/privacy.min.js" />
<? endif; ?>

<h3><?= @text('COM-ACTORS-PROFILE-EDIT-PRIVACY') ?></h3>

<form id="profile-privacy" action="<?=@route($item->getURL())?>" method="post">
    <input type="hidden" name="action" value="setprivacy" />
    
    <div class="control-group">
    	<label class="control-label"  for="actor-privacy">
    		<?= @text('COM-ACTORS-PRIVACY') ?>
    	</label>

    	<div class="controls">
    		<?= @helper('ui.privacy', array(
                'auto_submit' => false, 
                'entity' => $item
            )) ?>
        <? if ($item->isFollowable()) : ?>
        <label class="checkbox">
            <input type="checkbox" disabled name="allowFollowRequest" value="1" <?= $item->allowFollowRequest ? 'checked' : ''?> >
            <?= @text('COM-ACTORS-PERMISSION-CAN-SEND-FOLLOW-REQUEST') ?>
        </label>
        <? endif; ?>
    	</div>
    </div>
    
    <? if ($item->isAdministrable()): ?>
    <div class="control-group">
    	<label class="control-label"  for="leadables">
    		<?= @text('COM-ACTORS-PERMISSION-CAN-ADD-LEADABLES') ?>
    	</label>

    	<div class="controls">
    		<?= @helper('ui.privacy', array(
                'entity' => $item, 
                'name' => 'leadable:add', 
                'auto_submit' => false
            ))?>
    	</div>
    </div>
    <? endif; ?>
    
    <div class="form-actions">
        <button 
            type="submit" 
            class="btn" 
            data-loading-text="<?= @text('LIB-AN-ACTION-SAVING') ?>"
        >
            <?= @text('LIB-AN-ACTION-SAVE'); ?>
        </button>
    </div>
</form>