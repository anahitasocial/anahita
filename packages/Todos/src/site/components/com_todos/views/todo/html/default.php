<?php defined('KOOWA') or die ?>

<div class="row">
	<div class="span8">
	<?= @helper('ui.header', array()) ?>
	<?= @template('todo') ?>
	<?= @helper('ui.comments', $todo, array('pagination'=>true)) ?>
	</div>
	
	<div class="span4">
		<h4 class="block-title">
		<?= @text('LIB-AN-META') ?>
		</h4>				
		
		<div class="block-content">
    		<ul class="an-meta">
    			<?php if(isset($todo->editor)) : ?>
    			<li><?= sprintf( @text('LIB-AN-MEDIUM-EDITOR'), @date($todo->updateTime), @name($todo->editor)) ?></li>
    			<?php endif; ?>
    			<?php if(!$todo->open) : ?>
    			<li>				
    				<?= sprintf(@text('COM-TODOS-TODO-COMPLETED-BY-REPORT'), @date($todo->openStatusChangeTime), @name($todo->lastChanger)) ?>
    			</li>
    			<?php endif; ?>
    			<li><?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $todo->numOfComments) ?></li>
    		</ul>
		</div>
		
		<?php if($actor->authorize('administration')): ?>
		<h4 class="block-title">
		<?= @text('COM-TODOS-TODO-PRIVACY') ?>
		</h4>
		<div class="block-content">
		<?= @helper('ui.privacy',$todo) ?>
		</div>
		<?php endif; ?>
	</div>
</div>