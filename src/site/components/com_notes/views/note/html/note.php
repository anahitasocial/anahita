<?php defined('KOOWA') or die('Restricted access') ?>

<div class="an-entity">
	<div class="clearfix">
		<div class="entity-portrait-square">
			<?= @avatar($note->author) ?>
		</div>
		
		<div class="entity-container">
			<h4 class="author-name"><?= @name($note->author) ?></h4>
			<div class="an-meta">
				<?= @date($note->creationTime) ?>
			</div>
		</div>
	</div>

	<div class="entity-description">
		<?= @content($note->body) ?>
	</div>
	
	<div class="entity-meta">
		<?php if ( $note->numOfComments ) : ?> 
		<ul class="an-meta">
			<li><?= sprintf(@text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $note->numOfComments); ?></li> 
			<li><?= sprintf(@text('LIB-AN-MEDIUM-LAST-COMMENT-BY-X'), @name($note->lastCommenter), @date($note->lastCommentTime)) ?></li>
		</ul>
		<?php endif; ?>
		
		<div class="an-meta" id="vote-count-wrapper-<?= $note->id ?>">
			<?= @helper('ui.voters', $note); ?>
		</div>
	</div>
</div>