<?php defined('KOOWA') or die('Restricted access') ?>

<div class="an-entity">
	<div class="entity-portrait-square">
		<?= @avatar($note->author) ?>
	</div>

	<div class="entity-container">
		<h3 class="entity-title">
			<?= @name($note->author) ?>
		</h3>
	
		<div class="entity-description">
			<?= @content($note->body) ?>
		</div>
		
		<div class="entity-meta">
			<div class="an-meta">
			<?= sprintf( @text('LIB-AN-MEDIUM-AUTHOR'), @date($note->creationTime), @name($note->author)) ?>
			</div>
			
			<?php if ( $note->numOfComments ) : ?> 
			<div class="an-meta">
				<?= sprintf(@text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $note->numOfComments); ?> - 
				<?= sprintf(@text('LIB-AN-MEDIUM-LAST-COMMENT-BY-X'), @name($note->lastCommenter), @date($note->lastCommentTime)) ?>
			</div>
			<?php endif; ?>
			
			<div class="an-meta" id="vote-count-wrapper-<?= $note->id ?>">
				<?= @helper('ui.voters', $note); ?>
			</div>
		</div>
	</div>
</div>