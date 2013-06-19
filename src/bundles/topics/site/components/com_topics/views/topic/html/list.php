<?php defined('KOOWA') or die('Restricted access');?>

<?php $highlight = ($topic->isSticky && $filter != 'leaders') ? 'an-highlight' : '' ?>
<div class="an-entity <?= $highlight ?>">
	<div class="entity-portrait-square">
		<?php if( $topic->lastComment ) : ?>
		<?= @avatar($topic->lastCommenter) ?>
		<?php else : ?>
		<?= @avatar($topic->author) ?>
		<?php endif; ?>
	</div>
	
	<div class="entity-container">
		<h3 class="entity-title">
			<?php if( $topic->lastComment ) : ?>
			<a href="<?= @route($topic->getURL().'&permalink='.$topic->lastComment->id) ?>">
			<?= sprintf( @text('LIB-AN-MEDIUM-COMMENTED'), @escape($topic->title) ); ?>
			</a>
			<?php else: ?>
			<a href="<?= @route($topic->getURL()) ?>">
			<?= @escape($topic->title) ?>
			</a>
			<?php endif; ?>
		</h3>
		
		<div class="entity-description">
		<?= @helper('text.truncate', strip_tags($topic->body), array('length'=>200)); ?>
		</div>
		
		<div class="entity-meta">
			<?php if($filter == 'leaders'): ?>
			<div class="an-meta">
			<?= sprintf(@text('LIB-AN-MEDIUM-OWNER'), @name($topic->owner)) ?>
			</div>
			<?php endif; ?>
			
			<div class="an-meta">
			<?= sprintf( @text('LIB-AN-MEDIUM-AUTHOR'), @date($topic->creationTime), @name($topic->author)) ?> 
			</div>
			
			<div class="an-meta">
			<?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $topic->numOfComments) ?> 
			<?php if($topic->lastCommenter): ?>
			- <?= sprintf(@text('LIB-AN-MEDIUM-LAST-COMMENT-BY-X'), @name($topic->lastCommenter), @date($topic->lastCommentTime)) ?>
			<?php endif; ?>
			</div>
			
			<div class="an-meta">
				<div class="vote-count-wrapper" id="vote-count-wrapper-<?= $topic->id ?>">
					<?= @helper('ui.voters', $topic); ?>
				</div>
			</div>
		</div>
	</div>
</div>