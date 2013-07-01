<?php defined('KOOWA') or die('Restricted access');?>

<?php $highlight = ($page->enabled) ? '' : 'an-highlight' ?>
<div class="an-entity <?= $highlight ?>">
	<div class="entity-portrait-square">
		<?php if( $page->lastComment ) : ?>
		<?= @avatar($page->lastCommenter) ?>
		<?php else : ?>
		<?= @avatar($page->author) ?>
		<?php endif; ?>
	</div>

	<div class="entity-container">
		<h3 class="entity-title">
            <?php if ( $page->getRowData('search_result_preview') ) : ?>
                <?php if ( $page->getRowData('comment') ) : ?>
                <a href="<?= @route($page->getURL().'&permalink='.$page->lastComment->id) ?>">
				<?= sprintf( @text('LIB-AN-MEDIUM-COMMENTED'), @escape($page->title) ); ?>
				</a>
                <?php else: ?>    
                <a href="<?= @route($page->getURL()) ?>">
				<?= @escape($page->title) ?>
				</a>
                <?php endif; ?>                 
            <?php endif; ?>
		</h3>
		
		<div class="entity-description">	
		<?php if ( $preview = $page->getRowData('search_result_preview') ) : ?>
			<?php $preview = @helper('text.truncate', strip_tags($preview), array('length'=>400, 'read_more'=>true))?>
			<?= @helper('text.highlight', $preview, $keyword)?>
		<?php endif;?>
		</div>
		
		<div class="entity-meta">
			<?php if($filter == 'leaders'): ?>
			<div class="an-meta">
			<?= sprintf(@text('LIB-AN-MEDIUM-OWNER'), @name($page->owner)) ?>
			</div>
			<?php endif; ?>
			
			<div class="an-meta"> 
			<?= sprintf( @text('LIB-AN-MEDIUM-AUTHOR'), @date($page->creationTime), @name($page->author)) ?>
			</div>
			
			<div class="an-meta">
			<?= sprintf(@text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $page->numOfComments); ?>
			<?php if($page->lastCommenter): ?>
			- <?= sprintf(@text('LIB-AN-MEDIUM-LAST-COMMENT-BY-X'), @name($page->lastCommenter), @date($page->lastCommentTime)) ?>
			<?php endif; ?>
			</div>
			
			<div class="an-meta">
				<div class="vote-count-wrapper" id="vote-count-wrapper-<?= $page->id ?>">
					<?= @helper('ui.voters', $page); ?>
				</div>
			</div>
		</div>
	</div>
</div>