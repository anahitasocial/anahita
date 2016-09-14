<? defined('KOOWA') or die('Restricted access'); ?>

<data name="title">
    <? if ($object->access != 'public') : ?>
    <i class="icon-lock"></i>
    <? endif;?>
    <?= sprintf(@text('COM-NOTES-STORY-ADD'), @name($subject), @route($object->getURL())); ?>
</data>

<data name="body">
	<div class="entity-body">
		<?= @helper('text.truncate', @content(nl2br($object->body), array('exclude' => 'gist')), array('length' => 200, 'consider_html' => true, 'read_more' => true)); ?>
	</div>
</data>

<? if ($type == 'notification') : ?>
<data name="email_body">
<div><?= $object->body ?></div>
<? $commands->insert('viewstory', array('label' => @text('COM-NOTES-VIEW-POST')))->href($object->getURL()); ?>
</data>
<? endif; ?>
