<? defined('KOOWA') or die('Restricted access');?>

<data name="title">
	<?= sprintf(@text('COM-TOPICS-STORY-MENTION-COMMENT'), @name($subject), @route($object->getURL().'&permalink='.$comment->id)); ?>
</data>

<data name="body">
    <h4 class="entity-title">
    	<?= @link($object)?>
    </h4>
    <div class="entity-body">
			<blockquote>
        <? $body = @content($object->body, array('exclude' => 'gist')); ?>
        <?= @helper('text.truncate', $body, array('length' => 200, 'read_more' => true, 'consider_html' => true)); ?>
			</blockquote>
		</div>
</data>

<? if ($type == 'notification') :?>
<? $commands->insert('view-comment', array('label' => @text('LIB-AN-VIEW-COMMENT')))->href($object->getURL().'&permalink='.$comment->id)?>
<data name="email_body">
    <h4 class="entity-title">
    	<?= @link($object)?>
    </h4>
    <div class="entity-body">
	    <?= $comment->body ?>
	</div>
</data>
<? endif;?>
