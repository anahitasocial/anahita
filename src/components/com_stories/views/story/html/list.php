<?php defined('KOOWA') or die ?>
<div class="an-story an-entity an-record an-removable">
    <div class="entity-portrait-square">
        <?= is_array($subject) ? @avatar(array_shift($subject)) : @avatar($subject)?>
    </div>     
     
    <div class="story-container"> 
        <h4 class="story-title"><?= $title ?></h4>
        <?php if ( !empty($body) ) : ?>
        <div class="story-body">
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
        
        <div class="entity-actions">    
        <?php $can_comment = $commands->offsetExists('comment') ?>
        <?= @helper('ui.commands', $commands)?>
        </div>      
    </div>
    
    <div id="<?= 'story-comments-'.$item->id?>" class="story-comments an-comments">
		<?php if ( !empty($comments) || $can_comment ) : ?>
	    <?= @helper('ui.comments', $item, array('comments'=>$comments, 'can_comment'=>$can_comment, 'pagination'=>false, 'show_guest_prompt'=>false, 'truncate_body'=>array('consider_html'=>true, 'read_more'=>true))) ?>
	    <?php endif;?>
	    
	    <?php if( !empty($comments) && $can_comment ): ?>
	    <div class="comment-overtext-box">  
	    	<span class="action-comment-overtext" storyid="<?=$item->id?>">
	        	<?= @text('COM-STORIES-ADD-A-COMMENT') ?>
	        </span>
	    </div>
	    <?php endif; ?>
	</div>
</div>