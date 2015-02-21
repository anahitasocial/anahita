<?php defined('KOOWA') or die ?>

<?= @helper('ui.header', array()) ?>

<?php if(count($sets)): ?>
<div data-behavior="InfiniteScroll" data-InfiniteScroll-options="{'url':'<?= @route('layout=list') ?>'}" class="an-entities masonry">
<?= @template('list') ?>	
</div>
<?php else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>