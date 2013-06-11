<?php defined('KOOWA') or die('Restricted access') ?>

<?php @commands('toolbar') ?>

<div class="an-entity <?= ($topic->isSticky) ? 'an-highlight' : '' ?>">
	<div class="entity-portrait-square">
		<?= @avatar($topic->author) ?>
	</div>
		
	<div class="entity-container">
		<h3 class="entity-title">
			<?=@escape($topic->title) ?>
		</h3>
	
		<div class="entity-description">
			<?= @content($topic->body) ?>
		</div>
		
		<div class="entity-meta">
			<div class="an-meta">
			<?= sprintf( @text('LIB-AN-MEDIUM-AUTHOR'), @date($topic->creationTime), @name($topic->author)) ?>
			</div>
			
			<?php if ( $topic->numOfComments ) : ?> 
			<div class="an-meta">
				<?= sprintf(@text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $topic->numOfComments); ?> - 
				<?= sprintf(@text('LIB-AN-MEDIUM-LAST-COMMENT-BY-X'), @name($topic->lastCommenter), @date($topic->lastCommentTime)) ?>
			</div>
			<?php endif; ?>
			
			<div class="an-meta" id="vote-count-wrapper-<?= $topic->id ?>">
				<?= @helper('ui.voters', $topic); ?>
			</div>
		</div>
	</div>
</div>

