<?php defined('KOOWA') or die ?>

<module position="sidebar-b" style="simple"></module>

<div class="an-entity">
    <div class="entity-portrait-square">
        <?= @avatar($subject) ?>
    </div>
          
    <div class="entity-container"> 
        <h3 class="entity-title"><?= $title ?></h4>
        <?php if ( !empty($body) ) : ?>
        <div class="entity-body">
            <?= $body ?>
        </div>
        <?php endif; ?>
        
        <div class="entity-meta">
            <?= @date($timestamp) ?>
            
            <?php
               $votable_item = null;               
               if ( $item->hasObject() && !is_array($item->object) ) { 
                    $votable_item = $item->object;
               }
               elseif ( !$item->hasObject() ) {
                    $votable_item = $item;
               }
            ?>
            <?php if ( $votable_item && $votable_item->isVotable() ) : ?> 
            <div class="vote-count-wrapper" id="vote-count-wrapper-<?= $votable_item->id ?>">
            <?= @helper('ui.voters', $votable_item); ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= @helper('ui.comments', $story) ?>  