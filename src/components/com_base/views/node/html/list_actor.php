<? defined('KOOWA') or die; ?>

<div class="an-entity">
	<div class="clearfix">
		<div class="entity-portrait-square">
			<?= @avatar($item) ?>
		</div>

		<div class="entity-container">
			<h4 class="entity-name">
				<?= @name($item) ?>
			</h4>

			<ul class="an-meta inline">
				<li>
					<?= $item->followerCount ?>
					<span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-FOLLOWERS') ?></span>
					<? if ($item->isLeadable()): ?>
					/ <?= $item->leaderCount ?>
					<span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-LEADERS') ?></span>
					<? endif; ?>
				</li>
			</ul>
		</div>
	</div>

	<div class="entity-description">
		<?= @helper('text.truncate', @content($item->body, array('exclude' => array('syntax', 'video'))), array('length' => 400, 'consider_html' => true)) ?>
	</div>

	<? if(count($locations)) : ?>
	<div class="entity-meta">
			<?= @template('_locations') ?>
	</div>
	<? endif;?>
</div>
