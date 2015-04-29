<?php defined('KOOWA') or die; ?>

<?php if($photo->authorize('edit')) : ?>
<div class="an-entity editable" data-url="<?= @route($photo->getURL()) ?>">
<?php else : ?>
<div class="an-entity">
<?php endif; ?>
	<div class="clearfix">
		<div class="entity-portrait-square">
			<?= @avatar($photo->author) ?>
		</div>
		
		<div class="entity-container">
			<h4 class="author-name"><?= @name($photo->author) ?></h4>
			<ul class="an-meta inline">
				<li><?= @date($photo->creationTime) ?></li>
				<?php if(!$photo->owner->eql($viewer)): ?>
				<li><?= @name($photo->owner) ?></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>

	<div class="entity-portrait-medium">
		<a title="<?= @escape($photo->title) ?>" href="<?= @route($photo->getURL()) ?>">			
			<img alt="<?= @escape($photo->title) ?>" src="<?= $photo->getPortraitURL('medium') ?>" />
		</a>
	</div>
	
	<div class="entity-description-wrapper">
		<h3 class="entity-title">
			<?= @escape($photo->title) ?>
		</h3>
		
		<div class="entity-description">
			<?= @content(nl2br($photo->description), array('exclude'=>array('gist','video'))) ?>
		</div>
	</div>
	
	<div class="entity-meta">		
		<ul class="an-meta inline">
			<li><?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $photo->numOfComments) ?></li>
			<?php if($photo->lastCommenter): ?>
			<li><?= sprintf(@text('LIB-AN-MEDIUM-LAST-COMMENT-BY-X'), @name($photo->lastCommenter), @date($photo->lastCommentTime)) ?></li>
			<?php endif; ?>
		</ul>
		
		<div class="vote-count-wrapper an-meta" id="vote-count-wrapper-<?= $photo->id ?>">
			<?= @helper('ui.voters', $photo); ?>
		</div>
	</div>
	
	<div class="entity-actions">
		<?= @helper('ui.commands', @commands('list')) ?>
	</div>
</div>
