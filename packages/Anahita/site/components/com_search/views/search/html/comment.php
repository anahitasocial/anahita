<div class="an-entity">
	<div class="entity-portrait-square">
		<?= @avatar($item->author)  ?>
	</div>

	<div class="entity-container">
		<?php if( $item->parent->isDescribable() && $item->parent->title ): ?>
		<h3 class="entity-title">
			<a href="<?= @route($item->parent->getURL()).'#permalink='.$item->id ?>">
				<?= @escape($item->parent->title) ?>
			</a>
		</h3>
		<?php endif; ?>	
		<div class="entity-description">
			<?php $text = @helper('text.highlight', strip_tags($item->body), $keywords) ?>
			<?= @helper('text.truncate', $text, array('length'=>400, 'read_more'=>true))?>			
		</div>
		
		<div class="entity-meta">
			<div class="an-meta"><?= sprintf(@text('LIB-AN-MEDIUM-AUTHOR'), @date($item->creationTime), @name($item->author)) ?></div>
		</div>			
	</div>
</div>