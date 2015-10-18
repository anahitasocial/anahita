<?php defined('KOOWA') or die; ?>

<?= @helper('ui.header', array()) ?>

<div class="an-entities masonry" data-trigger="InfiniteScroll" data-url="<?= @route('layout=list') ?>">
<?= @template('list') ?>
</div>
