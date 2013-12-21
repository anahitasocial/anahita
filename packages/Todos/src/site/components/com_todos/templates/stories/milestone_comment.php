<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
    <?= sprintf( @text('COM-TODOS-STORY-NEW-MILESTONE-COMMENT'), @name($subject), @route($object->getURL())) ?>
</data>

<data name="body">
    <h4 class="entity-title">
    	<a href="<?= @route($object->getURL()) ?>">
    		<?= $object->title ?>
    	</a>
    </h4>
    <div class="entity-body">
	    <?= @helper('text.truncate', @content(strip_tags($object->body), array('exclude'=>'syntax')), array('length'=>200, 'consider_html'=>true, 'read_more'=>true)); ?>
	</div>	
</data>

<?php if ($type == 'notification') :?>
<?php $commands->insert('viewcomment', array('label'=>@text('LIB-AN-VIEW-COMMENT')))->href($object->getURL().'&permalink='.$comment->id)?>
<data name="email_body">
    <h4 class="entity-title">
    	<?= @link($object)?>
    </h4>
    <div class="entity-body">
	    <?= $comment->body ?>
	</div>		
</data>
<?php endif;?>