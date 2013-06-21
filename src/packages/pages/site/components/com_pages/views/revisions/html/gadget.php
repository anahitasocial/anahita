<?php defined('KOOWA') or die; ?>

<div data-behavior="InfinitScroll" data-infinitscroll-options="{'url':'<?= @route('layout=gadget') ?>'}" class="an-entities">
<?= @template('gadget_list') ?>
</div>

<div class="an-loading-prompt hide">
	<?= @message(@text('LIB-AN-LOADING-PROMPT')) ?>
</div>

