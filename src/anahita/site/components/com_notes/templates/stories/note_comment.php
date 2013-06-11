<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">     
    <?= sprintf(@text('COM-NOTES-STORY-COMMENT'), @name($subject),@route($object->getURL()), @possessive($target)) ?>
</data>

<data name="body">
    <?= @content($object->body) ?>
</data>

<?php if ($type == 'notification') :?>
<?php $commands->insert('view-comment', array('label'=> @text('LIB-AN-VIEW-COMMENT')))->href($object->getURL().'&permalink='.$comment->id)?>
<data name="email_body">    
    <?= $comment->body ?>
</data>
<?php endif;?>
