<?php defined('KOOWA') or die('Restricted access'); ?>

<div class="module-border well">
	<h2 class="module-title">
		<?= @name($item) ?>
	</h2>
	
	<div class="module-content">
		<div class="an-entity">
			<div class="clearfix">
				<div class="entity-portrait-square">
					<?= @avatar($item) ?>
				</div>
				
				<div class="entity-container">
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
		
		<?php $followers = $item->followers->where('filename', '!=', '')->limit(15) ?>
		
		<div class="media-grid">
			<?php foreach($followers as $item) : ?>
			<div><?= @avatar($item) ?></div>	
			<?php endforeach; ?>
		</div>
		
		<?php if ( count($commands) > 0 ) : ?>
		<div class="actions">
		<?= @helper('ui.commands', $commands)?>
		</div>
		<?php endif; ?>
	</div>
</div>