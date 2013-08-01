<?php defined('KOOWA') or die('Restricted access'); ?>

<div class="an-entity">
	<div class="entity-portrait-square">
		<?= @avatar($todo->author) ?>
	</div>

	<div class="entity-container">
		<h4 class="entity-title">
			<a href="<?= @route($todo->getURL()) ?>"><?= @escape($todo->title) ?></a>
		</h4>
		
		<?php if($todo->description): ?>
		<div class="entity-description">
			<?= @helper('text.truncate', strip_tags($todo->description), array('length'=>100)); ?>
		</div>
		<?php endif; ?>
		
		<div class="entity-meta">
			<div class="an-meta">
				<?= sprintf( @text('LIB-AN-MEDIUM-AUTHOR'), @date($todo->creationTime), @name($todo->author)) ?> 
			</div>

			<?php if($filter == 'leaders'): ?>
			<div class="an-meta">
				<?= sprintf(@text('LIB-AN-MEDIUM-OWNER'), @name($todo->owner)) ?>
			</div>
			<?php endif; ?>
			
			<div class="an-meta">
				<?= @text('COM-TODOS-TODO-PRIORITY') ?>: <span class="priority <?= @helper('priorityLabel', $todo) ?>"><?= @helper('priorityLabel', $todo) ?></span>
			</div>
			
			<div class="an-meta">
				<div class="vote-count-wrapper" id="vote-count-wrapper-<?= $todo->id ?>">
					<?= @helper('ui.voters', $todo); ?>
				</div>
			</div>
		</div>
	</div>
</div>