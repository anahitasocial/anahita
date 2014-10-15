<?php defined('KOOWA') or die('Restricted access');?>

<script src="lib_anahita/js/vendors/mediabox.js" />

<?= @helper('ui.header', array()) ?>

<?php if(count($photos)) : ?>
<div data-behavior="InfinitScroll" data-infinitscroll-options="{'url':'<?= @route('layout=masonry_list') ?>'}" class="an-entities masonry">
<?= @template('masonry_list') ?>
</div>
<?php else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>
