<?php defined('KOOWA') or die ?>

<?php @commands('toolbar') ?>

<div class="an-entity <?= ($page->pinned) ? 'an-highlight' : '' ?>">
	<div class="clearfix">
		<div class="entity-portrait-square">
			<?= @avatar($page->author) ?> 
		</div>
		
		<div class="entity-container">
			<h4 class="author-name"><?= @name($page->author) ?></h4>
			<div class="an-meta">
				<?= @date($page->creationTime) ?> 
			</div>
		</div>
	</div>
	
	<h1 class="entity-title">
		<?= @escape( $page->title ) ?>
	</h1>
	
	<?php if($page->description): ?>
	<div class="entity-description">
		<?= @content( $page->description ) ?>
	</div>
	<?php endif; ?>
	
	<div class="entity-meta">
		<div id="vote-count-wrapper-<?= $page->id ?>">
			<?= @helper('ui.voters', $page); ?>
		</div>
	</div>
</div>
