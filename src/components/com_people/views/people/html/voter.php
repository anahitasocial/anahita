<? defined('KOOWA') or die; ?>

<? $entity = @service('repos:base.node')->fetch($nid); ?>

<div class="popover-title">
	<?= $entity->voteUpCount?> +1s
</div>
<div class="popover-content">
<? if ($entity->voteUpCount == 1) : ?>
		<? if ($entity->voterUpIds->offsetExists($viewer->id)) : ?>
			<?= @text('LIB-AN-VOTE-ONLY-YOU-VOTED')?>
		<? else :?>
			<?= sprintf(@text('LIB-AN-VOTE-ONE-VOTED'), @name(@service('repos:actors.actor')->fetch(end($entity->voterUpIds->toArray()))))?>
		<? endif;?>
	<? elseif ($entity->voteUpCount > 1) : ?>
		<? if ($entity->voterUpIds->offsetExists($viewer->id)) : ?>
			<? if ($entity->voteUpCount == 2) : ?>
				<?
                    $ids = $entity->voterUpIds->toArray();
                    unset($ids[$viewer->id]);
                ?>
				<?= sprintf(@text('LIB-AN-VOTE-YOU-AND-ONE-PERSON'),  @name(@service('repos:actors.actor')->fetch(end($ids))))?>
			<? else : ?>
				<?= sprintf(@text('LIB-AN-VOTE-YOU-AND-OTHER-VOTED'), $entity->voteUpCount - 1)?>
			<? endif;?>
		<? else :?>
			<?= sprintf(@text('LIB-AN-VOTE-OTHER-VOTED'), $entity->voteUpCount)?>
		<? endif;?>
	<? endif;?>
<?= @template('gadget', array('actors' => $entity->voteups->voter)) ?>
</div>
