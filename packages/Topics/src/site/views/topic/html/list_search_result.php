<?php defined('KOOWA') or die('Restricted access');?>

<div class="an-entity">
	<div class="entity-portrait-square">
	<?php if( $topic->lastComment ) : ?>
	<?= @avatar($topic->lastCommenter) ?>
	<?php else : ?>
	<?= @avatar($topic->author) ?>
	<?php endif; ?>
	</div>
	
	<div class="entity-container">
		<h3 class="entity-title">
            <?php if ( $topic->getRowData('search_result_preview') ) : ?>
                <?php if ( $topic->getRowData('comment') ) : ?>
                <a href="<?= @route($topic->getURL().'&permalink='.$topic->lastComment->id) ?>">
				<?= sprintf( @text('LIB-AN-MEDIUM-COMMENTED'), @escape($topic->title) ); ?>
				</a>
                <?php else: ?>    
                <a href="<?= @route($topic->getURL()) ?>">
				<?= @escape($topic->title) ?>
				</a>
                <?php endif; ?>                 
            <?php endif; ?>
		</h3>
		
		<div class="entity-description">	
		<?php if ( $preview = $topic->getRowData('search_result_preview') ) : ?>
			<?php $preview = @helper('text.truncate', strip_tags($preview), array('length'=>400, 'read_more'=>true))?>
			<?= @helper('text.highlight', $preview, $keyword)?>
		<?php endif;?>
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
			<?= sprintf(@text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $topic->numOfComments); ?>
			<?php if($topic->lastCommenter): ?>
			- <?= sprintf(@text('LIB-AN-MEDIUM-LAST-COMMENT-BY-X'), @name($topic->lastCommenter), @date($topic->lastCommentTime)) ?>
			<?php endif; ?>
			</div>
		</div>
	</div>
</div>