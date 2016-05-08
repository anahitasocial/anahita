<? defined('KOOWA') or die; ?>

<div class="btn-toolbar clearfix">
    <div class="pull-right btn-group">
        <a class="btn <?= ($sort == 'name') ? 'disabled' : '' ?>" href="<?= @route('sort=name') ?>">
            <i class="icon icon-book"></i>
            <?= @text('LIB-AN-SORT-NAME') ?>
        </a>
        <a class="btn <?= ($sort == 'ordering') ? 'disabled' : '' ?>" href="<?= @route('sort=ordering') ?>">
            <i class="icon icon-resize-vertical"></i>
            <?= @text('LIB-AN-SORT-ORDERING') ?>
        </a>
    </div>
</div>
