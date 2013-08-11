<?php defined('KOOWA') or die ?>

<module position="sidebar-b" title="<?= @text('LIB-AN-META') ?>">	
	<ul class="an-meta">
		<?php if(isset($todo->editor)) : ?>
		<li><?= sprintf( @text('LIB-AN-MEDIUM-EDITOR'), @date($todo->updateTime), @name($todo->editor)) ?></li>
		<?php endif; ?>
		<?php if(!$todo->open) : ?>
		<li>				
			<?= sprintf(@text('COM-TODOS-TODOLIST-COMPLETED-BY-REPORT'), @date($todo->openStatusChangeTime), @name($todo->lastChanger)) ?>
		</li>
		<?php endif; ?>
		<li><?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $todo->numOfComments) ?></li>
	</ul>
</module>

<?php if ( $actor->authorize('administration') ) : ?>
<module position="sidebar-b" title="<?= @text('COM-TODOS-TODO-PRIVACY') ?>">
	<?= @helper('ui.privacy',$todo) ?>
</module>
<?php else: ?>
<module position="sidebar-b" style="none"></module>
<?php endif; ?>

<?= @template('todo') ?>

<?= @helper('ui.comments', $todo, array('pagination'=>true)) ?>