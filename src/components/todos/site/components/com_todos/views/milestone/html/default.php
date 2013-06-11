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

<module position="sidebar-b" title="<?= @text('LIB-AN-META') ?>">
<ul class="an-meta">
	<li><?= sprintf( @text('LIB-AN-MEDIUM-AUTHOR'), @date($milestone->creationTime), @name($milestone->author)) ?></li>
	<?php if(isset($photo->editor)) : ?>
	<li><?= sprintf( @text('LIB-AN-MEDIUM-EDITOR'), @date($milestone->updateTime), @name($milestone->editor)) ?></li>
	<?php endif; ?>
	<li><?= sprintf(@text('COM-TODOS-MILESTONE-META-END-DATE'), @date($milestone->endDate)) ?></li>
	<li><?= sprintf(@text('COM-TODOS-MILESTONE-NUMBER-OF-TODOLISTS'), (int) $milestone->numOfTodolists) ?></li>
	
	<?php if ( $milestone->numOfComments ) : ?>
	<li><?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $milestone->numOfComments) ?></li>
	<li><?= sprintf(@text('COM-TODOS-MEDIUM-LAST-COMMENT-BY-X-ON-DATETIME'),@name($milestone->lastCommenter), @date($milestone->lastCommentTime)) ?></li>
	<?php endif; ?>
</ul>
</module>

<?= @template('milestone') ?>

<?= @helper('ui.comments', $milestone, array('pagination'=>true)) ?>