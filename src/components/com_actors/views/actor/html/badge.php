<? defined('KOOWA') or die('Restricted access'); ?>

<div class="module-border well">
	<h2 class="block-title">
		<?= @name($item) ?>
	</h2>

	<div class="block-content">
		<div class="an-entity">
			<div class="clearfix">
				<div class="entity-portrait-square">
					<?= @avatar($item) ?>
				</div>

				<div class="entity-container">
					<div class="entity-meta">
					<?= $item->followerCount ?>
					<span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-FOLLOWERS') ?></span>

					<? if ($item->isLeadable()): ?>
					/ <?= $item->leaderCount ?>
					<span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-LEADERS') ?></span>
					<? endif; ?>
				</div>
				</div>
			</div>

			<div class="entity-description">
				<?= @helper('text.truncate', @content($item->body, array('exclude' => array('syntax', 'video'))), array('length' => 200)); ?>
			</div>
		</div>

		<? $followers = $item->followers->where('filename', '!=', '')->limit(15) ?>

		<div class="media-grid">
			<? foreach ($followers as $item) : ?>
			<div><?= @avatar($item) ?></div>
			<? endforeach; ?>
		</div>

		<? if (count($commands) > 0) : ?>
		<div class="actions">
		<?= @helper('ui.commands', $commands)?>
		</div>
		<? endif; ?>
	</div>
</div>
