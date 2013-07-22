<?php defined('KOOWA') or die; ?>

<?php $entity = @service('repos:base.node')->fetch($nid); ?>

<div class="popover-title">
	<?= $entity->voteUpCount?> +1s
</div>
<div class="popover-content">
<?php if (  $entity->voteUpCount == 1 ) : ?>
		<?php if ( $entity->voterUpIds->offsetExists($viewer->id) ) : ?>	
			<?= @text('LIB-AN-VOTE-ONLY-YOU-VOTED')?>
		<?php else :?>			
			<?= sprintf(@text('LIB-AN-VOTE-ONE-VOTED'), @name(@service('repos:actors.actor')->fetch(end($entity->voterUpIds->toArray()))))?>
		<?php endif;?>	
	<?php elseif ( $entity->voteUpCount > 1 ) : ?>
		<?php if ( $entity->voterUpIds->offsetExists($viewer->id) ) : ?>	
			<?php if (  $entity->voteUpCount == 2 ) : ?>
				<?php 
					$ids = $entity->voterUpIds->toArray();
					unset($ids[$viewer->id]);					
				?>
				<?= sprintf(@text('LIB-AN-VOTE-YOU-AND-ONE-PERSON'),  @name(@service('repos:actors.actor')->fetch(end($ids))))?>
			<?php else : ?>
				<?= sprintf(@text('LIB-AN-VOTE-YOU-AND-OTHER-VOTED'), $entity->voteUpCount - 1)?>
			<?php endif;?>
		<?php else :?>
			<?= sprintf(@text('LIB-AN-VOTE-OTHER-VOTED'), $entity->voteUpCount)?>
		<?php endif;?>
	<?php endif;?>
<?= @template('gadget', array('actors'=>$entity->voteups->voter)) ?>
</div>