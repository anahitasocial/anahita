<? defined('KOOWA') or die ?>

<div class="an-socialgraph-stat">
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

    <? if ($actor->isLeadable() && $viewer->isLeadable()) : ?>
        <? $commons = $actor->getCommonLeaders($viewer); ?>
        <? if (isset($commons) && !$viewer->eql($actor) && $commons->getTotal()) : ?>
        <div class="stat-count">
            <?= $commons->getTotal() ?>
            <span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-COMMON') ?></span>
        </div>
        <? endif; ?>
    <? endif; ?>
</div>

<? $limit = 7; ?>

<? if ($actor->leaderCount + $actor->followerCount) : ?>
<div class="an-gadget-socialgraph">
<? if ($actor->followerCount) : ?>
<h4>
	<?= @text('COM-ACTORS-SOCIALGRAPH-FOLLOWERS') ?>
	<? if ($actor->authorize('lead')): ?> -
	<small>
		<a href="<?= @route($actor->getURL().'&get=graph&type=leadables') ?>">
			<?= @text('LIB-AN-ACTION-ADD') ?>
		</a>
	</small>
	<? endif; ?>
</h4>
<?= @template('_grid', array('actors' => $actor->followers->order('updateTime', 'DESC')->limit($limit))) ?>
<? endif; ?>

<? if ($actor->leaderCount) : ?>
<h4><?= @text('COM-ACTORS-SOCIALGRAPH-LEADERS') ?></h4>
<?= @template('_grid', array('actors' => $actor->leaders->order('updateTime', 'DESC')->limit($limit))) ?>
<? endif; ?>

<? if (count($actor->administrators)): ?>
<h4><?= @text('COM-ACTORS-PROFILE-ADMINS') ?></h4>
<?= @template('_grid', array('actors' => $actor->administrators)) ?>
<? endif; ?>
</div>
<? else : ?>
<?= @message(@text('COM-ACTORS-SOCIALGRAPH-EMPTY-MESSAGE')) ?>
<? endif; ?>
