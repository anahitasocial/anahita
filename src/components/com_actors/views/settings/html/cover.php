<? defined('KOOWA') or die ?>

<? $uploadSizeLimit = ini_get('upload_max_filesize'); ?>

<h3><?= @text('LIB-AN-COVER-EDIT') ?></h3>

<? if ($item->coverSet()): ?>
<p><?= @cover($item, 'medium', false) ?></p>
<? endif ?>

<p class="lead"><?= sprintf(@text('LIB-AN-COVER-SELECT-IMAGE-ON-YOUR-COMPUTER'), $uploadSizeLimit, 1600) ?></p>

<form id="actor-cover" action="<?= @route($item->getURL().'&edit=cover') ?>" method="post" enctype="multipart/form-data">

    <div class="control-group">
        <div class="controls">
            <input type="file" name="cover" accept="image/*" data-limit="<?= $uploadSizeLimit ?>" />
        </div>
    </div>

    <? if ($item->coverSet()): ?>
    <div class="form-actions">
        <button data-trigger="DeleteCover" class="btn btn-danger">
            <?= @text('LIB-AN-COVER-DELETE') ?>
        </button>
    </div>
    <? endif ?>
</form>
