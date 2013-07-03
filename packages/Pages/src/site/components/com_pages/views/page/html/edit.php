<?php defined('KOOWA') or die('Restricted access');?>

<module position="sidebar-b" style="none">
<?php if ($actor->authorize('administration') ) : ?>
 
<?= @helper('ui.gadget', LibBaseTemplateObject::getInstance('revisions', array(
	'title' => @text('COM-PAGES-PAGE-REVISIONS'),
	'url'	=> 'view=revisions&layout=gadget&pid='.$page->id.'&oid='.$actor->id
))); ?>

<?php endif; ?>
</module>

<?php if(!$page->published): ?>
<?= @message(@text('COM-PAGES-PAGE-IS-UNPUBLISHED'), array('type'=>'warning')) ?>
<?php endif; ?>

<?= @template('form') ?>