<?php defined('KOOWA') or die('Restricted access') ?>

<?php @commands('toolbar') ?>

<div class="an-entity <?= ($topic->isSticky) ? 'an-highlight' : '' ?>">
	<div class="clearfix">
		<div class="entity-portrait-square">
			<?= @avatar($topic->author) ?>
		</div>
			
		<div class="entity-container">
			<h4 class="entity-author"><?= @name($topic->author) ?></h4>
			<div class="an-meta"><?= @date($topic->creationTime) ?></div>
		</div>
	</div>
	
	<h3 class="entity-title">
		<?=@escape($topic->title) ?>
	</h3>	
	
	<div class="entity-description">
		<?= @content($topic->body) ?>
	</div>
		
	<div class="entity-meta">
		<ul class="an-meta inline">
			<?php if ( $topic->numOfComments ) : ?> 
			<li><?= sprintf(@text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $topic->numOfComments); ?></li>
			<li><?= sprintf(@text('LIB-AN-MEDIUM-LAST-COMMENT-BY-X'), @name($topic->lastCommenter), @date($topic->lastCommentTime)) ?></li>
			<?php endif; ?>
			
			<?php if($topic->voteUpCount): ?>
			<li>
				<div class="vote-count-wrapper" id="vote-count-wrapper-<?= $topic->id ?>">
					<?= @helper('ui.voters', $topic); ?>
				</div> 
			</li>
			<?php endif; ?>
		</ul>
	</div>
</div>

