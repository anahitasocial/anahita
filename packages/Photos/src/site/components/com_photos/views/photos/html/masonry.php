<?php defined('KOOWA') or die('Restricted access');?>

<?= @helper('ui.header', array()) ?>

<?php if(count($photos)) : ?>
<div id="an-photos" class="an-entities masonry" data-trigger="InfiniteScroll" data-url="<?= @route('layout=masonry_list') ?>">
<?= @template('masonry_list') ?>
</div>
<?php else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>
