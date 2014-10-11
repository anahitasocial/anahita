<?php defined('KOOWA') or die; ?>

<?= @helper('ui.header', array()) ?>

<?= @helper('ui.filterbox', @route('layout=list')) ?>

<div class="an-entities-wrapper">
	<?= @template('list') ?>
</div>