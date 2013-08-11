<?php defined('KOOWA') or die ?>

<module position="sidebar-b" title="<?= @text('COM-TODOS-MILESTONE-TODOLISTS') ?>">
<?php if( count($milestone->todolists)): ?>
<?php $todolists = $milestone->todolists->limit(5); ?>
<ul>
	<?php foreach($todolists as $todolist): ?>
	<li>
		<a href="<?= @route($todolist->getURL()) ?>"><?= @escape($todolist->title) ?></a>
	</li>
	<?php endforeach; ?>
</ul>
<?php else: ?>
<?= @message(@text('COM-TODOS-TODOLISTS-EMPTY-LIST-MESSAGE')) ?>
<?php endif; ?>
</module>

<?= @template('milestone') ?>

<?= @helper('ui.comments', $milestone, array('pagination'=>true)) ?>