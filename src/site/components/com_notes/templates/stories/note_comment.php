<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">     
	<?php if( $object->access != 'public' ): ?>
    <i class="icon-lock"></i> 
    <?php endif; ?>
    <?= sprintf(@text('COM-NOTES-STORY-COMMENT'), @name($subject), @route($object->getURL())) ?> 
</data>

<data name="body">
	<div class="entity-body">
    <?= @helper('text.truncate', @content($object->body, array('exclude'=>'syntax')), array('length'=>200, 'consider_html'=>true, 'read_more'=>true)); ?>
    </div>
</data>

<?php if ($type == 'notification') :?>
<?php $commands->insert('view-comment', array('label'=> @text('LIB-AN-VIEW-COMMENT')))->href($object->getURL().'&permalink='.$comment->id)?>
<data name="email_body">    
    <?= $comment->body ?>
</data>
<?php endif;?>
