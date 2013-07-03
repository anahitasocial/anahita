<?php defined('KOOWA') or die ?>

<?php $published = ($page->enabled) ? '' : 'an-highlight' ?>
<div class="an-entity an-record an-removable <?= $published ?>">
	<div class="entity-portrait-square">
		<?= @avatar($page->author) ?>
	</div>

	<div class="entity-container">
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
		
		<?php if(!$page->published): ?>
		<div class="an-meta">
			<span class="label label-warning">
				<?= @text('COM-PAGES-PAGE-IS-UNPUBLISHED') ?>
			</span>
		</div>
		<?php endif; ?>
		
		<div class="an-meta"> 
			<?= sprintf( @text('LIB-AN-MEDIUM-AUTHOR'), @date($page->creationTime), @name($page->author)) ?> 
		</div>	
		
		<?php if(isset($page->editor)) : ?>
		<div class="an-meta"> 
		<?= sprintf( @text('LIB-AN-MEDIUM-EDITOR'), @date($page->updateTime), @name($page->editor)) ?>
		</div>
		<?php endif; ?>
		
		<div class="an-meta">
			<?= sprintf( @text('LIB-AN-MEDIUM-NUMBER-OF-COMMENTS'), $page->numOfComments) ?>
		</div>
		
		<?php if($filter == 'leaders'): ?>
		<div class="an-meta">
			<?= sprintf(@text('LIB-AN-MEDIUM-OWNER'), @name($page->owner)) ?>
		</div>
		<?php endif; ?>
		
		<div class="entity-meta">
			<div class="vote-count-wrapper" id="vote-count-wrapper-<?= $page->id ?>">
				<?= @helper('ui.voters', $page); ?>
			</div>
		</div>
		
		<div class="entity-actions">
			<?= @helper('ui.commands', @commands('list')) ?>
		</div>
	</div>
</div>