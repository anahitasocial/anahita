<?php defined('KOOWA') or die; ?>

<div class="an-entity an-record an-removable"">
	<div class="clearfix">
		<div class="entity-portrait-square">
			<?= @avatar($photo->author) ?>
		</div>
		
		<div class="entity-container">
			<h4 class="author-name"><?= @name($photo->author) ?></h4>
			<ul class="an-meta inline">
				<li><?= @date($photo->creationTime) ?></li>
				<?php if(!$photo->owner->eql($photo->author)): ?>
				<li><?= @name($photo->owner) ?></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
	
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
		
	<div class="entity-meta">
		<ul class="an-meta inline">
			<li>
				<a href="<?= @route($photo->getURL()) ?>">
				<?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $photo->numOfComments) ?>
				</a>
			</li>
			<?php if($photo->lastCommenter): ?>
		 	<li><?= sprintf(@text('LIB-AN-MEDIUM-LAST-COMMENT-BY-X'), @name($photo->lastCommenter), @date($photo->lastCommentTime)) ?></li>
			<?php endif; ?>
		</ul>
		
		<div class="vote-count-wrapper an-meta" id="vote-count-wrapper-<?= $photo->id ?>">
			<?= @helper('ui.voters', $photo); ?>
		</div>
	</div>
	
	<?php if( $photo->authorize('edit') ) : ?>
	<div class="entity-actions">
		<?= @helper('ui.commands', @commands('list')) ?>
	</div>
	<?php endif; ?>
</div>
