<?php defined('KOOWA') or die; ?>

<div class="an-entity">
	<div class="clearfix">
		<div class="entity-portrait-square">
			<?= @avatar($item->author) ?>
		</div>
		
		<div class="entity-container">
			<h4 class="author-name">
				<?= @name($item->author) ?>
			</h4>
			
			<ul class="an-meta inline">
				<li><?= @date($item->creationTime) ?></li>
			</ul>
		</div>
	</div>
	
	<?php if(!empty($item->title)): ?>
	<h3 class="entity-title">
		<a href="<?= @route($item->getURL()) ?>">
			<?= @escape($item->title) ?>
		</a>
	</h3>
	<?php endif; ?>
	
	<div class="entity-description">
		<?= @helper('text.truncate',  @content($item->body), array('length'=>400, 'consider_html'=>true)) ?>
	</div>
</div>