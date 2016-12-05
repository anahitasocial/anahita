<? defined('KOOWA') or die; ?>

<? if (defined('ANDEBUG') && ANDEBUG) : ?>
<script src="com_actors/js/permissions.js" />
<? else: ?>
<script src="com_actors/js/min/permissions.min.js" />
<? endif; ?>

<h3><?= @text('COM-ACTORS-PROFILE-EDIT-PERMISSIONS') ?></h3>
<form id="profile-permissions" action="<?=@route($item->getURL())?>" method="post">

<div class="control-group">
	<label class="control-label"  for="actor-privacy">
		<?= @text('COM-ACTORS-PRIVACY') ?>
	</label>

	<div class="controls">
		<?= @helper('ui.privacy', array('auto_submit' => false, 'entity' => $item)) ?>
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
		<?= @helper('ui.privacy', array('entity' => $item, 'name' => 'leadable:add', 'auto_submit' => false))?>
	</div>
</div>
<? endif; ?>

<? foreach ($components as $component) : ?>
	<input type="hidden" name="action"  value="setprivacy" />
	<fieldset>
		<legend><?= $component->name ?></legend>
		<? foreach ($component->permissions as $permission) : ?>
			<div class="control-group">
				<label class="control-label" ><?= $permission->label ?></label>
				<div class="controls">
					<?= @helper('ui.privacy', array('entity' => $item, 'name' => $permission->name, 'auto_submit' => false))?>
				</div>
			</div>
		<? endforeach;?>
	</fieldset>
<? endforeach;?>

    <div class="form-actions">
        <button type="submit" class="btn" data-loading-text="<?= @text('LIB-AN-ACTION-SAVING') ?>">
            <?= @text('LIB-AN-ACTION-SAVE'); ?>
        </button>
    </div>
</form>
