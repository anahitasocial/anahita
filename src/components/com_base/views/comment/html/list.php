<?php defined('KOOWA') or die('Restricted access') ?>
<div scroll-handle="<?=$comment->id?>" id="an-comment-<?= $comment->id ?>" class="an-entity an-comment an-record an-removable">
	<div class="entity-portrait-square">
		<?= @avatar($comment->author)  ?>
	</div>
	
    <?php $body = $comment->body; ?>  
	<?php $body = @content($body) ?>
	<?php if (empty($truncate_body) ) : ?>
	<?php $body =  stripslashes( $body ) ?>
	<?php else : ?>
	<?php $body = @helper('text.truncate', stripslashes( $body ), is_bool($truncate_body) ? array() : $truncate_body) ?>	
	<?php endif;?>
	
	<div class="entity-container">
		<div class="entity-body">  
			<div class="entity-author"><?= @name($comment->author) ?></div> 
			<?= $body ?>
		</div>
		
		<div class="entity-meta">
			<?=@date($comment->creationTime) ?> 
			<a href="<?= @route($comment->parent->getURL().'#permalink='.$comment->id) ?>">#</a>
			<div id="vote-count-wrapper-<?= $comment->id ?>">
				<?= @helper('ui.voters', $comment); ?>
			</div>
		</div>
		
		<div class="entity-actions">
			<?= @helper('ui.commands', @commands('list')) ?>
		</div>
	</div>
</div>
