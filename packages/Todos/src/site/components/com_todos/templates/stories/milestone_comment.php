<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
    <?= sprintf( @text('COM-TODOS-STORY-NEW-MILESTONE-COMMENT'), @name($subject)) ?>
</data>

<data name="body">
    <?= @helper('text.truncate',  @content($object->body, array('exclude'=>'syntax')), array('length'=>200, 'consider_html'=>true, 'read_more'=>true)); ?>
</data>

<?php if ($type == 'notification') :?>
<?php $commands->insert('viewcomment', array('label'=>@text('LIB-AN-VIEW-COMMENT')))->href($object->getURL().'&permalink='.$comment->id)?>
<data name="email_body">    
    <?= $comment->body ?>
</data>
<?php endif;?>