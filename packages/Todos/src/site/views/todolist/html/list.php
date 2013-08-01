<?php defined('KOOWA') or die ?>

<div class="an-entity an-record an-removable">
	<h3 class="entity-title">
		<a href="<?= @route( $todolist->getURL() ) ?>">
			<?= @escape($todolist->title) ?>
		</a>
	</h3>
	
	<?php if( $todolist->description ): ?>
	<div class="entity-description">
			<?= @helper('text.truncate', @content($todolist->description), array('length'=>500, 'consider_html'=>true, 'read_more'=>true)); ?>
		</div>
	<?php endif; ?>
	
	<div class="entity-meta">	
		<?php if(isset($todolist->milestone->id)): ?>
		<div class="an-meta">
		<?= @text('COM-TODOS-TODOLIST-META-MILESTONE') ?>: <a href="<?= @route($todolist->milestone->getURL()) ?>"><?= @escape($todolist->milestone->title) ?></a>
		</div>
		<?php endif; ?>
		
		<div class="an-meta">
			<?= sprintf(@text('COM-TODOS-TODOLIST-COUNTS'), (int) $todolist->numOfOpenTodos, (int) $todolist->numOfTodos) ?>
		</div>
		
		<div class="an-meta" class="vote-count-wrapper" id="vote-count-wrapper-<?= $todolist->id ?>">
			<?= @helper('ui.voters', $todolist); ?>
		</div>
	</div>
</div>