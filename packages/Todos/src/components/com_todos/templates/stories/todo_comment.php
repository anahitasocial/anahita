<? defined('ANAHITA') or die('Restricted access');?>

<data name="title">
	<?= sprintf(@text('COM-TODOS-STORY-NEW-TODO-COMMENT'), @name($subject), @route($object->getURL().'&permalink='.$comment->id)) ?>
</data>

<data name="body">
    <h4 class="entity-title">
    	<a href="<?= @route($object->getURL()) ?>">
    		<?= $object->title ?>
    	</a>
    </h4>
    <div class="entity-body">
		<blockquote>
	    	<?= @helper('text.truncate', @body($object->body), array('length' => 200, 'consider_html' => true, 'read_more' => true)); ?>
		</blockquote>
	</div>
</data>

<? if ($type == 'notification') :?>
<? $commands->insert('viewcomment', array('label' => @text('LIB-AN-VIEW-COMMENT')))->href($object->getURL().'&permalink='.$comment->id)?>
<data name="email_body">
    <h4 class="entity-title">
    	<?= @link($object)?>
    </h4>
    <div class="entity-body">
	    <?= @body($comment->body) ?>
	</div>
</data>
<? endif;?>
