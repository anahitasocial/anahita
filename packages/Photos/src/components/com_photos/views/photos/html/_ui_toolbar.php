<? defined('KOOWA') or die('Restricted access');?>

<div class="btn-toolbar clearfix">
    <div class="pull-left">
        <?= @helper('ui.commands', $toolbar->getCommands()->class('btn btn-primary')) ?>
    </div>

    <? $layout = $toolbar->getController()->getRequest()->layout ?>
    <div class="pull-right btn-group">
        <a class="btn <?= ($layout != 'masonry') ? 'disabled' : '' ?>" href="<?= @route('layout=default') ?>">
            <i class="icon-list"></i>
            <?= @text('LIB-AN-MEDIUMS-LIST-VIEW') ?>
        </a>
        <a class="btn <?= ($layout == 'masonry') ? 'disabled' : '' ?>" href="<?= @route('layout=masonry') ?>">
            <i class="icon-th"></i>
            <?= @text('LIB-AN-MEDIUMS-MASONRY-VIEW') ?>
        </a>
    </div>
</div>