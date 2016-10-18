<? defined('KOOWA') or die; ?>

<div class="row">
	<div class="span8">
		<?= @helper('ui.header') ?>

		<? if ($type == 'leadables'): ?>
		<h3><?= @text('COM-ACTORS-SOCIALGRAPH-FOLLOWERS-ADD-TITLE') ?></h3>
        <? endif; ?>
		<? $url = $actor->getURL().'&layout=list&get=graph&type='.$type.'&id='.$actor->id; ?>
        <?= @helper('ui.filterbox', @route($url)) ?>
				<?= @infinitescroll(null, array(
					'url' => $url,
					'id' => 'an-actor-socialgraph'
				)) ?>
	</div>

	<div class="span4 visible-desktop">
		<h3 class="block-title"><?= @text('COM-ACTORS-SOCIALGRAPH-STATS') ?></h3>
		<div class="block-content an-socialgraph-stat">
            <? if ($actor->isFollowable()) : ?>
            <div class="stat-count">
            	<?= $actor->followerCount ?>
            	<span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-FOLLOWERS') ?></span>
            </div>
            <? endif; ?>

            <? if ($actor->isLeadable()) : ?>
            <div class="stat-count">
            <?= $actor->leaderCount ?>
            <span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-LEADERS') ?></span>
            </div>
            <? endif; ?>
        </div>
	</div>
</div>
