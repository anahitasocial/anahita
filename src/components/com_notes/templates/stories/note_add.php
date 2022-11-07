<? defined('ANAHITA') or die('Restricted access'); ?>

<data name="title">
    <? if ($object->access != 'public') : ?>
    <i class="icon-lock"></i>
    <? endif;?>
    <?= sprintf(@text('COM-NOTES-STORY-ADD'), @name($subject), @route($object->getURL())); ?>
</data>

<data name="body">
	<div class="entity-body">
		<?= @helper('text.truncate', nl2br($object->body), array('length' => 200, 'consider_html' => true, 'read_more' => true)); ?>
	</div>
</data>

<? if ($type == 'notification') : ?>
<data name="email_body">
<div><?= @body($object->body) ?></div>
<? $commands->insert('viewstory', array('label' => @text('LIB-AN-MEDIUM-VIEW')))->href($object->getURL()); ?>
</data>
<? endif; ?>
