<?php defined('KOOWA') or die('Restricted access');?>

<?php $highlight = ($todo->open) ? 'an-highlight' : '' ?>
<div class="an-entity <?= $highlight ?>">
	<div class="entity-portrait-square">
		<?php if( $todo->lastComment ) : ?>
		<?= @avatar($todo->lastCommenter) ?>
		<?php else : ?>
		<?= @avatar($todo->author) ?>
		<?php endif; ?>
	</div>
	
	<div class="entity-container">
		<h3 class="entity-title">
			<?php if ( $todo->getRowData('search_result_preview') ) : ?>
                <?php if ( $todo->getRowData('comment') ) : ?>
                <a href="<?= @route($todo->getURL().'&permalink='.$todo->lastComment->id) ?>">
				<?= sprintf( @text('LIB-AN-MEDIUM-COMMENTED'), @escape($todo->title) ); ?>
				</a>
                <?php else: ?>    
                <a href="<?= @route($todo->getURL()) ?>">
				<?= @escape($todo->title) ?>
				</a>
                <?php endif; ?>                 
            <?php endif; ?>
		</h3>
		
		<div class="entity-description">	
		<?php if ( $preview = $todo->getRowData('search_result_preview') ) : ?>
			<?php $preview = @helper('text.truncate', strip_tags($preview), array('length'=>400, 'read_more'=>true))?>
			<?= @helper('text.highlight', $preview, $keyword)?>
		<?php endif;?>
		</div>
		
		<div class="entity-meta">
			<?php if($filter == 'leaders'): ?>
			<div class="an-meta">
			<?= sprintf(@text('LIB-AN-MEDIUM-OWNER'), @name($todo->owner)) ?>
			</div>
			<?php endif; ?>
			
			<div class="an-meta"> 
			<?= sprintf( @text('LIB-AN-MEDIUM-AUTHOR'), @date($todo->creationTime), @name($todo->author)) ?>
			</div>
			
			<div class="an-meta">
			<?= sprintf(@text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $todo->numOfComments); ?>
			<?php if($todo->lastCommenter): ?>
			- <?= sprintf(@text('LIB-AN-MEDIUM-LAST-COMMENT-BY-X'), @name($todo->lastCommenter), @date($todo->lastCommentTime)) ?>
			<?php endif; ?>
			</div>
			
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