<?php defined('KOOWA') or die('Restricted access');?>

<div class="row">
	<div class="span8">
	<?= @template('form') ?>
	</div>
	
	<?php if ($actor->authorize('administration')) : ?>
	<div class="span4">
	<?= @helper('ui.gadget', LibBaseTemplateObject::getInstance('revisions', array(
        'title' => @text('COM-ARTICLES-ARTICLE-REVISIONS'),
        'url' => 'view=revisions&layout=gadget&pid='.$article->id.'&oid='.$actor->id,
    ))); ?>
	</div>
	<?php endif; ?>
</div>