<?php defined('KOOWA') or die('Restricted access');?>

<?php $highlight = ($topic->isSticky && $filter != 'leaders') ? 'an-highlight' : '' ?>
<div class="an-entity <?= $highlight ?>">
	<div class="clearfix">
		<?php if( $topic->lastComment ) : ?>
		<div class="entity-portrait-square">
			<?= @avatar($topic->lastCommenter) ?>
		</div>
		
		<div class="entity-container">
			<h4 class="author-name"><?= @name($topic->lastCommenter) ?></h4>
			<div class="an-meta">
				<?= @date($topic->lastCommentTime) ?> 
			</div>
		</div>
		<?php else : ?>
		<div class="entity-portrait-square">
			<?= @avatar($topic->author) ?>
		</div>
		
		<div class="entity-container">
			<h4><?= @name($topic->author) ?></h4>
			<div class="an-meta">
				<?= @date($topic->creationTime) ?> 
			</div>
		</div>
		<?php endif; ?>
	</div>
	
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
		<ul class="an-meta inline">
			<?php if($filter == 'leaders'): ?>
			<li><?= sprintf(@text('LIB-AN-MEDIUM-OWNER'), @name($topic->owner)) ?></li>
			<?php endif; ?>
				
			<li><?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $topic->numOfComments) ?></li>
		</ul>
		
		<div class="an-meta vote-count-wrapper" id="vote-count-wrapper-<?= $topic->id ?>">
			<?= @helper('ui.voters', $topic); ?>
		</div>
	</div>
</div>