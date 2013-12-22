<?php defined('KOOWA') or die('Restricted access') ?>
<div scroll-handle="<?=$comment->id?>" id="an-comment-<?= $comment->id ?>" class="an-entity an-comment an-record an-removable">
	<div class="clearfix">
		<div class="entity-portrait-square">
			<?= @avatar($comment->author)  ?>
		</div>
		
		<div class="entity-container">
			<h5 class="author-name">
				<?= @name($comment->author) ?>
			</h5>
			
			<div class="an-meta">
				<?= @date($comment->creationTime) ?> 
				<a href="<?= @route($comment->parent->getURL()).'#permalink='.$comment->id ?>">#</a>
			</div>
		</div>
	</div>
		
	<?php $body = $comment->body ?>
	<?php if(!empty($strip_tags)): ?>
	<?php $body = strip_tags($body) ?>
	<?php endif; ?>
	
	<?php if (!empty($truncate_body) ) : ?>
	<?php $body = @helper('text.truncate', $body, $truncate_body) ?>	
	<?php endif;?>
	
	<div class="entity-body"> 
		<?= @content($body) ?>
	</div>
		
	<div class="entity-meta">
		<div id="vote-count-wrapper-<?= $comment->id ?>">
			<?= @helper('ui.voters', $comment); ?>
		</div>
	</div>
		
	<div class="entity-actions">
		<?= @helper('ui.commands', @commands('list')) ?>
	</div>
</div>
