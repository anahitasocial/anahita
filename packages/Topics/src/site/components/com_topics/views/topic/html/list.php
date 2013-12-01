<?php defined('KOOWA') or die('Restricted access');?>

<?php $highlight = ($topic->isSticky && $filter != 'leaders') ? 'an-highlight' : '' ?>
<?php $author = ($topic->lastComment) ? $topic->lastCommenter : $topic->author; ?>
<div class="an-entity <?= $highlight ?>">
	<div class="clearfix">
		<div class="entity-portrait-square">
			<?= @avatar($author) ?>
		</div>
		
		<div class="entity-container">
			<h4 class="author-name"><?= @name($author) ?></h4>
			<ul class="an-meta inline">
				<li><?= @date($topic->creationTime) ?></li>
				<?php if(!$topic->owner->eql($topic->author)): ?>
				<li><?= @name($topic->owner) ?></li>
				<?php endif; ?>
			</ul>
		</div>
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
			<li><?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $topic->numOfComments) ?></li>
		</ul>
		
		<div class="an-meta vote-count-wrapper" id="vote-count-wrapper-<?= $topic->id ?>">
			<?= @helper('ui.voters', $topic); ?>
		</div>
	</div>
</div>