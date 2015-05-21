<?php defined('KOOWA') or die('Restricted access') ?>

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
	<div class="entity-description">
	<?= @content( nl2br($todo->description) ); ?>
	</div>
	<?php endif; ?>
		
	<div class="entity-meta">
		<ul class="an-meta inline">
			<li><?= @text('COM-TODOS-TODO-PRIORITY') ?>: <span class="priority <?= @helper('priorityLabel', $todo) ?>"><?= @helper('priorityLabel', $todo) ?></span></li>
		</ul>
		
		<div class="an-meta" class="vote-count-wrapper" id="vote-count-wrapper-<?= $todo->id ?>">
			<?= @helper('ui.voters', $todo); ?>
		</div>
	</div>
</div>