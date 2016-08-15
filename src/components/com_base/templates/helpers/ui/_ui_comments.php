<? defined('KOOWA') or die('Restricted access') ?>

<div class="an-comments-wrapper">
<div class="an-comments an-entities">
	<? foreach ($comments as $comment) : ?>
	<?= @view('comment')->comment($comment)->truncate_body($truncate_body)->content_filter_exclude($content_filter_exclude) ?>
	<? endforeach; ?>
</div>

<? if (!empty($pagination)) : ?>
<?= $pagination ?>
<? endif; ?>

<? if ($can_comment) : ?>
<?= @view('comment')->load('form', array('parent' => $entity, 'comment' => null))?>
<? endif;?>

<? if ($show_guest_prompt && !$can_comment) : ?>
    <? if ($viewer->guest()) : ?>
        <?= $entity->get('type') ?>
        <? $return = base64_encode(@route($entity->getURL())); ?>

    <? elseif (!$entity->openToComment) : ?>
        <?= @message(@text('LIB-AN-COMMENTS-ARE-CLOSED')) ?>
    <? elseif (!empty($require_follow)) : ?>
       <div class="alert alert-info">
           <p><?= sprintf(@text('LIB-AN-COMMENT-MUST-FOLLOW'), $entity->owner->name) ?></p>
           <p>
               <a class="btn" data-action="follow" data-actor="<?= $entity->owner->id ?>" href="<?= @route($entity->owner->getURL()) ?>">
               <?= @text('COM-ACTORS-SOCIALGRAPH-FOLLOW')?>
               </a>
           </p>
       </div>
    <? else : ?>
        <?= @message(@text('LIB-AN-COMMENT-NO-PERMISSION')) ?>
    <? endif; ?>
<? endif; ?>
</div>
