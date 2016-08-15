<? if (count($toolbar->getCommands())) : ?>

<? $commands = $toolbar->getCommands(); ?>

<div class="btn-toolbar">
    <? if ($new = $commands->extract('new')) :?>
         <?= @html('tag', 'a', $new->label, $new->getAttributes())->class('btn btn-primary') ?>
    <? endif;?>

    <? if ($vote = $commands->extract('vote')) : ?>
         <?= @html('tag', 'a', $vote->label, $vote->getAttributes())->class('action-vote btn') ?>
    <? elseif ($unvote = $commands->extract('unvote')) : ?>
         <?= @html('tag', 'a', $unvote->label, $unvote->getAttributes())->class('action-unvote btn') ?>
    <? endif;?>

    <? if ($commands->count() > 1) : ?>
    <div class="btn-group pull-right">
        <?= @helper('ui.dropdown', $commands)?>
    </div>
    <? elseif ($commands->count() == 1) : ?>
        <? $command = $commands->extract(); ?>
    	<div class="pull-right">
    	<span><?= @html('tag', 'a', $command->label, $command->getAttributes())->class('btn') ?></span>
    	</div>
    <? endif; ?>
</div>
<? endif;?>
