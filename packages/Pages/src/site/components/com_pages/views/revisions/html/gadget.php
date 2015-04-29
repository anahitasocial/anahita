<?php defined('KOOWA') or die; ?>

<div data-behavior="InfiniteScroll" data-InfiniteScroll-options="{'url':'<?= @route('layout=gadget') ?>'}" class="an-entities">
<?= @template('gadget_list') ?>
</div>

<div class="an-loading-prompt hide">
	<?= @message(@text('LIB-AN-LOADING-PROMPT')) ?>
</div>

