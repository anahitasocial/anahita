<?php defined('KOOWA') or die ?>

<?php 
$current_time = new KDate();
$highlight = ($milestone->endDate->getDate(DATE_FORMAT_UNIXTIME) >= $current_time->getDate(DATE_FORMAT_UNIXTIME)) ? 'an-highlight' : ''; 
?>

<div class="an-entity an-record an-removable <?= $highlight ?>">
	<h3 class="entity-title">
		<a href="<?= @route($milestone->getURL()) ?>">
		<?= @escape($milestone->title) ?>
		</a>
	</h3>
	
	<?php if( $milestone->description ): ?>
	<div class="entity-description">
		<?= @helper('text.truncate', @content($milestone->description), array('length'=>500, 'consider_html'=>true, 'read_more'=>true)); ?>
	</div>
	<?php endif; ?>
	
	<div class="entity-meta">
		<div class="an-meta">
		<?= sprintf( @text('LIB-AN-MEDIUM-AUTHOR'), @date($milestone->creationTime), @name($milestone->author)) ?> 
		</div>

		<?php if(isset($milestone->editor)) : ?>
		<div class="an-meta">
		<?= sprintf( @text('LIB-AN-MEDIUM-EDITOR'), @date($milestone->updateTime), @name($milestone->editor)) ?> 
		</div>
		<?php endif; ?>
		
		<div class="an-meta">
		<?= sprintf(@text('COM-TODOS-MILESTONE-META-END-DATE'), @date($milestone->endDate)) ?>
		</div>
		
		<div class="an-meta">
		<?= sprintf( @text('COM-TODOS-MILESTONE-NUMBER-OF-TODOS'), $milestone->numOfTodolists) ?> - 
		<?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $milestone->numOfComments) ?>
		</div>
		
		<div class="an-meta" class="vote-count-wrapper" id="vote-count-wrapper-<?= $milestone->id ?>">
			<?= @helper('ui.voters', $milestone); ?>
		</div>
	</div>
</div>