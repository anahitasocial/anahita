<?php defined('KOOWA') or die ?>

<h3><?= @text('COM-ACTORS-PROFILE-EDIT-AVATAR') ?></h3>

<form id="actor-avatar" action="<?= @route($item->getURL().'&edit=avatar') ?>" method="post" enctype="multipart/form-data">
    <p><?= @avatar($item, 'medium') ?></p>
    <p><?= @text('LIB-AN-AVATAR-SELECT-IMAGE-ON-YOUR-COMPUTER') ?></p>
    
    <div class="control-group">
        <div class="controls">
            <input class="input-file" type="file" name="portrait" onchange="this.form.spin(); this.form.submit();" />
        </div>
    </div>
    <?php if($item->portraitSet()): ?>
        <div class="form-actions">
            <button onclick="this.form.spin(); this.form.submit();" class="btn btn-danger"><?= @text('LIB-AN-AVATAR-REMOVE-AVATAR') ?></button>
        </div>
    <?php endif ?>
</form>