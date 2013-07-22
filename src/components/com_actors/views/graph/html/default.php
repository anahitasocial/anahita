<?php defined('KOOWA') or die; ?>

<?= @helper('ui.searchbox', @route('view='.@listItemView()->getName().'&layout=list&get=graph&type='.$type.'&id='.$actor->id))?>

<module position="sidebar-b" title="<?= @text('COM-ACTORS-SOCIALGRAPH-STATS') ?>">  
    <div class="an-socialgraph-stat">
        <?php if ( $actor->isFollowable() ) : ?>
        <div class="stat-count">
            <?= $actor->followerCount ?>
            <span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-FOLLOWERS') ?></span>
        </div>
        <?php endif; ?>
        <?php if ( $actor->isLeadable() ) : ?>
            <div class="stat-count">
                <?= $actor->leaderCount ?>
                <span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-LEADERS') ?></span>
            </div>        
        <?php endif; ?>
        <?php if( $actor->isLeadable() && $actor->isFollowable() && $actor->mutualCount ) : ?>
        <div class="stat-count">
            <?= $actor->mutualCount ?>
            <span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-MUTUALS') ?></span>
        </div>
        <?php endif; ?>
    </div>
</module>



<div>
	<?= @template('list') ?>
</div>
	
<div class="an-loading-prompt hide">
	<?= @message(@text('LIB-AN-LOADING-PROMPT')) ?>
</div>