<?php defined('KOOWA') or die; ?>

<?php if($type != 'leadables'): ?>
<module position="sidebar-b" title="<?= @text('COM-ACTORS-SOCIALGRAPH-STATS') ?>">  
    <div class="an-socialgraph-stat">
        <?php if($actor->isFollowable()) : ?>
        <div class="stat-count">
            <?= $actor->followerCount ?>
            <span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-FOLLOWERS') ?></span>
        </div>
        <?php endif; ?>
        
        <?php if($actor->isLeadable()) : ?>
        <div class="stat-count">
            <?= $actor->leaderCount ?>
            <span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-LEADERS') ?></span>
        </div>        
        <?php endif; ?>
        
        <?php if($actor->isLeadable() && $actor->isFollowable() && $actor->mutualCount) : ?>
        <div class="stat-count">
            <?= $actor->mutualCount ?>
            <span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-MUTUALS') ?></span>
        </div>
        <?php endif; ?>
    </div>
</module>
<?php endif; ?>

<?php if($type == 'leadables'): ?>
<h3><?= @text('COM-ACTORS-SOCIALGRAPH-FOLLOWERS-ADD-TITLE') ?></h3>
<?php endif; ?>

<?= @helper('ui.filterbox', @route('view='.@listItemView()->getName().'&layout=list&get=graph&type='.$type.'&id='.$actor->id))?>

<div class="an-entities-wrapper">	
	<?= @template('list') ?>
</div>