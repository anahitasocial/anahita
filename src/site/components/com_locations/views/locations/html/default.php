<?php defined('KOOWA') or die; ?>

<?= @helper('ui.header', array()) ?>
<?= @helper('ui.filterbox', @route('layout=list')) ?>

<div class="an-entities masonry" data-trigger="InfiniteScroll" data-url="<?= @route('layout=list&sort='.$sort) ?>">
<?= @template('list') ?>
</div>
