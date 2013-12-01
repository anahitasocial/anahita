<?php defined('KOOWA') or die ?>

<?php $published = ($page->enabled) ? '' : 'an-highlight' ?>
<div class="an-entity an-record an-removable <?= $published ?>">
	<div class="clearfix">
		<div class="entity-portrait-square">
			<?= @avatar($page->author) ?> 
		</div>
		
		<div class="entity-container">
			<h4 class="author-name"><?= @name($page->author) ?></h4>
			<ul class="an-meta inline">
				<li><?= @date($page->creationTime) ?></li>
				<?php if(!$page->owner->eql($page->author)): ?>
				<li><?= @name($page->owner) ?></li>
				<?php endif; ?>
			</ul>
		</div>
	</div>

	<h3 class="entity-title">
		<a href="<?= @route($page->getURL()) ?>">
			<?= @escape($page->title) ?>
		</a>
	</h3>
	
	<?php if($page->excerpt): ?>
	<div class="entity-excerpt">
		<?= @escape( $page->excerpt ) ?>
	</div>
	<?php endif; ?>
	
	<div class="entity-meta">
		<?php if(!$page->published): ?>
		<p class="label label-warning">
			<?= @text('COM-PAGES-PAGE-IS-UNPUBLISHED') ?>
		</p>
		<?php endif; ?>
		
		<ul class="an-meta inline">
			<li><?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $page->numOfComments) ?></li>
			<?php if(isset($page->editor)) : ?>
			<li><?= sprintf( @text('LIB-AN-MEDIUM-EDITOR'), @date($page->updateTime), @name($page->editor)) ?></li>
			<?php endif; ?>
		</ul>
	
		<div class="vote-count-wrapper an-meta" id="vote-count-wrapper-<?= $page->id ?>">
			<?= @helper('ui.voters', $page); ?>
		</div>
	</div>
	
	<div class="entity-actions">
		<?= @helper('ui.commands', @commands('list')) ?>
	</div>
</div>