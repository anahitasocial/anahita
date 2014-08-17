<?php defined('KOOWA') or die('Restricted access');?>

<module position="sidebar-b" style="none">
<?php if ($actor->authorize('administration') ) : ?>
 
<?= @helper('ui.gadget', LibBaseTemplateObject::getInstance('revisions', array(
	'title' => @text('COM-PAGES-PAGE-REVISIONS'),
	'url'	=> 'view=revisions&layout=gadget&pid='.$page->id.'&oid='.$actor->id
))); ?>

<?php endif; ?>
</module>

<?= @template('form') ?>