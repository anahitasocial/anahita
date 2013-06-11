<?php defined('KOOWA') or die ?>

<?php $published = ($page->published) ? '' : 'an-highlight' ?>
<div class="an-entity <?= $published ?>">
	<div class="entity-portrait-square">
		<?= @avatar($page->author) ?>
	</div>

	<div class="entity-container">
		<h4 class="entity-title">
			<a href="<?= @route($page->getURL()) ?>">
				<?= @escape($page->title) ?>
			</a>
		</h4>
		
		<?php if($page->excerpt): ?>
		<div class="entity-excerpt">
			<?= @helper('text.truncate', @escape($page->excerpt), array('length'=>100)); ?>
		</div>
		<?php endif; ?>
		
		<div class="entity-meta">
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
			
			<?php if($filter == 'leaders'): ?>
			<div class="an-meta">
				<?= sprintf(@text('LIB-AN-MEDIUM-OWNER'), @name($page->owner)) ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
