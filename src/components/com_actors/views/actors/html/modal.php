<? defined('KOOWA') or die ?>

<div class="modal-header">
    &nbsp;
</div>

<div class="modal-body">

<div class="an-entities" id="an-entities-main">
	<div id="an-actors" class="an-entities">
		<? foreach ($items as $item) : ?>
            <div class="an-entity">
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
            </div>
		<? endforeach; ?>
	</div>
</div>

</div>
