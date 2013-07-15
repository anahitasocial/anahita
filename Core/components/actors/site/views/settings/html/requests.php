<?php defined('KOOWA') or die('Restricted access');?>

<h3><?= @text('COM-ACTORS-PROFILE-EDIT-REQUESTS') ?></h3>

<div id="an-actors" class="an-entities an-actors">
    <?php foreach($item->requesters as $actor ) : ?>
    <div class="an-entity an-record">
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
                <button data-trigger="Submit" href="<?= @route($item->getURL().'&action=addblocked&actor='.$actor->id) ?>" class="btn">
                    <i class="icon-ban-circle"></i>&nbsp;<?= @text('COM-ACTORS-SOCIALGRAPH-BLOCK') ?>
                </button>
                <button data-trigger="Submit" href="<?= @route($item->getURL().'&action=ignorerequester&requester='.$actor->id) ?>" class="btn">
                    <i class="icon-remove"></i>&nbsp;<?= @text('LIB-AN-ACTION-REMOVE') ?>
                </button>            
                <button data-trigger="Submit" href="<?= @route($item->getURL().'&action=confirmrequester&requester='.$actor->id) ?>" class="btn">
                    <i class="icon-ok"></i>&nbsp;<?= @text('LIB-AN-ACTION-CONFIRM') ?>
                </button>                            
            </div>
        </div>
    </div>      
    <?php endforeach; ?>
</div>