<?php defined('KOOWA') or die ?>

<?php $commands = @commands('list') ?>
<?php $highlight = ($item->isEnableable() && !$item->enabled) ? 'an-highlight' : '' ?>
<div class="an-entity dropdown-actions <?= $highlight ?>">
	<div class="clearfix">
		<div class="entity-portrait-square">
			<?= @avatar($item) ?>
		</div>
		
		<div class="entity-container">
			<h3 class="entity-name">
				<?= @name($item) ?>
			</h3>
	
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
		<?= @helper('text.truncate', @content($item->body, array('exclude'=>array('syntax', 'video'))), array('length'=>200, 'consider_html'=>true)); ?>
	</div>
			
	<?php if ( count($commands) ) : ?>
	<div class="entity-actions">
		<?php if ( $action = $commands->extract('follow') ) : ?>
			<?= @helper('ui.command', $action->class('btn btn-primary')) ?> 
		<?php elseif ( $action = $commands->extract('unfollow') ) : ?>
			<?= @helper('ui.command', $action->class('btn'))?> 
		<?php endif;?>
		
		<?php foreach($commands as $action) : ?>
			<?= @helper('ui.command', $action->class('btn')) ?>
		<?php endforeach;?>
	</div>
	<?php endif; ?>
</div>