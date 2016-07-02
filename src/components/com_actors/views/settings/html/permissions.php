<?php defined('KOOWA') or die; ?>

<?php if (defined('JDEBUG') && JDEBUG) : ?>
<script src="com_actors/js/permissions.js" />
<?php else: ?>
<script src="com_actors/js/min/permissions.min.js" />
<?php endif; ?>

<h3><?= @text('COM-ACTORS-PROFILE-EDIT-PERMISSIONS') ?></h3>
<form id="profile-permissions" action="<?=@route($item->getURL())?>" method="post">

<div class="control-group">
	<label class="control-label"  for="actor-privacy">
		<?= @text('COM-ACTORS-PRIVACY') ?>
	</label>

	<div class="controls">
		<?= @helper('ui.privacy', array('auto_submit' => false, 'entity' => $item)) ?>
    <?php if ($item->isFollowable()) : ?>
    <label class="checkbox">
        <input type="checkbox" disabled name="allowFollowRequest" value="1" <?= $item->allowFollowRequest ? 'checked' : ''?> >
        <?= @text('COM-ACTORS-PERMISSION-CAN-SEND-FOLLOW-REQUEST') ?>
    </label>
    <?php endif; ?>
	</div>
</div>

<?php if ($item->isAdministrable()): ?>
<div class="control-group">
	<label class="control-label"  for="leadables">
		<?= @text('COM-ACTORS-PERMISSION-CAN-ADD-LEADABLES') ?>
	</label>

	<div class="controls">
		<?= @helper('ui.privacy', array('entity' => $item, 'name' => 'leadable:add', 'auto_submit' => false))?>
	</div>
</div>
<?php endif; ?>

<?php foreach ($components as $component) : ?>
	<input type="hidden" name="action"  value="setprivacy" />
	<fieldset>
		<legend><?= $component->name ?></legend>
		<?php foreach ($component->permissions as $permission) : ?>
			<div class="control-group">
				<label class="control-label" ><?= $permission->label ?></label>
				<div class="controls">
					<?= @helper('ui.privacy', array('entity' => $item, 'name' => $permission->name, 'auto_submit' => false))?>
				</div>
			</div>
		<?php endforeach;?>
	</fieldset>
<?php endforeach;?>

    <div class="form-actions">
        <button type="submit" class="btn" data-loading-text="<?= @text('LIB-AN-ACTION-SAVING') ?>">
            <?= @text('LIB-AN-ACTION-SAVE'); ?>
        </button>
    </div>
</form>
