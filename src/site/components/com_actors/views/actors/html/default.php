<?php defined('KOOWA') or die; ?>

<module position="sidebar-b" style="none"></module>

<?= @helper('ui.filterbox', @route('layout=list')) ?>

<div class="an-entities-wrapper">	
	<?= @template('list') ?>
</div>