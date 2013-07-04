<?php defined('KOOWA') or die('Restricted access') ?>

<?php @commands('toolbar') ?>

<?php $highlight = ($todo->open) ? 'an-highlight' : '' ?>
<div class="an-entity <?= $highlight ?>">
	<div class="entity-portrait-square"><?= @avatar($todo->author) ?></div>
	
	<div class="entity-container">
		<h3 class="entity-title"><?= @escape($todo->title) ?></h3>
		
		<?php if($todo->description): ?>
		<div class="entity-description"><?= @content( $todo->description ) ?></div>
		<?php endif; ?>
		
		<div class="entity-meta">
			<?php if(isset($todo->todolist->id)): ?>
			<div class="an-meta">
				<?= @text('COM-TODOS-TODO-META-TODOLIST') ?>: <a href="<?= @route($todo->todolist->getURL()) ?>"><?= @escape($todo->todolist->title) ?></a>
			</div>
			<?php endif; ?>
			
			<div class="an-meta">
				<?= @text('COM-TODOS-TODO-PRIORITY') ?>: <span class="priority <?= @helper('priorityLabel', $todo) ?>"><?= @helper('priorityLabel', $todo) ?></span>
			</div>
			
			<div class="an-meta" class="vote-count-wrapper" id="vote-count-wrapper-<?= $todo->id ?>">
				<?= @helper('ui.voters', $todo); ?>
			</div>
		</div>
	</div>
</div>