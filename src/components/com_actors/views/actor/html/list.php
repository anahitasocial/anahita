<? defined('KOOWA') or die ?>

<? $commands = @commands('list') ?>
<? $highlight = ($item->isEnableable() && !$item->enabled) ? 'an-highlight' : '' ?>
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

			<? if ($item->isLeadable()): ?>
			/ <?= $item->leaderCount ?>
			<span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-LEADERS') ?></span>
			<? endif; ?>
			</div>
		</div>
	</div>

	<div class="entity-description">
		<?= @helper('text.truncate', @content($item->body, array('exclude' => array('syntax', 'video'))), array('length' => 200, 'consider_html' => true)); ?>
	</div>

	<? if (count($commands)) : ?>
	<div class="entity-actions">
		<? if ($action = $commands->extract('follow')) : ?>
			<?= @helper('ui.command', $action->class('btn btn-primary')) ?>
		<? elseif ($action = $commands->extract('unfollow')) : ?>
			<?= @helper('ui.command', $action->class('btn'))?>
		<? endif;?>

		<? foreach ($commands as $action) : ?>
			<?= @helper('ui.command', $action->class('btn')) ?>
		<? endforeach;?>
	</div>
	<? endif; ?>
</div>
