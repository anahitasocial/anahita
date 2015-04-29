<?php defined('KOOWA') or die ?>

<?= @helper('ui.header', array()) ?>

<div data-behavior="InfiniteScroll" data-InfiniteScroll-options="{'url':'<?= @route('layout=list') ?>'}" class="an-entities masonry">
<?= @template('list') ?>	
</div>