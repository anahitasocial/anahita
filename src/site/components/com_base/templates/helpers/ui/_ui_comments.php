<?php defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<div class="an-comments-wrapper">
<div class="an-comments an-entities">
	<?php foreach($comments as $comment) : ?>
	<?= @view('comment')->comment($comment)->truncate_body($truncate_body)->content_filter_exclude($content_filter_exclude) ?>
	<?php endforeach; ?>
</div>

<?php if (!empty($pagination)) : ?>
<?= $pagination ?>
<?php endif; ?>

<?php if ( $can_comment ) : ?>
<?= @view('comment')->load('form', array( 'parent'=>$entity, 'comment'=>null))?>
<?php endif;?>

<?php if ( $show_guest_prompt && !$can_comment ) : ?>
    <?php if( $viewer->guest() ) : ?>
        <?= $entity->get('type') ?>
        <?php $return = base64_encode(@route($entity->getURL())); ?>
        
    <?php elseif ( !$entity->openToComment ) : ?>
        <?= @message(@text('LIB-AN-COMMENTS-ARE-CLOSED')) ?>
    <?php elseif ( !empty($require_follow) ) : ?>
       <div class="alert alert-info">
           <p><?= sprintf(@text('LIB-AN-COMMENT-MUST-FOLLOW'), $entity->owner->name) ?></p>
           <p>
               <a class="btn" data-action="follow" data-actor="<?= $entity->owner->id ?>" href="<?= @route( $entity->owner->getURL() ) ?>">
               <?= @text('COM-ACTORS-SOCIALGRAPH-FOLLOW')?>
               </a>
           </p>
       </div>
    <?php else : ?>
        <?= @message(@text('LIB-AN-COMMENT-NO-PERMISSION')) ?>
    <?php endif; ?>    
<?php endif; ?>
</div>