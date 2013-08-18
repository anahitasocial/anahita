<?php defined('KOOWA') or die ?>

<?php @listItemView()->layout('list') ?>
<?php $items->order('status_update_time', 'desc'); ?>
	
<div class="an-entities">
<?php foreach($items as $item): ?>
	<div class="an-entity">
		<div class="clearfix">
			<div class="entity-portrait-square">
				<?= @avatar($item) ?>
			</div>
			
			<div class="entity-container">
				<h4 class="entity-name"><?= @name($item) ?></h4>
				
				<div class="entity-meta">
				<?= $item->followerCount ?>
				<span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-FOLLOWERS') ?></span> 
				
				<?php if($item->isLeadable()): ?>
				/ <?= $item->leaderCount ?>
				<span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-LEADERS') ?></span>
				<?php endif; ?>
				</div>
			
			</div>
		</div>

		<div class="entity-description">
			<?= @helper('text.truncate', strip_tags($item->description), array('length'=>200)); ?>
		</div>
	</div>
<?php endforeach; ?>
</div>