<?php defined('KOOWA') or die ?>

<?php $highlight = ($todo->open) ? 'an-highlight' : '' ?>
<div class="an-entity an-record an-removable <?= $highlight ?>">
	<div class="clearfix">
		<div class="entity-portrait-square">
			<?= @avatar($todo->author) ?>
		</div>
		
		<div class="entity-container">
			<h4 class="author-name"><?= @name($todo->author) ?></h4>
			<ul class="an-meta inline">
				<li><?= @date($todo->creationTime) ?></li>
				<?php if(!$todo->owner->eql($todo->author)): ?>
				<li><?= @name($todo->owner) ?></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
	
	<h3 class="entity-title">
		<a href="<?= @route($todo->getURL()) ?>"><?= @escape($todo->title) ?></a>
	</h3>
	
	<?php if($todo->description): ?>
	<div class="entity-description">
		<?= @helper('text.truncate', @content($todo->description), array('length'=>500, 'consider_html'=>true, 'read_more'=>true)); ?>
	</div>
	<?php endif; ?>
	
	<div class="entity-meta">
		<ul class="an-meta inline">
			<li><?= @text('COM-TODOS-TODO-PRIORITY') ?>: <span class="priority <?= @helper('priorityLabel', $todo) ?>"><?= @helper('priorityLabel', $todo) ?></span></li> 
			<li><?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $todo->numOfComments) ?></li>
			
			<?php if( !isset($pid) && isset($todo->todolist) ): ?>
			<li><?= @text('COM-TODOS-TODO-META-TODOLIST') ?>: <a href="<?= @route($todo->todolist->getURL()) ?>"><?= @escape($todo->todolist->title) ?></a></li>
			<?php endif; ?>
			
			<?php if(!$todo->open) : ?>			
			<li><?= sprintf(@text('COM-TODOS-TODOLIST-COMPLETED-BY-REPORT'), @date($todo->openStatusChangeTime), @name($todo->lastChanger)) ?></li>
		<?php endif; ?>
		</ul>
		
		<div class="an-meta" class="vote-count-wrapper" id="vote-count-wrapper-<?= $todo->id ?>">
			<?= @helper('ui.voters', $todo); ?> 
		</div>
	</div>
	
	<div class="entity-actions">
		<?= @helper('ui.commands', @commands('list')) ?>
	</div>
</div>