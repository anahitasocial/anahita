<?php defined('KOOWA') or die; ?>

<?php if ($item->inherits('ComBaseDomainEntityComment')): ?>
<?= @template('list_comment') ?>
<?php elseif ($item->inherits('ComActorsDomainEntityActor')): ?>
<?= @template('list_actor') ?>
<?php else: ?>
<?= @template('list_node') ?>
<?php endif; ?>
