<?php defined('KOOWA') or die('Restricted access') ?>

<?php @commands('toolbar') ?>

<?php $highlight = ($todo->open) ? 'an-highlight' : '' ?>
<div class="an-entity <?= $highlight ?>">
	<div class="clearfix">
		<div class="entity-portrait-square"><?= @avatar($todo->author) ?></div>
		
		<div class="entity-container">
			<h4 class="author-name"><?= @name($todo->author) ?></h4>
			<div class="an-meta">
				<?= @date($todo->creationTime) ?>
			</div>
		</div>
	</div>
	
	<h3 class="entity-title"><?= @escape($todo->title) ?></h3>
		
	<?php if($todo->description): ?>
	<div class="entity-description"><?= @content( $todo->description ) ?></div>
	<?php endif; ?>
		
	<div class="entity-meta">
		<ul class="an-meta inline">
			<?php if(isset($todo->todolist->id)): ?>
			<li><?= @text('COM-TODOS-TODO-META-TODOLIST') ?>: <a href="<?= @route($todo->todolist->getURL()) ?>"><?= @escape($todo->todolist->title) ?></a></li>
			<?php endif; ?>
			<li><?= @text('COM-TODOS-TODO-PRIORITY') ?>: <span class="priority <?= @helper('priorityLabel', $todo) ?>"><?= @helper('priorityLabel', $todo) ?></span></li>
		</ul>
		
		<div class="an-meta" class="vote-count-wrapper" id="vote-count-wrapper-<?= $todo->id ?>">
			<?= @helper('ui.voters', $todo); ?>
		</div>
	</div>
</div>