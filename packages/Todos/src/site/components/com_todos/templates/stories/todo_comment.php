<?php defined('KOOWA') or die('Restricted access');?>

<data name="title">
	<?= sprintf( @text('COM-TODOS-STORY-NEW-TODO-COMMENT'), @name($subject), @route($object->getURL())) ?>
</data>

<data name="body">
    <h1 class="entity-title">
    	<a href="<?= @route($object->getURL()) ?>">
    		<?= $object->title ?>
    	</a>
    </h1>
    <div class="entity-body">
	    <?= @helper('text.truncate', @content(strip_tags($object->body), array('exclude'=>'syntax')), array('length'=>200, 'consider_html'=>true, 'read_more'=>true)); ?>
	</div>	
</data>

<?php if ($type == 'notification') :?>
<?php $commands->insert('viewcomment', array('label'=>@text('LIB-AN-VIEW-COMMENT')))->href($object->getURL().'&permalink='.$comment->id)?>
<data name="email_body">
    <h1 class="entity-title">
    	<?= @link($object)?>
    </h1>
    <div class="entity-body">
	    <?= $comment->body ?>
	</div>		
</data>
<?php endif;?>





