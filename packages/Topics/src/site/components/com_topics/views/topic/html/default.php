<?php defined('KOOWA') or die('Restricted access') ?>

<?php if ( $actor->authorize('administration') ) : ?>
<module position="sidebar-b" title="<?= @text('COM-TOPICS-TOPIC-PRIVACY') ?>">
	<?= @helper('ui.privacy', $topic) ?>
</module>
<?php else: ?>
<module position="sidebar-b" style="none"></module>
<?php endif; ?>

<?= @template('topic') ?>

<?= @helper('ui.comments', $topic, array('editor'=>true)) ?>