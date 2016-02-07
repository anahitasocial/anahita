<?php defined('KOOWA') or die('Restricted access');?>

<?php $view = @view('node')->layout('list'); ?>
<?php foreach ($items as $item): ?>
<?= $view->item($item) ?>
<?php endforeach; ?>
