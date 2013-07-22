<?php defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<div class="an-comments-wrapper">
<div class="an-comments an-entities">
	<?php foreach($comments as $comment) : ?>
	<?= @view('comment')->comment($comment)->strip_tags($strip_tags)->truncate_body($truncate_body)->editor($editor) ?>
	<?php endforeach; ?>
</div>

<?php if (!empty($pagination)) : ?>
<?= $pagination ?>
<?php endif; ?>

<?php if ( $can_comment ) : ?>
<?= @view('comment')->load('form', array('parent'=>$entity,'editor'=>$editor,'comment'=>null))?>
<?php endif;?>

<?php if ( $show_guest_prompt && !$can_comment ) : ?>
    <?php if( $viewer->guest() ) : ?>
        <?= $entity->get('type') ?>
        <?php $return = base64_encode(@route($entity->getURL())); ?>
        <?= @message(sprintf(@text('LIB-AN-COMMENT-GUEST-MUST-LOGIN'), @route(array('option'=>'com_user', 'view'=>'login', 'return'=>$return))), array('type'=>'warning')) ?>
    <?php elseif ( !$entity->openToComment ) : ?>
        <?= @message(@text('LIB-AN-COMMENTS-ARE-CLOSED')) ?>
    <?php elseif ( !empty($require_follow) ) : ?>
       <div class="alert alert-info">
            <p><?= sprintf(@text('LIB-AN-COMMENT-MUST-FOLLOW'), $entity->owner->name) ?></p>
            <p><a class="btn" data-trigger="Submit" href="<?= @route($entity->owner->getURL().'&action=follow') ?>"><?= @text('COM-ACTORS-SOCIALGRAPH-FOLLOW')?></a></p>
       </div>
    <?php else : ?>
        <?= @message(@text('LIB-AN-COMMENT-NO-PERMISSION')) ?>
    <?php endif; ?>    
<?php endif; ?>
</div>