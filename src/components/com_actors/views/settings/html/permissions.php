<? defined('ANAHITA') or die; ?>

<h3><?= @text('COM-ACTORS-PROFILE-EDIT-PERMISSIONS') ?></h3>
<form action="<?=@route($item->getURL())?>" method="post">
	<input type="hidden" name="action" value="setpermission" />
	<? foreach ($components as $component) : ?>
	<fieldset>
		<legend><?= $component->name ?></legend>
		<? foreach ($component->permissions as $permission) : ?>
			<div class="control-group">
				<label class="control-label" ><?= $permission->label ?></label>
				<div class="controls">
					<?= @helper('ui.privacy', array(
						'entity' => $item, 
						'name' => $permission->name, 
						'auto_submit' => false
					))?>
				</div>
			</div>
		<? endforeach;?>
	</fieldset>
	<? endforeach;?>

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
