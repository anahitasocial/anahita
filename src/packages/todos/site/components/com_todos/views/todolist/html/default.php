<?php defined('KOOWA') or die ?>

<script src="com_todos/js/todos.js" />

<module position="sidebar-b" title="<?= @text('LIB-AN-META') ?>">
	<ul class="an-meta">
		<li><?= sprintf( @text('LIB-AN-MEDIUM-AUTHOR'), @date($todolist->creationTime), @name($todolist->author)) ?></li>
		<?php if(isset($todo->editor)) : ?>
		<li><?= sprintf( @text('LIB-AN-MEDIUM-EDITOR'), @date($todolist->updateTime), @name($todolist->editor)) ?></li>
		<?php endif; ?>
		<li><?= sprintf(@text('COM-TODOS-TODOLIST-COUNTS'), (int) $todolist->numOfOpenTodos, (int) $todolist->numOfTodos) ?></li>
		
		<?php if(isset($todolist->milestone->id)): ?>
		<li>
			<?= @text('COM-TODOS-TODOLIST-META-MILESTONE') ?>: <a href="<?= @route($todolist->milestone->getURL()) ?>"><?= @escape($todolist->milestone->title) ?></a>
		</li>
		<?php endif; ?>
	</ul>
</module>

<?= @template('todolist') ?>

<?php if( $actor->authorize('action', 'com_todos:todo:add') ): ?>
<div class="btn-toolbar clearfix">
	<a id="new" class="btn btn-primary" data-trigger="ReadForm" href="<?= @route('view=todo&layout=add&oid='.$actor->id) ?>">
		<?= @text('COM-TODOS-TOOLBAR-TODO-NEW') ?>
	</a>
</div>
<?php endif; ?>

<?= 
    @controller('todo')
        ->q(empty($q) ? '' : $q)
        ->pid($todolist->id)
        ->layout('todolist')
        ->view('todos')
        ->display()            
?>