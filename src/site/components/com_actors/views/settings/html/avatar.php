<?php defined('KOOWA') or die ?>

<h3><?= @text('COM-ACTORS-PROFILE-EDIT-AVATAR') ?></h3>

<p><?= @avatar($item, 'medium', false) ?></p>
    
<form id="actor-avatar" action="<?= @route($item->getURL().'&edit=avatar') ?>" method="post" enctype="multipart/form-data">
    
    <p><?= @text('LIB-AN-AVATAR-SELECT-IMAGE-ON-YOUR-COMPUTER') ?></p>

    <div class="control-group">
        <div class="controls">
            <input type="file" name="portrait" />
        </div>
    </div>
    
    <?php if($item->portraitSet()): ?>
    <div class="form-actions">
        <button data-trigger="DeleteAvatar" class="btn btn-danger">
            <?= @text('LIB-AN-AVATAR-REMOVE-AVATAR') ?>
        </button>
    </div>
    <?php endif ?>
</form>