<? defined('KOOWA') or die; ?>

<? if ($item->inherits('ComBaseDomainEntityComment')): ?>
<?= @template('list_comment') ?>
<? elseif ($item->inherits('ComActorsDomainEntityActor')): ?>
<?= @template('list_actor') ?>
<? else: ?>
<?= @template('list_node') ?>
<? endif; ?>
