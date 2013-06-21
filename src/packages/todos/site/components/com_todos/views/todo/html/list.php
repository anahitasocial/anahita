<?php defined('KOOWA') or die ?>

<?php $highlight = ($todo->open) ? 'an-highlight' : '' ?>
<div class="an-entity an-record an-removable <?= $highlight ?>">
	<div class="entity-portrait-square">
		<?= @avatar($todo->author) ?>
	</div>
	
	<div class="entity-container">
		<h3 class="entity-title">
			<a href="<?= @route($todo->getURL()) ?>"><?= @escape($todo->title) ?></a>
		</h3>
		
		<?php if($todo->description): ?>
		<div class="entity-description">
			<?= @helper('text.truncate', @content($todo->description), array('length'=>500, 'consider_html'=>true, 'read_more'=>true)); ?>
		</div>
		<?php endif; ?>
		
		<div class="entity-meta">
			<div class="an-meta">
				<?= sprintf( @text('LIB-AN-MEDIUM-AUTHOR'), @date($todo->creationTime), @name($todo->author)) ?>
			</div>
			
			<?php if( !isset($pid) && isset($todo->todolist) ): ?>
			<div class="an-meta">
				<?= @text('COM-TODOS-TODO-META-TODOLIST') ?>: <a href="<?= @route($todo->todolist->getURL()) ?>"><?= @escape($todo->todolist->title) ?></a>
			</div>
			<?php endif; ?>

			<?php if(!$todo->open) : ?>
			<div class="an-meta">				
				<?= sprintf(@text('COM-TODOS-TODOLIST-COMPLETED-BY-REPORT'), @date($todo->openStatusChangeTime), @name($todo->lastChanger)) ?>
			</div>
			<?php endif; ?>
			
			<?php if($filter == 'leaders'): ?>
			<div class="an-meta">
				<?= sprintf(@text('LIB-AN-MEDIUM-OWNER'), @name($todo->owner)) ?>
			</div>
			<?php endif; ?>
			
			<div class="an-meta">
				<?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $todo->numOfComments) ?>
			</div>
			
			<div class="an-meta">
				<?= @text('COM-TODOS-TODO-PRIORITY') ?>: <span class="priority <?= @helper('priorityLabel', $todo) ?>"><?= @helper('priorityLabel', $todo) ?></span>
			</div>
			
			<div class="an-meta" class="vote-count-wrapper" id="vote-count-wrapper-<?= $todo->id ?>">
				<?= @helper('ui.voters', $todo); ?>
			</div>
		</div>
		
		<div class="entity-actions">
			<?= @helper('ui.commands', @commands('list')) ?>
		</div>
	</div>
</div>