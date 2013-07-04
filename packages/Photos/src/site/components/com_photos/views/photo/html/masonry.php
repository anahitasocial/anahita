<?php defined('KOOWA') or die; ?>

<div class="an-entity an-record an-removable"">
	<?php if( $photo->authorize('edit') ) : ?>
	<div class="entity-actions">
		<?= @helper('ui.commands', @commands('list')) ?>
	</div>
	<?php endif; ?>
	
	<div class="entity-portrait-medium" data-behavior="Mediabox">
		<?php 
			$rel = 'lightbox[actor-'.$photo->owner->id.' 900 900]';
		
			$caption = htmlspecialchars($photo->title, ENT_QUOTES).
			(($photo->title && $photo->description) ? ' :: ' : '').
			@helper('text.script', $photo->description);			 
		?>
		<a title="<?= $caption ?>" href="<?= $photo->getPortraitURL('medium') ?>" rel="<?= $rel ?>">			
			<img alt="<?= @escape($photo->title) ?>" src="<?= $photo->getPortraitURL('medium') ?>" />
		</a>
	</div>
	
	<?php if($photo->title): ?>
	<h4 class="entity-title">
		<a title="<?= @escape($photo->title) ?>" href="<?= @route($photo->getURL()) ?>">
		<?= @escape($photo->title) ?>
		</a>
	</h4>
	<?php endif; ?>
		
	<?php if($photo->description): ?>
	<div class="entity-description">
	<?= @helper('text.truncate', strip_tags($photo->description), array('length'=>200, 'read_more'=>true)); ?>
	</div>
	<?php endif; ?>
	
	<div class="clearfix">
		<div class="entity-portrait-square">
			<?= @avatar($photo->author) ?>
		</div>
		
		<div class="entity-container">
			<div class="entity-meta">
				<?php if($filter == 'leaders'): ?>
				<div class="an-meta">
				<?= sprintf(@text('LIB-AN-MEDIUM-OWNER'), @name($photo->owner)) ?>
				</div>
				<?php endif; ?>
			
				<div class="an-meta">
				<?= sprintf( @text('LIB-AN-MEDIUM-AUTHOR'), @date($photo->creationTime), @name($photo->author)) ?> 
				</div>
				
				<div class="an-meta">
				<a href="<?= @route($photo->getURL()) ?>">
					<?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $photo->numOfComments) ?>
				</a>
				<?php if($photo->lastCommenter): ?>
				 - <?= sprintf(@text('LIB-AN-MEDIUM-LAST-COMMENT-BY-X'), @name($photo->lastCommenter), @date($photo->lastCommentTime)) ?>
				<?php endif; ?>
				</div>
				
				<div class="vote-count-wrapper" id="vote-count-wrapper-<?= $photo->id ?>">
					<?= @helper('ui.voters', $photo); ?>
				</div>
			</div>
		</div>
	</div>
</div>
