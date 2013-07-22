<?php defined('KOOWA') or die('Restricted access');?>

<module position="sidebar-b" style="simple"></module>

<?= @helper('ui.searchbox', @route('layout=list')) ?>

<div class="an-entities-wrapper">
<?= @template('list') ?>
</div>