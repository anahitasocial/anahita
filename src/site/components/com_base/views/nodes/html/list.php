<?php defined('KOOWA') or die('Restricted access');?>

<div></div>

<?php if (count($items)) :?>
	<?php $view = @view('node'); ?>
	<?php foreach ($items as $item): ?>

	<?php if ($item->inherits('ComBaseDomainEntityComment')): ?>
	<?= $view->layout('list_comment')->item($item) ?>
	<?php elseif ($item->inherits('ComActorsDomainEntityActor')): ?>
	<?= $view->layout('list_actor')->item($item) ?>
	<?php else: ?>
	<?= $view->layout('list')->item($item) ?>
	<?php endif; ?>

	<?php endforeach; ?>
<?php else: ?>
<?= @message(@text('LIB-AN-NODES-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>
