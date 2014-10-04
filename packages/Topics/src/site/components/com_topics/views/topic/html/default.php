<?php defined('KOOWA') or die('Restricted access') ?>

<div class="row">	
	<div class="span8">
	
	<?php if($actor->authorize('administration')): ?>
	<h4><?= @text('COM-TOPICS-TOPIC-PRIVACY') ?></h4>
	<?= @helper('ui.privacy', $topic) ?>
	<?php endif; ?>
	
	<?= @template('topic') ?>
	<?= @helper('ui.comments', $topic, array('editor'=>true)) ?>
	</div>
</div>