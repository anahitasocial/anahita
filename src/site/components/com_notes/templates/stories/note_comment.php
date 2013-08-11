<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">     
    <?= sprintf(@text('COM-NOTES-STORY-COMMENT'), @route($object->getURL())) ?> 
    <?php if( $object->access != 'public' ): ?>
    <i class="icon-lock pull-right"></i> 
    <?php endif; ?>
</data>

<data name="body">
    <?= @helper('text.truncate', @content($object->body, array('exclude'=>'syntax')), array('length'=>200, 'consider_html'=>true, 'read_more'=>true)); ?>
</data>

<?php if ($type == 'notification') :?>
<?php $commands->insert('view-comment', array('label'=> @text('LIB-AN-VIEW-COMMENT')))->href($object->getURL().'&permalink='.$comment->id)?>
<data name="email_body">    
    <?= $comment->body ?>
</data>
<?php endif;?>
