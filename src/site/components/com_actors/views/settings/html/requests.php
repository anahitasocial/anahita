<?php defined('KOOWA') or die('Restricted access');?>

<h3><?= @text('COM-ACTORS-PROFILE-EDIT-REQUESTS') ?></h3>

<div id="an-actors" class="an-entities an-actors">
    <?php foreach($item->requesters as $actor ) : ?>
    <div class="an-entity">
        <div class="entity-portrait-square">
            <?= @avatar($actor) ?>
        </div>
        
        <div class="entity-container">
            <h3 class="entity-name"><?= @name($actor) ?></h3>
            
            <div class="entity-meta">
                <?= $actor->followerCount ?>
                <span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-FOLLOWERS') ?></span> 
                / <?= $item->leaderCount ?>
                <span class="stat-name"><?= @text('COM-ACTORS-SOCIALGRAPH-LEADERS') ?></span>
            </div>
            
            <div class="entity-description">
            <?= @helper('text.truncate',strip_tags($actor->description), array('length'=>200)); ?>
            </div>
                
            <div class="entity-actions">
            	<?php $url = $item->getURL().'&layout=list'; ?>
                <button data-action="ignorerequest" data-actor="<?= $actor->id ?>" href="<?= @route($url) ?>" class="btn">
                    <?= @text('LIB-AN-ACTION-IGNORE') ?>
                </button> 
                            
                <button data-action="confirmrequest" data-actor="<?= $actor->id ?>" href="<?= @route($url) ?>" class="btn btn-primary">
                    <?= @text('LIB-AN-ACTION-CONFIRM') ?>
                </button>                            
            </div>
        </div>
    </div>      
    <?php endforeach; ?>
</div>