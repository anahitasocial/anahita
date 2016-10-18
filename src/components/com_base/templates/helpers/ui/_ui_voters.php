<? defined('KOOWA') or die; ?>
<?
$query = 'avatars=1';
if (is($entity, 'ComBaseDomainEntityComment')) {
    $query = 'comment[avatars]=1';
}
$url = $entity->getURL().'&get=voters&'.$query
?>
<? if ($entity->voteUpCount > 0): ?>

    <? if ($entity->voteUpCount == 1) : ?>
        <? if ($entity->voterUpIds->offsetExists($viewer->id)) : ?>
            <?= @text('LIB-AN-VOTE-ONLY-YOU-VOTED')?>
        <? else :?>
        	<? $ids = $entity->voterUpIds->toArray(); ?>
            <?= sprintf(@text('LIB-AN-VOTE-ONE-VOTED'), @name(@service('repos:actors.actor')->fetch(end($ids)))) ?>
        <? endif;?>
    <? elseif ($entity->voteUpCount > 1) : ?>
        <? if ($entity->voterUpIds->offsetExists($viewer->id)) : ?>
            <? if ($entity->voteUpCount == 2) : ?>
                <?
                $ids = $entity->voterUpIds->toArray();
                unset($ids[$viewer->id]);
                ?>
                <?= sprintf(@text('LIB-AN-VOTE-YOU-AND-ONE-PERSON'),  @name(@service('repos:actors.actor')->fetch(end($ids))))?>
            <? else : ?>
                <?= sprintf(@text('LIB-AN-VOTE-YOU-AND-OTHER-VOTED-POPOVER'), @route($url), $entity->voteUpCount - 1)?>
            <? endif;?>
        <? else :?>
            <?= sprintf(@text('LIB-AN-VOTE-OTHER-VOTED-POPOVER'), @route($url), $entity->voteUpCount)?>
        <? endif;?>
    <? endif;?>

<? endif; ?>
