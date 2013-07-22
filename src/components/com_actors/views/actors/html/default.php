<?php defined('KOOWA') or die; ?>

<module position="sidebar-b" style="simple"></module>

<?= @helper('ui.searchbox', @route('layout=list'))?>

<div class="an-entities-wrapper">	
	<?= @template('list') ?>
</div>