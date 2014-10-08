<?php defined('KOOWA') or die('Restricted access') ?>

<div class="row">	
	<div class="span8">
	<?= @helper('ui.header', array()) ?>
	<?= @template('topic') ?>
	<?= @helper('ui.comments', $topic, array('editor'=>true)) ?>
	</div>
	
	<?php if($actor->authorize('administration')): ?>
	<div class="span4">
	<h4><?= @text('COM-TOPICS-TOPIC-PRIVACY') ?></h4>
	<?= @helper('ui.privacy', $topic) ?>
	</div>
	<?php endif; ?>
</div>