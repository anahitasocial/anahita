<? defined('KOOWA') or die('Restricted access'); ?>

<data name="title">
	<? if ($object->access != 'public'): ?>
    <i class="icon-lock"></i>
    <? endif; ?>
    <?= sprintf(@text('COM-NOTES-STORY-COMMENT'), @name($subject), @route($object->getURL().'&permalink='.$comment->id)); ?>
</data>

<data name="body">
	<div class="entity-body">
		<blockquote>
	  <?= @helper('text.truncate', @content(nl2br($object->body), array('exclude' => 'gist')), array('length' => 200, 'consider_html' => true, 'read_more' => true)); ?>
	</blockquote>
	</div>
</data>

<? if ($type == 'notification') : ?>
<? $commands->insert('view-comment', array('label' => @text('LIB-AN-VIEW-COMMENT')))->href($object->getURL().'&permalink='.$comment->id); ?>
<data name="email_body">
    <?= $comment->body ?>
</data>
<? endif; ?>
