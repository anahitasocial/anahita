<?php defined('KOOWA') or die; ?>

<?= @helper('ui.header', array()) ?>

<?= @helper('ui.filterbox', @route('layout=list')) ?>

<div id="an-actors" class="an-entities masonry" data-trigger="InfiniteScroll" data-url="<?= @route('layout=list') ?>">
<?= @template('list') ?>
</div>