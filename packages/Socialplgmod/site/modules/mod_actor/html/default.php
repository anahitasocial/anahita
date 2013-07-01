<?php defined('KOOWA') or die('Restricted access'); ?>

<div class="an-entity">
	<div class="entity-portrait-square">
		<?= @avatar(@$actor) ?>
	</div>
	
	<div class="entity-container">
		<div class="entity-description">
			<?= @helper('text.truncate', strip_tags($actor->description), array('length'=>200)); ?>
		</div>
		
		<div class="entity-meta">
			<?= sprintf( @text('MOD-ACTOR-NUMBER-OF-FOLLOWERS'), $actor->followerCount) ?>
		</div>
	</div>
</div>

<div class="media-grid">
	<?php foreach($followers as $actor) : ?>
	<div><?= @avatar($actor) ?></div>	
	<?php endforeach; ?>
</div>

<?php if ( count($commands) > 0 ) : ?>
<div class="actions">
<?= @helper('ui.commands', $commands)?>
</div>
<?php endif; ?>